<?php

// Rotas do carrinho
$router->add('carrinho', 'CarrinhoController', 'index');
$router->add('carrinho/adicionar', 'CarrinhoController', 'adicionar');
$router->add('carrinho/remover', 'CarrinhoController', 'remover');
$router->add('carrinho/atualizar', 'CarrinhoController', 'atualizar');
$router->add('carrinho/aplicar-cupom', 'CarrinhoController', 'aplicarCupom');
$router->add('carrinho/remover-cupom', 'CarrinhoController', 'removerCupom');
$router->add('carrinho/finalizar', 'CarrinhoController', 'finalizar');
$router->add('carrinho/sucesso', 'CarrinhoController', 'sucesso');

// Rotas de pedidos
$router->add('pedidos', 'PedidosController', 'index');
$router->add('pedidos/detalhes', 'PedidosController', 'detalhes'); 