<?php
/**
 * Created by PhpStorm.
 * User: alemonteiro
 * Date: 13/07/2016
 * Time: 10:10
 */

namespace MotaMonteiro\Gerador\Entities\Generators;


use Silex\Application;

class Migration
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
        $this->stub_path = $this->app['config']['stub_path'].$this->app['config']['array_destination_folder']['Migration'];
        $this->destination_path = $this->app['config']['destination_path'].$this->app['config']['array_destination_folder']['Migration'];
    }

    public function gerarArquivo()
    {
        $arquivosCriados = '';

        $tabelas = listarObjTabelas($this->app);
        $migrationFkStub = '';

        foreach ($tabelas as $tabela) {

            $stubColunas = '';
            $migrationFK = '';
            $flgTimeStamps = true;


            foreach ($tabela->getColunas() as $coluna) {

                $migrationColuna = '        $table';

                if(($coluna->getCampoMinusculo() == 'created_at') || ($coluna->getCampoMinusculo() == 'updated_at')) {
                    $flgTimeStamps = true;
                } else {


                    if($coluna->getChave() == 'PRI') {
                        $migrationColuna .= "->increments('".$coluna->getCampoMinusculo()."')";
                    } else {

                        if(substr($coluna->getTipo(),0,3) == 'int') {
                            $migrationColuna .= "->integer('".$coluna->getCampoMinusculo()."')";
                        } elseif (substr($coluna->getTipo(),0,4) == 'text'){
                            $migrationColuna .= "->text('".$coluna->getCampoMinusculo()."')";
                        } else {

                            if(substr($coluna->getTipo(),0,7) == 'varchar'){
                                $tipoMigration = 'string';
                                preg_match('#\((.*?)\)#', $coluna->getTipo(), $match); //Pega o que estiver entre parentesis
                                $tamanho = (isset($match[1])) ?  $match[1] : '';
                            } else {

                                $tipoMigration = explode('(',$coluna->getTipo())[0];
                                preg_match('#\((.*?)\)#', $coluna->getTipo(), $match); //Pega o que estiver entre parentesis
                                $tamanho = (isset($match[1])) ?  $match[1] : '';

                            }

                            if(isset($tamanho) && $tamanho !='') {
                                $migrationColuna .= "->".$tipoMigration."('".$coluna->getCampoMinusculo()."', ".$tamanho.")";
                                $tamanho = '';
                            } else {
                                $migrationColuna .= "->".$tipoMigration."('".$coluna->getCampoMinusculo()."')";
                            }

                        }

                        if((substr($coluna->getTipo(),-8) == 'unsigned') || ($coluna->getChave() == 'MUL')){
                            $migrationColuna .= "->unsigned()";
                        }

                        if($coluna->isNulo() == 1) {
                            $migrationColuna .= "->nullable()";
                        }

                        if($coluna->getChave() == 'MUL') {
                            if($tabela->getPrefixo() != ''){
                                $migrationFK .= "
            \$table->foreign('".$coluna->getCampoMinusculo()."')->references('".$coluna->getCampoChaveEstrangeiraMinusculo()."')->on('".$tabela->getPrefixoMinusculo().'_'.$coluna->getCampoTabelaEstrangeiraMinusculo()."');";
                            }else{
                                $migrationFK .= "
            \$table->foreign('".$coluna->getCampoMinusculo()."')->references('".$coluna->getCampoChaveEstrangeiraMinusculo()."')->on('".$coluna->getCampoTabelaEstrangeiraMinusculo()."');";
                            }

                            //$migrationColuna .= "\$table->foreign('".$coluna->getCampoMinusculo()."')->references('".$coluna->getCampoChaveEstrangeiraMinusculo()."')->on('".$coluna->getCampoTabelaEstrangeiraMinusculo()."');";
                        }
                    }


                    $migrationColuna .= ";";

                    $replaces = [
                        'MIGRATION_COLUNA' => $migrationColuna,
                    ];

                    $stubColunas .= preencherStub($this->stub_path, '_COLUNAS', $replaces);

                }

            }

            $replaces = [
                'CLASS'                => $tabela->getPrefixoCamelCase().$tabela->getNomeCamelCaseSingular(),
                'NOME_COMPLETO_TABELA' => $tabela->getNomeCompletoMinusculo(),
                '_COLUNAS'   => $stubColunas,
                'TIMESTAMPS' => ($flgTimeStamps) ? '$table->timestamps();' : '',
//            'MIGRATION_FK' => ($migrationFK!='') ? 'Schema::table(\''.$tabela->getNomeCompletoMinusculo().'\', function (Blueprint $table) {'.$migrationFK.'});' : '',
            ];
            $stub = preencherStub($this->stub_path, 'migration', $replaces);

            $arquivo = $this->destination_path.date('Y_i_d_hms').'_create_'.$tabela->getNomeCompletoMinusculo().'_table.php';
            criarArquivo($stub, $arquivo);
            $arquivosCriados .= $arquivo.'<br>';

            //Criar stub somente com as chaves estrangeiras
            $migrationFkStub .= ($migrationFK!='') ? 'Schema::table(\''.$tabela->getNomeCompletoMinusculo().'\', function (Blueprint $table) {'.$migrationFK.'
        });
        
        ' : '';

        }

        $replaces = [
            'MIGRATION_FK' => $migrationFkStub,
        ];
        $stub = preencherStub($this->stub_path, 'migration_fk', $replaces);
        $anoMais1 = date("Y") + 1;
        $arquivo = $this->destination_path.$anoMais1.'_'.date('i_d_hms').'_create_fk_all_table.php';
        criarArquivo($stub, $arquivo);
        $arquivosCriados .= $arquivo.'<br>';

        return $arquivosCriados;
    }


}