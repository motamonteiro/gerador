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

    private $destination_path;

    function __construct(Application $app)
    {
        $this->app = $app;
        $this->stub_path = $this->app['config']['stub_path'].$this->app['config']['array_destination_folder']['Presenter'];
        $this->destination_path = $this->app['config']['destination_path'].$this->app['config']['array_destination_folder']['Presenter'];
    }

    public function gerarArquivo()
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

            $arquivo = $this->destination_path.$tabela->getNomeCamelCaseSingular().'Presenter.php';
            criarArquivo($stub, $arquivo);
            $arquivosCriados .= $arquivo.'<br>';

        }

        $replaces = [
            'NAMESPACE' => 'namespace '.$this->app['config']['project_name'].'\Presenters;',
        ];

        $stub = preencherStub($this->stub_path, 'basePresenter', $replaces);

        $arquivo = $this->destination_path.'BasePresenter.php';
        criarArquivo($stub, $arquivo);
        $arquivosCriados .= $arquivo.'<br>';

        return $arquivosCriados;
    }


}