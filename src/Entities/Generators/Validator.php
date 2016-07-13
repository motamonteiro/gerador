<?php
/**
 * Created by PhpStorm.
 * User: alemonteiro
 * Date: 13/07/2016
 * Time: 10:10
 */

namespace MotaMonteiro\Gerador\Entities\Generators;


use Silex\Application;

class Validator
{

    /**
     * @var Application
     */
    private $app;

    private $stub_path;

    function __construct(Application $app)
    {
        $this->app = $app;
        $this->stub_path = $this->app['config']['stub_path'].'app/Validators/';
    }

    public function gerarArquivo($destination_path)
    {
        $arquivosCriados = '';

        $tabelas = listarObjTabelas($this->app);

        foreach ($tabelas as $tabela) {

            $stubRules = '';

            foreach ($tabela->getColunas() as $coluna) {

                if (($coluna->getChave() != "PRI") && ($coluna->getCampoMinusculo() != 'created_at') && ($coluna->getCampoMinusculo() != 'updated_at')) {

                    $replaces = [
                        'NOME_COLUNA_MINUSCULO' => $coluna->getCampoMinusculo(),
                        'REGRA_VALIDATOR' => $coluna->getRegraValidator(),
                    ];

                    $stubRules .= preencherStub($this->stub_path, '_RULES', $replaces);
                }
            }

            $replaces = [
                'NAMESPACE' => 'namespace '.$this->app['config']['project_name'].'\Validators;',
                'CLASS'     => $tabela->getNomeCamelCaseSingular(),
                '_RULES'    => $stubRules,
            ];
            $stub = preencherStub($this->stub_path, 'validator', $replaces);

            $arquivo = $destination_path.$tabela->getNomeCamelCaseSingular().'Validator.php';
            criarArquivo($stub, $arquivo);
            $arquivosCriados .= $arquivo.'<br>';
        }

        return $arquivosCriados;
    }


}