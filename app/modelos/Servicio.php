<?php

namespace Servicio;


class Servicio{
    private $id;
    private $nombre;
    private $descripcion;
    private $identorno;
    private $file_wsdl;
    private $version;
    private $file_doc;
    private $url;
    private $idempresa;

    /**
     * Servicio constructor.
     * @param $id
     * @param $nombre
     * @param $identorno
     * @param $file_wsdl
     * @param $version
     * @param $file_doc
     * @param $url
     */
    public function __construct($id=null, $nombre=null, $descripcion=null,$identorno=null, $file_wsdl=null, $version=null, $file_doc=null, $url=null,$idempresa=null)
    {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->descripcion=$descripcion;
        $this->identorno = $identorno;
        $this->file_wsdl = $file_wsdl;
        $this->version = $version;
        $this->file_doc = $file_doc;
        $this->url = $url;
        $this->idempresa=$idempresa;
    }

    /**
     * @return null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param null $id
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
    public function getIdentorno()
    {
        return $this->identorno;
    }

    /**
     * @param mixed $identorno
     */
    public function setIdentorno($identorno)
    {
        $this->identorno = $identorno;
    }

    /**
     * @return mixed
     */
    public function getFileWsdl()
    {
        return $this->file_wsdl;
    }

    /**
     * @param mixed $file_wsdl
     */
    public function setFileWsdl($file_wsdl)
    {
        $this->file_wsdl = $file_wsdl;
    }

    /**
     * @return mixed
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param mixed $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * @return mixed
     */
    public function getFileDoc()
    {
        return $this->file_doc;
    }

    /**
     * @param mixed $file_doc
     */
    public function setFileDoc($file_doc)
    {
        $this->file_doc = $file_doc;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return null
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * @param null $descripcion
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;
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