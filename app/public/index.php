<?php

require_once '../srv/exceptionhandler.php';
require_once '../srv/wsaa.class.php';
//require_once '../srv/wsfe.class.php';
require_once '../srv/wsfev1.class.php';

$empresa = "CE";

/**********************
 * Ejemplo WSAA
 * ********************/

$wsaa = new WSAA('./', $empresa);


if( $wsaa->get_expiration() < date("Y-m-d h:m:i") ) {
  if ($wsaa->generar_TA()) {
    echo 'obtenido nuevo TA';  
  } else {
    echo 'error al obtener el TA';
  };
} else {
  $vto = $wsaa->get_expiration();
};



/**********************
 * Ejemplo WSFEv1
 * ********************
 */

$wsfev1 = new WSFEV1( './', $empresa );
