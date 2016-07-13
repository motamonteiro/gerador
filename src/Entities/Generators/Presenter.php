<?php
/**
 * Created by PhpStorm.
 * User: alemonteiro
 * Date: 13/07/2016
 * Time: 10:10
 */

namespace MotaMonteiro\Gerador\Entities\Generators;


use Silex\Application;

class Presenter
{

    /**
     * @var Application
     */
    private $app;

    private $stub_path;

    function __construct(Application $app)
    {
        $this->app = $app;
        $this->stub_path = $this->app['config']['stub_path'].'app/Presenters/';
    }

    public function gerarArquivo($destination_path)
    {
        $arquivosCriados = '';

        $tabelas = listarObjTabelas($this->app);

        foreach ($tabelas as $tabela) {

            $replaces = [
                'NAMESPACE'            => 'namespace '.$this->app['config']['project_name'].'\Presenters;',
                'CLASS'                => $tabela->getNomeCamelCaseSingular(),
                'PROJETO'              => $this->app['config']['project_name'],
            ];

            $stub = preencherStub($this->stub_path, 'presenter', $replaces);

            $arquivo = $destination_path.$tabela->getNomeCamelCaseSingular().'Presenter.php';
            criarArquivo($stub, $arquivo);
            $arquivosCriados .= $arquivo.'<br>';

        }

        return $arquivosCriados;
    }


}