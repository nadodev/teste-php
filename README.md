# Sistema ERP

Sistema ERP desenvolvido em PHP puro seguindo princípios de arquitetura limpa.

## Requisitos do Sistema

### Versões Necessárias
- PHP >= 7.4
- MySQL >= 5.7
- Composer (para gerenciamento de dependências)

### Dependências
- PHPMailer (^6.10)
- Resend PHP SDK (^0.18.0)

## Instalação

1. Clone o repositório
```bash
git clone [url-do-repositorio]
cd erp_projeto
```

2. Instale as dependências
```bash
composer install
```

3. Configure o ambiente
- Copie o arquivo `.env.example` para `.env`
- Ajuste as variáveis de ambiente no arquivo `.env`:
```env
# Configurações do Banco de Dados
DB_HOST=localhost
DB_NAME=erp_teste
DB_USER=root
DB_PASS=1234
DB_CHARSET=utf8mb4

# Configurações de Email
RESEND_API_KEY=sua_chave_api
MAIL_FROM_ADDRESS=seu_email@dominio.com
MAIL_FROM_NAME=ERP Store
```

4. Crie o banco de dados
- Execute o script SQL em `database/init.sql` para criar o banco de dados e tabelas

5. Configure o servidor web
- Configure o DocumentRoot para a pasta `public/`
- Certifique-se que o mod_rewrite está habilitado (Apache)
- Configure as permissões corretas nas pastas

## Estrutura do Banco de Dados

### Tabelas Principais

1. **produtos**
   - id (INT, AUTO_INCREMENT, PRIMARY KEY)
   - nome (VARCHAR(255))
   - preco (DECIMAL(10,2))

2. **estoque**
   - id (INT, AUTO_INCREMENT, PRIMARY KEY)
   - produto_id (INT, FOREIGN KEY)
   - variacao (VARCHAR(100))
   - quantidade (INT)

3. **pedidos**
   - id (INT, AUTO_INCREMENT, PRIMARY KEY)
   - email (VARCHAR(255))
   - cidade (VARCHAR(100))
   - estado (CHAR(2))
   - subtotal (DECIMAL(10,2))
   - desconto (DECIMAL(10,2))
   - frete (DECIMAL(10,2))
   - total (DECIMAL(10,2))
   - status (VARCHAR(50))
   - created_at (TIMESTAMP)

4. **pedido_itens**
   - id (INT, AUTO_INCREMENT, PRIMARY KEY)
   - pedido_id (INT, FOREIGN KEY)
   - produto_id (INT, FOREIGN KEY)
   - quantidade (INT)
   - preco_unitario (DECIMAL(10,2))
   - subtotal (DECIMAL(10,2))

5. **cupons**
   - codigo (VARCHAR(50), PRIMARY KEY)
   - valor_desconto (DECIMAL(10,2))
   - valor_minimo (DECIMAL(10,2))
   - validade (DATE)
   - created_at (TIMESTAMP)

## Rotas do Sistema

### Produtos
- `GET /produtos` - Lista todos os produtos
- `GET /produto/novo` - Formulário de novo produto
- `POST /produto/novo` - Cria novo produto
- `GET /produto/editar` - Formulário de edição
- `POST /produto/editar` - Atualiza produto
- `GET /produto/excluir` - Remove produto

### Carrinho
- `GET /carrinho` - Visualiza carrinho
- `POST /carrinho/adicionar` - Adiciona item
- `POST /carrinho/remover` - Remove item
- `POST /carrinho/atualizar` - Atualiza quantidade
- `POST /carrinho/aplicar-cupom` - Aplica cupom
- `POST /carrinho/remover-cupom` - Remove cupom
- `POST /carrinho/finalizar` - Finaliza compra
- `GET /carrinho/sucesso` - Página de sucesso

### Cupons
- `GET /cupons` - Lista cupons
- `GET /cupom/novo` - Formulário de novo cupom
- `POST /cupom/novo` - Cria cupom
- `GET /cupom/editar` - Formulário de edição
- `POST /cupom/editar` - Atualiza cupom
- `GET /cupom/excluir` - Remove cupom

### Pedidos
- `GET /pedidos` - Lista pedidos
- `GET /pedidos/detalhes` - Detalhes do pedido

## Funcionalidades Principais

### Gestão de Produtos
- Cadastro de produtos com nome e preço
- Controle de estoque com variações
- Atualização de preços e quantidades

### Carrinho de Compras
- Adição/remoção de produtos
- Cálculo de subtotal
- Aplicação de cupons de desconto
- Cálculo de frete
- Finalização de compra

### Cupons de Desconto
- Criação de cupons com valor mínimo
- Validação de data de validade
- Aplicação automática de desconto

### Pedidos
- Registro de pedidos
- Detalhamento de itens
- Status de pedido
- Envio de email de confirmação

## Configuração de Email

O sistema utiliza o Resend API para envio de emails. Configure as credenciais no arquivo `.env`:

```env
RESEND_API_KEY=sua_chave_api
MAIL_FROM_ADDRESS=seu_email@dominio.com
MAIL_FROM_NAME=ERP Store
```

## Debug

O sistema possui funções de debug:
- `dd($variavel)` - Dump and die (para e mostra variável)
- `d($variavel)` - Dump (mostra variável sem parar)

## Estrutura de Diretórios

```
src/
├── Domain/           # Regras de negócio e entidades
├── Infrastructure/   # Implementações concretas (DB, etc)
├── Application/      # Casos de uso da aplicação
└── Presentation/    # Controllers e interface com usuário
```

## Segurança

- Validação de dados em todos os formulários
- Proteção contra SQL Injection usando PDO
- Sanitização de saída HTML
- Controle de sessão para carrinho

## Suporte

Para problemas ou dúvidas, verifique os logs em:
- PHP error log
- Logs do sistema em `storage/logs/`

## Contribuição

1. Faça um Fork do projeto
2. Crie uma Branch para sua Feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a Branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## Licença

Este projeto está sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.

## Contato

Seu Nome - [@seutwitter](https://twitter.com/seutwitter) - email@exemplo.com

Link do Projeto: [https://github.com/seu-usuario/erp_projeto](https://github.com/seu-usuario/erp_projeto) 