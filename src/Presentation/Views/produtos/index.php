
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-header">
            <i class="bi bi-box-seam me-2"></i>Produtos
        </h1>
        <a href="/produto/novo" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>Novo Produto
        </a>
    </div>

    <div class="row g-4">
        <?php foreach ($produtos as $produto): ?>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h5 class="card-title text-primary mb-0">
                                <?= htmlspecialchars($produto->getNome()) ?>
                            </h5>
                            <div class="d-flex gap-2 align-items-start">
                                <span class="badge bg-primary">
                                    R$ <?= number_format($produto->getPreco(), 2, ',', '.') ?>
                                </span>
                                <div class="btn-group">
                                    <a href="/produto/editar?id=<?= $produto->getId() ?>" 
                                       class="btn btn-sm btn-outline-primary" 
                                       title="Editar produto">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-danger" 
                                            title="Excluir produto"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#deleteModal<?= $produto->getId() ?>">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <form action="/carrinho/adicionar" method="post">
                            <input type="hidden" name="produto_id" value="<?= $produto->getId() ?>">
                            
                            <div class="mb-3">
                                <label class="form-label d-flex justify-content-between">
                                    <span>Quantidade:</span>
                                    <small class="text-muted">
                                        Disponível: <?= isset($estoques[$produto->getId()]) ? $estoques[$produto->getId()]->getQuantidade() : 0 ?>
                                    </small>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-123"></i>
                                    </span>
                                    <input type="number" name="quantidade" value="1" min="1" 
                                           max="<?= isset($estoques[$produto->getId()]) ? $estoques[$produto->getId()]->getQuantidade() : 999 ?>"
                                           class="form-control">
                                </div>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-cart-plus me-2"></i>Adicionar ao Carrinho
                                </button>
                            </div>
                        </form>

                        <?php if (isset($estoques[$produto->getId()]) && $estoques[$produto->getId()]->getVariacao()): ?>
                            <div class="mt-3">
                                <small class="text-muted">
                                    <i class="bi bi-tag me-1"></i>
                                    Variação: <?= htmlspecialchars($estoques[$produto->getId()]->getVariacao()) ?>
                                </small>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Modal de confirmação de exclusão -->
            <div class="modal fade" id="deleteModal<?= $produto->getId() ?>" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Confirmar Exclusão</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p>Tem certeza que deseja excluir o produto <strong><?= htmlspecialchars($produto->getNome()) ?></strong>?</p>
                            <p class="text-danger mb-0">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                Esta ação não pode ser desfeita.
                            </p>
                        </div>
                        <div class="modal-footer">
                            <form action="/produto/excluir" method="post">
                                <input type="hidden" name="id" value="<?= $produto->getId() ?>">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-danger">
                                    <i class="bi bi-trash me-2"></i>Excluir Produto
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
