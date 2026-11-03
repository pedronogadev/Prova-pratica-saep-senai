# Sistema de Gerenciamento - Clinica Veterinaria (SAEP SENAI)

Este projeto consiste em um sistema web completo desenvolvido para a Avaliacao Pratica de Desempenho do Estudante (SAEP - SENAI), cujo objetivo e informatizar o atendimento, o gerenciamento de tutores, pets e agendamentos de uma clinica veterinaria, alem de garantir o cumprimento das diretrizes da LGPD (Lei Geral de Protecao de Dados).

## Estrutura de Arquivos da Entrega

A estrutura de diretorios e arquivos solicitada na avaliacao esta organizada da seguinte forma:

```
/
├── Anexo1_Documentacao_de_Software.docx  # Documentacao de software preenchida (Anexo 1)
├── saep_db.sql                           # Script para criacao e povoamento do banco de dados
├── README.md                             # Documentacao geral do projeto
└── sistema/                              # Diretorio do codigo-fonte da aplicacao
    ├── conexao.php                       # Configuracao de conexao segura via PDO
    ├── login.php                         # Autenticacao de usuarios e validacao de login
    ├── principal.php                     # Painel de navegacao e controle de sessao
    ├── logout.php                        # Encerramento de sessao do usuario
    ├── tutores.php                       # Modulo de gestao de tutores (busca, cadastro, edicao e CPF criptografado)
    ├── pets.php                          # Modulo de gerenciamento de pets e vinculo com tutores
    └── agendamentos.php                  # Modulo de agendamentos ordenados por ano e edicao

```

## Tecnologias e Infraestrutura Utilizadas

* SGBD: MySQL (Versao 8.0)

* Linguagem de Programacao: PHP (Versao 8.2)

* Camada de Dados: PDO (PHP Data Objects)

* Criptografia: AES-128 via OpenSSL para dados sensiveis (CPF) e Hashes de senha para usuarios

* Frontend: HTML5, CSS3 nativo (estilizacao responsiva em cards e tabelas)

## Seguranca e Boas Praticas (LGPD e OWASP)

1. Protecao contra SQL Injection:

   * Todas as operacoes com banco de dados utilizam Prepared Statements e Parameters Binding atraves do PDO, impedindo a injecao de comandos SQL maliciosos.

2. Criptografia de Dados Sensiveis (LGPD - RF03):

   * O CPF dos tutores e armazenado de forma criptografada na coluna `tutor.cpf` do banco de dados, sendo descriptografado apenas em tempo de execucao para exibicao na interface.

3. Controle de Acesso e Sessoes (RF01 e RF02):

   * Validacao de sessao ativa em todas as paginas administrativas. Tentativas de acesso direto por URL redirecionam o visitante para a pagina de login.

## Instrucoes para Instalacao e Execucao

### 1. Configuracao do Banco de Dados

1. Abra o seu gerenciador de banco de dados (ex.: MySQL Workbench, phpMyAdmin ou linha de comando).

2. Importe e execute o arquivo `saep_db.sql`.

3. O script criara o banco de dados `saep_clinica_veterinaria` e todas as tabelas necessarias (`usuario`, `tutor`, `pet`, `agendamento`), preenchendo-as com 3 registros iniciais cada.

### 2. Configuracao da Conexao PHP

1. Copie a pasta `sistema/` para o diretorio raiz do seu servidor web (ex.: `htdocs` no XAMPP ou `www` no WampServer).

2. Se necessario, ajuste as credenciais de acesso ao banco de dados no arquivo `sistema/conexao.php`:

   ```
   $host = 'localhost';
   $db   = 'saep_clinica_veterinaria';
   $user = 'root';
   $pass = ''; // Insira a senha do seu ambiente
   
   ```

### 3. Credenciais para Acesso ao Sistema

Para testar a autenticacao no arquivo `login.php`, utilize as seguintes credenciais pre-cadastradas:

* E-mail: `admin@veterinaria.com`

* Senha: `admin123`

## Mapeamento dos Requisitos Funcionais (RF)

| Requisito | Funcionalidade | Arquivo(s) Responsavel(is) | 
| ----- | ----- | ----- | 
| RF01 | Autenticacao de usuarios com validacao e mensagens de erro | `login.php` | 
| RF02 | Exibicao do usuario logado e encerramento de sessao | `principal.php`, `logout.php` | 
| RF03 | Criptografia do CPF dos tutores para conformidade com a LGPD | `tutores.php` | 
| RF04 | Cadastro, listagem, pesquisa por nome e edicao de tutores | `tutores.php` | 
| RF05 | Cadastro de pets vinculados aos tutores | `pets.php` | 
| RF06 | Listagem de agendamentos ordenada por ano e atualizacao de datas | `agendamentos.php` | 
