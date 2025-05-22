<?php require_once __DIR__ . '/../layout/header.php'; ?>

<h1 class="mb-4">Novo Cupom de Desconto</h1>

<form action="?route=cupom/novo" method="post" class="mb-4">
    <div class="mb-3">
        <label for="codigo" class="form-label">Código do Cupom</label>
        <input type="text" class="form-control" id="codigo" name="codigo" required>
    </div>

    <div class="mb-3">
        <label for="valor_desconto" class="form-label">Valor do Desconto (R$)</label>
        <input type="number" class="form-control" id="valor_desconto" name="valor_desconto" step="0.01" min="0" required>
    </div>

    <div class="mb-3">
        <label for="validade" class="form-label">Data de Validade</label>
        <input type="date" class="form-control" id="validade" name="validade" required>
    </div>

    <div class="mb-3">
        <label for="valor_minimo" class="form-label">Valor Mínimo do Pedido (R$)</label>
        <input type="number" class="form-control" id="valor_minimo" name="valor_minimo" step="0.01" min="0" required>
    </div>

    <button type="submit" class="btn btn-primary">Salvar Cupom</button>
    <a href="?route=cupons" class="btn btn-secondary">Cancelar</a>
</form>

<?php require_once __DIR__ . '/../layout/footer.php'; ?> 