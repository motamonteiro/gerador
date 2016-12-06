<?php
set_time_limit(0);
require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Response;

$dotenv = new Dotenv\Dotenv(__DIR__.'/..');
$dotenv->load();

$app = new Silex\Application();

$app['config'] = [
    'framework' => $_ENV['PROJECT_FRAMEWORK'],
    'db_connection' => $_ENV['DB_CONNECTION'],
    'db_host' => $_ENV['DB_HOST'],
    'db_name' => $_ENV['DB_NAME'],
    'db_username' => $_ENV['DB_USERNAME'],
    'db_password' => $_ENV['DB_PASSWORD'],
    'table_prefix' => $_ENV['DB_TABLE_PREFIX'],
    'project_name' => $_ENV['PROJECT_NAME'],
    'destination_path' =>  ($_ENV['PROJECT_DIR'] != '') ? $_ENV['PROJECT_DIR'] : __DIR__.'/arquivos/',
    'stub_path' =>  '../src/Stubs/',
    'array_destination_folder' => [
        'EntityEloquent'      => 'app/Entities/Eloquent/',
        'EntityOrmSefaz'      => 'app/Entities/OrmSefaz/',
        'Presenter'           => 'app/Presenters/',
        'RepositoryInterface' => 'app/Repositories/Interfaces/',
        'Provider'            => 'app/Providers/',
        'RepositoryEloquent'  => 'app/Repositories/Eloquent/',
        'Transformer'         => 'app/Transformers/',
        'Validator'           => 'app/Validators/',
        'Service'             => 'app/Services/',
        'Controller'          => 'app/Http/Controllers/',
        'Route'               => 'app/Http/',
        'LangPtBrValidation'  => 'resources/lang/pt-br/',
        'Migration'           => 'database/migrations/',        
    ]

];

$app['debug'] = true;

$app['db'] = function() use ($app) {
    return new \PDO('mysql:host='.$app['config']['db_host'].';dbname='.$app['config']['db_name'].'',''.$app['config']['db_username'].'',''.$app['config']['db_password'].'');
};

//rota principal
$app->get('/', function() use ($app) {
    return new Response(file_get_contents('../resources/views/index.html'), 200);
});

$app->get('/tabelas', function() use ($app) {

    print_r(listarObjTabelas($app));
    exit;

});

$app->get('/configuracoes', function() use ($app) {

    echo listarConfiguracoesDotEnv($app);
    exit;

});

$app->get('/limparArquivos', function() use ($app) {

    return new Response(limparDiretorios( $app['config']['destination_path'] ), 200);
});
$app->get('/criarDiretorios', function() use ($app) {

    return new Response(criarDiretorios($app), 200);
});

$app->get('/entityEloquent', function() use ($app) {    
    
    $generator = new \MotaMonteiro\Gerador\Entities\Generators\EntityEloquent($app);
    return new Response($generator->gerarArquivo(), 200);
    
});

$app->get('/entityOrmSefaz', function() use ($app) {

    $generator = new \MotaMonteiro\Gerador\Entities\Generators\EntityOrmSefaz($app);
    return new Response($generator->gerarArquivo(), 200);
    
});

$app->get('/presenter', function() use ($app) {

    $generator = new \MotaMonteiro\Gerador\Entities\Generators\Presenter($app);
    return new Response($generator->gerarArquivo(), 200);

});

$app->get('/repositoryInterface', function() use ($app) {

    $generator = new \MotaMonteiro\Gerador\Entities\Generators\RepositoryInterface($app);
    return new Response($generator->gerarArquivo(), 200);

});

$app->get('/provider', function() use ($app) {

    $generator = new \MotaMonteiro\Gerador\Entities\Generators\Provider($app);
    return new Response($generator->gerarArquivo(), 200);

});

$app->get('/repositoryEloquent', function() use ($app) {

    $generator = new \MotaMonteiro\Gerador\Entities\Generators\RepositoryEloquent($app);
    return new Response($generator->gerarArquivo(), 200);

});

$app->get('/transformer', function() use ($app) {

    $generator = new \MotaMonteiro\Gerador\Entities\Generators\Transformer($app);
    return new Response($generator->gerarArquivo(), 200);

});

$app->get('/validator', function() use ($app) {

    $generator = new \MotaMonteiro\Gerador\Entities\Generators\Validator($app);
    return new Response($generator->gerarArquivo(), 200);

});

$app->get('/service', function() use ($app) {

    $generator = new \MotaMonteiro\Gerador\Entities\Generators\Service($app);
    return new Response($generator->gerarArquivo(), 200);

});

$app->get('/controller', function() use ($app) {

    $generator = new \MotaMonteiro\Gerador\Entities\Generators\Controller($app);
    return new Response($generator->gerarArquivo(), 200);

});

$app->get('/route', function() use ($app) {

    $generator = new \MotaMonteiro\Gerador\Entities\Generators\Route($app);
    return new Response($generator->gerarArquivo(), 200);

});

$app->get('/langPt-brValidation', function() use ($app) {

    $generator = new \MotaMonteiro\Gerador\Entities\Generators\LangPtBrValidation($app);
    return new Response($generator->gerarArquivo(), 200);
});

$app->get('/database/migrations', function() use ($app) {

    $generator = new \MotaMonteiro\Gerador\Entities\Generators\Migration($app);
    return new Response($generator->gerarArquivo(), 200);

});

$app->get('/generateAll', function() use ($app) {
    $arquivoGerado = '';
    foreach ($app['config']['array_destination_folder'] as $gerador => $item) {

        if(($gerador <> 'EntityOrmSefaz') && ($gerador <> 'LangPtBrValidation') && ($gerador <> 'Migration')){

            $nomeClasse = "\\MotaMonteiro\\Gerador\\Entities\\Generators\\".str_replace("/","\\",$gerador);
            $generator = new $nomeClasse($app);
            $arquivoGerado .= $generator->gerarArquivo();
        }
    }

    return new Response($arquivoGerado, 200);

});

$app->run();
