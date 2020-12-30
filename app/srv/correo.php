<?php

/**
 * Created by PhpStorm.
 * User: danielbertoni
 * Date: 24/10/2016
 * Time: 04:08 PM
 */
require_once "../../../include.inc";
require_once "../../../clases/sucursal.cmx.class.inc.php";
require_once "../../../clases/sucursaldao.cmx.class.inc.php";
require_once "../clases/MailEcomerce.php";
$codsuc = (isset($_POST['codsuc']))?$_POST['codsuc']:$_GET['codsuc'];
$empresa = (isset($_POST['empresa']))?$_POST['empresa']:$_GET['empresa'];
$empresa = ($codsuc==67)?"CE":$empresa;
$factura = (isset($_POST['factura']))?$_POST['factura']:$_GET['factura'];
$ntrack = (isset($_POST['ntrack']))?$_POST['ntrak']:$_GET['ntrack'];
$cliente = (isset($_POST['cliente']))?$_POST['cliente']:$_GET['cliente'];
$email = (isset($_POST['email']))?$_POST['email']:$_GET['email'];
$accion = (isset($_POST['accion']))?$_POST['accion']:$_GET['accion'];
$vale = (isset($_POST['vale']))?$_POST['vale']:$_GET['vale'];
$archivo = (isset($_POST['archivo']))?$_POST['archivo']:$_GET['archivo'];
$productos = array();
foreach ($_GET as $key=>$value){
    $key_aux=explode("_", $key);
    if(count($key_aux)>1){
        $sub1 = $key_aux[0];
        $sub2 = $key_aux[1];
        $productos[$sub2][$sub1] = $value;
    }
}
//print_r($productos);
//if($accion!=6) {
    $correo = new MailEcommerce();
    $correo->setEmpresa($empresa);
    $sucursalDAO = new SucursalDAO();
    $sucursal = $sucursalDAO->selectSucursalHogar($codsuc)->getSucursal() . " - " . $sucursalDAO->selectSucursalHogar($codsuc)->getDomicilio();
    $emailServicio = ($correo->getEmpresa() == "CE") ? "contactoweb@centroelectricosa.com.ar" : "contactoweb@casarizzi.com";
    $correo->setDireccionCorreoServicio($emailServicio);
    $correo->setNombreCorreoServicio("Centro Electrico");
    $correo->configuracion();
    $correo->setCliente($cliente);
    $correo->setCorreoCliente($email);
    $correo->setFactura($factura);
    $correo->setSucursal($sucursal);
    $correo->setArchivo($archivo);
    $correo->setNtrack($ntrack);
    $correo->setVale($vale);
    $correo->setProductos($productos);
    $fecha = new DateTime();
    $estado['asunto'] = ($correo->SubjectMail() === true) ? "OK" : "Error";
    $estado['cuerpo'] = $correo->BodyMail($accion);
    $estado['envio']  = $correo->Enviar();
    $estado['fecha']  = $fecha->format("dd/mm/yy HH:ss");
    print_r($estado);
//}