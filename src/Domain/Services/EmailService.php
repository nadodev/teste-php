<?php

namespace Domain\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailService
{
    private PHPMailer $mailer;
    private bool $isConfigured = false;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);
        
        // Check if SMTP configuration exists
        if (
            !empty($_ENV['SMTP_HOST']) &&
            !empty($_ENV['SMTP_USER']) &&
            !empty($_ENV['SMTP_PASS']) &&
            !empty($_ENV['SMTP_PORT']) &&
            !empty($_ENV['SMTP_FROM']) &&
            !empty($_ENV['SMTP_FROM_NAME'])
        ) {
            // Server settings
            $this->mailer->isSMTP();
            $this->mailer->Host = "smtp.hostinger.com";
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = "contato@leonardogeja.com.br";
            $this->mailer->Password = "LeonardoGeja2536!!";
            $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mailer->Port = 465;
            
            // Default settings
            $this->mailer->setFrom("contato@leonardogeja.com.br", "Leonardo Geja");
            $this->mailer->isHTML(true);

            $this->isConfigured = true;
        }
    }

    public function enviarConfirmacaoPedido(
        string $email,
        array $items,
        float $subtotal,
        float $frete,
        float $desconto,
        float $total,
        ?array $endereco = null
    ): bool {
        if (!$this->isConfigured) {
            // Log the error or handle it appropriately
            error_log('Email service not configured. Please check SMTP settings.');
            return false;
        }

        try {
            $this->mailer->addAddress($email);
            $this->mailer->Subject = 'Confirmação de Pedido';

            $html = '<h1>Confirmação de Pedido</h1>';
            $html .= '<h2>Itens do Pedido:</h2>';
            $html .= '<table border="1" cellpadding="5">';
            $html .= '<tr><th>Produto</th><th>Quantidade</th><th>Preço</th><th>Subtotal</th></tr>';

            foreach ($items as $item) {
                $produto = $item['produto'];
                $quantidade = $item['quantidade'];
                $preco = $produto->getPreco();
                $subtotalItem = $preco * $quantidade;

                $html .= sprintf(
                    '<tr><td>%s</td><td>%d</td><td>R$ %.2f</td><td>R$ %.2f</td></tr>',
                    htmlspecialchars($produto->getNome()),
                    $quantidade,
                    $preco,
                    $subtotalItem
                );
            }

            $html .= '</table>';
            $html .= sprintf('<p>Subtotal: R$ %.2f</p>', $subtotal);
            $html .= sprintf('<p>Frete: R$ %.2f</p>', $frete);
            
            if ($desconto > 0) {
                $html .= sprintf('<p>Desconto: R$ %.2f</p>', $desconto);
            }
            
            $html .= sprintf('<h3>Total: R$ %.2f</h3>', $total);

            if ($endereco) {
                $html .= '<h2>Endereço de Entrega:</h2>';
                $html .= sprintf('<p>%s</p>', $endereco['logradouro']);
                $html .= sprintf('<p>Bairro: %s</p>', $endereco['bairro']);
                $html .= sprintf('<p>%s - %s</p>', $endereco['cidade'], $endereco['estado']);
                $html .= sprintf('<p>CEP: %s</p>', $endereco['cep']);
            }

            $this->mailer->Body = $html;
            $this->mailer->send();
            
            return true;
        } catch (Exception $e) {
            error_log('Email error: ' . $e->getMessage());
            return false;
        }
    }
} 