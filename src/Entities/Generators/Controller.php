<?php
/**
 * Created by PhpStorm.
 * User: alemonteiro
 * Date: 13/07/2016
 * Time: 10:10
 */

namespace MotaMonteiro\Gerador\Entities\Generators;


use Silex\Application;

class Controller
{

    /**
     * @var Application
     */
    private $app;

    private $stub_path;

    private $destination_path;

    function __construct(Application $app)
    {
        $this->app = $app;
        $this->stub_path = $this->app['config']['stub_path'].$this->app['config']['array_destination_folder']['Controller'];
        $this->destination_path = $this->app['config']['destination_path'].$this->app['config']['array_destination_folder']['Controller'];
    }

    public function gerarArquivo()
    {
        $arquivosCriados = '';

        $tabelas = listarObjTabelas($this->app);

        foreach ($tabelas as $tabela) {

            $replaces = [
                'NAMESPACE' => 'namespace '.$this->app['config']['project_name'].'\Http\Controllers;',
                'PROJETO' => $this->app['config']['project_name'],
                'CLASS'     => $tabela->getNomeCamelCaseSingular(),
                'SERVICE'     => $tabela->getNomeCamelCaseLcFirstSingular(),
            ];
            $stub = preencherStub($this->stub_path, 'controller', $replaces);

            $arquivo = $this->destination_path.$tabela->getNomeCamelCaseSingular().'Controller.php';
            criarArquivo($stub, $arquivo);
            $arquivosCriados .= $arquivo.'<br>';
        }

        return $arquivosCriados;
    }


}