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
    private $campoTabelaEstrangeira;
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

    public function __construct($campo, $tipo, $nulo, $chave, $auto_increment, $campoTabelaEstrangeira = '', $campoChaveEstrangeira = '')
    {
        $this->campo = $campo;
        $this->campoTabelaEstrangeira = $campoTabelaEstrangeira;
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
    public function getCampoTabelaEstrangeira()
    {
        if(!empty($_ENV['DB_TABLE_PREFIX'])) {
            return str_replace($_ENV['DB_TABLE_PREFIX'].'_', '', $this->campoTabelaEstrangeira);
        }

        return $this->campoTabelaEstrangeira;
    }

    /**
     * @return string
     */
    public function getCampoTabelaEstrangeiraMinusculo()
    {
        return strtolower($this->getCampoTabelaEstrangeira());
    }

    /**
     * @param string $campo
     * @return Coluna
     */
    public function setCampoTabelaEstrangeira($campoTabelaEstrangeira)
    {
        $this->campoTabelaEstrangeira = $campoTabelaEstrangeira;
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

    /**
     * Retorna o campo informado, retirando o _id ou id_ montando assim o nome da classe
     * Exemplo: ID_USUARIO_DECLARANTE retorna USUARIO_DECLARANTE
     * @return string
     */
    public function getNomeClasse($campo) 
    {
        $campo = strtolower($campo);
        
        if(substr($campo,0,3) == "id_"){
            $nomeClasse = substr($campo,2);
        }elseif(substr($campo,-3) == "_id"){
            $nomeClasse = substr($campo,0,-2);
        } else {
            echo 'erro ao identificar o id no campo '.$campo;
            exit;
        }
        
        return $nomeClasse;
        
    }

    /**
     * Retorna o campo informado, retirando o _id ou id_ montando assim o nome da classe no formato camel case
     * Exemplo: ID_USUARIO_DECLARANTE retorna UsuarioDeclarante
     * @return string
     */
    public function getNomeClasseCamelCase($campo)
    {
        $nome = str_replace('_', ' ', $this->getNomeClasse($campo));
        $nome = ucwords(strtolower($nome));
        $nome = str_replace(' ', '', $nome);
        return $nome; 

    }

    /**
     * Retorna o campo informado, retirando o _id ou id_ montando assim o nome da classe no formato camel case com a primeira letra minuscula
     * Exemplo: ID_USUARIO_DECLARANTE retorna usuarioDeclarante
     * @return string
     */
    public function getNomeClasseCamelCaseLcFirst($campo)
    {        
        return lcfirst($this->getNomeClasseCamelCase($campo));

    }

    public function getRegraValidator()
    {
        $regra = '';
        if($this->isNulo() == 0){
            $regra .= 'required';
        }

        if(substr($this->getTipo(),0,3) == 'int'){
            $regra .= '|integer';
        }

        if(substr($this->getTipo(),-8) == 'unsigned'){
            $regra .= '|min:0';
        }

        if(substr($this->getTipo(),0,7) == 'varchar'){
            preg_match('#\((.*?)\)#', $this->getTipo(), $match); //Pega o que estiver entre parentesis
            $regra .= '|max:'.$match[1];
        }

        if(substr($this->getTipo(),0,7) == 'decimal'){
            preg_match('#\((.*?)\)#', $this->getTipo(), $match); //Pega o que estiver entre parentesis
            $result = explode(',', $match[1]);
            $max = $result[0];
            //$decimal = $result[1];
            $regra .= 'numeric|max:'.$max;
        }

        if(substr($this->getTipo(),0,4) == 'date'){
            $regra .= '|date_format:d/m/Y';
        }

        if ($this->getChave() == 'MUL') {
            $regra .= '|exists:'.$this->getCampoTabelaEstrangeiraMinusculo().','.$this->getCampoChaveEstrangeiraMinusculo();
        }

        if ($this->getCampoMinusculo() == 'estado') {
            $regra .= '|in:AL,AM,AC,AP,BA,CE,DF,ES,GO,MA,MT,MS,MG,PA,PB,PR,PE,PI,RJ,RN,RO,RS,RR,SC,SE,SP,TO';
        }

        if ($this->getCampoMinusculo() == 'password') {
            $regra .= '|confirmed';
        }

        return $regra;
    }

}