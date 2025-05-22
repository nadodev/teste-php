# Sistema ERP

## Descrição
Sistema ERP desenvolvido em PHP com arquitetura em camadas, seguindo os princípios SOLID e boas práticas de programação.

## Estrutura do Projeto
```
src/
├── Domain/           # Regras de negócio e entidades
│   ├── Entities/     # Entidades do sistema
│   └── Services/     # Serviços de domínio
├── Infrastructure/   # Implementações técnicas
│   ├── Config/       # Configurações
│   └── Repositories/ # Repositórios
└── Presentation/     # Interface com usuário
    ├── Controllers/  # Controladores
    └── Views/        # Views
```

## Funcionalidades

### Carrinho de Compras
- Adicionar produtos
- Remover produtos
- Atualizar quantidade
- Aplicar cupom de desconto
- Cálculo de frete
- Finalização de pedido

### Cálculo de Valores
- Subtotal: soma dos produtos
- Frete: 
  - Grátis para compras acima de R$ 200,00
  - R$ 15,00 para compras entre R$ 52,00 e R$ 166,59
  - R$ 20,00 para demais compras
- Desconto: aplicado via cupom
- Total: subtotal + frete - desconto

### Cupom de Desconto
- Validação de código
- Verificação de validade
- Aplicação de desconto
- Remoção de cupom

### Webhook de Pedidos
O sistema possui um endpoint para atualização de status de pedidos via webhook.

#### Endpoint
```
POST /webhook
```

#### Formato do Payload
```json
{
  "pedido_id": "7",
  "status": "cancelado"
}
```

#### Comportamento
- Se o status for "cancelado":
  - O pedido é excluído do sistema
  - Todos os itens relacionados são removidos
- Para outros status:
  - Apenas atualiza o status do pedido
  - Mantém o histórico de alterações



## Requisitos
- PHP 8.0 ou superior
- MySQL 5.7 ou superior
- Composer
- Servidor web (Apache/Nginx)

## Instalação
1. Clone o repositório
```bash
git clone [url-do-repositorio]
```

2. Instale as dependências
```bash
composer install
```

3. Configure o banco de dados
- Copie o arquivo `.env.example` para `.env`
- Ajuste as configurações do banco de dados
- Importe o bancao de dados que está na pasta ./database


## Licença
Este projeto está sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.

## Contato

Leonardo Geja - [Linkdin](https://www.linkedin.com/in/leonardogeja/)

Link do Projeto: [https://github.com/nadodev/erp_projeto](https://github.com/nadodev/erp_projeto) 