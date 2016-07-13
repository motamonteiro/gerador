<?php
/**
 * Created by PhpStorm.
 * User: alemonteiro
 * Date: 13/07/2016
 * Time: 10:10
 */

namespace MotaMonteiro\Gerador\Entities\Generators;


use Silex\Application;

class EntityOrmSefaz
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
        $this->stub_path = $this->app['config']['stub_path'].$this->app['config']['array_destination_folder']['EntityOrmSefaz'];
        $this->destination_path = $this->app['config']['destination_path'].$this->app['config']['array_destination_folder']['EntityOrmSefaz'];
    }

    public function gerarArquivo()
    {
        $tabelas = listarObjTabelas($this->app);

        $arquivosCriados = '';
        foreach ($tabelas as $tabela) {
            $stubPrivateNomeColuna = '';
            $stubConstrutorNomeColuna = '';
            $stubGetSet = '';
            $stubConstrutorColunaEstrangeiraDetalhada = '';
            $stubConstrutorColunaEstrangeira = '';
            foreach ($tabela->getColunas() as $coluna) {

                $replaces = [
                    'NOME_COLUNA_CAMEL_CASE_LC_FIRST' => $coluna->getCampoCamelCaseLcFirst()
                ];

                $stubPrivateNomeColuna .= preencherStub($this->stub_path, 'PRIVATE_NOME_COLUNAS', $replaces);

                $replaces = [
                    'NOME_COLUNA_CAMEL_CASE_LC_FIRST' => $coluna->getCampoCamelCaseLcFirst(),
                    'NOME_COLUNA_MAIUSCULO' => $coluna->getCampoMaiusculo()
                ];

                $stubConstrutorNomeColuna .= preencherStub($this->stub_path, 'CONSTRUTOR_NOME_COLUNA', $replaces);

                $replaces = [
                    'NOME_COLUNA_CAMEL_CASE_LC_FIRST' => $coluna->getCampoCamelCaseLcFirst(),
                    'NOME_COLUNA_CAMEL_CASE' => $coluna->getCampoCamelCase()
                ];

                $stubGetSet .= preencherStub($this->stub_path, 'GET_SET', $replaces);

                if($coluna->getChave() == "MUL"){
                    $tabelasEstrangeiras = $tabela->getTabelasEstrangeiras();
                    foreach ($tabelasEstrangeiras as $tabelaEstrangeira) {
                        $tabEstColunas = $tabelaEstrangeira->getColunas();
                        foreach ($tabEstColunas as $tabEstColuna) {
                            if($tabEstColuna->getChave() == "PRI" && $tabEstColuna->getCampo() == $coluna->getCampoChaveEstrangeira()){
                                $nmeTabelaEstrangeira = $tabelaEstrangeira->getNomeCamelCaseSingular();
                            }
                        }
                    }

                    $replaces = [
                        'NOME_TABELA_ESTRANGEIRA' => $nmeTabelaEstrangeira,
                        'COLUNA_TABELA_ESTRANGEIRA_CAMEL_CASE' => $coluna->getCampoChaveEstrangeiraCamelCase(),
                        'NOME_COLUNA_MAIUSCULO' => $coluna->getCampoMaiusculo(),
                        'NOME_CLASSE_ESTRANGEIRA' => lcfirst(substr($coluna->getCampoCamelCaseLcFirst(),2))
                    ];

                    $stubConstrutorColunaEstrangeiraDetalhada .= preencherStub($this->stub_path, 'CONSTRUTOR_COLUNA_ESTRANGEIRA_DETALHADA', $replaces);

                    $replaces = [
                        'NOME_TABELA_ESTRANGEIRA' => $nmeTabelaEstrangeira,
                        'COLUNA_TABELA_ESTRANGEIRA_CAMEL_CASE' => $coluna->getCampoChaveEstrangeiraCamelCase(),
                        'NOME_COLUNA_MAIUSCULO' => $coluna->getCampoMaiusculo(),
                        'NOME_CLASSE_ESTRANGEIRA' => lcfirst(substr($coluna->getCampoCamelCaseLcFirst(),2))
                    ];

                    $stubConstrutorColunaEstrangeira .= preencherStub($this->stub_path, 'CONSTRUTOR_COLUNA_ESTRANGEIRA', $replaces);

                }

            }

            $replaces = [
                'NAMESPACE'            => 'namespace '.$this->app['config']['project_name'].'\Entities\OrmSefaz;',
                'CLASS'                => $tabela->getNomeCamelCaseSingular(),
                'PRIVATE_NOME_COLUNAS' => $stubPrivateNomeColuna,
                'CONSTRUTOR_NOME_COLUNA' => $stubConstrutorNomeColuna,
                'GET_SET'              => $stubGetSet,
                'CONSTRUTOR_COLUNA_ESTRANGEIRA_DETALHADA' => $stubConstrutorColunaEstrangeiraDetalhada,
                'CONSTRUTOR_COLUNA_ESTRANGEIRA' => $stubConstrutorColunaEstrangeira
            ];

            $stub = preencherStub($this->stub_path, 'entity', $replaces);

            $arquivo = $this->destination_path.$tabela->getNomeCamelCaseSingular().'.php';
            criarArquivo($stub, $arquivo);
            $arquivosCriados .= $arquivo.'<br>';

        }

        return $arquivosCriados;
    }


}