<?php require_once __DIR__ . '/../layout/header.php'; ?>

<h1 class="mb-4">Carrinho de Compras</h1>

<?php if (empty($carrinho->getItems())): ?>
    <div class="alert alert-info">
        Seu carrinho está vazio.
        <a href="?route=produtos" class="alert-link">Ver produtos</a>
    </div>
<?php else: ?>
    <div class="table-responsive mb-4">
        <table class="table">
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Preço</th>
                    <th>Quantidade</th>
                    <th>Subtotal</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($carrinho->getItems() as $id => $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['produto']->getNome()) ?></td>
                        <td>R$ <?= number_format($item['produto']->getPreco(), 2, ',', '.') ?></td>
                        <td>
                            <form action="?route=carrinho" method="post" class="d-inline">
                                <input type="hidden" name="action" value="atualizar">
                                <input type="hidden" name="produto_id" value="<?= $id ?>">
                                <input type="number" name="quantidade" value="<?= $item['quantidade'] ?>" 
                                       min="1" class="form-control form-control-sm" style="width: 80px"
                                       onchange="this.form.submit()">
                            </form>
                        </td>
                        <td>R$ <?= number_format($item['produto']->getPreco() * $item['quantidade'], 2, ',', '.') ?></td>
                        <td>
                            <form action="?route=carrinho" method="post" class="d-inline">
                                <input type="hidden" name="action" value="remover">
                                <input type="hidden" name="produto_id" value="<?= $id ?>">
                                <button type="submit" class="btn btn-danger btn-sm">Remover</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Resumo do Pedido</h5>
            <p class="card-text">Subtotal: R$ <?= number_format($carrinho->getSubtotal(), 2, ',', '.') ?></p>
            <p class="card-text">Frete: R$ <?= number_format($carrinho->getFrete(), 2, ',', '.') ?></p>
            <h4>Total: R$ <?= number_format($carrinho->getTotal(), 2, ',', '.') ?></h4>
            
            <form action="?route=carrinho" method="post" class="mt-3">
                <input type="hidden" name="action" value="limpar">
                <button type="submit" class="btn btn-warning">Limpar Carrinho</button>
                <a href="?route=produtos" class="btn btn-primary">Continuar Comprando</a>
            </form>
        </div>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../layout/footer.php'; ?> 