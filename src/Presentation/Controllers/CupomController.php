<?php

namespace Presentation\Controllers;

use Domain\Entities\Cupom;
use Infrastructure\Repositories\CupomRepository;
use Presentation\View;

class CupomController
{
    private CupomRepository $cupomRepository;
    private View $view;

    public function __construct()
    {
        $this->cupomRepository = new CupomRepository();
        $this->view = new View();
    }

    public function index(): void
    {
        $cupons = $this->cupomRepository->findAll();
        $this->view->render('cupons/index', [
            'cupons' => $cupons
        ]);
    }

    public function create(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $codigo = strtoupper($_POST['codigo'] ?? '');
            $valor_desconto = (float) ($_POST['valor_desconto'] ?? 0);
            $valor_minimo = (float) ($_POST['valor_minimo'] ?? 0);
            $validade = $_POST['validade'] ?? null;

            $errors = [];
            if (empty($codigo)) {
                $errors[] = "O código do cupom é obrigatório.";
            }
            if ($valor_desconto <= 0) {
                $errors[] = "O valor do desconto deve ser maior que zero.";
            }
            if ($valor_minimo < 0) {
                $errors[] = "O valor mínimo não pode ser negativo.";
            }
            if (empty($validade)) {
                $errors[] = "A data de validade é obrigatória.";
            }

            $cupomExistente = $this->cupomRepository->findByCodigo($codigo);
            if ($cupomExistente) {
                $errors[] = "Já existe um cupom com este código.";
            }

            if (!empty($errors)) {
                $_SESSION['message'] = [
                    'type' => 'danger',
                    'text' => implode('<br>', $errors)
                ];
            } else {
                $cupom = new Cupom($codigo, $valor_desconto, $valor_minimo, $validade);
                $this->cupomRepository->save($cupom);

                $_SESSION['message'] = [
                    'type' => 'success',
                    'text' => 'Cupom criado com sucesso!'
                ];
                header('Location: /cupons');
                exit;
            }
        }

        $this->view->render('cupons/form');
    }

    public function edit(): void
    {
        $codigo = $_GET['codigo'] ?? '';
        $cupom = $this->cupomRepository->findByCodigo($codigo);
        
        if (!$cupom) {
            header('Location: /cupons');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $valor_desconto = (float) ($_POST['valor_desconto'] ?? 0);
            $valor_minimo = (float) ($_POST['valor_minimo'] ?? 0);
            $validade = $_POST['validade'] ?? null;

            $errors = [];
            if ($valor_desconto <= 0) {
                $errors[] = "O valor do desconto deve ser maior que zero.";
            }
            if ($valor_minimo < 0) {
                $errors[] = "O valor mínimo não pode ser negativo.";
            }
            if (empty($validade)) {
                $errors[] = "A data de validade é obrigatória.";
            }

            if (!empty($errors)) {
                $_SESSION['message'] = [
                    'type' => 'danger',
                    'text' => implode('<br>', $errors)
                ];
            } else {
                $cupomAtualizado = new Cupom($codigo, $valor_desconto, $valor_minimo, $validade);
                $this->cupomRepository->update($cupomAtualizado);

                $_SESSION['message'] = [
                    'type' => 'success',
                    'text' => 'Cupom atualizado com sucesso!'
                ];
                header('Location: /cupons');
                exit;
            }
        }

        $this->view->render('cupons/form', [
            'cupom' => $cupom
        ]);
    }

    public function delete(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /cupons');
            exit;
        }

        $codigo = $_POST['codigo'] ?? '';
        $cupom = $this->cupomRepository->findByCodigo($codigo);
        
        if ($cupom) {
            $this->cupomRepository->delete($cupom->getCodigo());
            $_SESSION['message'] = [
                'type' => 'success',
                'text' => 'Cupom excluído com sucesso!'
            ];
        } else {
            $_SESSION['message'] = [
                'type' => 'danger',
                'text' => 'Cupom não encontrado.'
            ];
        }

        header('Location: /cupons');
        exit;
    }
} 