<?php
require_once ("../../lib/mysql/mysql.class.php");
require_once ("../../modelos/Parametros.php");

/** parseo los inputs */
$accion = $_POST['accion'];
$parametro = new \Config\Parametro();

$parametro->setId(isset($_POST['id'])?$_POST['id']:null);
$parametro->setNombre(isset($_POST['nombre'])?$_POST['nombre']:null);
$parametro->setValor(isset($_POST['valor'])?$_POST['valor']:null);

$parametros= new \Config\Parametros();

$data['res']['code']=99;
$data['res']['message']='Sin Accion definida';

switch ($accion){
    case 'add':
        $id=$parametros->insert($parametro);
        if($id){
            $data['res']['code']=10;
            $data['res']['message']=$id;
        }else{
            $data['res']['code']=11;
            $data['res']['message']='Nose pudo Insertar';
        }
        break;
    case "edit":
        $id=$parametros->update($parametro);
        if($id){
            $data['res']['code']=10;
            $data['res']['message']=$id;
        }else{
            $data['res']['code']=11;
            $data['res']['message']='No se pudo borrar';
        }
        break;
    case "del":
        $id=$parametros->delete($parametro);
        if($id){
            $data['res']['code']=10;
            $data['res']['message']=$id;
        }else{
            $data['res']['code']=11;
            $data['res']['message']='No se pudo actualizar';
        }
        break;
}
header('Content-Type: application/json');
echo json_encode($data);