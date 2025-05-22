<?php require_once __DIR__ . '/../layout/header.php'; ?>

<h1 class="mb-4">Novo Produto</h1>

<form action="?route=produto/novo" method="post" class="mb-4">
    <div class="mb-3">
        <label for="nome" class="form-label">Nome do Produto</label>
        <input type="text" class="form-control" id="nome" name="nome" required>
    </div>

    <div class="mb-3">
        <label for="preco" class="form-label">Preço</label>
        <input type="number" class="form-control" id="preco" name="preco" step="0.01" min="0" required>
    </div>

    <div class="mb-3">
        <label for="variacao" class="form-label">Variação (opcional)</label>
        <input type="text" class="form-control" id="variacao" name="variacao">
    </div>

    <div class="mb-3">
        <label for="quantidade" class="form-label">Quantidade em Estoque</label>
        <input type="number" class="form-control" id="quantidade" name="quantidade" min="0" required>
    </div>

    <button type="submit" class="btn btn-primary">Salvar Produto</button>
    <a href="?route=produtos" class="btn btn-secondary">Cancelar</a>
</form>

<?php require_once __DIR__ . '/../layout/footer.php'; ?> 