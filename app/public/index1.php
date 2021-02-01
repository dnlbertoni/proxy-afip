<?php
require_once '../conf/include.all.php';
//require_once '../srv/exceptionhandler.php';
require_once '../srv/wsbasic.class.php';
require_once '../srv/wsaa.class.php';
require_once '../srv/wsfev1.class.php';

$empresa = "20268667033";

/**********************
 * Ejemplo WSAA
 * ********************/
/*
$wsaa = new WSAA($empresa, 'wsfe');

$rs=$wsaa->Token();
var_dump($rs);
*/

/**********************
 * Ejemplo WSFEv1
 * ********************
 */
$wsfev1 = new WSFEV1($empresa);
$rs =$wsfev1->solicitarCAE(1,6);
header('Content-Type: application/json');
echo json_encode($rs);

