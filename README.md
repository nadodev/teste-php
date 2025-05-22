# Sistema ERP

Sistema ERP desenvolvido em PHP puro seguindo princípios de arquitetura limpa.

## Instalação

1. Clone o repositório
2. Execute `composer install`
3. Copie `.env.example` para `.env` e configure suas variáveis de ambiente
4. Execute o script SQL em `database/init.sql` para criar o banco de dados

## Estrutura do Projeto

```
src/
├── Domain/           # Regras de negócio e entidades
├── Infrastructure/   # Implementações concretas (DB, etc)
├── Application/      # Casos de uso da aplicação
└── Presentation/    # Controllers e interface com usuário
```

## Fases Completadas

### ✅ Fase 1 - Modelagem e Criação do Banco de Dados
- [x] Criação das tabelas do banco de dados
- [x] Configuração da conexão PDO
- [x] Estrutura base do projeto

## Tecnologias Utilizadas
- PHP 7.4+
- MySQL
- PHPMailer (para envio de emails) 