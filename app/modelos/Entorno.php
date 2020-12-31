<?php


namespace Config;


class Entorno{
    private $id;
    private $nombre;
    private $debug_activo;
    private $actual;
    private $idempresa;

    /**
     * Entorno constructor.
     * @param $id
     * @param $nombre
     * @param $debug_activo
     * @param $actual
     */
    public function __construct($id=null, $nombre=null, $idempresa=null,$debug_activo=null, $actual=null){
        $this->id = $id;
        $this->nombre = $nombre;
        $this->debug_activo = $debug_activo;
        $this->actual = $actual;
        $this->idempresa = $idempresa;
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
    public function getDebugActivo()
    {
        return $this->debug_activo;
    }

    /**
     * @param mixed $debug_activo
     */
    public function setDebugActivo($debug_activo)
    {
        $this->debug_activo = $debug_activo;
    }

    /**
     * @return mixed
     */
    public function getActual()
    {
        return $this->actual;
    }

    /**
     * @param mixed $actual
     */
    public function setActual($actual)
    {
        $this->actual = $actual;
    }

    /**
     * @return null
     */
    public function getIdempresa()
    {
        return $this->idempresa;
    }

    /**
     * @param null $idempresa
     */
    public function setIdempresa($idempresa)
    {
        $this->idempresa = $idempresa;
    }


}