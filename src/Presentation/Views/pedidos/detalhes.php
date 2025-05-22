<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-header">
            <i class="bi bi-receipt me-2"></i>Pedido #<?= str_pad($pedido->getId(), 6, '0', STR_PAD_LEFT) ?>
        </h1>
        <a href="/pedidos" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Voltar para Pedidos
        </a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Itens do Pedido</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Produto</th>
                                    <th class="text-center">Quantidade</th>
                                    <th class="text-end">Preço Unit.</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pedido->getItens() as $item): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($item['produto_nome']) ?></td>
                                        <td class="text-center"><?= $item['quantidade'] ?></td>
                                        <td class="text-end">R$ <?= number_format($item['preco_unitario'], 2, ',', '.') ?></td>
                                        <td class="text-end">R$ <?= number_format($item['subtotal'], 2, ',', '.') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end fw-bold">Subtotal:</td>
                                    <td class="text-end">R$ <?= number_format($pedido->getSubtotal(), 2, ',', '.') ?></td>
                                </tr>
                                <?php if ($pedido->getDesconto() > 0): ?>
                                    <tr>
                                        <td colspan="3" class="text-end fw-bold text-success">Desconto:</td>
                                        <td class="text-end text-success">- R$ <?= number_format($pedido->getDesconto(), 2, ',', '.') ?></td>
                                    </tr>
                                <?php endif; ?>
                                <tr>
                                    <td colspan="3" class="text-end fw-bold">Frete:</td>
                                    <td class="text-end">R$ <?= number_format($pedido->getFrete(), 2, ',', '.') ?></td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end fw-bold">Total:</td>
                                    <td class="text-end fw-bold">R$ <?= number_format($pedido->getTotal(), 2, ',', '.') ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informações do Pedido</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Status:</dt>
                        <dd class="col-sm-8">
                            <span class="badge bg-success">
                                <?= ucfirst($pedido->getStatus()) ?>
                            </span>
                        </dd>

                        <dt class="col-sm-4">Data:</dt>
                        <dd class="col-sm-8">
                            <?= date('d/m/Y H:i', strtotime($pedido->getCreatedAt())) ?>
                        </dd>

                        <dt class="col-sm-4">Email:</dt>
                        <dd class="col-sm-8">
                            <?= htmlspecialchars($pedido->getEmail()) ?>
                        </dd>

                        <dt class="col-sm-4">Cidade:</dt>
                        <dd class="col-sm-8">
                            <?= htmlspecialchars($pedido->getCidade()) ?>
                        </dd>

                        <dt class="col-sm-4">Estado:</dt>
                        <dd class="col-sm-8">
                            <?= $pedido->getEstado() ?>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>