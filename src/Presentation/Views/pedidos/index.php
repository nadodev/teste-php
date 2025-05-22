<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-header">
            <i class="bi bi-receipt me-2"></i>Pedidos
        </h1>
    </div>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?= $_SESSION['message']['type'] ?> alert-dismissible fade show">
            <i class="bi bi-info-circle me-2"></i>
            <?= $_SESSION['message']['text'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <?php if (empty($pedidos)): ?>
        <div class="text-center py-5">
            <i class="bi bi-receipt display-1 text-muted mb-4"></i>
            <h2 class="h4 text-muted">Nenhum pedido encontrado</h2>
            <p class="text-muted mb-4">Não há pedidos registrados no sistema.</p>
            <a href="/produtos" class="btn btn-primary">
                <i class="bi bi-cart-plus me-2"></i>Fazer uma compra
            </a>
        </div>
    <?php else: ?>
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Pedido #</th>
                                <th>Data</th>
                                <th>Email</th>
                                <th>Cidade/UF</th>
                                <th class="text-end">Total</th>
                                <th class="text-center">Status</th>
                                <th class="text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pedidos as $pedido): ?>
                                <tr>
                                    <td>
                                        <a href="/pedidos/detalhes?id=<?= $pedido->getId() ?>" class="text-decoration-none">
                                            #<?= str_pad($pedido->getId(), 6, '0', STR_PAD_LEFT) ?>
                                        </a>
                                    </td>
                                    <td><?= date('d/m/Y H:i', strtotime($pedido->getCreatedAt())) ?></td>
                                    <td><?= htmlspecialchars($pedido->getEmail()) ?></td>
                                    <td><?= htmlspecialchars($pedido->getCidade()) ?>/<?= $pedido->getEstado() ?></td>
                                    <td class="text-end">R$ <?= number_format($pedido->getTotal(), 2, ',', '.') ?></td>
                                    <td class="text-center">
                                        <span class="badge bg-success">
                                            <?= ucfirst($pedido->getStatus()) ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <a href="/pedidos/detalhes?id=<?= $pedido->getId() ?>" 
                                           class="btn btn-sm btn-outline-primary"
                                           title="Ver detalhes">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>