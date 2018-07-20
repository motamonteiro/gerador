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

    private $destination_path;

    function __construct(Application $app)
    {
        $this->app = $app;
        $this->stub_path = $this->app['config']['stub_path'].$this->app['config']['array_destination_folder']['RepositoryEloquent'];
        $this->destination_path = $this->app['config']['destination_path'].$this->app['config']['array_destination_folder']['RepositoryEloquent'];
    }

    public function gerarArquivo()
    {
        $arquivosCriados = '';

        $tabelas = listarObjTabelas($this->app);

        foreach ($tabelas as $tabela) {

            $stubRules = '';
            foreach ($tabela->getColunas() as $coluna) {

                if (($coluna->getChave() != "PRI") && ($coluna->getCampoMinusculo() != 'created_at') && ($coluna->getCampoMinusculo() != 'updated_at')) {

                    $replaces = [
                        'NOME_COLUNA_MINUSCULO' => $coluna->getCampoMinusculo(),
                        'REGRA_VALIDATOR' => "'".$coluna->getRegraValidator()."'",
                    ];

                    if (substr($coluna->getCampoMinusculo(), 0,3) == 'vlr') {
                        $replaces = [
                            'NOME_COLUNA_MINUSCULO' => $coluna->getCampoMinusculo(),
                            'REGRA_VALIDATOR' => $coluna->getRegraValidator(),
                        ];
                    }



                    $stubRules .= preencherStub($this->stub_path, '_RULES', $replaces);
                }
            }

            $arrayNmeColunas = array();
            $stubAttributes = '';

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
                        'NOME_COLUNA_CAMEL_CASE_LC_FIRST' => $coluna->getCampoMinusculo(),
                        'NOME_COLUNA_CAMEL_CASE_WITH_SPACES' => $nmeColuna,
                    ];

                    $stubAttributes .= preencherStub($this->stub_path, "_ATTRIBUTES", $replaces);
                }
            }


            $replaces = [
                'NAMESPACE' => 'namespace ' . $this->app['config']['project_name'] . '\Repositories\Eloquent;',
                'CLASS' => $tabela->getNomeCamelCaseSingular(),
                'PROJETO' => $this->app['config']['project_name'],
                'COLUNAS' => '\''.$tabela->getChavePrimariaMinusculo().'\', '.$tabela->getColunasCamposSemPkPorVirgulaMinusculo(),
                '_RULES'    => $stubRules,
                '_ATTRIBUTES' => $stubAttributes,
            ];

            $stub = preencherStub($this->stub_path, 'repositoryV1', $replaces);

            $arquivo = $this->destination_path . $tabela->getNomeCamelCaseSingular().'Repository.php';
            criarArquivo($stub, $arquivo);
            $arquivosCriados .= $arquivo.'<br>';

        }

        $replaces = [
            'NAMESPACE' => 'namespace '.$this->app['config']['project_name'].'\Repositories\Eloquent;',
            'PROJETO' => $this->app['config']['project_name'],
        ];

        $stub = preencherStub($this->stub_path, 'baseRepositoryV1', $replaces);

        $arquivo = $this->destination_path.'BaseRepository.php';
        criarArquivo($stub, $arquivo);
        $arquivosCriados .= $arquivo.'<br>';

        return $arquivosCriados;
    }


}