<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Cupons de Desconto</h1>
    <a href="?route=cupom/novo" class="btn btn-primary">Novo Cupom</a>
</div>

<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>Código</th>
                <th>Valor Desconto</th>
                <th>Validade</th>
                <th>Valor Mínimo</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($cupons as $cupom): ?>
                <tr>
                    <td><?= htmlspecialchars($cupom->getCodigo()) ?></td>
                    <td>R$ <?= number_format($cupom->getValorDesconto(), 2, ',', '.') ?></td>
                    <td><?= $cupom->getValidade()->format('d/m/Y') ?></td>
                    <td>R$ <?= number_format($cupom->getValorMinimo(), 2, ',', '.') ?></td>
                    <td>
                        <?php if ($cupom->isValido()): ?>
                            <span class="badge bg-success">Válido</span>
                        <?php else: ?>
                            <span class="badge bg-danger">Expirado</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?> 