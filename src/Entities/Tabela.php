<?php

/**
 * Created by PhpStorm.
 * User: alemonteiro
 * Date: 10/05/2016
 * Time: 16:41
 */

namespace MotaMonteiro\Gerador\Entities;


class Tabela
{
    protected $nomeCompleto;
    protected $prefixo;
    protected $colunas;
    protected $tabelasEstrangeiras;

    public function __construct($nomeCompleto = null, $prefixo = null)
    {
        $this->setNomeCompleto($nomeCompleto);
        $this->setPrefixo($prefixo);
        $this->colunas = [];
        $this->tabelasEstrangeiras = [];
    }


    /**
     * @return string
     */
    public function getNomeCompleto()
    {
        return $this->nomeCompleto;
    }

    /**
     * @param null $nomeCompleto
     * @return Tabela
     */
    public function setNomeCompleto($nomeCompleto)
    {
        $this->nomeCompleto = ($nomeCompleto) ? strtoupper($nomeCompleto) : '';
        return $this;
    }

    /**
     * @return string
     */
    public function getPrefixo()
    {
        return $this->prefixo;
    }

    /**
     * @param null $prefixo
     * @return Tabela
     */
    public function setPrefixo($prefixo)
    {
        $this->prefixo = ($prefixo)? strtoupper($prefixo) : '';
        return $this;
    }

    /**
     * @return array
     */
    public function getColunas()
    {
        return $this->colunas;
    }
    
    /**
     * @param Coluna $coluna
     * @return Tabela
     */
    public function addColuna($coluna)
    {
        $this->colunas[] = $coluna;
        return $this;
    }

    /**
     * @return array
     */
    public function getTabelasEstrangeiras()
    {
        return $this->tabelasEstrangeiras;
    }
    
    /**
     * @param Tabela $tabelaEstrangeira
     * @return Tabela
     */
    public function addTabelaEstrangeira($tabelaEstrangeira)
    {
        $this->tabelasEstrangeiras[] = $tabelaEstrangeira;
        return $this;
    }
    
    /**
     * Retira o prefixo que está no nomeCompleto da tabela
     * @return string
     */
    public function getNome()
    {
        if(!empty($this->prefixo)) {
            return str_replace($this->prefixo.'_', '', $this->nomeCompleto);
        }

        return $this->nomeCompleto;

    }

    /**
     * Retorna o nomeCompleto da tabela minusculo
     * @return string
     */
    public function getNomeCompletoMinusculo()
    {
        return strtolower($this->getNomeCompleto());
    }

    /**
     * Retorna o nomeCompleto da tabela maiusculo
     * @return string
     */
    public function getNomeCompletoMaiusculo()
    {
        return strtoupper($this->getNomeCompleto());
    }    

    /**
     * Retorna o nome da tabela minusculo
     * @return string
     */
    public function getNomeMinusculo()
    {
        return strtolower($this->getNome());        
    }

    /**
     * Retorna o nome da tabela maiusculo
     * @return string
     */
    public function getNomeMaiusculo()
    {
        return strtoupper($this->getNome());
    }

    /**
     * Retorna o nome da tabela como camel case
     * @return string
     */
    public function getNomeCamelCase()
    {
        $nome = str_replace('_', ' ', $this->getNome());
        $nome = ucwords(strtolower($nome));
        $nome = str_replace(' ', '', $nome);
        return $nome;
    }

    /**
     * Retorna o nome da tabela como camel case com a primeira letra minuscula
     * @return string
     */
    public function getNomeCamelCaseLcFirst()
    {
        return lcfirst($this->getNomeCamelCase());
    }

    /**
     * Retorna o campo que contem chave igual a PRI nas colunas da tabela
     * @return string
     */
    public function getChavePrimaria()
    {
        $colunas = $this->getColunas();        
        foreach ($colunas as $coluna) {            
            if($coluna->getChave() == 'PRI') {
                return $coluna->getCampo();
            }
        }
        return '';
    }

    /**
     * Retorna a chavePrimaria da tabela minusculo
     * @return string
     */
    public function getChavePrimariaMinusculo()
    {
        return strtolower($this->getChavePrimaria());
    }

    /**
     * Retorna a chavePrimaria da tabela maiusculo
     * @return string
     */
    public function getChavePrimariaMaiusculo()
    {
        return strtoupper($this->getChavePrimaria());
    }

    /**
     * Retorna um nome em minusculo e no singular
     * @param string $nome
     * @return string
     */
    public function getSingularMinusculo($nome)
    {
        $nome = strtolower($nome);

        if($nome == ''){
            return '';
        }

        if(substr($nome, -3) == 'ies'){
            return substr($nome,0,-3).'y';
        }

        if(substr($nome, -3) == 'oes'){
            return substr($nome,0,-3).'ao';
        }

        if(substr($nome, -3) == 'ais'){
            return substr($nome,0,-3).'al';
        }

        if((substr($nome, -1) == 's') && (substr($nome, -3) != 'tus')){
            return substr($nome,0,-1);
        }

        return $nome;


    }

    /**
     * Retorna o nome da tabela como camel case no singular
     * @return string
     */
    public function getNomeCamelCaseSingular()
    {
        if(!empty($this->prefixo)) {
            $nome = str_replace($this->prefixo.'_', '', $this->nomeCompleto);
        } else {
            $nome = $this->nomeCompleto;
        }

        $array = explode('_', $nome);
        $cont = 0;
        foreach ($array as $a) {
            $array[$cont] = $this->getSingularMinusculo($a);
            $cont++;
        }
        $nome = implode('_',$array);
        
        $nome = $this->getSingularMinusculo($nome);
        $nome = str_replace('_', ' ', $nome);
        $nome = ucwords(strtolower($nome));
        $nome = str_replace(' ', '', $nome);
        return $nome;
    }

    /**
     * Retorna o nome da tabela como camel case com a primeira letra minuscula no singular
     * @return string
     */
    public function getNomeCamelCaseLcFirstSingular()
    {
        return lcfirst($this->getNomeCamelCaseSingular());
    }

    /**
     * Retorna uma string com o nome dos campos da tabela que nao tem chave primaria semarados por virgula
     * @return string
     */
    public function getColunasCamposSemPkPorVirgula()
    {
        $camposSemPk = '';
        $colunas = $this->getColunas();
        foreach ($colunas as $coluna) {
            if ($coluna->getChave() != 'PRI') {
                $camposSemPk .= '\''.$coluna->getCampo().'\', ';
            }
        }
        return $camposSemPk;
    }

    /**
     * Retorna uma string com o nome dos campos da tabela que nao tem chave primaria semarados por virgula minusculo
     * @return string
     */
    public function getColunasCamposSemPkPorVirgulaMinusculo()
    {
        return strtolower($this->getColunasCamposSemPkPorVirgula());
    }

    /**
     * Retorna uma string com o nome dos campos da tabela que nao tem chave primaria semarados por virgula maiusculo
     * @return string
     */
    public function getColunasCamposSemPkPorVirgulaMaiusculo()
    {
        return strtoupper($this->getColunasCamposSemPkPorVirgula());
    }
    
}