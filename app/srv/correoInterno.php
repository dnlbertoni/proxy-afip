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
require_once "../clases/mail.php";
$email = (isset($_POST['email']))?$_POST['email']:$_GET['email'];
$asunto = (isset($_POST['asunto']))?$_POST['asunto']:$_GET['asunto'];
$mensaje = (isset($_POST['mensaje']))?$_POST['mensaje']:$_GET['mensaje'];
$accion = (isset($_POST['accion']))?$_POST['accion']:$_GET['accion'];
$mensaje = str_replace("_", " ", $mensaje);
$asunto = str_replace("_", " ", $asunto);
$correo = new Mail();
$emailServicio = "notificaciones@dilfer.com.ar";
$correo->setDireccionCorreoServicio($emailServicio);
$correo->setNombreCorreoServicio("Notificacoines del Sistemas");
$correo->setAsunto($asunto);
$resultado=$correo->enviarMailBasico($email,$mensaje);
$fecha = new DateTime();

$estado['asunto'] = $correo->getAsunto();
$estado['cuerpo'] = $mensaje;
$estado['mail']  = $resultado;
$estado['fecha']  = $fecha->format("dd/mm/yy HH:ss");
header("Content-type:application/json");
echo json_encode($estado);
