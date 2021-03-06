<?php
/**
 * Created by PhpStorm.
 * User: alemonteiro
 * Date: 13/07/2016
 * Time: 10:10
 */

namespace MotaMonteiro\Gerador\Entities\Generators;


use Silex\Application;

class Service
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
        $this->stub_path = $this->app['config']['stub_path'].$this->app['config']['array_destination_folder']['Service'];
        $this->destination_path = $this->app['config']['destination_path'].$this->app['config']['array_destination_folder']['Service'];
    }

    public function gerarArquivo()
    {
        $arquivosCriados = '';

        $tabelas = listarObjTabelas($this->app);

        foreach ($tabelas as $tabela) {

            $replaces = [
                'NAMESPACE' => 'namespace '.$this->app['config']['project_name'].'\Services;',
                'PROJETO' => $this->app['config']['project_name'],
                'CLASS'     => $tabela->getNomeCamelCaseSingular(),
            ];
            $stub = preencherStub($this->stub_path, 'service', $replaces);

            $arquivo = $this->destination_path.$tabela->getNomeCamelCaseSingular().'Service.php';
            criarArquivo($stub, $arquivo);
            $arquivosCriados .= $arquivo.'<br>';
        }

        $replaces = [
            'PROJETO' => $this->app['config']['project_name'],
            'NAMESPACE' => 'namespace '.$this->app['config']['project_name'].'\Services;',
        ];
        $stub = preencherStub($this->stub_path, 'baseService', $replaces);

        $arquivo = $this->destination_path.'BaseService.php';
        criarArquivo($stub, $arquivo);
        $arquivosCriados .= $arquivo.'<br>';

        return $arquivosCriados;
    }


}