<?php
declare(strict_types=1);

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

    public function __construct(string $nomeCompleto = null, string $prefixo = null)
    {
        $this->setNomeCompleto($nomeCompleto);
        $this->setPrefixo($prefixo);
        $this->colunas = [];
        $this->tabelasEstrangeiras = [];
    }


    /**
     * @return string
     */
    public function getNomeCompleto(): string
    {
        return $this->nomeCompleto;
    }

    /**
     * @param null $nomeCompleto
     * @return Tabela
     */
    public function setNomeCompleto($nomeCompleto): Tabela
    {
        $this->nomeCompleto = ($nomeCompleto) ? strtoupper($nomeCompleto) : '';
        return $this;
    }

    /**
     * @return string
     */
    public function getPrefixo(): string
    {
        return $this->prefixo;
    }

    /**
     * @param null $prefixo
     * @return Tabela
     */
    public function setPrefixo($prefixo): Tabela
    {
        $this->prefixo = ($prefixo)? strtoupper($prefixo) : '';
        return $this;
    }

    /**
     * @return array
     */
    public function getColunas():array
    {
        return $this->colunas;
    }
    
    /**
     * @param Coluna $coluna
     * @return Tabela
     */
    public function addColuna(Coluna $coluna): Tabela
    {
        $this->colunas[] = $coluna;
        return $this;
    }

    /**
     * @return array
     */
    public function getTabelasEstrangeiras():array
    {
        return $this->tabelasEstrangeiras;
    }
    
    /**
     * @param Tabela $tabelaEstrangeira
     * @return Tabela
     */
    public function addTabelaEstrangeira(Tabela $tabelaEstrangeira): Tabela
    {
        $this->tabelasEstrangeiras[] = $tabelaEstrangeira;
        return $this;
    }
    
    /**
     * Retira o prefixo que está no nomeCompleto da tabela
     * @return string
     */
    public function getNome(): string
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
    public function getNomeCompletoMinusculo(): string
    {
        return strtolower($this->getNomeCompleto());
    }

    /**
     * Retorna o nomeCompleto da tabela maiusculo
     * @return string
     */
    public function getNomeCompletoMaiusculo(): string
    {
        return strtoupper($this->getNomeCompleto());
    }    

    /**
     * Retorna o nome da tabela minusculo
     * @return string
     */
    public function getNomeMinusculo(): string
    {
        return strtolower($this->getNome());        
    }

    /**
     * Retorna o nome da tabela maiusculo
     * @return string
     */
    public function getNomeMaiusculo(): string
    {
        return strtoupper($this->getNome());
    }

    /**
     * Retorna o nome da tabela como camel case
     * @return string
     */
    public function getNomeCamelCase(): string
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
    public function getNomeCamelCaseLcFirst(): string
    {
        return lcfirst($this->getNomeCamelCase());;
    }

    /**
     * Retorna o campo que contem chave igual a PRI nas colunas da tabela
     * @return string
     */
    public function getChavePrimaria(): string
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
    public function getChavePrimariaMinusculo(): string
    {
        return strtolower($this->getChavePrimaria());
    }

    /**
     * Retorna a chavePrimaria da tabela maiusculo
     * @return string
     */
    public function getChavePrimariaMaiusculo(): string
    {
        return strtoupper($this->getChavePrimaria());
    }

    /**
     * Retorna um nome em minusculo e no singular
     * @param string $nome
     * @return string
     */
    public function getSingularMinusculo(string $nome): string
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

        if((substr($nome, -1) == 's') && (substr($nome, -3) != 'tus')){
            return substr($nome,0,-1);
        }

        return $nome;


    }

    /**
     * Retorna o nome da tabela como camel case no singular
     * @return string
     */
    public function getNomeCamelCaseSingular(): string
    {
        $nome = $this->getSingularMinusculo($this->getNome());
        $nome = str_replace('_', ' ', $nome);
        $nome = ucwords(strtolower($nome));
        $nome = str_replace(' ', '', $nome);
        return $nome;
    }

    /**
     * Retorna o nome da tabela como camel case com a primeira letra minuscula no singular
     * @return string
     */
    public function getNomeCamelCaseLcFirstSingular(): string
    {
        return lcfirst($this->getNomeCamelCaseSingular());;
    }

    /**
     * Retorna uma string com o nome dos campos da tabela que nao tem chave primaria semarados por virgula
     * @return string
     */
    public function getColunasCamposSemPkPorVirgula():string
    {
        $camposSemPk = '';
        $colunas = $this->getColunas();
        foreach ($colunas as $coluna) {
            if ($coluna->getChave() != 'PRI') {
                $camposSemPk .= $coluna->getCampo().', ';
            }
        }
        return $camposSemPk;
    }
    
}