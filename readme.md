O gerador gera os arquivos para apis Laravel ou Lumen com estrutura de Entities, Presenters, Repositories, Services, Transformers e Validators.

Segue abaixo o passo a passo das configurações:
 

Crie uma nova aplicação Laravel:

``` bash
laravel new minha-aplicacao
```

Configure o arquivo `.env`

Gere uma chave para a aplicação:
``` bash
php artisan key:generate
```

Verifique a instalação com o comando:

 ``` bash
php artisan serve
 ```

 Altere o nome da aplicação com o comando:

  ``` bash
 php artisan app:name MinhaAplicacao
  ```


Coloque as dependências do projeto no `composer.json` executando os seguintes passos:

``` bash
composer require prettus/l5-repository
```

``` bash
composer require league/fractal
```

Adicione `"prettus/laravel-validation": "1.1.*"` no composer.json

``` json
"require": {
        "php": ">=5.5.9",
        "laravel/framework": "5.2.*",
        "prettus/l5-repository": "^2.6",
        "prettus/laravel-validation": "1.1.*",
        "league/fractal": "^0.13.0",
        "barryvdh/laravel-cors": "^0.8.1"
    },
```

No seu `config/app.php` adicione `Prettus\Repository\Providers\RepositoryServiceProvider::class`  e `Barryvdh\Cors\ServiceProvider::class` no final do array providers:

``` php
'providers' => [
    ...
    Prettus\Repository\Providers\RepositoryServiceProvider::class,
    Barryvdh\Cors\ServiceProvider::class,
],
```

Publique a configuração

``` bash
php artisan vendor:publish
```

Crie o arquivo `app/Serializers/DataArraySerializer.php`

``` php
<?php
/**
 * User: motamonteiro
 * Date: 27/04/2016
 * Time: 09:37
 */

namespace MinhaAplicacao\Serializers;


use League\Fractal\Serializer\ArraySerializer;

class DataArraySerializer extends ArraySerializer
{

    /**
     * Serialize a collection.
     *
     * @param string $resourceKey
     * @param array  $data
     *
     * @return array
     */
    public function collection($resourceKey, array $data)
    {
        return $data;
        //return ["data" => $data];
    }
    /**
     * Serialize an item.
     *
     * @param string $resourceKey
     * @param array  $data
     *
     * @return array
     */
    public function item($resourceKey, array $data)
    {
        return $data;
        //return ["data" => $data];
    }

}
```

Altere o serializer do arquivo `config/repository.php`

``` php
'fractal'=>[
        'params'=>[
            'include'=>'include'
        ],
        'serializer' => \MinhaAplicacao\Serializers\DataArraySerializer::class
    ],
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

No arquivo config/app.php registre o `MinhaAplicacao\Providers\MinhaAplicacaoRepositoryProvider::class,`

``` php
/*
 * Application Service Providers...
 */
MinhaAplicacao\Providers\AppServiceProvider::class,
MinhaAplicacao\Providers\AuthServiceProvider::class,
MinhaAplicacao\Providers\EventServiceProvider::class,
MinhaAplicacao\Providers\RouteServiceProvider::class,
MinhaAplicacao\Providers\MinhaAplicacaoRepositoryProvider::class,
```

Rode a aplicação

``` bash
php artisan serve
```