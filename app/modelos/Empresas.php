<?php

namespace Empresa;

require_once (__DIR__.'/'."../conf/database.php");
require_once (__DIR__."/Empresa.php");


class Empresas{
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
    public function insert($empresa=null){
        if (is_object($empresa)){
            if( $this->db->isConnected() ){
                $this->setSql('insert');
                $sql = sprintf($this->getSql(), $empresa->getRazonSocial(),$empresa->getCuit(),$empresa->getActivo());
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

    public function update($empresa=null){
        if (is_object($empresa)){
            if( $this->db->isConnected() ){
                $this->setSql('update');
                $sql = sprintf($this->getSql(), $empresa->getRazonSocial(),$empresa->getCuit(),$empresa->getActivo(), $empresa->getId());
                $rs = $this->db->Query($sql);
                if($rs !=null ){
                    if($rs->AffectedRows()>0){
                        return $empresa->getId();
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

    public function delete($empresa=null){
        if (is_object($empresa)){
            if( $this->db->isConnected() ){
                $this->setSql('delete');
                $sql = sprintf($this->getSql(), $empresa->getId());
                $rs = $this->db->Query($sql);
                if($rs !=null ){
                    if($rs->AffectedRows()>0){
                        return $empresa->getId();
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

    public function getEmpresas(){
        if( $this->db->isConnected() ){
            $this->setSql();
            $sql = $this->getSql();
            $rs = $this->db->Query($sql);
            if($rs !=null ){
                $data = new \ArrayObject();
                if($rs->NumRows() > 0 ){
                    while($r=$rs->Fetch()){
                        $data->append(new Empresa($rs->Fields['id'],$rs->Fields['razon_social'],$rs->Fields['cuit'],$rs->Fields['activo']));
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
            //die($sql);
            $rs = $this->db->Query($sql);
            if($rs !=null ){
                $data = new \ArrayObject();
                if($rs->NumRows() > 0 ){
                    while($r=$rs->Fetch()){
                        $data=new Empresa($rs->Fields['id'],$rs->Fields['razon_social'],$rs->Fields['cuit'],$rs->Fields['activo']);
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

    public function getByCuit($cuit){
        if( $this->db->isConnected() ){
            $this->setSql("byCuit");
            $sql = sprintf($this->getSql(), $cuit);
            $rs = $this->db->Query($sql);
            if($rs !=null ){
                $data = new \ArrayObject();
                if($rs->NumRows() > 0 ){
                    while($r=$rs->Fetch()){
                        $data=new Empresa($rs->Fields['id'],$rs->Fields['razon_social'],$rs->Fields['cuit'],$rs->Fields['activo']);
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
                $sql = "select * from empresas where id=%s";
                break;
            case "insert":
                $sql = "insert into empresas (razon_social, cuit, activo) values ('%s','%s', %d)";
                break;
            case "delete":
                $sql = "delete from  empresas where id=%d";
                break;
            case "update":
                $sql = "update empresas set razon_social='%s', cuit='%s', activo=%d where id=%d";
                break;
            case "byCuit":
                $sql = "select * from empresas where cuit='%s'";
                break;
            default:
                $sql = "select * from empresas";
                break;
        }
        $this->sql = $sql;
    }

}