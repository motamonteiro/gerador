Gera um estrutura inicial a partir das tabelas já criadas em um Banco MYSQL. 

Segue abaixo o passo a passo das configurações:
 

Crie uma nova aplicação Laravel:

``` bash
laravel new minha-aplicacao
```

Configure o arquivo `.env`

Gere uma chave para a aplicação (opcional):
``` bash
php artisan key:generate
```

Verifique a instalação com o comando:

 ``` bash
php artisan serve
 ```

Altere o nome da aplicação com o comando (opcional):

``` bash
php artisan app:name MinhaAplicacao
```


Coloque as dependências do projeto no `composer.json` executando os seguintes passos:

``` bash
composer require motamonteiro/helpers
```

``` bash
composer require prettus/l5-repository
```

``` bash
php artisan vendor:publish --provider "Prettus\Repository\Providers\RepositoryServiceProvider"
```

Ative o cache dos repositórios no arquivo `config/repository.php`

``` php
/*
    |--------------------------------------------------------------------------
    | Cache Config
    |--------------------------------------------------------------------------
    |
    */
    'cache'      => [
        /*
         |--------------------------------------------------------------------------
         | Cache Status
         |--------------------------------------------------------------------------
         |
         | Enable or disable cache
         |
         */
        'enabled'    => true,
```

Utilize o [motamonteiro/gerador](https://github.com/motamonteiro/gerador/) para gerar os arquivos a partir do banco de dados MySql já criado conforme abaixo:

Configure o arquivo `.env` definindo o seu banco de dados MySql já criado e informe a pasta do seu projeto.

Baixe as dependências do gerador
``` bash
composer install
```

Inicie o servidor do php
``` bash
php -S localhost:8080 -t public
```

Clique no link `/criarDiretorios` para ele criar os diretórios aonde os arquivos ficarão e depois clique no botão voltar do navegador para voltar para a página inicial.

Clique nos links para gerar os arquivos desejados e depois clique no botão voltar do navegador para voltar para a página inicial.
 
De volta em `MinhaAplicacao` prossiga com as configurações.

Rode a aplicação

``` bash
php artisan serve
```