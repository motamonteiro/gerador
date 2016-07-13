<?php
/**
 * Created by PhpStorm.
 * User: alemonteiro
 * Date: 13/07/2016
 * Time: 10:10
 */

namespace MotaMonteiro\Gerador\Entities\Generators;


use Silex\Application;

class Provider
{

    /**
     * @var Application
     */
    private $app;

    private $stub_path;

    function __construct(Application $app)
    {
        $this->app = $app;
        $this->stub_path = $this->app['config']['stub_path'].'app/Providers/';
    }

    public function gerarArquivo($destination_path)
    {
        $arquivosCriados = '';

        $tabelas = listarObjTabelas($this->app);

        $stubBinds = '';
        foreach ($tabelas as $tabela) {

            $replaces = [
                'PATH_INTERFACE_REPOSITORY_CLASS' => '\\'.$this->app['config']['project_name'].'\Repositories\\Interfaces\\'.$tabela->getNomeCamelCaseSingular().'Interface::class',
                'PATH_REPOSITORY_CLASS'           => '\\'.$this->app['config']['project_name'].'\Repositories\\Eloquent\\'.$tabela->getNomeCamelCaseSingular().'Repository::class',
            ];

            $stubBinds .= preencherStub($this->stub_path, '_BINDS', $replaces);
        }

        $replaces = [
            'NAMESPACE' => 'namespace '.$this->app['config']['project_name'].'\Providers;',
            'CLASS'     => $this->app['config']['project_name'],
            'BINDS'     => $stubBinds
        ];

        $stub = preencherStub($this->stub_path, 'provider', $replaces);

        $arquivo = $destination_path.$this->app['config']['project_name'].'RepositoryProvider.php';
        criarArquivo($stub, $arquivo);
        $arquivosCriados .= $arquivo.'<br>';

        return $arquivosCriados;
    }


}