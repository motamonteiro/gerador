<?php
/**
 * Created by PhpStorm.
 * User: alemonteiro
 * Date: 13/07/2016
 * Time: 10:10
 */

namespace MotaMonteiro\Gerador\Entities\Generators;


use Silex\Application;

class Resource
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
        $this->stub_path = $this->app['config']['stub_path'].$this->app['config']['array_destination_folder']['Resource'];
        $this->destination_path = $this->app['config']['destination_path'].$this->app['config']['array_destination_folder']['Resource'];
    }

    public function gerarArquivo()
    {
        $arquivosCriados = '';

        $tabelas = listarObjTabelas($this->app);

        foreach ($tabelas as $tabela) {

            $stubDefaultIncludes = '';
            $stubReturnResource = '';
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

                if(substr($coluna->getCampoMinusculo(),0,4) == "dat_") {
                    $replaces = [
                        'NOME_COLUNA_MINUSCULO_MODEL' => '($this->'.$coluna->getCampoMinusculo().') ? $helper->dataFormatoDestino($this->' . $coluna->getCampoMinusculo() . ', $helper->FORMATO_DATA_HORA_BR) : '."''",
                        'NOME_COLUNA_MINUSCULO' => $coluna->getCampoCamelCaseLcFirst(),
                    ];
                    $stubReturnResource .= preencherStub($this->stub_path, '_RETURN_RESOURCE', $replaces);
                } elseif(substr($coluna->getCampoMinusculo(),0,4) == "vlr_"){
                    $replaces = [
                        'NOME_COLUNA_MINUSCULO_MODEL' => '($this->'.$coluna->getCampoMinusculo().') ? $helper->numeroFormatoSqlParaMoedaBr($this->' . $coluna->getCampoMinusculo() . ') : '."''",
                        'NOME_COLUNA_MINUSCULO' => $coluna->getCampoCamelCaseLcFirst(),
                    ];
                    $stubReturnResource .= preencherStub($this->stub_path, '_RETURN_RESOURCE', $replaces);
                } elseif(substr($coluna->getCampoCamelCaseLcFirst(),0,6) == "codCpf"){
                    $replaces = [
                        'NOME_COLUNA_MINUSCULO_MODEL' => '($this->'.$coluna->getCampoMinusculo().') ? $helper->formatarCpf($this->' . $coluna->getCampoMinusculo() . ') : '."''",
                        'NOME_COLUNA_MINUSCULO' => $coluna->getCampoCamelCaseLcFirst(),
                    ];
                    $stubReturnResource .= preencherStub($this->stub_path, '_RETURN_RESOURCE', $replaces);
                } elseif(substr($coluna->getCampoCamelCaseLcFirst(),0,7) == "codCnpj"){
                    $replaces = [
                        'NOME_COLUNA_MINUSCULO_MODEL' => '($this->'.$coluna->getCampoMinusculo().') ? $helper->formatarCnpj($this->' . $coluna->getCampoMinusculo() . ') : '."''",
                        'NOME_COLUNA_MINUSCULO' => $coluna->getCampoCamelCaseLcFirst(),
                    ];
                    $stubReturnResource .= preencherStub($this->stub_path, '_RETURN_RESOURCE', $replaces);
                } elseif(substr($coluna->getCampoCamelCaseLcFirst(),0,10) == "codCpfCnpj"){
                    $replaces = [
                        'NOME_COLUNA_MINUSCULO_MODEL' => '($this->'.$coluna->getCampoMinusculo().') ? $helper->formatarIeCpfCnpj($this->' . $coluna->getCampoMinusculo() . ') : '."''",
                        'NOME_COLUNA_MINUSCULO' => $coluna->getCampoCamelCaseLcFirst(),
                    ];
                    $stubReturnResource .= preencherStub($this->stub_path, '_RETURN_RESOURCE', $replaces);
                } else {
                    $replaces = [
                        'NOME_COLUNA_MINUSCULO_MODEL' => '$this->'.$coluna->getCampoMinusculo()." ?? ''",
                        'NOME_COLUNA_MINUSCULO' => $coluna->getCampoCamelCaseLcFirst(),
                    ];
                    $stubReturnResource .= preencherStub($this->stub_path, '_RETURN_RESOURCE', $replaces);
                }

            }


            $replaces = [
                'NAMESPACE'         => 'namespace '.$this->app['config']['project_name'].'\Http\Resources;',
                'PROJETO'           => $this->app['config']['project_name'],
                'CLASS'             => $tabela->getNomeCamelCaseSingular(),
                '_USE_ENTITIES'     => $stubUseEntities,
                '_DEFAULT_INCLUDES' => $stubDefaultIncludes,
                '_RETURN_RESOURCE' => $stubReturnResource,
                '_FUNCTION_INCLUDES' => $stubFunctionIncludes,
            ];
            $stub = preencherStub($this->stub_path, 'resource', $replaces);

            $arquivo = $this->destination_path.$tabela->getNomeCamelCaseSingular().'Resource.php';
            criarArquivo($stub, $arquivo);
            $arquivosCriados .= $arquivo.'<br>';
        }

        return $arquivosCriados;
    }


}