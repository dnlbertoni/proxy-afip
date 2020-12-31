<?php
namespace Empresa;


class Empresa{
    private $id;
    private $razon_social;
    private $cuit;
    private $activo;
    private $apitoken;

    /**
     * Empresa constructor.
     * @param $id
     * @param $razon_social
     * @param $cuit
     * @param $activo
     * @param $apitoken
     */
    public function __construct($id=null, $razon_social=null, $cuit=null, $activo=null, $apitoken=null){
        $this->id = $id;
        $this->razon_social = $razon_social;
        $this->cuit = $cuit;
        $this->activo = $activo;
        $this->apitoken = $apitoken;
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
    public function getRazonSocial()
    {
        return $this->razon_social;
    }

    /**
     * @param mixed $razon_social
     */
    public function setRazonSocial($razon_social)
    {
        $this->razon_social = $razon_social;
    }

    /**
     * @return mixed
     */
    public function getCuit()
    {
        return $this->cuit;
    }

    /**
     * @param mixed $cuit
     */
    public function setCuit($cuit)
    {
        $this->cuit = $cuit;
    }

    /**
     * @return mixed
     */
    public function getActivo()
    {
        return $this->activo;
    }

    /**
     * @param mixed $activo
     */
    public function setActivo($activo)
    {
        $this->activo = $activo;
    }

    /**
     * @return mixed
     */
    public function getApitoken()
    {
        return $this->apitoken;
    }

    /**
     * @param mixed $activo
     */
    public function setApitoken($apitoken)
    {
        $this->apitoken = $apitoken;
    }


}