<?php

namespace Servicio;
require_once (__DIR__. "/../conf/database.php");
require_once ( __DIR__. "/Servicio.php");


class Servicios{
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
    public function insert($servicio=null){
        if (is_object($servicio)){
            if( $this->db->isConnected() ){
                $this->setSql('insert');
                $sql = sprintf($this->getSql(),
                    $servicio->getNombre(),
                    $servicio->getDescripcion(),
                    $servicio->getIdentorno(),
                    $servicio->getFileWsdl(),
                    $servicio->getVersion(),
                    $servicio->getFileDoc(),
                    $servicio->getUrl(),
                    $servicio->getIdentorno()                    
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

    public function update($servicio=null){
        if (is_object($servicio)){
            if( $this->db->isConnected() ){
                $this->setSql('update');
                $sql = sprintf($this->getSql(),
                    $servicio->getNombre(),
                    $servicio->getDescripcion(),
                    $servicio->getIdentorno(),
                    $servicio->getFileWsdl(),
                    $servicio->getVersion(),
                    $servicio->getFileDoc(),
                    $servicio->getUrl(),
                    $servicio->getId());
                $rs = $this->db->Query($sql);
                if($rs !=null ){
                    if($rs->AffectedRows()>0){
                        return $servicio->getId();
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

    public function delete($servicio=null){
        if (is_object($servicio)){
            if( $this->db->isConnected() ){
                $this->setSql('delete');
                $sql = sprintf($this->getSql(), $servicio->getId());
                $rs = $this->db->Query($sql);
                if($rs !=null ){
                    if($rs->AffectedRows()>0){
                        return $servicio->getId();
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

    public function getServicios(){
        if( $this->db->isConnected() ){
            $this->setSql();
            $sql = $this->getSql();
            $rs = $this->db->Query($sql);
            if($rs !=null ){
                $data = new \ArrayObject();
                if($rs->NumRows() > 0 ){
                    while($r=$rs->Fetch()){
                        $data->append(
                            new Servicio( $rs->Fields['id'],
                                                    $rs->Fields['nombre'],
                                                    $rs->Fields['descripcion'],
                                                    $rs->Fields['identorno'],
                                                    $rs->Fields['file_wsdl'],
                                                    $rs->Fields['version'],
                                                    $rs->Fields['file_doc'],
                                                    $rs->Fields['url']
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
                        $data= new Servicio(     $rs->Fields['id'],
                                                $rs->Fields['nombre'],
                                                $rs->Fields['descripcion'],
                                                $rs->Fields['identorno'],
                                                $rs->Fields['file_wsdl'],
                                                $rs->Fields['version'],
                                                $rs->Fields['file_doc'],
                                                $rs->Fields['url']
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

    public function getByName($nombre){
        if( $this->db->isConnected() ){
            $this->setSql("byName");
            $sql = sprintf($this->getSql(), $nombre);
            $rs = $this->db->Query($sql);
            if($rs !=null ){
                $data = new \ArrayObject();
                if($rs->NumRows() > 0 ){
                    while($r=$rs->Fetch()){
                        $data= new Servicio(     $rs->Fields['id'],
                            $rs->Fields['nombre'],
                            $rs->Fields['descripcion'],
                            $rs->Fields['identorno'],
                            $rs->Fields['file_wsdl'],
                            $rs->Fields['version'],
                            $rs->Fields['file_doc'],
                            $rs->Fields['url']
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
    public function setSql($id=null)
    {
        switch ($id){
            case "byId":
                $sql = "select * from servicios where id=%s";
                break;
            case "byName":
                $sql = "select * from servicios where nombre='%s'";
                break;
            case "insert":
                $sql = "insert into servicios   (nombre,descripcion,identorno,file_wsdl, version, file_doc, url, idempresa) 
                                        values  ('%s'  ,'%s'       ,%d       , '%s'    ,'%s'    ,'%s'     ,'%s', %d)";
                break;
            case "delete":
                $sql = "delete from servicios where id=%d";
                break;
            case "update":
                $sql = "update servicios set    nombre='%s',
                                                descripcion='%s',
                                                identorno=%d,
                                                file_wsdl='%s',
                                                version='%s',
                                                file_doc='%s',
                                                url='%s',                                                
                                                idempresa=%d
                                        where id=%d";
                break;
            default:
                $sql = "select s.* from servicios s inner join entornos e on e.id=s.identorno";
                break;
        }
        $this->sql = $sql;
    }

}