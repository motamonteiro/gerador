<?php
/**
 * Created by PhpStorm.
 * User: alemonteiro
 * Date: 13/07/2016
 * Time: 10:10
 */

namespace MotaMonteiro\Gerador\Entities\Generators;


use Silex\Application;

class RepositoryInterface
{

    /**
     * @var Application
     */
    private $app;

    private $stub_path;

    function __construct(Application $app)
    {
        $this->app = $app;
        $this->stub_path = $this->app['config']['stub_path'].'app/Repositories/Interfaces/';
    }

    public function gerarArquivo($destination_path)
    {
        $arquivosCriados = '';

        $tabelas = listarObjTabelas($this->app);

        foreach ($tabelas as $tabela) {

            $replaces = [
                'NAMESPACE' => 'namespace '.$this->app['config']['project_name'].'\Repositories\Interfaces;',
                'CLASS'     => $tabela->getNomeCamelCaseSingular(),
            ];

            $stub = preencherStub($this->stub_path, 'interface', $replaces);

            $arquivo = $destination_path.$tabela->getNomeCamelCaseSingular().'Interface.php';
            criarArquivo($stub, $arquivo);
            $arquivosCriados .= $arquivo.'<br>';

        }

        //Cria o BaseInterface.php
        $replaces = [
            'NAMESPACE' => 'namespace '.$this->app['config']['project_name'].'\Repositories\Interfaces;',
        ];

        $stub = preencherStub($this->stub_path, 'baseInterfaceV1', $replaces);

        $arquivo = $destination_path.'BaseInterface.php';
        criarArquivo($stub, $arquivo);
        $arquivosCriados .= $arquivo.'<br>';

        return $arquivosCriados;
    }


}