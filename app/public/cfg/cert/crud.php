<?php
    require_once '../../../conf/include.all.php';
    require_once ("../../../modelos/Certificados.php");

/** parseo los inputs */
$accion = $_POST['accion'];
$certificado = new Certificado();
$certificado->setId((isset($_POST[id])?$_POST['id']:null));
$certificado->setIdentorno((isset($_POST['identorno'])?$_POST['identorno']:null));
$certificado->setIdempresa((isset($_POST['idempresa'])?$_POST['idempresa']:null));
$certificado->setFilename((isset($_POST['filename'])?$_POST['filename']:null));
$certificado->setTipo((isset($_POST['tipo'])?$_POST['tipo']:null));
$certificado->setPasswordCertificado((isset($_POST['password_certificado'])?$_POST['password_certificado']:null));
$certificado->setActivo((isset($_POST['activo'])?$_POST['activo']:null));
$certificado->setFechaemision((isset($_POST['fechaemision'])?$_POST['fechaemision']:null));
$certificado->setFechavencimiento((isset($_POST['fechavencimiento'])?$_POST['fechavencimiento']:null));
$certificado->setCertificadoRaw((isset($_POST['certificado_raw'])?$_POST['certificado_raw']:null));

$certificados = new Certificados();

$data['res']['code']=99;
$data['res']['message']='Sin Accion definida';

switch ($accion){
    case 'add':
        $id=$certificados->insert($certificado);
        if($id){
            $data['res']['code']=10;
            $data['res']['message']=$id;
        }else{
            $data['res']['code']=11;
            $data['res']['message']='Nose pudo Insertar';
        }
        break;
    case "edit":
        $id=$certificados->update($certificado);
        if($id){
            $data['res']['code']=10;
            $data['res']['message']=$id;
        }else{
            $data['res']['code']=11;
            $data['res']['message']='No se pudo actualizar';
        }
        break;
    case "edit":
        $id=$certificados->update($certificado);
        if($id){
            $data['res']['code']=10;
            $data['res']['message']=$id;
        }else{
            $data['res']['code']=11;
            $data['res']['message']='No se pudo eliminar';
        }
        break;
    default
        $data['res']['code']=12;
        $data['res']['message']='Accion no definida';
        break;
}
header('Content-Type: application/json');
echo json_encode($data);