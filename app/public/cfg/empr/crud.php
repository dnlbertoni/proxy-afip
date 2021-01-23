<?php
require_once ("../../lib/mysql/mysql.class.php");
require_once ("../../modelos/Empresas.php");

/** parseo los inputs */
$accion = $_POST['accion'];
$empresa = new \Empresa\Empresa();
$empresa->setId(isset($_POST['id'])?$_POST['id']:null);
$empresa->setRazonSocial(isset($_POST['razon_social'])?$_POST['razon_social']:null);
$empresa->setCuit(isset($_POST['cuit'])?$_POST['cuit']:null);
$empresa->setActivo(isset($_POST['activo'])?$_POST['activo']:null);

$empresas = new \Empresa\Empresas();

$data['res']['code']=99;
$data['res']['message']='Sin Accion definida';

switch ($accion){
    case 'add':
        $id=$empresas->insert($empresa);
        if($id){
            $data['res']['code']=10;
            $data['res']['message']=$id;
        }else{
            $data['res']['code']=11;
            $data['res']['message']='Nose pudo Insertar';
        }
        break;
    case "edit":
        $id=$empresas->update($empresa);
        if($id){
            $data['res']['code']=10;
            $data['res']['message']=$id;
        }else{
            $data['res']['code']=11;
            $data['res']['message']='No se pudo actualizar';
        }
        break;
    case "del":
        $id=$empresas->delete($empresa);
        if($id){
            $data['res']['code']=10;
            $data['res']['message']=$id;
        }else{
            $data['res']['code']=11;
            $data['res']['message']='No se pudo borrar';
        }
        break;
}
header('Content-Type: application/json');
echo json_encode($data);