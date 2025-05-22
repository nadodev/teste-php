✅ Fase 1 – Modelagem e Criação do Banco de Dados [COMPLETED]
🔹 Tarefa 1.1 – Criar Script SQL com as tabelas: [DONE]
- Banco de dados erp_teste criado
- Tabelas produtos, estoque, pedidos e cupons criadas
- Estrutura do projeto configurada com arquitetura limpa

Crie o banco de dados erp_teste com as tabelas:

CREATE TABLE produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    preco DECIMAL(10,2) NOT NULL
);

CREATE TABLE estoque (
    id INT AUTO_INCREMENT PRIMARY KEY,
    produto_id INT NOT NULL,
    variacao VARCHAR(100),
    quantidade INT NOT NULL,
    FOREIGN KEY (produto_id) REFERENCES produtos(id)
);

CREATE TABLE pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subtotal DECIMAL(10,2) NOT NULL,
    frete DECIMAL(10,2) NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    cep VARCHAR(10),
    status VARCHAR(50),
    email VARCHAR(255),
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE cupons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(50) NOT NULL UNIQUE,
    valor_desconto DECIMAL(10,2) NOT NULL,
    validade DATE NOT NULL,
    valor_minimo DECIMAL(10,2) NOT NULL
);


✅ Fase 2 – Backend [COMPLETED]
🔹 Tarefa 2.1 – Conectar ao banco [DONE]
- Conexão PDO implementada
- Configuração de ambiente (.env) criada

🔹 Tarefa 2.2 – CRUD de Produto + Estoque [DONE]
- Entidades e Repositórios criados
- Formulário de cadastro implementado
- Listagem de produtos implementada

🔹 Tarefa 2.3 – Implementar Sessão de Carrinho [DONE]
- Carrinho implementado com sessão
- Cálculo de frete implementado
- Interface do carrinho criada

✅ Fase 3 – Funcionalidades Extras [COMPLETED]
🔹 Tarefa 3.1 – Integração com ViaCEP [DONE]
- Serviço ViaCEP implementado
- Consulta de CEP no carrinho
- Exibição do endereço de entrega

🔹 Tarefa 3.2 – Cupons de desconto [DONE]
- CRUD de cupons implementado
- Validação de cupons
- Aplicação de desconto no carrinho

🔹 Tarefa 3.3 – Envio de E-mail [DONE]
- Configuração do PHPMailer
- Template de e-mail de confirmação
- Envio após finalização do pedido

🔹 Tarefa 3.4 – Webhook para atualização de pedidos [DONE]
- Endpoint POST /webhook implementado
- Atualização de status
- Deleção de pedidos cancelados

✅ Fase 4 – Frontend
🔹 Tarefa 4.1 – Tela de cadastro de produto
Inputs: Nome, Preço, Variação, Quantidade

Usar Bootstrap para organização

🔹 Tarefa 4.2 – Tela de carrinho/pedido
Listagem dos produtos no carrinho

Aplicar cupom

Mostrar cálculo de frete

Campo CEP, E-mail

Botão Finalizar pedido

✅ Fase 5 – Entrega
🔹 Tarefa 5.1 – Subir no GitHub
Estrutura limpa

README com instruções de:

Instalação

Banco de dados (script incluído)

Tecnologias utilizadas




no final de cada fase quero que faça o commit e atualize esse documento que foi feito.