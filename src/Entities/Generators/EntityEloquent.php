<?php
/**
 * Created by PhpStorm.
 * User: alemonteiro
 * Date: 13/07/2016
 * Time: 10:10
 */

namespace MotaMonteiro\Gerador\Entities\Generators;


use Silex\Application;

class EntityEloquent
{

    /**
     * @var Application
     */
    private $app;

    private $stub_path;

    function __construct(Application $app)
    {
        $this->app = $app;
        $this->stub_path = $this->app['config']['stub_path'].'app/Entities/Eloquent/';
    }

    public function gerarArquivo($destination_path)
    {
        $arquivosCriados = '';
        $tabelas = listarObjTabelas($this->app);

        foreach ($tabelas as $tabela) {

            $stubFuncoesBelongsTo = '';
            $flgTimeStamps = false;
            foreach ($tabela->getColunas() as $coluna) {

                if ($coluna->getCampoMinusculo() == 'created_at') {
                    $flgTimeStamps = true;
                }

                if ($coluna->getChave() == "MUL") {

                    $tabelasEstrangeiras = $tabela->getTabelasEstrangeiras();
                    foreach ($tabelasEstrangeiras as $tabelaEstrangeira) {
                        if (($tabelaEstrangeira->getNome() == $coluna->getCampoTabelaEstrangeira())) {
                            $nmeTabelaEstrangeira = $tabelaEstrangeira->getNomeCamelCaseSingular();
                            break;
                        }
                    }

                    $replaces = [
                        'NOME_CLASSE_ESTRANGEIRA_CAMEL_CASE_LC_FIRST' => $coluna->getNomeClasseCamelCaseLcFirst($coluna->getCampo()),
                        'NOME_TABELA_FK_CAMEL_CASE' => $nmeTabelaEstrangeira,
                        'NOME_COLUNA_FK' => $coluna->getCampoChaveEstrangeiraMinusculo(),
                        'NOME_COLUNA' => $coluna->getCampoMinusculo(),
                    ];

                    $stubFuncoesBelongsTo .= preencherStub($this->stub_path, '_FUNCOES_BELONGS_TO', $replaces);
                }
            }

            $replaces = [
                'NAMESPACE' => 'namespace ' . $this->app['config']['project_name'] . '\Entities\Eloquent;',
                'CLASS' => $tabela->getNomeCamelCaseSingular(),
                'PUBLIC_CONNECTION' => ($this->app['config']['db_connection'] == 'oracle') ? 'protected $connection = \'' . $this->app['config']['db_connection'] . '\';' : '',
                'PUBLIC_SEQUENCE' => ($this->app['config']['db_connection'] == 'oracle') ? 'public $sequence = \'' . $tabela->getNomeCompletoMinusculo() . '_seq\';' : '',
                'PUBLIC_TIMESTAMPS' => (!$flgTimeStamps) ? 'public $timestamps = false;' : '',
                'NOME_COMPLETO_TABELA' => $tabela->getNomeCompletoMinusculo(),
                'NOME_COLUNA_PK' => $tabela->getChavePrimariaMinusculo(),
                'COLUNAS_SEM_PK' => $tabela->getColunasCamposSemPkPorVirgulaMinusculo(),
                'FUNCOES_BELONGS_TO' => $stubFuncoesBelongsTo
            ];

            $stub = preencherStub($this->stub_path, 'entity', $replaces);

            $arquivo = $destination_path . $tabela->getNomeCamelCaseSingular() . '.php';
            criarArquivo($stub, $arquivo);
            $arquivosCriados .= $arquivo . '<br>';
        }

        return $arquivosCriados;
    }
}