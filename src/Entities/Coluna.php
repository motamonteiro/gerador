<?php
/**
 * Created by PhpStorm.
 * User: alemonteiro
 * Date: 11/05/2016
 * Time: 09:46
 */

namespace MotaMonteiro\Gerador\Entities;


class Coluna
{

    /**
     * @var string
     */
    private $campo;
    /**
     * @var string
     */
    private $tipo;
    /**
     * @var bool
     */
    private $nulo;
    /**
     * @var string
     */
    private $chave;
    /**
     * @var bool
     */
    private $auto_increment;

    public function __construct(string $campo, string $tipo, bool $nulo, string $chave, bool $auto_increment)
    {
        $this->campo = $campo;
        $this->tipo = $tipo;
        $this->nulo = $nulo;
        $this->chave = $chave;
        $this->auto_increment = $auto_increment;
    }

    /**
     * @return string
     */
    public function getCampo(): string 
    {
        return $this->campo;
    }

    /**
     * @param string $campo
     * @return Coluna
     */
    public function setCampo(string $campo): Coluna
    {
        $this->campo = $campo;
        return $this;
    }

    /**
     * @return string
     */
    public function getTipo(): string 
    {
        return $this->tipo;
    }

    /**
     * @param string $tipo
     * @return Coluna
     */
    public function setTipo(string $tipo): Coluna
    {
        $this->tipo = $tipo;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isNulo(): bool
    {
        return $this->nulo;
    }

    /**
     * @param boolean $nulo
     * @return Coluna
     */
    public function setNulo(bool $nulo): Coluna
    {
        $this->nulo = $nulo;
        return $this;
    }

    /**
     * @return string
     */
    public function getChave(): string 
    {
        return $this->chave;
    }

    /**
     * @param string $chave
     * @return Coluna
     */
    public function setChave(string $chave): Coluna
    {
        $this->chave = $chave;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isAutoIncrement(): bool 
    {
        return $this->auto_increment;
    }

    /**
     * @param boolean $auto_increment
     * @return Coluna
     */
    public function setAutoIncrement(bool $auto_increment): Coluna
    {
        $this->auto_increment = $auto_increment;
        return $this;
    }

    /**
     * Retorna o campo da coluna minusculo
     * @return string
     */
    public function getCampoMinusculo(): string
    {
        return strtolower($this->getCampo());
    }

    /**
     * Retorna o campo da coluna maiusculo
     * @return string
     */
    public function getCampoMaiusculo(): string
    {
        return strtoupper($this->getCampo());
    }
    

    /**
     * Retorna o nome da coluna como camel case
     * @return string
     */
    public function getCampoCamelCase(): string
    {
        $nome = str_replace('_', ' ', $this->getCampo());
        $nome = ucwords(strtolower($nome));
        $nome = str_replace(' ', '', $nome);
        return $nome;
    }

    /**
     * Retorna o nome da coluna como camel case com a primeira letra minuscula
     * @return string
     */
    public function getCampoCamelCaseLcFirst(): string
    {
        return lcfirst($this->getCampoCamelCase());;
    }
    
    

}