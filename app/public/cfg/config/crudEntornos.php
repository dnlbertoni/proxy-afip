<?php
require_once ("../../lib/mysql/mysql.class.php");
require_once ("../../modelos/Entornos.php");

/** parseo los inputs */
$accion = $_POST['accion'];
$entorno = new \Config\Entorno();

$entorno->setId(isset($_POST['id'])?$_POST['id']:null);
$entorno->setNombre(isset($_POST['nombre'])?$_POST['nombre']:null);
$entorno->setIdempresa(isset($_POST['idempresa'])?$_POST['idempresa']:null);
$entorno->setDebugActivo(isset($_POST['debug_activo'])?$_POST['debug_activo']:null);
$entorno->setActual(isset($_POST['actual'])?$_POST['actual']:null);

$entornos = new \Config\Entornos();

$data['res']['code']=99;
$data['res']['message']='Sin Accion definida';

switch ($accion){
    case 'add':
        $id=$entornos->insert($entorno);
        if($id){
            $data['res']['code']=10;
            $data['res']['message']=$id;
        }else{
            $data['res']['code']=11;
            $data['res']['message']='Nose pudo Insertar';
        }
        break;
    case "edit":
        $id=$entornos->update($entorno);
        if($id){
            $data['res']['code']=10;
            $data['res']['message']=$id;
        }else{
            $data['res']['code']=11;
            $data['res']['message']='No se pudo borrar';
        }
        break;
    case "del":
        $id=$entornos->delete($entorno);
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