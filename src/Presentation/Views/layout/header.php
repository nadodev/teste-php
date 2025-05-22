<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema ERP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #475569;
            --success-color: #16a34a;
            --warning-color: #ca8a04;
            --danger-color: #dc2626;
            --light-bg: #f8fafc;
            --card-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        }

        body {
            background-color: var(--light-bg);
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
        }

        .navbar {
            box-shadow: var(--card-shadow);
            background: linear-gradient(to right, var(--primary-color), #1d4ed8);
        }

        .card {
            border: none;
            box-shadow: var(--card-shadow);
            transition: transform 0.2s ease-in-out;
        }

        .card:hover {
            transform: translateY(-2px);
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: #1d4ed8;
            border-color: #1d4ed8;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgb(37 99 235 / 25%);
        }

        .table {
            background-color: white;
            border-radius: 0.5rem;
            overflow: hidden;
        }

        .badge {
            font-weight: 500;
            padding: 0.5em 1em;
        }

        .alert {
            border: none;
            box-shadow: var(--card-shadow);
        }

        .page-header {
            color: var(--secondary-color);
            font-weight: 600;
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="?route=home">
                <i class="bi bi-shop me-2"></i>ERP System
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="?route=produtos">
                            <i class="bi bi-box-seam me-1"></i>Produtos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="?route=produto/novo">
                            <i class="bi bi-plus-circle me-1"></i>Novo Produto
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="?route=carrinho">
                            <i class="bi bi-cart me-1"></i>Carrinho
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container"><?php if (isset($message)): ?>
        <div class="alert alert-<?= $message['type'] ?> alert-dismissible fade show">
            <?= $message['text'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?> 