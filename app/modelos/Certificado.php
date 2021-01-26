<?php

namespace Certificado;

class Certificado{
    private $id;
    private $idempresa;
    private $identorno;
    private $filename;
    private $tipo;
    private $password_certificado;
    private $activo;
    private $fechaemision;
    private $fechavencimiento;
    private $certificado_raw;

    /**
     * Certificado constructor.
     * @param $id
     * @param $idempresa
     * @param $identorno
     * @param $filename
     * @param $tipo
     * @param $password_certificado
     * @param $activo
     * @param $fechaemision
     * @param $fechavencimiento
     * @param $certificado_raw     
     */
    public function __construct($id=null, $idempresa=null, $identorno=null, $filename=null, $tipo=null, $password_certificado=null, $activo=null, $fechaemision=null, $fechavencimiento=null, $certificado_raw=null)
    {
        $this->id = $id;
        $this->idempresa = $idempresa;
        $this->identorno = $identorno;
        $this->filename = $filename;
        $this->tipo = $tipo;
        $this->password_certificado = $password_certificado;
        $this->activo = $activo;
        $this->fechaemision = $fechaemision;
        $this->fechavencimiento = $fechavencimiento;
        $this->certificado_raw = $certificado_raw;
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

    /**
     * @return null
     */
    public function getIdentorno()
    {
        return $this->identorno;
    }

    /**
     * @param null $identorno
     */
    public function setIdentorno($identorno)
    {
        $this->identorno = $identorno;
    }

    /**
     * @return null
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @param null $filename
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
    }

    /**
     * @return null
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * @param null $tipo
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    }

    /**
     * @return null
     */
    public function getPasswordCertificado()
    {
        return $this->password_certificado;
    }

    /**
     * @param null $password_certificado
     */
    public function setPasswordCertificado($password_certificado)
    {
        $this->password_certificado = $password_certificado;
    }

    /**
     * @return null
     */
    public function getActivo()
    {
        return $this->activo;
    }

    /**
     * @param null $activo
     */
    public function setActivo($activo)
    {
        $this->activo = $activo;
    }

    /**
     * @return null
     */
    public function getFechaemision()
    {
        return $this->fechaemision;
    }

    /**
     * @param null $fechaemision
     */
    public function setFechaemision($fechaemision)
    {
        $this->fechaemision = $fechaemision;
    }

    /**
     * @return null
     */
    public function getFechavencimiento()
    {
        return $this->fechavencimiento;
    }

    /**
     * @param null $fechavencimiento
     */
    public function setFechavencimiento($fechavencimiento)
    {
        $this->fechavencimiento = $fechavencimiento;
    }

    /**
     * @return null
     */
    public function getCertificadoRaw()
    {
        return $this->certificado_raw;
    }

    /**
     * @param null $fechavencimiento
     */
    public function setCertificadoRaw($certificado_raw)
    {
        $this->certificado_raw = $certificado_raw;
    }

}