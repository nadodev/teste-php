<div class="container py-4">
    <h1 class="page-header mb-4">
        <i class="bi bi-cart3 me-2"></i>Carrinho de Compras
    </h1>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?= $_SESSION['message']['type'] ?> alert-dismissible fade show">
            <i class="bi bi-info-circle me-2"></i>
            <?= $_SESSION['message']['text'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <?php if (empty($itens)): ?>
        <div class="text-center py-5">
            <i class="bi bi-cart3 display-1 text-muted mb-4"></i>
            <h2 class="h4 text-muted">Seu carrinho está vazio</h2>
            <p class="text-muted mb-4">Adicione produtos para continuar comprando.</p>
            <a href="/produtos" class="btn btn-primary">
                <i class="bi bi-arrow-left me-2"></i>Voltar para Produtos
            </a>
        </div>
    <?php else: ?>
        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-body">
                        <?php foreach ($itens as $item): ?>
                            <div class="d-flex align-items-center mb-4">
                                <div class="flex-grow-1">
                                    <h5 class="mb-1"><?= htmlspecialchars($item['produto']->getNome()) ?></h5>
                                    <div class="text-muted small">
                                        <?php if ($item['estoque'] && $item['estoque']->getVariacao()): ?>
                                            Variação: <?= htmlspecialchars($item['estoque']->getVariacao()) ?>
                                        <?php endif; ?>
                                    </div>
                                    <div class="mt-2">
                                        <form action="/carrinho/atualizar" method="post" class="d-inline-block">
                                            <input type="hidden" name="produto_id" value="<?= $item['produto']->getId() ?>">
                                            <div class="input-group input-group-sm" style="width: 150px;">
                                                <span class="input-group-text">Qtd</span>
                                                <input type="number" 
                                                       class="form-control" 
                                                       name="quantidade" 
                                                       value="<?= $item['quantidade'] ?>"
                                                       min="1"
                                                       max="<?= $item['estoque'] ? $item['estoque']->getQuantidade() : 999 ?>"
                                                       onchange="this.form.submit()">
                                            </div>
                                        </form>
                                        <form action="/carrinho/remover" method="post" class="d-inline-block ms-2">
                                            <input type="hidden" name="produto_id" value="<?= $item['produto']->getId() ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold">
                                        R$ <?= number_format($item['subtotal'], 2, ',', '.') ?>
                                    </div>
                                    <div class="text-muted small">
                                        R$ <?= number_format($item['produto']->getPreco(), 2, ',', '.') ?> cada
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Cupom de Desconto</h5>
                        <?php if ($cupom): ?>
                            <div class="alert alert-success mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong><?= htmlspecialchars($cupom->getCodigo()) ?></strong>
                                        <div class="small">
                                            Desconto: R$ <?= number_format($desconto, 2, ',', '.') ?>
                                        </div>
                                    </div>
                                    <form action="/carrinho/remover-cupom" method="post">
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Remover cupom">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php else: ?>
                            <form action="/carrinho/aplicar-cupom" method="post">
                                <div class="input-group">
                                    <input type="text" 
                                           class="form-control" 
                                           name="codigo" 
                                           placeholder="Digite o código"
                                           required>
                                    <button class="btn btn-primary" type="submit">
                                        Aplicar
                                    </button>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Resumo do Pedido</h5>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal</span>
                            <span>R$ <?= number_format($subtotal, 2, ',', '.') ?></span>
                        </div>

                        <?php if ($desconto > 0): ?>
                            <div class="d-flex justify-content-between mb-2 text-success">
                                <span>Desconto</span>
                                <span>- R$ <?= number_format($desconto, 2, ',', '.') ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <div class="d-flex justify-content-between mb-3">
                            <span>Frete</span>
                            <span class="<?= $frete === 0 ? 'text-success' : '' ?>">
                                <?= $descricaoFrete ?>
                            </span>
                        </div>

                        <?php if ($valorRestanteFreteGratis !== null): ?>
                            <div class="alert alert-info mb-3">
                                <i class="bi bi-truck me-2"></i>
                                Falta R$ <?= number_format($valorRestanteFreteGratis, 2, ',', '.') ?> 
                                para Frete Grátis!
                            </div>
                        <?php endif; ?>

                        <hr>

                        <div class="d-flex justify-content-between mb-4">
                            <span class="h5 mb-0">Total</span>
                            <span class="h5 mb-0">R$ <?= number_format($total, 2, ',', '.') ?></span>
                        </div>

                        <div class="d-grid gap-2">
                            <form action="/carrinho/finalizar" method="post" id="formFinalizarCompra">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email para receber os detalhes do pedido</label>
                                    <input type="email" 
                                           class="form-control" 
                                           id="email" 
                                           name="email" 
                                           required 
                                           placeholder="seu@email.com">
                                </div>

                                <div class="mb-3">
                                    <label for="cep" class="form-label">CEP</label>
                                    <div class="input-group">
                                        <input type="text" 
                                               class="form-control" 
                                               id="cep" 
                                               maxlength="9"
                                               placeholder="00000-000">
                                        <button class="btn btn-outline-secondary" 
                                                type="button" 
                                                id="buscarCep">
                                            Buscar
                                        </button>
                                    </div>
                                    <div class="form-text" id="cepFeedback"></div>
                                </div>

                                <div class="mb-3">
                                    <label for="cidade" class="form-label">Cidade</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="cidade" 
                                           name="cidade" 
                                           required>
                                </div>

                                <div class="mb-3">
                                    <label for="estado" class="form-label">Estado</label>
                                    <select class="form-select" id="estado" name="estado" required>
                                        <option value="">Selecione o estado</option>
                                        <option value="AC">Acre</option>
                                        <option value="AL">Alagoas</option>
                                        <option value="AP">Amapá</option>
                                        <option value="AM">Amazonas</option>
                                        <option value="BA">Bahia</option>
                                        <option value="CE">Ceará</option>
                                        <option value="DF">Distrito Federal</option>
                                        <option value="ES">Espírito Santo</option>
                                        <option value="GO">Goiás</option>
                                        <option value="MA">Maranhão</option>
                                        <option value="MT">Mato Grosso</option>
                                        <option value="MS">Mato Grosso do Sul</option>
                                        <option value="MG">Minas Gerais</option>
                                        <option value="PA">Pará</option>
                                        <option value="PB">Paraíba</option>
                                        <option value="PR">Paraná</option>
                                        <option value="PE">Pernambuco</option>
                                        <option value="PI">Piauí</option>
                                        <option value="RJ">Rio de Janeiro</option>
                                        <option value="RN">Rio Grande do Norte</option>
                                        <option value="RS">Rio Grande do Sul</option>
                                        <option value="RO">Rondônia</option>
                                        <option value="RR">Roraima</option>
                                        <option value="SC">Santa Catarina</option>
                                        <option value="SP">São Paulo</option>
                                        <option value="SE">Sergipe</option>
                                        <option value="TO">Tocantins</option>
                                    </select>
                                </div>

                                <button type="submit" class="btn btn-primary w-100 mb-2">
                                    <i class="bi bi-credit-card me-2"></i>Finalizar Compra
                                </button>
                            </form>
                            <a href="/produtos" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Continuar Comprando
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
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

<script>
document.getElementById('formFinalizarCompra').addEventListener('submit', function() {
    var modal = new bootstrap.Modal(document.getElementById('loadingModal'));
    modal.show();
});

// Máscara para o CEP
document.getElementById('cep').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length > 5) {
        value = value.substring(0, 5) + '-' + value.substring(5);
    }
    e.target.value = value;
});

// Busca CEP
document.getElementById('buscarCep').addEventListener('click', function() {
    const cep = document.getElementById('cep').value.replace(/\D/g, '');
    const feedback = document.getElementById('cepFeedback');
    
    if (cep.length !== 8) {
        feedback.innerHTML = '<span class="text-danger">CEP inválido</span>';
        return;
    }

    feedback.innerHTML = '<span class="text-muted">Buscando CEP...</span>';
    
    fetch(`https://viacep.com.br/ws/${cep}/json/`)
        .then(response => response.json())
        .then(data => {
            if (data.erro) {
                feedback.innerHTML = '<span class="text-danger">CEP não encontrado</span>';
                return;
            }

            document.getElementById('cidade').value = data.localidade;
            document.getElementById('estado').value = data.uf;
            
            feedback.innerHTML = '<span class="text-success">CEP encontrado!</span>';
        })
        .catch(error => {
            console.error('Erro ao buscar CEP:', error);
            feedback.innerHTML = '<span class="text-danger">Erro ao buscar CEP</span>';
        });
});
</script>
