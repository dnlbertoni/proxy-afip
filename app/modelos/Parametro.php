<?php
namespace Config;
class Parametro{
    private $id;
    private $nombre;
    private $valor;
    private $tipo;

    /**
     * Parametro constructor.
     * @param $id
     * @param $nombre
     * @param $valor
     */
    public function __construct( $id=null, $nombre=null, $valor=null, $tipo='CAMPO'){
        $this->id = $id;
        $this->nombre = $nombre;
        $this->valor = $valor;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * @param mixed $nombre
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    /**
     * @return mixed
     */
    public function getValor()
    {
        return $this->valor;
    }

    /**
     * @param mixed $valor
     */
    public function setValor($valor)
    {
        $this->valor = $valor;
    }

    /**
     * @return mixed
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * @param mixed $tipo
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    }


}