<?php
/**
 * Created by PhpStorm.
 * User: Danielito
 * Date: 17/10/2017
 * Time: 12:00
 */


require_once (__DIR__).'/../clases/exceptionhandler.php';
require_once (__DIR__).'/../clases/wsaa.class.php';
require_once (__DIR__).'/../clases/wsfev1.class.php';

/**********************
 * Ejemplo WSAA
 * ********************/
$wsaa = new WSAA('./',$empresa);

if($wsaa->get_expiration() < date("Y-m-d h:m:i")) {
    if ($wsaa->generar_TA()) {
        $tok= true;
    } else {
        $tok= false;
    }
} else {
    $vto = $wsaa->get_expiration();
    $tok= true;
};

$wsfev1 = new WSFEV1('./', $empresa);

if($tok){

    $wsfev1->openTA();
    $wsfev1->getInfo();
    //$wsfev1->getPuntosVentaHabilitados();
}

