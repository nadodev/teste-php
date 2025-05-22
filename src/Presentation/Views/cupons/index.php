<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-header">
            <i class="bi bi-ticket-perforated me-2"></i>Cupons de Desconto
        </h1>
        <a href="/cupom/novo" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>Novo Cupom
        </a>
    </div>

    <?php if (isset($message)): ?>
        <div class="alert alert-<?= $message['type'] ?> alert-dismissible fade show">
            <i class="bi bi-info-circle me-2"></i>
            <?= $message['text'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Código</th>
                            <th class="text-end">Desconto</th>
                            <th class="text-end">Valor Mínimo</th>
                            <th>Validade</th>
                            <th>Status</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($cupons)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <i class="bi bi-ticket-perforated display-4 d-block mb-2"></i>
                                    Nenhum cupom cadastrado
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($cupons as $cupom): ?>
                                <tr>
                                    <td>
                                        <span class="fw-medium"><?= htmlspecialchars($cupom->getCodigo()) ?></span>
                                    </td>
                                    <td class="text-end">
                                        R$ <?= number_format($cupom->getValorDesconto(), 2, ',', '.') ?>
                                    </td>
                                    <td class="text-end">
                                        R$ <?= number_format($cupom->getValorMinimo(), 2, ',', '.') ?>
                                    </td>
                                    <td>
                                        <?= (new DateTime($cupom->getValidade()))->format('d/m/Y') ?>
                                    </td>
                                    <td>
                                        <?php if ($cupom->isValido()): ?>
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle me-1"></i>Válido
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">
                                                <i class="bi bi-x-circle me-1"></i>Expirado
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group">
                                            <a href="/cupom/editar?codigo=<?= urlencode($cupom->getCodigo()) ?>" 
                                               class="btn btn-sm btn-outline-primary"
                                               title="Editar cupom">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-danger"
                                                    title="Excluir cupom"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#deleteModal<?= md5($cupom->getCodigo()) ?>">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modais de confirmação de exclusão -->
<?php foreach ($cupons as $cupom): ?>
    <div class="modal fade" id="deleteModal<?= md5($cupom->getCodigo()) ?>" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmar Exclusão</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Tem certeza que deseja excluir o cupom <strong><?= htmlspecialchars($cupom->getCodigo()) ?></strong>?</p>
                    <p class="text-danger mb-0">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Esta ação não pode ser desfeita.
                    </p>
                </div>
                <div class="modal-footer">
                    <form action="/cupom/excluir" method="post">
                        <input type="hidden" name="codigo" value="<?= htmlspecialchars($cupom->getCodigo()) ?>">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash me-2"></i>Excluir Cupom
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>