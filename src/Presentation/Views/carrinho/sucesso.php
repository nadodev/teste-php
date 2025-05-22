<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="container py-5">
    <div class="text-center">
        <i class="bi bi-check-circle text-success display-1 mb-4"></i>
        <h1 class="mb-4">Pedido Finalizado com Sucesso!</h1>
        <p class="lead mb-4">
            Enviamos os detalhes do seu pedido para o email: <strong><?= htmlspecialchars($email) ?></strong>
        </p>
        
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Local de Entrega</h5>
                        <p class="mb-0">
                            <?= htmlspecialchars($endereco['cidade']) ?> - <?= htmlspecialchars($endereco['estado']) ?>
                        </p>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Resumo do Pedido</h5>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span>R$ <?= number_format($subtotal, 2, ',', '.') ?></span>
                        </div>
                        <?php if ($desconto > 0): ?>
                            <div class="d-flex justify-content-between mb-2 text-success">
                                <span>Desconto:</span>
                                <span>- R$ <?= number_format($desconto, 2, ',', '.') ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Frete:</span>
                            <span>R$ <?= number_format($frete, 2, ',', '.') ?></span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <span class="h5 mb-0">Total:</span>
                            <span class="h5 mb-0">R$ <?= number_format($total, 2, ',', '.') ?></span>
                        </div>
                    </div>
                </div>

                <a href="?route=produtos" class="btn btn-primary">
                    <i class="bi bi-bag me-2"></i>Continuar Comprando
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Loading -->
<div class="modal fade" id="loadingModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Carregando...</span>
                </div>
                <h5 class="mb-0">Processando seu pedido...</h5>
                <p class="text-muted mb-0">Por favor, aguarde.</p>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?> 