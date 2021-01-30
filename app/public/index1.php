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
$wsfev1 = new WsBasic($empresa, 'wsfev1');
$rs =$wsfev1->Dummy();
var_dump($rs);

