<?php

require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Response;

$app = new Silex\Application();

$app['config'] = [
    'db_connection' => 'oracle',
    'db_host' => '127.0.0.1',
    'db_name' => 'itcmd',
    'db_username' => 'root',
    'db_password' => 'root',
    'table_prefix' => 'itcmd',
    'project_name' => 'Itcmd',
    'stub_path' =>  '../src/Stubs/',
    'destination_path' =>  __DIR__.'/arquivos/' //D:\web\www\gerador\public\arquivos

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

    $stub_path = $app['config']['stub_path'].'Entities/Eloquent/';
    $destination_path = $app['config']['destination_path'].'Entities/Eloquent/';
    $arquivosCriados = '';

    $tabelas = listarObjTabelas($app);

    foreach ($tabelas as $tabela) {

        $stubFuncoesBelongsTo = '';
        $flgTimeStamps = false;
        foreach ($tabela->getColunas() as $coluna) {
            
            if($coluna->getCampoMinusculo() == 'created_at'){
                $flgTimeStamps = true;        
            }
            
            if($coluna->getChave() == "MUL"){

                $tabelasEstrangeiras = $tabela->getTabelasEstrangeiras();
                foreach ($tabelasEstrangeiras as $tabelaEstrangeira) {
                    if (($tabelaEstrangeira->getNome() == $coluna->getCampoTabelaEstrangeira())) {
                        $nmeTabelaEstrangeira = $tabelaEstrangeira->getNomeCamelCaseSingular();
                        break;
                    }                    
                }

                $replaces = [
                    'NOME_CLASSE_ESTRANGEIRA_CAMEL_CASE_LC_FIRST' => $coluna->getNomeClasseCamelCaseLcFirst($coluna->getCampo()),
                    'NOME_TABELA_FK_CAMEL_CASE' => $nmeTabelaEstrangeira,
                    'NOME_COLUNA_FK'            => $coluna->getCampoChaveEstrangeiraMinusculo(),
                    'NOME_COLUNA'               => $coluna->getCampoMinusculo(),
                ];

                $stubFuncoesBelongsTo .= preencherStub($stub_path, '_FUNCOES_BELONGS_TO', $replaces);
            }
        }

        $stub = file_get_contents($app['config']['stub_path'].'Entities/Eloquent/entity.stub');
        $replaces = [
            'NAMESPACE'            => 'namespace '.$app['config']['project_name'].'\Entities\Eloquent;',
            'CLASS'                => $tabela->getNomeCamelCaseSingular(),
            'PUBLIC_CONNECTION'    => ($app['config']['db_connection'] == 'oracle') ? 'protected $connection = \''.$app['config']['db_connection'].'\';' : '',
            'PUBLIC_SEQUENCE'      => ($app['config']['db_connection'] == 'oracle') ? 'protected $sequence = \''.$tabela->getNomeCompletoMinusculo().'_seq\';' : '',
            'PUBLIC_TIMESTAMPS'    => (!$flgTimeStamps) ? 'public $timestamps = false;' : '',
            'NOME_COMPLETO_TABELA' => $tabela->getNomeCompletoMinusculo(),
            'NOME_COLUNA_PK'       => $tabela->getChavePrimariaMinusculo(),
            'COLUNAS_SEM_PK'       => $tabela->getColunasCamposSemPkPorVirgulaMinusculo(),
            'FUNCOES_BELONGS_TO'   => $stubFuncoesBelongsTo
        ];

        foreach ($replaces as $search => $replace) {
            $stub = str_replace('$' . strtoupper($search) . '$', $replace, $stub);
        }

        $arquivo = $destination_path.$tabela->getNomeCamelCaseSingular().'.php';
        criarArquivo($stub, $arquivo);
        $arquivosCriados .= $arquivo.'<br>';

    }

    return new Response($arquivosCriados, 200);

});

$app->get('/entityOrmSefaz', function() use ($app) {

    $stub_path = $app['config']['stub_path'].'Entities/OrmSefaz/';
    $destination_path = $app['config']['destination_path'].'Entities/OrmSefaz/';
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

    $stub_path = $app['config']['stub_path'].'Presenters/';
    $destination_path = $app['config']['destination_path'].'Presenters/';
    $arquivosCriados = '';

    $tabelas = listarObjTabelas($app);

    foreach ($tabelas as $tabela) {

        $replaces = [
            'NAMESPACE'            => 'namespace '.$app['config']['project_name'].'\Presenters;',
            'CLASS'                => $tabela->getNomeCamelCaseSingular(),
            'PROJETO'              => $app['config']['project_name'],
        ];

        $stub = preencherStub($stub_path, 'presenter', $replaces);

        $arquivo = $destination_path.$tabela->getNomeCamelCaseSingular().'Presenter.php';
        criarArquivo($stub, $arquivo);
        $arquivosCriados .= $arquivo.'<br>';

    }

    return new Response($arquivosCriados, 200);

});

$app->get('/repositoryInterface', function() use ($app) {

    $stub_path = $app['config']['stub_path'].'Repositories/Interfaces/';
    $destination_path = $app['config']['destination_path'].'Repositories/Interfaces/';
    $arquivosCriados = '';

    $tabelas = listarObjTabelas($app);

    foreach ($tabelas as $tabela) {

        $replaces = [
            'NAMESPACE' => 'namespace '.$app['config']['project_name'].'\Repositories\Interfaces;',
            'CLASS'     => $tabela->getNomeCamelCaseSingular(),
        ];

        $stub = preencherStub($stub_path, 'interface', $replaces);

        $arquivo = $destination_path.$tabela->getNomeCamelCaseSingular().'Interface.php';
        criarArquivo($stub, $arquivo);
        $arquivosCriados .= $arquivo.'<br>';

    }

    //Cria o BaseInterface.php
    $replaces = [
        'NAMESPACE' => 'namespace '.$app['config']['project_name'].'\Repositories\Interfaces;',
    ];

    $stub = preencherStub($stub_path, 'baseInterfaceV1', $replaces);

    $arquivo = $destination_path.'BaseInterface.php';
    criarArquivo($stub, $arquivo);
    $arquivosCriados .= $arquivo.'<br>';

    return new Response($arquivosCriados, 200);

});


$app->get('/provider', function() use ($app) {

    $stub_path = $app['config']['stub_path'].'Providers/';
    $destination_path = $app['config']['destination_path'].'Providers/';
    $arquivosCriados = '';

    $tabelas = listarObjTabelas($app);

    $stubBinds = '';
    foreach ($tabelas as $tabela) {

        $replaces = [
            'PATH_INTERFACE_REPOSITORY_CLASS' => '\\'.$app['config']['project_name'].'\Repositories\\Interfaces\\'.$tabela->getNomeCamelCaseSingular().'Interface::class',
            'PATH_REPOSITORY_CLASS'           => '\\'.$app['config']['project_name'].'\Repositories\\Eloquent\\'.$tabela->getNomeCamelCaseSingular().'Repository::class',
        ];

        $stubBinds .= preencherStub($stub_path, '_BINDS', $replaces);
    }

    $replaces = [
        'NAMESPACE' => 'namespace '.$app['config']['project_name'].'\Providers;',
        'CLASS'     => $app['config']['project_name'],
        'BINDS'     => $stubBinds
    ];

    $stub = preencherStub($stub_path, 'provider', $replaces);

    $arquivo = $destination_path.$app['config']['project_name'].'RepositoryProvider.php';
    criarArquivo($stub, $arquivo);
    $arquivosCriados .= $arquivo.'<br>';

    return new Response($arquivosCriados, 200);

});

$app->get('/repositoryEloquent', function() use ($app) {

    $stub_path = $app['config']['stub_path'].'Repositories/Eloquent/';
    $destination_path = $app['config']['destination_path'].'Repositories/Eloquent/';
    $arquivosCriados = '';

    $tabelas = listarObjTabelas($app);

    foreach ($tabelas as $tabela) {

        $replaces = [
            'NAMESPACE' => 'namespace ' . $app['config']['project_name'] . '\Repositories\Eloquent;',
            'CLASS' => $tabela->getNomeCamelCaseSingular(),
            'PROJETO' => $app['config']['project_name'],
            'COLUNAS' => '\''.$tabela->getChavePrimariaMinusculo().'\', '.$tabela->getColunasCamposSemPkPorVirgulaMinusculo()
        ];

        $stub = preencherStub($stub_path, 'repositoryV1', $replaces);

        $arquivo = $destination_path . $tabela->getNomeCamelCaseSingular().'Repository.php';
        criarArquivo($stub, $arquivo);
        $arquivosCriados .= $arquivo.'<br>';

    }

    $replaces = [
        'NAMESPACE' => 'namespace '.$app['config']['project_name'].'\Repositories\Eloquent;',
    ];

    $stub = preencherStub($stub_path, 'baseRepositoryV1', $replaces);

    $arquivo = $destination_path.'BaseRepository.php';
    criarArquivo($stub, $arquivo);
    $arquivosCriados .= $arquivo.'<br>';

    return new Response($arquivosCriados, 200);

});

$app->get('/transformer', function() use ($app) {

    $stub_path = $app['config']['stub_path'].'Transformers/';
    $destination_path = $app['config']['destination_path'].'Transformers/';
    $arquivosCriados = '';

    $tabelas = listarObjTabelas($app);

    foreach ($tabelas as $tabela) {
        
        $stubDefaultIncludes = '';
        $stubReturnTransformer = '';
        $stubFunctionIncludes = '';

        $replaces = [
            'TABELA_ESTRANGEIRA_SINGULAR_CAMEL_CASE' => $app['config']['project_name'].'\Entities\Eloquent\\'.$tabela->getNomeCamelCaseSingular(),
        ];
        $stubUseEntities = preencherStub($stub_path, '_USE_ENTITIES', $replaces);

        foreach ($tabela->getColunas() as $coluna) {

            if ($coluna->getChave() == "MUL") {

                $tabelasEstrangeiras = $tabela->getTabelasEstrangeiras();
                foreach ($tabelasEstrangeiras as $tabelaEstrangeira) {
                    if (($tabelaEstrangeira->getNome() == $coluna->getCampoTabelaEstrangeira())) {

                        $replaces = [
                            'TABELA_ESTRANGEIRA_SINGULAR_CAMEL_CASE' => $app['config']['project_name'].'\Entities\Eloquent\\'.$tabelaEstrangeira->getNomeCamelCaseSingular(),
                        ];
                        $stubUseEntities .= preencherStub($stub_path, '_USE_ENTITIES', $replaces);


                        $replaces = [
                            'TABELA_ESTRANGEIRA_SINGULAR_CAMEL_CASE_LC_FIRST' => $tabelaEstrangeira->getNomeCamelCaseLcFirstSingular(),
                        ];
                        $stubDefaultIncludes .= preencherStub($stub_path, '_DEFAULT_INCLUDES', $replaces);

                        $replaces = [
                            'CLASS' => $tabela->getNomeCamelCaseSingular(),
                            'TABELA_ESTRANGEIRA_SINGULAR_CAMEL_CASE_LC_FIRST' => $tabelaEstrangeira->getNomeCamelCaseLcFirstSingular(),
                            'TABELA_ESTRANGEIRA_SINGULAR_CAMEL_CASE' => $tabelaEstrangeira->getNomeCamelCaseSingular(),
                        ];
                        $stubFunctionIncludes .= preencherStub($stub_path, '_FUNCTION_INCLUDES', $replaces);

                        break;
                    }
                }
            }

            $replaces = [
                'NOME_COLUNA_CAMEL_CASE_LC_FIRST' => $coluna->getCampoCamelCaseLcFirst(),
                'NOME_COLUNA_MINUSCULO' => $coluna->getCampoMinusculo(),
            ];
            $stubReturnTransformer .= preencherStub($stub_path, '_RETURN_TRANSFORM', $replaces);
            
        }


        $replaces = [
            'NAMESPACE'         => 'namespace '.$app['config']['project_name'].'\Transformers;',
            'CLASS'             => $tabela->getNomeCamelCaseSingular(),
            '_USE_ENTITIES'     => $stubUseEntities,
            '_DEFAULT_INCLUDES' => $stubDefaultIncludes,
            '_RETURN_TRANSFORM' => $stubReturnTransformer,
            '_FUNCTION_INCLUDES' => $stubFunctionIncludes
        ];
        $stub = preencherStub($stub_path, 'transformer', $replaces);

        $arquivo = $destination_path.$tabela->getNomeCamelCaseSingular().'Transformer.php';
        criarArquivo($stub, $arquivo);
        $arquivosCriados .= $arquivo.'<br>';
    }

    return new Response($arquivosCriados, 200);

});

$app->get('/validator', function() use ($app) {

    $stub_path = $app['config']['stub_path'].'Validators/';
    $destination_path = $app['config']['destination_path'].'Validators/';
    $arquivosCriados = '';

    $tabelas = listarObjTabelas($app);

    foreach ($tabelas as $tabela) {

        $stubRules = '';
        
        foreach ($tabela->getColunas() as $coluna) {

            if (($coluna->getChave() != "PRI") && ($coluna->getCampoMinusculo() != 'created_at') && ($coluna->getCampoMinusculo() != 'updated_at')) {

                $replaces = [
                    'NOME_COLUNA_MINUSCULO' => $coluna->getCampoMinusculo(),
                    'REGRA_VALIDATOR' => $coluna->getRegraValidator(),
                ];

                $stubRules .= preencherStub($stub_path, '_RULES', $replaces);
            }
        }

        $replaces = [
            'NAMESPACE' => 'namespace '.$app['config']['project_name'].'\Validators;',
            'CLASS'     => $tabela->getNomeCamelCaseSingular(),
            '_RULES'    => $stubRules,
        ];
        $stub = preencherStub($stub_path, 'validator', $replaces);

        $arquivo = $destination_path.$tabela->getNomeCamelCaseSingular().'Validator.php';
        criarArquivo($stub, $arquivo);
        $arquivosCriados .= $arquivo.'<br>';
    }

    return new Response($arquivosCriados, 200);

});

$app->run();
