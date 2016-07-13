<?php
/**
 * Created by PhpStorm.
 * User: alemonteiro
 * Date: 13/07/2016
 * Time: 10:10
 */

namespace MotaMonteiro\Gerador\Entities\Generators;


use Silex\Application;

class RepositoryEloquent
{

    /**
     * @var Application
     */
    private $app;

    private $stub_path;

    function __construct(Application $app)
    {
        $this->app = $app;
        $this->stub_path = $this->app['config']['stub_path'].'app/Repositories/Eloquent/';
    }

    public function gerarArquivo($destination_path)
    {
        $arquivosCriados = '';

        $tabelas = listarObjTabelas($this->app);

        foreach ($tabelas as $tabela) {

            $replaces = [
                'NAMESPACE' => 'namespace ' . $this->app['config']['project_name'] . '\Repositories\Eloquent;',
                'CLASS' => $tabela->getNomeCamelCaseSingular(),
                'PROJETO' => $this->app['config']['project_name'],
                'COLUNAS' => '\''.$tabela->getChavePrimariaMinusculo().'\', '.$tabela->getColunasCamposSemPkPorVirgulaMinusculo()
            ];

            $stub = preencherStub($this->stub_path, 'repositoryV1', $replaces);

            $arquivo = $destination_path . $tabela->getNomeCamelCaseSingular().'Repository.php';
            criarArquivo($stub, $arquivo);
            $arquivosCriados .= $arquivo.'<br>';

        }

        $replaces = [
            'NAMESPACE' => 'namespace '.$this->app['config']['project_name'].'\Repositories\Eloquent;',
        ];

        $stub = preencherStub($this->stub_path, 'baseRepositoryV1', $replaces);

        $arquivo = $destination_path.'BaseRepository.php';
        criarArquivo($stub, $arquivo);
        $arquivosCriados .= $arquivo.'<br>';

        return $arquivosCriados;
    }


}