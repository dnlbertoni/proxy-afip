<?php

namespace Certificado;
require_once (__DIR__.'/'."../conf/database.php");
require_once (__DIR__.'/'."Certificado.php");


class Certificados{
    private $sql;
    private $db;

    /**
     * Empresas constructor.
     * @param $sql
     * @param $db
     */
    public function __construct(){
        $this->db = new \DataBase(MYSQL_HOST,MYSQL_DB,MYSQL_USER,MYSQL_PASS);
        $this->db->Connect();
    }

    public function insert($certificado=null){
        if (is_object($certificado)){
            if( $this->db->isConnected() ){
                $this->setSql('insert');
                $sql = sprintf($this->getSql(), 
                            $certificado->getIdempresa(),
                            $certificado->getIdentorno(),
                            $certificado->getFilename(),
                            $certificado->getTipo(),
                            $certificado->getPasswordCertificado(),
                            $certificado->getActivo(),
                            $certificado->getfechaemision(),
                            $certificado->getFechavencimiento(),
                            $certificado->getCertificadoRaw()
                );
                $rs = $this->db->Query($sql);
                if($rs !=null ){
                    if($rs->AffectedRows()>0){
                        return $this->db->lastInsertId();
                    }else{
                        return false;
                    }
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    public function delete($certificado=null){
        if (is_object($certificado)){
            if( $this->db->isConnected() ){
                $this->setSql('delete');
                $sql = sprintf($this->getSql(), 
                            $certificado->getId()
                );
                $rs = $this->db->Query($sql);
                if($rs !=null ){
                    if($rs->AffectedRows()>0){
                        return true;
                    }else{
                        return false;
                    }
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }else{
            return false;
        }
    }


    public function update($certificado=null){
        if (is_object($certificado)){
            if( $this->db->isConnected() ){
                $this->setSql('update');
                $sql = sprintf($this->getSql(),
                    $certificado->getIdempresa(),
                    $certificado->getIdentorno(),
                    $certificado->getFilename(),
                    $certificado->getTipo(),
                    $certificado->getPasswordCertificado(),
                    $certificado->getActivo(),
                    $certificado->getfechaemision(),
                    $certificado->getFechavencimiento(),
                    $certificado->getCertificadoRaw(),
                    $certificado->getId()
                );
                $rs = $this->db->Query($sql);
                if($rs !=null ){
                    if($rs->AffectedRows()>0){
                        return $certificado->getId();
                    }else{
                        return false;
                    }
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    public function getCertificados(){
        if( $this->db->isConnected() ){
            $this->setSql();
            $sql = $this->getSql();
            $rs = $this->db->Query($sql);
            if($rs !=null ){
                $data = new \ArrayObject();
                if($rs->NumRows() > 0 ){
                    while($r=$rs->Fetch()){
                        $data->append(
                            new Certificado( $rs->Fields['id'],
                                             $rs->Fields['idempresa'],
                                             $rs->Fields['identorno'],
                                             $rs->Fields['filename'],
                                             $rs->Fields['tipo'],
                                             $rs->Fields['password_certificado'],
                                             $rs->Fields['activo'],
                                             $rs->Fields['fechaemision'],
                                             $rs->Fields['fechavencimiento']
                            )
                        );
                    };
                    return $data;
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    public function getById($id){
    if( $this->db->isConnected() ){
        $this->setSql("byId");
        $sql = sprintf($this->getSql(), $id);
        $rs = $this->db->Query($sql);
        if($rs !=null ){
            $data = new \ArrayObject();
            if($rs->NumRows() > 0 ){
                while($r=$rs->Fetch()){
                    $data = new Certificado( $rs->Fields['id'],
                        $rs->Fields['idempresa'],
                        $rs->Fields['identorno'],
                        $rs->Fields['filename'],
                        $rs->Fields['tipo'],
                        $rs->Fields['password_certificado'],
                        $rs->Fields['activo'],
                        $rs->Fields['fechaemision'],
                        $rs->Fields['fechavencimiento'],
                        $rs->Fields['certifcado_raw']
                    );
                };
                return $data;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }else{
        return false;
    }
}
    public function getCertificadoEntorno($identorno, $tipo, $idempresa){
        if( $this->db->isConnected() ){
            $this->setSql("certificadoEntorno");
            $sql = sprintf($this->getSql(), $identorno, $tipo, $idempresa);
            $rs = $this->db->Query($sql);
            if($rs !=null ){
                $data = new \ArrayObject();
                if($rs->NumRows() > 0 ){
                    while($r=$rs->Fetch()){
                        $data = new Certificado( $rs->Fields['id'],
                            $rs->Fields['idempresa'],
                            $rs->Fields['identorno'],
                            $rs->Fields['filename'],
                            $rs->Fields['tipo'],
                            $rs->Fields['password_certificado'],
                            $rs->Fields['activo'],
                            $rs->Fields['fechaemision'],
                            $rs->Fields['fechavencimiento'],
                            $rs->Fields['certifcado_raw']
                        );
                    };
                    return $data;
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    /**
     * @return mixed
     */
    public function getSql(){
        return $this->sql;
    }

    /**
     * @param mixed $sql
     */
    public function setSql($id=null){
        switch ($id){
            case "byId":
                $sql = "select * from certificados where id=%s";
                break;
            case "insert":
                $sql = "insert into certificados ( idempresa ,identorno ,filename ,tipo ,password_certificado ,activo ,fechaemision ,fechavencimiento ,certificado_raw)   
                                        values   (%d         ,%d        ,'%s'     ,'%s' ,'%s'                 ,%d     ,'%s'         ,'%s'             ,'%s')";
                break;
            case "update":
                $sql = "update certificados set idempresa=%d ,
                                                identorno=%d ,
                                                filename='%s' ,
                                                tipo='%s' ,
                                                password_certificado='%s' ,
                                                activo=%d ,
                                                fechaemision='%s' ,
                                                fechavencimiento='%s', 
                                                certificado_raw='%s'
                                        where id=%d";
                break;
            case "delete":
                $sql = "delete from certificados where id=%d";
                break;                
            case "certificadoEntorno":
                $sql = "select c.* from certificados c 
                                inner join entornos e   on e.id=c.identorno
                                inner join empresas emp on emp.id=c.idempresa
                                where c.activo=1 and emp.activo=1 and e.id=%d and c.tipo='%s' and c.idempresa=%d limit 1";
                break;
            default:
                $sql = "select c.* from certificados c 
                                inner join entornos e   on e.id=c.identorno
                                inner join empresas emp on emp.id=c.idempresa";
                break;
        }
        $this->sql = $sql;
    }

}