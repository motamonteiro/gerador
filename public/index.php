<?php

require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Response;

$app = new Silex\Application();

$app['config'] = [
    'db_connection' => 'oracle',
    'db_host' => '127.0.0.1',
    'db_name' => 'itcmd',
    'db_username' => 'root',
    'db_password' => '1234',
    'table_prefix' => 'itcmd',
    'project_name' => 'Itcmd',
    'stub_path' =>  '../src/Stubs/',
    'destination_path' =>  __DIR__.'\\arquivos\\' //D:\web\www\gerador\public\arquivos

];

$app['debug'] = true;

$app['db'] = function() use ($app) {
    return new \PDO('mysql:host='.$app['config']['db_host'].';dbname='.$app['config']['db_name'].'',''.$app['config']['db_username'].'',''.$app['config']['db_password'].'');
};

$app->get('/', function() use ($app) {
    return new Response(file_get_contents('../resources/views/index.html'), 200);
});

$app->get('/tabelas', function() use ($app) {

    print_r(listarObjTabelas($app));
    exit;

});


$app->get('/entityEloquent', function() use ($app) {

    $tabelas = listarObjTabelas($app);

    $arquivosCriados = '';
    foreach ($tabelas as $tabela) {

        $tabelasEstrangeiras = $tabela->getTabelasEstrangeiras();
        $stubFuncoesBelongsTo = '';

        foreach ($tabelasEstrangeiras as $tabelaEstrangeira) {

            $stubFuncoesBelongsToAux = file_get_contents($app['config']['stub_path'].'Entities/Eloquent/_FUNCOES_BELONGS_TO.stub');
            $replaces = [
                'NOME_TABELA_FK_CAMEL_CASE_LC_FIRST' => $tabelaEstrangeira->getNomeCamelCaseLcFirstSingular(),
                'NOME_TABELA_FK_CAMEL_CASE'          => $tabelaEstrangeira->getNomeCamelCaseSingular(),
                'NOME_COLUNA_FK'                     => $tabelaEstrangeira->getChavePrimaria(),
            ];

            foreach ($replaces as $search => $replace) {
                $stubFuncoesBelongsToAux = str_replace('$' . strtoupper($search) . '$', $replace, $stubFuncoesBelongsToAux);
            }

            $stubFuncoesBelongsTo .= $stubFuncoesBelongsToAux;
        }

        $stub = file_get_contents($app['config']['stub_path'].'Entities/Eloquent/entity.stub');
        $replaces = [
            'NAMESPACE'            => 'namespace '.$app['config']['project_name'].'\Entities\Eloquent;',
            'CLASS'                => $tabela->getNomeCamelCaseSingular(),
            'DB_CONNECTION'        => $app['config']['db_connection'],
            'NOME_COMPLETO_TABELA' => $tabela->getNomeCompletoMinusculo(),
            'NOME_COLUNA_PK'       => $tabela->getChavePrimariaMinusculo(),
            'COLUNAS_SEM_PK'       => $tabela->getColunasCamposSemPkPorVirgulaMinusculo(),
            'FUNCOES_BELONGS_TO'   => $stubFuncoesBelongsTo
        ];

        foreach ($replaces as $search => $replace) {
            $stub = str_replace('$' . strtoupper($search) . '$', $replace, $stub);
        }

        $arquivo = $app['config']['destination_path'].'Entities\\Eloquent\\'.$tabela->getNomeCamelCaseSingular().'.php';
        criarArquivo($stub, $arquivo);
        $arquivosCriados .= $arquivo.'<br>';

    }

    return new Response($arquivosCriados, 200);

});

$app->get('/entityOrmSefaz', function() use ($app) {

    $stub_path = $app['config']['stub_path'].'Entities/OrmSefaz/';
    $destination_path = $app['config']['destination_path'].'Entities\\OrmSefaz\\';
    $tabelas = listarObjTabelas($app);

    $arquivosCriados = '';
    foreach ($tabelas as $tabela) {
        $stubPrivateNomeColuna = '';
        $stubConstrutorNomeColuna = '';
        $stubGetSet = '';
        $stubConstrutorColunaEstrangeiraDetalhada = '';
        $stubConstrutorColunaEstrangeira = '';
        foreach ($tabela->getColunas() as $coluna) {

          $replaces = [
            'NOME_COLUNA_CAMEL_CASE_LC_FIRST' => $coluna->getCampoCamelCaseLcFirst()
          ];

          $stubPrivateNomeColuna .= preencherStub($stub_path, 'PRIVATE_NOME_COLUNAS', $replaces);

          $replaces = [
            'NOME_COLUNA_CAMEL_CASE_LC_FIRST' => $coluna->getCampoCamelCaseLcFirst(),
            'NOME_COLUNA_MAIUSCULO' => $coluna->getCampoMaiusculo()
          ];

          $stubConstrutorNomeColuna .= preencherStub($stub_path, 'CONSTRUTOR_NOME_COLUNA', $replaces);

          $replaces = [
            'NOME_COLUNA_CAMEL_CASE_LC_FIRST' => $coluna->getCampoCamelCaseLcFirst(),
            'NOME_COLUNA_CAMEL_CASE' => $coluna->getCampoCamelCase()
          ];

          $stubGetSet .= preencherStub($stub_path, 'GET_SET', $replaces);

          if($coluna->getChave() == "MUL"){
            $tabelasEstrangeiras = $tabela->getTabelasEstrangeiras();
            foreach ($tabelasEstrangeiras as $tabelaEstrangeira) {
                $tabEstColunas = $tabelaEstrangeira->getColunas();
                foreach ($tabEstColunas as $tabEstColuna) {
                    if($tabEstColuna->getChave() == "PRI" && $tabEstColuna->getCampo() == $coluna->getCampoChaveEstrangeira()){
                        $nmeTabelaEstrangeira = $tabelaEstrangeira->getNomeCamelCaseSingular();
                    }
                }
            }

            $replaces = [
              'NOME_TABELA_ESTRANGEIRA' => $nmeTabelaEstrangeira,
              'COLUNA_TABELA_ESTRANGEIRA_CAMEL_CASE' => $coluna->getCampoChaveEstrangeiraCamelCase(),
              'NOME_COLUNA_MAIUSCULO' => $coluna->getCampoMaiusculo(),
              'NOME_CLASSE_ESTRANGEIRA' => lcfirst(substr($coluna->getCampoCamelCaseLcFirst(),2))
            ];

            $stubConstrutorColunaEstrangeiraDetalhada .= preencherStub($stub_path, 'CONSTRUTOR_COLUNA_ESTRANGEIRA_DETALHADA', $replaces);

            $replaces = [
              'NOME_TABELA_ESTRANGEIRA' => $nmeTabelaEstrangeira,
              'COLUNA_TABELA_ESTRANGEIRA_CAMEL_CASE' => $coluna->getCampoChaveEstrangeiraCamelCase(),
              'NOME_COLUNA_MAIUSCULO' => $coluna->getCampoMaiusculo(),
              'NOME_CLASSE_ESTRANGEIRA' => lcfirst(substr($coluna->getCampoCamelCaseLcFirst(),2))
            ];

            $stubConstrutorColunaEstrangeira .= preencherStub($stub_path, 'CONSTRUTOR_COLUNA_ESTRANGEIRA', $replaces);

          }

        }

        $replaces = [
            'NAMESPACE'            => 'namespace '.$app['config']['project_name'].'\Entities\OrmSefaz;',
            'CLASS'                => $tabela->getNomeCamelCaseSingular(),
            'PRIVATE_NOME_COLUNAS' => $stubPrivateNomeColuna,
            'CONSTRUTOR_NOME_COLUNA' => $stubConstrutorNomeColuna,
            'GET_SET'              => $stubGetSet,
            'CONSTRUTOR_COLUNA_ESTRANGEIRA_DETALHADA' => $stubConstrutorColunaEstrangeiraDetalhada,
            'CONSTRUTOR_COLUNA_ESTRANGEIRA' => $stubConstrutorColunaEstrangeira
        ];

        $stub = preencherStub($stub_path, 'entity', $replaces);

        $arquivo = $destination_path.$tabela->getNomeCamelCaseSingular().'.php';
        criarArquivo($stub, $arquivo);
        $arquivosCriados .= $arquivo.'<br>';

    }

    return new Response($arquivosCriados, 200);

});

$app->get('/presenter', function() use ($app) {

    $tabelas = listarObjTabelas($app);

    $arquivosCriados = '';
    foreach ($tabelas as $tabela) {

        $stub = file_get_contents($app['config']['stub_path'].'Presenters/presenter.stub');
        $replaces = [
            'NAMESPACE'            => 'namespace '.$app['config']['project_name'].'\Presenter;',
            'CLASS'                => $tabela->getNomeCamelCaseSingular(),
            'PROJETO'              => $app['config']['project_name'],
        ];

        foreach ($replaces as $search => $replace) {
            $stub = str_replace('$' . strtoupper($search) . '$', $replace, $stub);
        }

        $arquivo = $app['config']['destination_path'].'Presenters\\'.$tabela->getNomeCamelCaseSingular().'Presenter.php';
        criarArquivo($stub, $arquivo);
        $arquivosCriados .= $arquivo.'<br>';

    }

    return new Response($arquivosCriados, 200);

});

$app->get('/repositoryInterface', function() use ($app) {

    $tabelas = listarObjTabelas($app);

    $arquivosCriados = '';
    foreach ($tabelas as $tabela) {

        $stub = file_get_contents($app['config']['stub_path'].'Repositories/Interfaces/interface.stub');
        $replaces = [
            'NAMESPACE'            => 'namespace '.$app['config']['project_name'].'\Repositories\Interfaces;',
            'CLASS'                => $tabela->getNomeCamelCaseSingular(),
        ];

        foreach ($replaces as $search => $replace) {
            $stub = str_replace('$' . strtoupper($search) . '$', $replace, $stub);
        }

        $arquivo = $app['config']['destination_path'].'Repositories\\Interfaces\\'.$tabela->getNomeCamelCaseSingular().'Interface.php';
        criarArquivo($stub, $arquivo);
        $arquivosCriados .= $arquivo.'<br>';

    }

    //Cria o BaseInterface.php
    $stub = file_get_contents($app['config']['stub_path'].'Repositories/Interfaces/baseInterface.stub');
    $replaces = [
        'NAMESPACE'            => 'namespace '.$app['config']['project_name'].'\Repositories\Interfaces;',
    ];

    foreach ($replaces as $search => $replace) {
        $stub = str_replace('$' . strtoupper($search) . '$', $replace, $stub);
    }

    $arquivo = $app['config']['destination_path'].'Repositories\\Interfaces\\BaseInterface.php';
    criarArquivo($stub, $arquivo);
    $arquivosCriados .= $arquivo.'<br>';


    return new Response($arquivosCriados, 200);

});


$app->run();
