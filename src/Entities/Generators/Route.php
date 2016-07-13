<?php
/**
 * Created by PhpStorm.
 * User: alemonteiro
 * Date: 13/07/2016
 * Time: 10:10
 */

namespace MotaMonteiro\Gerador\Entities\Generators;


use Silex\Application;

class Route
{

    /**
     * @var Application
     */
    private $app;

    private $stub_path;

    function __construct(Application $app)
    {
        $this->app = $app;
        $this->stub_path = $this->app['config']['stub_path'].'app/Http/';
    }

    public function gerarArquivo($destination_path)
    {
        $arquivosCriados = '';

        $tabelas = listarObjTabelas($this->app);

        $stubRotas = '';
        foreach ($tabelas as $tabela) {

            $replaces = [
                'NOME_CAMEL_CASE_LC_FIRST' => $tabela->getNomeCamelCaseLcFirstSingular(),
                'NOME_CAMEL_CASE' => $tabela->getNomeCamelCaseSingular(),
            ];
            $stubRotas .= preencherStub($this->stub_path, ($this->app['config']['framework']=='laravel')?'_ROTAS_LARAVEL':'_ROTAS_LUMEN', $replaces);
        }

        $replaces = [
            '_ROTAS' => $stubRotas,
        ];
        $stub = preencherStub($this->stub_path, 'route', $replaces);

        $arquivo = $destination_path.'routes.php';
        criarArquivo($stub, $arquivo);
        $arquivosCriados .= $arquivo.'<br>';

        return $arquivosCriados;
    }


}