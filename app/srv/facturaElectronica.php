<?php

require_once '../clases/exceptionhandler.php';
require_once '../clases/wsaa.class.php';
require_once '../clases/wsfe.class.php';
require_once '../clases/wsfev1.class.php';

$empresa = "CE";
/**********************
 * Ejemplo WSAA
 * ********************/

$wsaa = new WSAA('./', $empresa);


if($wsaa->get_expiration() < date("Y-m-d h:m:i")) {
  if ($wsaa->generar_TA()) {
    echo 'obtenido nuevo TA';  
  } else {
    echo 'error al obtener el TA';
  }
} else {
  $vto = $wsaa->get_expiration();
};



/**********************
 * Ejemplo WSFEv1
 * ********************
 */

$wsfev1 = new WSFEV1('./', $empresa);

 
// Carga el archivo TA.xml
$wsfev1->openTA();
$wsfev1->setTipoCbte(6);
$wsfev1->setPuntoVenta(2);
//echo $wsfe->cantidadComprobantes();
//var_dump($wsfe->getFunciones());
//var_dump($wsfev1->getTipos());
//var_dump($wsfev1->Dummy());
$nro = $wsfev1->FECompUltimoAutorizado();
/*
echo $wsfev1->getTiposDoc();
echo $wsfev1->getTiposComprobantes();
echo $wsfev1->getTiposMonedas();
echo $wsfev1->getTiposIva();
echo $wsfev1->getTiposTributos();

echo $wsfev1->getPuntosVentaHabilitados();
*/
//die();

/*
// devuelve el cae
//$ptovta = 1;
//$tipocbte = 1;
                   
// registro con los datos de la factura
/*
$regfac['tipo_doc'] = 80;
$regfac['nro_doc'] = 23111111112;
$regfac['imp_total'] = 121.67;
$regfac['imp_tot_conc'] = 0;
$regfac['imp_neto'] = 100.55;
$regfac['impto_liq'] = 21.12;
$regfac['impto_liq_rni'] = 0.0;
$regfac['imp_op_ex'] = 0.0;
$regfac['fecha_venc_pago'] = date('Ymd');

//$ultimo = $wsfev1->FECompUltimoAutorizado();
$result = $wsfev1->getTipos();
for($i=2;$i<17;$i++){
    var_dump($result[$i]);
    echo "<br/>";
}
*/
$regfac[0]['Concepto']     = 1; //producto
$regfac[0]['DocTipo']      = 01 ; //cuit
$regfac[0]['DocNro']       = 26866703; //cuit
$regfac[0]['CbteDesde']    = $nro['nro'] + 1;
$regfac[0]['CbteHasta']    = $nro['nro'] + 1;
$regfac[0]['CbteFch']      = date('Ymd');
$regfac[0]['ImpTotal']     = 121.67;
$regfac[0]['ImpTotConc']   = 0;
$regfac[0]['ImpNeto']      = 100.55 ;
$regfac[0]['ImpOpEx']      = 0.0;
$regfac[0]['ImpTrib']      = 0.0;
$regfac[0]['ImpIVA']       = 21.12;
$regfac[0]['FchServDesd']  = '';
$regfac[0]['FchServHasta'] = '';
$regfac[0]['FchVtoPago']   = '';
$regfac[0]['MonId']        = 'PES';
$regfac[0]['MonCotiz']     = 1;
//$regfac[0]['CbtesAsoc']    = array();
//$regfac[0]['Tributos']     = array(0);
$regfac[0]['Iva']          = array( array(  "Id"       => 5, "BaseImp"  => 100.55,    "Importe"  => 21.12) );
//$regfac[0]['Opcionales']   = array();
/*
$nro=0;
if($nro == false) echo "errorr ultimo Numero <br/>";

$cmp = $ultimo['nro'];
if($cmp == false) echo "error comprobante <br/>";
*/
//echo $wsfev1->getTiposIva();

$cae = $wsfev1->solicitarCAE($regfac);
if($cae == false) echo "error Cae";

print_r($cae);

?>