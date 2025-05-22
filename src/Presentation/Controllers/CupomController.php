<?php

namespace Presentation\Controllers;

use Domain\Entities\Cupom;
use Infrastructure\Repositories\CupomRepository;

class CupomController
{
    private CupomRepository $cupomRepository;

    public function __construct()
    {
        $this->cupomRepository = new CupomRepository();
    }

    public function index(): void
    {
        $cupons = $this->cupomRepository->findAll();
        require_once __DIR__ . '/../Views/cupons/index.php';
    }

    public function create(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $codigo = $_POST['codigo'] ?? '';
            $valor_desconto = (float) ($_POST['valor_desconto'] ?? 0);
            $validade = new \DateTime($_POST['validade'] ?? 'now');
            $valor_minimo = (float) ($_POST['valor_minimo'] ?? 0);

            $cupom = new Cupom(null, $codigo, $valor_desconto, $validade, $valor_minimo);
            $this->cupomRepository->save($cupom);

            header('Location: ?route=cupons');
            exit;
        }

        require_once __DIR__ . '/../Views/cupons/form.php';
    }
} 