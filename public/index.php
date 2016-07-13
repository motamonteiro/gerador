<?php

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
    'stub_path' =>  '../src/Stubs/',
    'destination_path' =>  ($_ENV['PROJECT_DIR'] != '') ? $_ENV['PROJECT_DIR'] : __DIR__.'/arquivos/'

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

$app->get('/limparArquivos', function() use ($app) {

    return new Response(limparDiretorios( $app['config']['destination_path'] ), 200);
});

$app->get('/entityEloquent', function() use ($app) {
    
    $destination_path = $app['config']['destination_path'].'app/Entities/Eloquent/';
    $generator = new \MotaMonteiro\Gerador\Entities\Generators\EntityEloquent($app);
    return new Response($generator->gerarArquivo($destination_path), 200);
    
});

$app->get('/entityOrmSefaz', function() use ($app) {
    
    $destination_path = $app['config']['destination_path'].'app/Entities/OrmSefaz/';
    $generator = new \MotaMonteiro\Gerador\Entities\Generators\EntityOrmSefaz($app);
    return new Response($generator->gerarArquivo($destination_path), 200);
    
});

$app->get('/presenter', function() use ($app) {

    $destination_path = $app['config']['destination_path'].'app/Presenters/';
    $generator = new \MotaMonteiro\Gerador\Entities\Generators\Presenter($app);
    return new Response($generator->gerarArquivo($destination_path), 200);

});

$app->get('/repositoryInterface', function() use ($app) {
    
    $destination_path = $app['config']['destination_path'].'app/Repositories/Interfaces/';
    $generator = new \MotaMonteiro\Gerador\Entities\Generators\RepositoryInterface($app);
    return new Response($generator->gerarArquivo($destination_path), 200);

});

$app->get('/provider', function() use ($app) {

    $destination_path = $app['config']['destination_path'].'app/Providers/';
    $generator = new \MotaMonteiro\Gerador\Entities\Generators\Provider($app);
    return new Response($generator->gerarArquivo($destination_path), 200);

});

$app->get('/repositoryEloquent', function() use ($app) {

    $destination_path = $app['config']['destination_path'].'app/Repositories/Eloquent/';
    $generator = new \MotaMonteiro\Gerador\Entities\Generators\RepositoryEloquent($app);
    return new Response($generator->gerarArquivo($destination_path), 200);

});

$app->get('/transformer', function() use ($app) {

    $destination_path = $app['config']['destination_path'].'app/Transformers/';
    $generator = new \MotaMonteiro\Gerador\Entities\Generators\Transformer($app);
    return new Response($generator->gerarArquivo($destination_path), 200);

});

$app->get('/validator', function() use ($app) {

    $destination_path = $app['config']['destination_path'].'app/Validators/';
    $generator = new \MotaMonteiro\Gerador\Entities\Generators\Validator($app);
    return new Response($generator->gerarArquivo($destination_path), 200);

});

$app->get('/service', function() use ($app) {

    $destination_path = $app['config']['destination_path'].'app/Services/';
    $generator = new \MotaMonteiro\Gerador\Entities\Generators\Service($app);
    return new Response($generator->gerarArquivo($destination_path), 200);

});

$app->get('/controller', function() use ($app) {

    $destination_path = $app['config']['destination_path'].'app/Http/Controllers/';
    $generator = new \MotaMonteiro\Gerador\Entities\Generators\Controller($app);
    return new Response($generator->gerarArquivo($destination_path), 200);

});

$app->get('/route', function() use ($app) {

    $destination_path = $app['config']['destination_path'].'app/Http/';
    $generator = new \MotaMonteiro\Gerador\Entities\Generators\Route($app);
    return new Response($generator->gerarArquivo($destination_path), 200);

});

$app->get('/langPt-brValidation', function() use ($app) {
    $destination_path = $app['config']['destination_path'].'resources/lang/pt-br/';
    $generator = new \MotaMonteiro\Gerador\Entities\Generators\LangPtBrValidation($app);
    return new Response($generator->gerarArquivo($destination_path), 200);
});

$app->get('/database/migrations', function() use ($app) {

    $destination_path = $app['config']['destination_path'].'database/migrations/';
    $generator = new \MotaMonteiro\Gerador\Entities\Generators\Migration($app);
    return new Response($generator->gerarArquivo($destination_path), 200);

});

$app->get('/generateAll', function() use ($app) {

    $destination_path = $app['config']['destination_path'].'app/Entities/Eloquent/';
    $generator = new \MotaMonteiro\Gerador\Entities\Generators\EntityEloquent($app);
    $arquivoGerado = $generator->gerarArquivo($destination_path);

    $destination_path = $app['config']['destination_path'].'app/Presenters/';
    $generator = new \MotaMonteiro\Gerador\Entities\Generators\Presenter($app);
    $arquivoGerado .= $generator->gerarArquivo($destination_path);

    $destination_path = $app['config']['destination_path'].'app/Repositories/Interfaces/';
    $generator = new \MotaMonteiro\Gerador\Entities\Generators\RepositoryInterface($app);
    $arquivoGerado .= $generator->gerarArquivo($destination_path);

    $destination_path = $app['config']['destination_path'].'app/Providers/';
    $generator = new \MotaMonteiro\Gerador\Entities\Generators\Provider($app);
    $arquivoGerado .= $generator->gerarArquivo($destination_path);

    $destination_path = $app['config']['destination_path'].'app/Repositories/Eloquent/';
    $generator = new \MotaMonteiro\Gerador\Entities\Generators\RepositoryEloquent($app);
    $arquivoGerado .= $generator->gerarArquivo($destination_path);

    $destination_path = $app['config']['destination_path'].'app/Transformers/';
    $generator = new \MotaMonteiro\Gerador\Entities\Generators\Transformer($app);
    $arquivoGerado .= $generator->gerarArquivo($destination_path);

    $destination_path = $app['config']['destination_path'].'app/Validators/';
    $generator = new \MotaMonteiro\Gerador\Entities\Generators\Validator($app);
    $arquivoGerado .= $generator->gerarArquivo($destination_path);

    $destination_path = $app['config']['destination_path'].'app/Services/';
    $generator = new \MotaMonteiro\Gerador\Entities\Generators\Service($app);
    $arquivoGerado .= $generator->gerarArquivo($destination_path);

    $destination_path = $app['config']['destination_path'].'app/Http/Controllers/';
    $generator = new \MotaMonteiro\Gerador\Entities\Generators\Controller($app);
    $arquivoGerado .= $generator->gerarArquivo($destination_path);

    $destination_path = $app['config']['destination_path'].'app/Http/';
    $generator = new \MotaMonteiro\Gerador\Entities\Generators\Route($app);
    $arquivoGerado .= $generator->gerarArquivo($destination_path);

//    $destination_path = $app['config']['destination_path'].'resources/lang/pt-br/';
//    $generator = new \MotaMonteiro\Gerador\Entities\Generators\LangPtBrValidation($app);
//    $arquivoGerado .= $generator->gerarArquivo($destination_path);
//
//    $destination_path = $app['config']['destination_path'].'database/migrations/';
//    $generator = new \MotaMonteiro\Gerador\Entities\Generators\Migration($app);
//    $arquivoGerado .= $generator->gerarArquivo($destination_path);

    return new Response($arquivoGerado, 200);

});

$app->run();
