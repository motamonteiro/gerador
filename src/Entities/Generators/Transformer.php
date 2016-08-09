<?php
/**
 * Created by PhpStorm.
 * User: alemonteiro
 * Date: 13/07/2016
 * Time: 10:10
 */

namespace MotaMonteiro\Gerador\Entities\Generators;


use Silex\Application;

class Transformer
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
        $this->stub_path = $this->app['config']['stub_path'].$this->app['config']['array_destination_folder']['Transformer'];
        $this->destination_path = $this->app['config']['destination_path'].$this->app['config']['array_destination_folder']['Transformer'];
    }

    public function gerarArquivo()
    {
        $arquivosCriados = '';

        $tabelas = listarObjTabelas($this->app);

        foreach ($tabelas as $tabela) {

            $stubDefaultIncludes = '';
            $stubReturnTransformer = '';
            $stubFunctionIncludes = '';

            $replaces = [
                'TABELA_ESTRANGEIRA_SINGULAR_CAMEL_CASE' => $this->app['config']['project_name'].'\Entities\Eloquent\\'.$tabela->getNomeCamelCaseSingular(),
            ];
            $stubUseEntities = preencherStub($this->stub_path, '_USE_ENTITIES', $replaces);

            foreach ($tabela->getColunas() as $coluna) {

                if ($coluna->getChave() == "MUL") {

                    $tabelasEstrangeiras = $tabela->getTabelasEstrangeiras();
                    foreach ($tabelasEstrangeiras as $tabelaEstrangeira) {
                        if (($tabelaEstrangeira->getNome() == $coluna->getCampoTabelaEstrangeira())) {

                            $replaces = [
                                'TABELA_ESTRANGEIRA_SINGULAR_CAMEL_CASE' => $this->app['config']['project_name'].'\Entities\Eloquent\\'.$tabelaEstrangeira->getNomeCamelCaseSingular(),
                            ];
                            $stubUseEntities .= preencherStub($this->stub_path, '_USE_ENTITIES', $replaces);


                            $replaces = [
                                'TABELA_ESTRANGEIRA_SINGULAR_CAMEL_CASE_LC_FIRST' => $tabelaEstrangeira->getNomeCamelCaseLcFirstSingular(),
                            ];
                            $stubDefaultIncludes .= preencherStub($this->stub_path, '_DEFAULT_INCLUDES', $replaces);

                            $replaces = [
                                'CLASS' => $tabela->getNomeCamelCaseSingular(),
                                'TABELA_ESTRANGEIRA_SINGULAR_CAMEL_CASE_LC_FIRST' => $tabelaEstrangeira->getNomeCamelCaseLcFirstSingular(),
                                'TABELA_ESTRANGEIRA_SINGULAR_CAMEL_CASE' => $tabelaEstrangeira->getNomeCamelCaseSingular(),
                            ];
                            $stubFunctionIncludes .= preencherStub($this->stub_path, '_FUNCTION_INCLUDES', $replaces);

                            break;
                        }
                    }
                }

                $replaces = [
                    'NOME_COLUNA_MINUSCULO_MODEL' => '$model->'.$coluna->getCampoMinusculo(),
                    'NOME_COLUNA_MINUSCULO' => $coluna->getCampoMinusculo(),
                ];
                $stubReturnTransformer .= preencherStub($this->stub_path, '_RETURN_TRANSFORM', $replaces);

                if(substr($coluna->getCampoMinusculo(),0,4) == "dat_"){
                    $replaces = [
                        'NOME_COLUNA_MINUSCULO_MODEL' => "dateTimeBR(".'$model->'.$coluna->getCampoMinusculo().")",
                        'NOME_COLUNA_MINUSCULO' => $coluna->getCampoMinusculo()."_br",
                    ];
                    $stubReturnTransformer .= preencherStub($this->stub_path, '_RETURN_TRANSFORM', $replaces);
                }

            }


            $replaces = [
                'NAMESPACE'         => 'namespace '.$this->app['config']['project_name'].'\Transformers;',
                'CLASS'             => $tabela->getNomeCamelCaseSingular(),
                '_USE_ENTITIES'     => $stubUseEntities,
                '_DEFAULT_INCLUDES' => $stubDefaultIncludes,
                '_RETURN_TRANSFORM' => $stubReturnTransformer,
                '_FUNCTION_INCLUDES' => $stubFunctionIncludes
            ];
            $stub = preencherStub($this->stub_path, 'transformer', $replaces);

            $arquivo = $this->destination_path.$tabela->getNomeCamelCaseSingular().'Transformer.php';
            criarArquivo($stub, $arquivo);
            $arquivosCriados .= $arquivo.'<br>';
        }

        return $arquivosCriados;
    }


}