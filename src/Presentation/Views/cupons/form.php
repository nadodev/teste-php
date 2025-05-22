<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h1 class="card-title h4 mb-4">
                        <i class="bi bi-ticket-perforated me-2"></i>
                        <?= isset($cupom) ? 'Editar Cupom' : 'Novo Cupom' ?>
                    </h1>

                    <?php if (isset($_SESSION['message'])): ?>
                        <div class="alert alert-<?= $_SESSION['message']['type'] ?> alert-dismissible fade show">
                            <i class="bi bi-info-circle me-2"></i>
                            <?= $_SESSION['message']['text'] ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php unset($_SESSION['message']); ?>
                    <?php endif; ?>

                    <form method="post" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label for="codigo" class="form-label">Código do Cupom</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="codigo" 
                                   name="codigo" 
                                   value="<?= isset($cupom) ? htmlspecialchars($cupom->getCodigo()) : '' ?>"
                                   <?= isset($cupom) ? 'readonly' : 'required' ?>
                                   placeholder="Ex: DESCONTO20">
                            <div class="form-text">
                                O código do cupom deve ser único e será usado pelos clientes para aplicar o desconto.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="valor_desconto" class="form-label">Valor do Desconto (R$)</label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="number" 
                                       class="form-control" 
                                       id="valor_desconto" 
                                       name="valor_desconto" 
                                       value="<?= isset($cupom) ? number_format($cupom->getValorDesconto(), 2, '.', '') : '' ?>"
                                       step="0.01" 
                                       min="0.01" 
                                       required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="valor_minimo" class="form-label">Valor Mínimo do Pedido (R$)</label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="number" 
                                       class="form-control" 
                                       id="valor_minimo" 
                                       name="valor_minimo" 
                                       value="<?= isset($cupom) ? number_format($cupom->getValorMinimo(), 2, '.', '') : '0.00' ?>"
                                       step="0.01" 
                                       min="0">
                            </div>
                            <div class="form-text">
                                Valor mínimo do pedido para que o cupom possa ser aplicado. Use 0 para não exigir valor mínimo.
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="validade" class="form-label">Data de Validade</label>
                            <input type="date" 
                                   class="form-control" 
                                   id="validade" 
                                   name="validade" 
                                   value="<?= isset($cupom) ? (new DateTime($cupom->getValidade()))->format('Y-m-d') : '' ?>"
                                   min="<?= date('Y-m-d') ?>"
                                   required>
                            <div class="form-text">
                                Data até quando o cupom poderá ser utilizado.
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <a href="/cupons" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Voltar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-2"></i>Salvar Cupom
                            </button>
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