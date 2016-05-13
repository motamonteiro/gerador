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
    private $campoChaveEstrangeira;
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

    public function __construct($campo, $tipo, $nulo, $chave, $auto_increment, $campoChaveEstrangeira = '')
    {
        $this->campo = $campo;
        $this->campoChaveEstrangeira = $campoChaveEstrangeira;
        $this->tipo = $tipo;
        $this->nulo = $nulo;
        $this->chave = $chave;
        $this->auto_increment = $auto_increment;
    }

    /**
     * @return string
     */
    public function getCampo()
    {
        return $this->campo;
    }

    /**
     * @param string $campo
     * @return Coluna
     */
    public function setCampo($campo)
    {
        $this->campo = $campo;
        return $this;
    }

    /**
     * @return string
     */
    public function getCampoChaveEstrangeira()
    {
        return $this->campoChaveEstrangeira;
    }

    /**
     * @param string $campo
     * @return Coluna
     */
    public function setCampoChaveEstrangeira($campoChaveEstrangeira)
    {
        $this->campoChaveEstrangeira = $campoChaveEstrangeira;
        return $this;
    }

    /**
     * @return string
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * @param string $tipo
     * @return Coluna
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isNulo()
    {
        return $this->nulo;
    }

    /**
     * @param boolean $nulo
     * @return Coluna
     */
    public function setNulo($nulo)
    {
        $this->nulo = $nulo;
        return $this;
    }

    /**
     * @return string
     */
    public function getChave()
    {
        return $this->chave;
    }

    /**
     * @param $chave
     * @return Coluna
     */
    public function setChave($chave)
    {
        $this->chave = $chave;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isAutoIncrement()
    {
        return $this->auto_increment;
    }

    /**
     * @param boolean $auto_increment
     * @return Coluna
     */
    public function setAutoIncrement($auto_increment)
    {
        $this->auto_increment = $auto_increment;
        return $this;
    }

    /**
     * Retorna o campo da coluna minusculo
     * @return string
     */
    public function getCampoMinusculo()
    {
        return strtolower($this->getCampo());
    }

    /**
     * Retorna o campo da coluna maiusculo
     * @return string
     */
    public function getCampoMaiusculo()
    {
        return strtoupper($this->getCampo());
    }


    /**
     * Retorna o nome da coluna como camel case
     * @return string
     */
    public function getCampoCamelCase()
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
    public function getCampoCamelCaseLcFirst()
    {
        return lcfirst($this->getCampoCamelCase());
    }

    /**
     * Retorna o campo da coluna minusculo
     * @return string
     */
    public function getCampoChaveEstrangeiraMinusculo()
    {
        return strtolower($this->getCampoChaveEstrangeira());
    }

    /**
     * Retorna o campo da coluna maiusculo
     * @return string
     */
    public function getCampoChaveEstrangeiraMaiusculo()
    {
        return strtoupper($this->getCampoChaveEstrangeira());
    }


    /**
     * Retorna o nome da coluna como camel case
     * @return string
     */
    public function getCampoChaveEstrangeiraCamelCase()
    {
        $nome = str_replace('_', ' ', $this->getCampoChaveEstrangeira());
        $nome = ucwords(strtolower($nome));
        $nome = str_replace(' ', '', $nome);
        return $nome;
    }

    /**
     * Retorna o nome da coluna como camel case com a primeira letra minuscula
     * @return string
     */
    public function getCampoChaveEstrangeiraCamelCaseLcFirst()
    {
        return lcfirst($this->getCampoChaveEstrangeiraCamelCase());
    }

}