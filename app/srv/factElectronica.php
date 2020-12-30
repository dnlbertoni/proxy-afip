<?php
/**
 * Created by PhpStorm.
 * User: danielbertoni
 * Date: 12/01/2017
 * Time: 14:30
 */

require_once '../clases/exceptionhandler.php';
require_once '../clases/wsaa.class.php';
require_once '../clases/wsfev1.class.php';

/**
 * seteo parametros
 */



$entorno      = (isset($_GET['entorno']))?$_GET['entorno']:"produccion";
switch ($_GET['Cuit']){
    case 30565887617:
        $empresa="CE";
        break;
    case 30646941446:
        $empresa="CR";
        break;
    default:
        $empresa=false;
        break;
}
// $empresa='CR'; PARCHE DE SABADO A LA MAÑANA 10:12HS...  PARA QUE FUNCIONE...

//$empresa      = ($_GET['Cuit']==30565887617)?"CE":"CR";
$doctipo      =  (isset($_GET['DocTipo']))?$_GET['DocTipo']:null;
$docnro       =  (isset($_GET['DocNro']))?$_GET['DocNro']:null;
$cbtedesde    =  (isset($_GET['CbteDesde']))?$_GET['CbteDesde']:null;
$cbtehasta    =  (isset($_GET['CbteHasta']))?$_GET['CbteHasta']:null;
$fecha        =  (isset($_GET['CbteFch']))?$_GET['CbteFch']:null;
$total        =  (isset($_GET['ImpTotal']))?$_GET['ImpTotal']:null;
$imptotconc   =  (isset($_GET['ImpTotConc']))?$_GET['ImpTotConc']:null;
$neto         =  (isset($_GET['ImpNeto']))?$_GET['ImpNeto']:null;
$impOpEx      =  (isset($_GET['ImpOpex']))?$_GET['ImpOpex']:null;
$impTrib      =  (isset($_GET['ImpTrib']))?floatval($_GET['ImpTrib']):null;
$impIva       =  (isset($_GET['ImpIva']))?$_GET['ImpIva']:null;
$fchServDesde =  (isset($_GET['FchServDesde']))?$_GET['FchServDesde']:null;
$fchServHasta =  (isset($_GET['FchServHasta']))?$_GET['FchServHasta']:null;
$fchVtoPago   =  (isset($_GET['FchVtoPago']))?$_GET['FchVtoPago']:null;
$moneda       =  (isset($_GET['MonId']))?$_GET['MonId']:null;
$cotizacion   =  (isset($_GET['MonCotiz']))?$_GET['MonCotiz']:null;
$ivas=array();
$tribs=array();
foreach ($_GET as $index => $valor) {
    $aux=explode("_",$index);
    if($aux[0]=='Iva'){
        $sub=$aux[1]-1;
        $nombre=$aux[2];
        $ivas[$sub][$nombre]=$valor;
    }
    if($aux[0]=='Tributos'){
        $sub=$aux[1]-1;
        $nombre=$aux[2];
        $tribs[$sub][$nombre]=$valor;
    }
} ;

//var_dump($tribs);
$iva['AlicIva']          =  $ivas;
$tributos = $tribs;

//die();

$concepto     =  (isset($_GET['Concepto']))?$_GET['Concepto']:1;
$cantreg      =  (isset($_GET['CantReg']))?$_GET['CantReg']:0;
$CbteTipo     =  (isset($_GET['CbteTipo']))?intval($_GET['CbteTipo']):null;
$PtoVta       =  (isset($_GET['PtoVta']))?intval($_GET['PtoVta']):null;

/**********************
 * Ejemplo WSAA
 * ********************/
$wsaa = new WSAA('./',$empresa,$entorno);

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

$wsfev1 = new WSFEV1('./', $empresa,$entorno);

$wsfev1->openTA();
$wsfev1->setTipoCbte($CbteTipo);
$wsfev1->setPuntoVenta($PtoVta);

if($tok){
    if($wsfev1->getErrores()==0){
        switch (trim($_GET['Servicio'])){
            case "FECompUltimoAutorizado":
                $nro = $wsfev1->FECompUltimoAutorizado();
                $datos['Estado']   = ($nro['Errors']==0)?10:$nro['Errors'];
                $datos['Mensaje']  = ($datos['Estado']==10)?"OK":$nro['Msg'];
                $datos ['CbteNro'] = $nro['nro'];
                $datos ['Errors']  = $nro['Errors'];
                break;
            case "FECAESolicitar":
                $registro = $wsfev1->ArmoRegistro($doctipo, $docnro, $cbtedesde,$cbtehasta,$fecha,$total,$imptotconc,$neto,$impOpEx,$impTrib,$impIva,$fchServDesde,$fchServHasta,$fchVtoPago,$moneda,$cotizacion,$tributos,$iva,$concepto);
                $comprobante = $wsfev1->solicitarCAE($registro);
                /**
                 * verifico errores
                 */
                if(isset($comprobante->Errors)){
                    $datos['Estado']  = $comprobante->Errors->Err->Code;
                    $datos['Mensaje'] = $comprobante->Errors->Err->Msg;
                    $datos ['CAE']    = " ";
                    $datos ['CAEFchVto'] = " ";
                    $datos ['Errors'] = count($comprobante->Errors);
                }else{
                    /**
                     * verifico observaciones
                     */
                    if(isset($comprobante->FeDetResp->FECAEDetResponse->Observaciones->Obs)){
                        if(count($comprobante->FeDetResp->FECAEDetResponse->Observaciones->Obs)>1){
                            foreach ($comprobante->FeDetResp->FECAEDetResponse->Observaciones->Obs as $key=>$obs){
                                if($key==0){
                                    $textoEstado  =$obs->Code;
                                    $textoMensaje =$obs->Msg;
                                }
                            }
                        }else{
                            $textoEstado=$comprobante->FeDetResp->FECAEDetResponse->Observaciones->Obs->Code;
                            $textoMensaje=$comprobante->FeDetResp->FECAEDetResponse->Observaciones->Obs->Msg;
                        }
                        $datos['Estado']  = $textoEstado;
                        $datos['Mensaje'] = $textoMensaje;
                        $datos ['CAE']    = " ";
                        $datos ['CAEFchVto'] = " ";
                        $datos ['Observaciones'] = count($comprobante->FeDetResp->FECAEDetResponse->Observaciones->Obs);
                    }else{
                        $datos['Estado']  = 10;
                        $datos['Mensaje'] = "OK";
                        $datos ['CAE']    = $comprobante->FeDetResp->FECAEDetResponse->CAE;
                        $datos ['CAEFchVto'] = $comprobante->FeDetResp->FECAEDetResponse->CAEFchVto;
                        $datos ['Errors'] = count($comprobante->Errors);
                    }
                }

                break;
        }
        $nro = $wsfev1->FECompUltimoAutorizado();
    }else{
        $datos['Estado']=$wsfev1->getErrores();
        $datos['Mensaje']="TimeOut Afip";
        $datos ['CbteNro'] = 0;
        $datos['Errors']=$wsfev1->getErrores();
    }
}else{
    $datos['Estado']=1000;
    $datos['Mensaje']="Error Token";
    $datos ['CbteNro'] = 0;
    $datos['Errors']=" ";
}
$datos['Mensaje'] = substr($datos['Mensaje'],0,69);
header('Content-Type: application/json');
echo json_encode($datos);
