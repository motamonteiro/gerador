<?php
/**
 * Created by PhpStorm.
 * User: alemonteiro
 * Date: 13/07/2016
 * Time: 10:10
 */

namespace MotaMonteiro\Gerador\Entities\Generators;


use Silex\Application;

class LangPtBrValidation
{

    /**
     * @var Application
     */
    private $app;

    private $stub_path;

    function __construct(Application $app)
    {
        $this->app = $app;
        $this->stub_path = $this->app['config']['stub_path'].'resources/lang/pt-br/';
    }

    public function gerarArquivo($destination_path)
    {
        $arquivosCriados = '';

        $tabelas = listarObjTabelas($this->app);
        $arrayNmeColunas = array();
        $stubAttributes = '';
        foreach ($tabelas as $tabela) {

            foreach ($tabela->getColunas() as $coluna) {

                $campoCamelCaseLcFirstSingular = $coluna->getCampoCamelCaseLcFirst();
                if(!in_array($campoCamelCaseLcFirstSingular,$arrayNmeColunas)){
                    array_push($arrayNmeColunas, $campoCamelCaseLcFirstSingular);

                    $nmeColuna = $coluna->getCampo();
                    $nmeColuna = str_replace('_', ' ', $nmeColuna);
                    $nmeColuna = ucwords(strtolower($nmeColuna));
                    $arrayNmeColuna = explode(" ",$nmeColuna);
                    switch ($arrayNmeColuna[0]){
                        case "Vlr":
                            $arrayNmeColuna[0] = "Valor";
                            break;
                        case "Dat":
                            $arrayNmeColuna[0] = "Data de";
                            break;
                        default:
                            array_shift($arrayNmeColuna);
                    }

                    $nmeColuna = implode(" ",$arrayNmeColuna);

                    $replaces = [
                        'NOME_COLUNA_CAMEL_CASE_LC_FIRST' => $coluna->getCampoCamelCaseLcFirst(),
                        'NOME_COLUNA_CAMEL_CASE_WITH_SPACES' => $nmeColuna,
                    ];

                    $stubAttributes .= preencherStub($this->stub_path, "ATTRIBUTES", $replaces);
                }
            }
        }

        $replaces = [
            'ATTRIBUTES' => $stubAttributes,
        ];
        $stub = preencherStub($this->stub_path, 'validation', $replaces);

        $arquivo = $destination_path.'validation.php';
        criarArquivo($stub, $arquivo);
        $arquivosCriados .= $arquivo.'<br>';

        return $arquivosCriados;
    }


}