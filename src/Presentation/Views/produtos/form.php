<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-white py-3">
                    <h2 class="page-header mb-0">
                        <i class="bi <?= isset($produto) ? 'bi-pencil-square' : 'bi-plus-circle' ?> me-2"></i>
                        <?= isset($produto) ? 'Editar Produto' : 'Novo Produto' ?>
                    </h2>
                </div>
                <div class="card-body">
                    <?php if (isset($message)): ?>
                        <div class="alert alert-<?= $message['type'] ?> alert-dismissible fade show">
                            <i class="bi bi-info-circle me-2"></i>
                            <?= $message['text'] ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form action="<?= isset($produto) ? '/produto/editar?id=' . $produto->getId() : '/produto/novo' ?>" method="post" class="needs-validation" novalidate>
                        <?php if (isset($produto)): ?>
                            <input type="hidden" name="id" value="<?= $produto->getId() ?>">
                        <?php endif; ?>

                        <div class="mb-4">
                            <label for="nome" class="form-label">Nome do Produto</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-tag"></i>
                                </span>
                                <input type="text" class="form-control" id="nome" name="nome" 
                                       value="<?= isset($produto) ? htmlspecialchars($produto->getNome()) : '' ?>"
                                       required>
                                <div class="invalid-feedback">
                                    Por favor, informe o nome do produto.
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="preco" class="form-label">Preço</label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="number" class="form-control" id="preco" name="preco" 
                                       value="<?= isset($produto) ? $produto->getPreco() : '' ?>"
                                       step="0.01" min="0" required>
                                <div class="invalid-feedback">
                                    Por favor, informe um preço válido.
                                </div>
                            </div>
                            <small class="text-muted">Use ponto para separar centavos (ex: 99.99)</small>
                        </div>

                        <div class="mb-4">
                            <label for="variacao" class="form-label">Variação</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-boxes"></i>
                                </span>
                                <input type="text" class="form-control" id="variacao" name="variacao" 
                                       value="<?= isset($estoque) ? htmlspecialchars($estoque->getVariacao()) : '' ?>"
                                       placeholder="Ex: Tamanho, Cor, etc">
                            </div>
                            <small class="text-muted">Opcional - Use para identificar diferentes versões do mesmo produto</small>
                        </div>

                        <div class="mb-4">
                            <label for="quantidade" class="form-label">Quantidade em Estoque</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-123"></i>
                                </span>
                                <input type="number" class="form-control" id="quantidade" name="quantidade" 
                                       value="<?= isset($estoque) ? $estoque->getQuantidade() : '0' ?>"
                                       min="0" required>
                                <div class="invalid-feedback">
                                    Por favor, informe a quantidade em estoque.
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1">
                                <i class="bi <?= isset($produto) ? 'bi-check-lg' : 'bi-plus-lg' ?> me-2"></i>
                                <?= isset($produto) ? 'Atualizar' : 'Cadastrar' ?> Produto
                            </button>
                            <a href="/produtos" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Voltar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Form validation
(function () {
    'use strict'
    var forms = document.querySelectorAll('.needs-validation')
    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
            }
            form.classList.add('was-validated')
        }, false)
    })
})()
</script>