<?php require_once __DIR__ . '/../layout/header.php'; ?>

<h1 class="mb-4">Carrinho de Compras</h1>

<?php if (empty($carrinho->getItems())): ?>
    <div class="alert alert-info">
        Seu carrinho está vazio.
        <a href="?route=produtos" class="alert-link">Ver produtos</a>
    </div>
<?php else: ?>
    <div class="table-responsive mb-4">
        <table class="table">
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Preço</th>
                    <th>Quantidade</th>
                    <th>Subtotal</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($carrinho->getItems() as $id => $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['produto']->getNome()) ?></td>
                        <td>R$ <?= number_format($item['produto']->getPreco(), 2, ',', '.') ?></td>
                        <td>
                            <form action="?route=carrinho" method="post" class="d-inline">
                                <input type="hidden" name="action" value="atualizar">
                                <input type="hidden" name="produto_id" value="<?= $id ?>">
                                <input type="number" name="quantidade" value="<?= $item['quantidade'] ?>" 
                                       min="1" class="form-control form-control-sm" style="width: 80px"
                                       onchange="this.form.submit()">
                            </form>
                        </td>
                        <td>R$ <?= number_format($item['produto']->getPreco() * $item['quantidade'], 2, ',', '.') ?></td>
                        <td>
                            <form action="?route=carrinho" method="post" class="d-inline">
                                <input type="hidden" name="action" value="remover">
                                <input type="hidden" name="produto_id" value="<?= $id ?>">
                                <button type="submit" class="btn btn-danger btn-sm">Remover</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Cupom de Desconto</h5>
                    <form action="?route=carrinho" method="post" class="mb-3">
                        <input type="hidden" name="action" value="aplicar_cupom">
                        <div class="input-group">
                            <input type="text" name="codigo_cupom" class="form-control" placeholder="Código do cupom">
                            <button type="submit" class="btn btn-primary">Aplicar</button>
                        </div>
                    </form>
                    <?php if ($carrinho->getCupom()): ?>
                        <div class="alert alert-success">
                            Cupom aplicado: <?= htmlspecialchars($carrinho->getCupom()->getCodigo()) ?>
                            <br>
                            Desconto: R$ <?= number_format($carrinho->getDesconto(), 2, ',', '.') ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Calcular Frete</h5>
                    <form action="?route=carrinho" method="post" class="mb-3">
                        <input type="hidden" name="action" value="calcular_frete">
                        <div class="input-group">
                            <input type="text" name="cep" class="form-control" placeholder="Digite seu CEP">
                            <button type="submit" class="btn btn-primary">Calcular</button>
                        </div>
                    </form>
                    <?php if (isset($endereco)): ?>
                        <div class="alert alert-info">
                            <strong>Endereço de entrega:</strong><br>
                            <?= $endereco['logradouro'] ?><br>
                            <?= $endereco['bairro'] ?><br>
                            <?= $endereco['cidade'] ?> - <?= $endereco['estado'] ?><br>
                            CEP: <?= $endereco['cep'] ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Resumo do Pedido</h5>
                    <p class="card-text">Subtotal: R$ <?= number_format($carrinho->getSubtotal(), 2, ',', '.') ?></p>
                    <?php if ($carrinho->getDesconto() > 0): ?>
                        <p class="card-text">Desconto: R$ <?= number_format($carrinho->getDesconto(), 2, ',', '.') ?></p>
                    <?php endif; ?>
                    <p class="card-text">Frete: R$ <?= number_format($carrinho->getFrete(), 2, ',', '.') ?></p>
                    <h4>Total: R$ <?= number_format($carrinho->getTotal(), 2, ',', '.') ?></h4>
                    
                    <form action="?route=carrinho" method="post" class="mt-3">
                        <input type="hidden" name="action" value="finalizar">
                        <div class="mb-3">
                            <label for="email" class="form-label">E-mail para confirmação</label>
                            <input type="email" name="email" id="email" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-success">Finalizar Pedido</button>
                        <button type="submit" name="action" value="limpar" class="btn btn-warning">Limpar Carrinho</button>
                        <a href="?route=produtos" class="btn btn-primary">Continuar Comprando</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../layout/footer.php'; ?> 