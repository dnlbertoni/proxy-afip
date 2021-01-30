<?php

namespace Config;
require_once (__DIR__.'/'."../conf/database.php");
require_once (__DIR__."/Entorno.php");


class Entornos{
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
    public function insert($entorno=null){
        if (is_object($entorno)){
            if( $this->db->isConnected() ){
                $this->setSql('insert');
                $sql = sprintf($this->getSql(), $entorno->getNombre(),$entorno->getIdempresa(),$entorno->getDebugActivo(),$entorno->getActual());
                //die($sql);
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

    public function update($entorno=null){
        if (is_object($entorno)){
            if( $this->db->isConnected() ){
                $this->setSql('update');
                $sql = sprintf($this->getSql(), $entorno->getNombre(),$entorno->getIdempresa(),$entorno->getDebugActivo(),$entorno->getActual(), $entorno->getId());
                $rs = $this->db->Query($sql);
                if($rs !=null ){
                    if($rs->AffectedRows()>0){
                        return $entorno->getId();
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

    public function delete($entorno=null){
        if (is_object($entorno)){
            if( $this->db->isConnected() ){
                $this->setSql('delete');
                $sql = sprintf($this->getSql(),  $entorno->getId());
                $rs = $this->db->Query($sql);
                if($rs !=null ){
                    if($rs->AffectedRows()>0){
                        return $entorno->getId();
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

    public function getEntornos($empresa=null){
        if( $this->db->isConnected() ){
            $this->setSql();
            $sql = $this->getSql();
            if($empresa===null){
                $where = '';
            }else{
                $where= ' and idempresa='.$empresa;
            }
            $sql = sprintf($sql, $where);
            $rs = $this->db->Query($sql);
            if($rs !=null ){
                $data = new \ArrayObject();
                if($rs->NumRows() > 0 ){
                    while($r=$rs->Fetch()){
                        $data->append(
                            new Entorno(    $rs->Fields['id'],
                                            $rs->Fields['nombre'],
                                            $rs->Fields['idempresa'],
                                            $rs->Fields['debug_activo'],
                                            $rs->Fields['actual']
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
                        $data = new Entorno(    $rs->Fields['id'],
                                                $rs->Fields['nombre'],
                                                $rs->Fields['idempresa'],
                                                $rs->Fields['debug_activo'],
                                                $rs->Fields['actual']
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

    public function getActual($idempresa){
        if( $this->db->isConnected() ){
            $this->setSql("byActual");
            $sql = sprintf($this->getSql(), $idempresa);
            $rs = $this->db->Query($sql);
            if($rs !=null ){
                $data = new \ArrayObject();
                if($rs->NumRows() > 0 ){
                    while($r=$rs->Fetch()){
                        $data = new Entorno(    $rs->Fields['id'],
                            $rs->Fields['nombre'],
                            $rs->Fields['idempresa'],
                            $rs->Fields['debug_activo'],
                            $rs->Fields['actual']
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
                $sql = "select * from entornos where id=%s ";
                break;
            case "byActual":
                $sql = "select * from entornos where actual=1 and idempresa=%d limit 1";
                break;
            case "insert":
                $sql = "insert into entornos (nombre, idempresa, debug_activo, actual) values ('%s',%d,%d, %d)";
                break;
            case "update":
                $sql = "update entornos set nombre='%s',idempresa=%d, debug_activo=%d, actual=%d where id=%d";
                break;
            case "delete":
                $sql = "delete from entornos where id=%d";
                break;
            default:
                $sql = "select * from entornos where 1=1 %s order by idempresa,actual desc";
                break;
        }
        $this->sql = $sql;
    }

}