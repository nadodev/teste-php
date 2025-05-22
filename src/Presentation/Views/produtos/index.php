<?php require_once __DIR__ . '/../layout/header.php'; ?>

<h1 class="mb-4">Produtos</h1>

<div class="row">
    <?php foreach ($produtos as $produto): ?>
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($produto->getNome()) ?></h5>
                    <p class="card-text">
                        Preço: R$ <?= number_format($produto->getPreco(), 2, ',', '.') ?>
                    </p>
                    <form action="?route=carrinho" method="post">
                        <input type="hidden" name="action" value="adicionar">
                        <input type="hidden" name="produto_id" value="<?= $produto->getId() ?>">
                        <div class="mb-3">
                            <label class="form-label">Quantidade:</label>
                            <input type="number" name="quantidade" value="1" min="1" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-primary">Adicionar ao Carrinho</button>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?> 