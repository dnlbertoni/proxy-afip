<?php

namespace Config;
require_once ( __DIR__ . "../../conf/database.php");
require_once (__DIR__ . "/Parametro.php");


class Parametros{
    private $sql;
    private $db;

    /**
     * @param $sql
     * @param $db
     */
    public function __construct(){
        $this->db = new \DataBase(MYSQL_HOST,MYSQL_DB,MYSQL_USER,MYSQL_PASS);
        $this->db->Connect();
    }
    public function insert($parametro=null){
        if (is_object($parametro)){
            if( $this->db->isConnected() ){
                $this->setSql('insert');
                $sql = sprintf($this->getSql(), strtoupper($parametro->getNombre()),$parametro->getValor(),$parametro->getTipo());
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

    public function update($parametro=null){
        if (is_object($parametro)){
            if( $this->db->isConnected() ){
                $this->setSql('update');
                $sql = sprintf($this->getSql(), strtoupper($parametro->getNombre()),$parametro->getValor(),$parametro->getTipo(),$parametro->getId());
                $rs = $this->db->Query($sql);
                if($rs !=null ){
                    if($rs->AffectedRows()>0){
                        return $parametro->getId();
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

    public function delete($parametro=null){
        if (is_object($parametro)){
            if( $this->db->isConnected() ){
                $this->setSql('delete');
                $sql = sprintf($this->getSql(),  $parametro->getId());
                $rs = $this->db->Query($sql);
                if($rs !=null ){
                    if($rs->AffectedRows()>0){
                        return $parametro->getId();
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

    public function getParametros(){
        if( $this->db->isConnected() ){
            $this->setSql();
            $sql = $this->getSql();
            $rs = $this->db->Query($sql);
            if($rs !=null ){
                $data = new \ArrayObject();
                if($rs->NumRows() > 0 ){
                    while($r=$rs->Fetch()){
                        $data->append(
                            new Parametro(    $rs->Fields['id'],
                                            $rs->Fields['nombre'],
                                            $rs->Fields['valor'],
                                            $rs->Fields['tipo']
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
                        $data = new Parametro(  $rs->Fields['id'],
                                                $rs->Fields['nombre'],
                                                $rs->Fields['valor'],
                                                $rs->Fields['tipo']
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

    public function getNombre($nombre){
        if( $this->db->isConnected() ){
            $this->setSql("byNombre");
            $sql = sprintf($this->getSql(), trim(strtoupper($nombre)));
            $rs = $this->db->Query($sql);
            if($rs !=null ){
                $data = new \ArrayObject();
                if($rs->NumRows() > 0 ){
                    while($r=$rs->Fetch()){
                        $data = new Parametro(  $rs->Fields['id'],
                            $rs->Fields['nombre'],
                            $rs->Fields['valor'],
                            $rs->Fields['tipo']
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
                $sql = "select * from parametros where id=%s ";
                break;
            case "byNombre":
                $sql = "select * from parametros where nombre='%s' ";
                break;
            case "insert":
                $sql = "insert into parametros (nombre, valor, tipo) values ('%s','%s')";
                break;
            case "update":
                $sql = "update parametros set nombre='%s',valor='%s', tipo='%s' where id=%d";
                break;
            case "delete":
                $sql = "delete from parametros where id=%d";
                break;
            default:
                $sql = "select * from parametros order by nombre desc";
                break;
        }
        $this->sql = $sql;
    }

}