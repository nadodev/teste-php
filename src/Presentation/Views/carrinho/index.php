<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-header">
            <i class="bi bi-cart me-2"></i>Carrinho de Compras
        </h1>
        <a href="?route=produtos" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left me-2"></i>Continuar Comprando
        </a>
    </div>

    <?php if (isset($message)): ?>
        <div class="alert alert-<?= $message['type'] ?> alert-dismissible fade show">
            <i class="bi bi-info-circle me-2"></i>
            <?= $message['text'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (empty($carrinho->getItems())): ?>
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bi bi-cart-x display-1 text-muted mb-4"></i>
                <h3 class="text-muted mb-4">Seu carrinho está vazio</h3>
                <a href="?route=produtos" class="btn btn-primary">
                    <i class="bi bi-shop me-2"></i>Ver produtos disponíveis
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title d-flex justify-content-between align-items-center mb-4">
                            <span>
                                <i class="bi bi-cart-check me-2"></i>Itens do Carrinho
                            </span>
                            <span class="badge bg-primary">
                                <?= count($carrinho->getItems()) ?> <?= count($carrinho->getItems()) > 1 ? 'itens' : 'item' ?>
                            </span>
                        </h5>
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Produto</th>
                                        <th class="text-center">Preço</th>
                                        <th class="text-center">Quantidade</th>
                                        <th class="text-end">Subtotal</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($carrinho->getItems() as $id => $item): ?>
                                        <tr>
                                            <td class="text-primary fw-medium"><?= htmlspecialchars($item['produto']->getNome()) ?></td>
                                            <td class="text-center">R$ <?= number_format($item['produto']->getPreco(), 2, ',', '.') ?></td>
                                            <td class="text-center" style="width: 150px;">
                                                <form action="?route=carrinho" method="post" class="d-inline">
                                                    <input type="hidden" name="action" value="atualizar">
                                                    <input type="hidden" name="produto_id" value="<?= $id ?>">
                                                    <div class="input-group input-group-sm">
                                                        <button type="button" class="btn btn-outline-secondary" onclick="decrementQuantity(this)">
                                                            <i class="bi bi-dash"></i>
                                                        </button>
                                                        <input type="number" name="quantidade" value="<?= $item['quantidade'] ?>" 
                                                               min="1" class="form-control text-center"
                                                               onchange="this.form.submit()">
                                                        <button type="button" class="btn btn-outline-secondary" onclick="incrementQuantity(this)">
                                                            <i class="bi bi-plus"></i>
                                                        </button>
                                                    </div>
                                                </form>
                                            </td>
                                            <td class="text-end fw-bold">R$ <?= number_format($item['produto']->getPreco() * $item['quantidade'], 2, ',', '.') ?></td>
                                            <td class="text-end">
                                                <form action="?route=carrinho" method="post" class="d-inline">
                                                    <input type="hidden" name="action" value="remover">
                                                    <input type="hidden" name="produto_id" value="<?= $id ?>">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Remover item">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Cupom de Desconto -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-3">
                            <i class="bi bi-ticket-perforated me-2"></i>Cupom de Desconto
                        </h5>
                        <form action="?route=carrinho" method="post">
                            <input type="hidden" name="action" value="aplicar_cupom">
                            <div class="input-group">
                                <input type="text" name="codigo_cupom" class="form-control" placeholder="Digite seu cupom">
                                <button type="submit" class="btn btn-outline-primary">Aplicar</button>
                            </div>
                        </form>
                        <?php if ($carrinho->getCupom()): ?>
                            <div class="alert alert-success mt-3 mb-0">
                                <small>
                                    <i class="bi bi-check-circle me-2"></i>
                                    Cupom aplicado: <strong><?= htmlspecialchars($carrinho->getCupom()->getCodigo()) ?></strong>
                                    <br>
                                    Desconto: <strong>R$ <?= number_format($carrinho->getDesconto(), 2, ',', '.') ?></strong>
                                </small>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Cálculo de Frete -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-3">
                            <i class="bi bi-truck me-2"></i>Calcular Frete
                        </h5>
                        <form action="?route=carrinho" method="post">
                            <input type="hidden" name="action" value="calcular_frete">
                            <div class="input-group">
                                <input type="text" name="cep" class="form-control" placeholder="Digite seu CEP" 
                                       pattern="[0-9]{5}-?[0-9]{3}" title="Digite um CEP válido">
                                <button type="submit" class="btn btn-outline-primary">Calcular</button>
                            </div>
                        </form>
                        <?php if (isset($endereco)): ?>
                            <div class="alert alert-info mt-3 mb-0">
                                <small>
                                    <i class="bi bi-geo-alt me-2"></i>
                                    <strong>Endereço de entrega:</strong><br>
                                    <?= $endereco['logradouro'] ?><br>
                                    <?= $endereco['bairro'] ?><br>
                                    <?= $endereco['cidade'] ?> - <?= $endereco['estado'] ?><br>
                                    CEP: <?= $endereco['cep'] ?>
                                </small>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Resumo do Pedido -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-4">
                            <i class="bi bi-receipt me-2"></i>Resumo do Pedido
                        </h5>
                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Subtotal:</span>
                                <span>R$ <?= number_format($carrinho->getSubtotal(), 2, ',', '.') ?></span>
                            </div>
                            <?php if ($carrinho->getDesconto() > 0): ?>
                                <div class="d-flex justify-content-between mb-2 text-success">
                                    <span>Desconto:</span>
                                    <span>-R$ <?= number_format($carrinho->getDesconto(), 2, ',', '.') ?></span>
                                </div>
                            <?php endif; ?>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Frete:</span>
                                <span>R$ <?= number_format($carrinho->getFrete(), 2, ',', '.') ?></span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="h5 mb-0">Total:</span>
                                <span class="h4 mb-0 text-primary">R$ <?= number_format($carrinho->getTotal(), 2, ',', '.') ?></span>
                            </div>
                        </div>

                        <form action="?route=carrinho" method="post">
                            <input type="hidden" name="action" value="finalizar">
                            <div class="mb-3">
                                <label for="email" class="form-label">
                                    <i class="bi bi-envelope me-2"></i>E-mail para confirmação
                                </label>
                                <input type="email" name="email" id="email" class="form-control" required>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-2"></i>Finalizar Pedido
                                </button>
                                <button type="submit" name="action" value="limpar" class="btn btn-outline-danger">
                                    <i class="bi bi-trash me-2"></i>Limpar Carrinho
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
function decrementQuantity(button) {
    const input = button.parentElement.querySelector('input[type="number"]');
    if (input.value > 1) {
        input.value = parseInt(input.value) - 1;
        input.form.submit();
    }
}

function incrementQuantity(button) {
    const input = button.parentElement.querySelector('input[type="number"]');
    input.value = parseInt(input.value) + 1;
    input.form.submit();
}
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?> 