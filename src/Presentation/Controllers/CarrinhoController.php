<?php

namespace Presentation\Controllers;

use Domain\Services\CarrinhoService;
use Domain\Services\ViaCEPService;
use Domain\Services\EmailService;
use Infrastructure\Repositories\ProdutoRepository;
use Infrastructure\Repositories\CupomRepository;
use Infrastructure\Database\Connection;

class CarrinhoController
{
    private CarrinhoService $carrinho;
    private ProdutoRepository $produtoRepository;
    private CupomRepository $cupomRepository;
    private ViaCEPService $viaCEPService;
    private EmailService $emailService;
    private $connection;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->carrinho = CarrinhoService::obterCarrinho();
        $this->produtoRepository = new ProdutoRepository();
        $this->cupomRepository = new CupomRepository();
        $this->viaCEPService = new ViaCEPService();
        $this->emailService = new EmailService();
        $this->connection = Connection::getInstance();
    }

    public function index(): void
    {
        $endereco = $_SESSION['endereco_entrega'] ?? null;
        $carrinho = $this->carrinho;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            
            switch ($action) {
                case 'adicionar':
                    $produto_id = (int) ($_POST['produto_id'] ?? 0);
                    $quantidade = (int) ($_POST['quantidade'] ?? 1);
                    $produto = $this->produtoRepository->findById($produto_id);
                    
                    if ($produto) {
                        $this->carrinho->adicionarProduto($produto, $quantidade);
                    }
                    break;

                case 'remover':
                    $produto_id = (int) ($_POST['produto_id'] ?? 0);
                    $this->carrinho->removerProduto($produto_id);
                    break;

                case 'atualizar':
                    $produto_id = (int) ($_POST['produto_id'] ?? 0);
                    $quantidade = (int) ($_POST['quantidade'] ?? 1);
                    $this->carrinho->atualizarQuantidade($produto_id, $quantidade);
                    break;

                case 'limpar':
                    $this->carrinho->limpar();
                    unset($_SESSION['endereco_entrega']);
                    break;

                case 'aplicar_cupom':
                    $codigo = $_POST['codigo_cupom'] ?? '';
                    $cupom = $this->cupomRepository->findByCodigo($codigo);
                    
                    if ($cupom) {
                        if (!$this->carrinho->aplicarCupom($cupom)) {
                            $_SESSION['message'] = [
                                'type' => 'danger',
                                'text' => 'Cupom inválido ou valor mínimo não atingido'
                            ];
                        }
                    } else {
                        $_SESSION['message'] = [
                            'type' => 'danger',
                            'text' => 'Cupom não encontrado'
                        ];
                    }
                    break;

                case 'calcular_frete':
                    $cep = $_POST['cep'] ?? '';
                    $endereco = $this->viaCEPService->consultarCEP($cep);
                    
                    if ($endereco) {
                        $_SESSION['endereco_entrega'] = $endereco;
                    } else {
                        $_SESSION['message'] = [
                            'type' => 'danger',
                            'text' => 'CEP não encontrado'
                        ];
                    }
                    break;

                case 'finalizar':
                    if (empty($_SESSION['endereco_entrega'])) {
                        $_SESSION['message'] = [
                            'type' => 'danger',
                            'text' => 'Informe o CEP para entrega'
                        ];
                        break;
                    }

                    $email = $_POST['email'] ?? '';
                    if (empty($email)) {
                        $_SESSION['message'] = [
                            'type' => 'danger',
                            'text' => 'Informe um e-mail válido'
                        ];
                        break;
                    }

                    try {
                        $this->connection->beginTransaction();

                        $stmt = $this->connection->prepare(
                            "INSERT INTO pedidos (subtotal, frete, total, cep, status, email) VALUES (?, ?, ?, ?, ?, ?)"
                        );
                        $stmt->execute([
                            $this->carrinho->getSubtotal(),
                            $this->carrinho->getFrete(),
                            $this->carrinho->getTotal(),
                            $_SESSION['endereco_entrega']['cep'],
                            'pendente',
                            $email
                        ]);

                        // Enviar e-mail de confirmação
                        $this->emailService->enviarConfirmacaoPedido(
                            $email,
                            $this->carrinho->getItems(),
                            $this->carrinho->getSubtotal(),
                            $this->carrinho->getFrete(),
                            $this->carrinho->getDesconto(),
                            $this->carrinho->getTotal(),
                            $_SESSION['endereco_entrega']
                        );

                        $this->connection->commit();
                        $this->carrinho->limpar();
                        unset($_SESSION['endereco_entrega']);

                        $_SESSION['message'] = [
                            'type' => 'success',
                            'text' => 'Pedido realizado com sucesso! Verifique seu e-mail.'
                        ];
                    } catch (\Exception $e) {
                        $this->connection->rollBack();
                        $_SESSION['message'] = [
                            'type' => 'danger',
                            'text' => 'Erro ao finalizar pedido'
                        ];
                    }
                    break;
            }

            if ($action !== 'finalizar') {
                header('Location: ?route=carrinho');
                exit;
            }
        }

        $message = $_SESSION['message'] ?? null;
        unset($_SESSION['message']);

        require_once __DIR__ . '/../Views/carrinho/index.php';
    }
} 