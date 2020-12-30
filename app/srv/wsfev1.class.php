<?php
require_once ("../modelos/Empresas.php");
require_once ("../modelos/Servicios.php");
require_once ("../modelos/Entornos.php");
require_once ("../modelos/Certificados.php");

class WSFEV1 {
  //const CUITDNL = "20268667033";              # C U I T del emisor de las facturas dnl
  //const CUITMD = "20216979169";               # C U I T del emisor de las facturas miguel

    /*
    * manejo de errores
    */
    public $error = '';

    /**
    * Cliente SOAP
    */
    private $client;

    private $empresa;
    private $certificadoAFIP;
    private $certificadoLocal;
    private $entorno;
    private $passphrase;
    private $LOG_XMLS;
    private $TA;

  /*
   * Constructor
   */
  public function __construct($cuit) {

      $this->Archivos = new ArrayObject();
      $pathCert       = __DIR__ ."../data/cert/";
      $pathXML        = __DIR__ ."../data/xml/";
      $pathDebug      = __DIR__ ."../data/debug/";

      if($cuit !=null) {
          /*** levanto los modelos ***/
          $empresas = new \Empresa\Empresas();
          $entornos = new \Config\Entornos();
          $certificados = new \Servicio\Certificados();
          $servicios = new \Servicio\Servicios();

          $this->empresa = $empresas->getByCuit($cuit);
          $this->entorno = $entornos->getActual($this->empresa->getId());
          $this->LOG_XMLS = ($this->entorno->getDebugActivo()==1);
          $this->servicio = $servicios->getByName('wsfev1');
          // seteos en php
          ini_set("soap.wsdl_cache_enabled", "0");
          ini_set("default_socket_timeout", 120);

          if (is_object($this->empresa)) {
              $this->certificadoAFIP = $certificados->getCertificadoEntorno($entornos->getActual()->getId(), 'CERT', $this->empresa->getId());
              $this->certificadoLocal = $certificados->getCertificadoEntorno($entornos->getActual()->getId(), 'PRIVATEKEY', $this->empresa->getId());
              $this->passphrase = $this->certificadoLocal->getPasswordCErtificado();
              /**** defino los nombres de los archivos en funcion de la configuracion ***/
              $this->Archivos->append(array('cert'       => $pathCert.$this->certificadoAFIP->getFilename()));
              $this->Archivos->append(array('privatekey' => $pathCert.$this->certificadoLocal->getFilename()));
              $this->Archivos->append(array('wdsl'       => $this->servicio->getFileWsdl()));
              $this->Archivos->append(array('ta'         => $pathXML."TA_".$this->entorno->getNombre().$this->empresa->getCuit().".xml"));
              $this->Archivos->append(array('tra'        => $pathXML."TRA_".$this->entorno->getNombre().$this->empresa->getCuit().".xml"));
              $this->Archivos->append(array('debugOUT'   => $pathDebug."request-%s".$this->entorno->getNombre().$this->empresa->getCuit().".xml"));
              $this->Archivos->append(array('debugIN'    => $pathDebug."response-%s".$this->entorno->getNombre().$this->empresa->getCuit().".xml"));
              // validar archivos necesarios
              if (!file_exists($this->Archivos['cert'])) $this->error .= " Error de Apertura ". $this->Archivos['cert'];
              if (!file_exists($this->Archivos['privatekey'])) $this->error .= " Error de Apertura ".$this->Archivos['privatekey'];
              if (!file_exists($this->Archivos['wsdl'])) $this->error .= " Error de Apertura ".$this->Archivos['wsdl'];
              if ( $this->servicio->getUrl()=='') $this->error .= "Soap url not found";


              while (!empty($this->error)) {
                  try {
                      $this->client = new SoapClient($this->WSDL, array(
                              'soap_version' => SOAP_1_2,
                              'location' => $this->servicio->getUrl(),
                              'exceptions' => 0,
                              'trace' => 1,
                              'connection_timeout' => 60
                          )
                      );
                  } catch (Exception $e) {
                      trigger_error("Error occured while connection soap client<br />" . $e->getMessage(), E_USER_ERROR);
                      //sleep(60);
                      $this->error = $e->getMessage();
                      break;
                  }
                  if ($this->client) {
                      break;
                  }
              }
          }
      }
  }

  public function getErrores(){
      return $this->error;
  }

    /**
     * Chequea los errores en la operacion, si encuentra algun error falta lanza una exepcion
     * si encuentra un error no fatal, loguea lo que paso en $this->error
     * @param $results
     * @param $method
     * @return bool
     * @throws Exception
     */
  private function _checkErrors($results, $method){
    if ($this->LOG_XMLS) {
      file_put_contents(sprintf($this->Archivos['debugOUT'],$method),$this->client->__getLastRequest());
      file_put_contents(sprintf($this->Archivos['debugIN'],$method),$this->client->__getLastResponse());
    }
    
    if (is_soap_fault($results)) {
      throw new Exception('WSFEV1 class. Error Cadena: ' . $results->faultcode.' '.$results->faultstring);
    }

    $XXX=$method.'Result';
    if ($results->$XXX->RError->percode != 0) {
        $this->error = "Method=$method errcode=".$results->$XXX->RError->percode." errmsg=".$results->$XXX->RError->perrmsg;
    }
    
    return $results->$XXX->RError->percode != 0 ? true : false;
  }

  /**
   * Abre el archivo de TA xml,
   * si hay algun problema devuelve false
   */
  public function openTA()
  {
    $this->TA = simplexml_load_file($this->Archivos['ta']);
    
    return $this->TA == false ? false : true;
  }

  /****************** a partir de ahora son el parseo de cada metodo del WS **********************/


  /**
     * Retorna la cantidad maxima de registros de detalle que
     * puede tener una invocacion al FEAutorizarRequest
     */
  public function Dummy()
    {
        $results = $this->client->FEDummy();
        try {
            $e = $this->_checkErrors($results, 'FEDummy');
        } catch (Exception $e) {
        }
        return array('App' => $results->FEDummyResult->AppServer, 'DB' => $results->FEDummyResult->DbServer, 'Auth' => $results->FEDummyResult->AuthServer);
    }

  /*
   * Retorna el ultimo comprobante autorizado para el tipo de comprobante /cuit / punto de venta ingresado.
   */ 
  public function FECompUltimoAutorizado(){
        $results = $this->client->FECompUltimoAutorizado(
            array(  'Auth'     =>  array(   'Token'    => $this->TA->credentials->token,
                                            'Sign'     => $this->TA->credentials->sign,
                                            'Cuit'     => $this->empresa->getCuit()
                                    ),
                    'PtoVta'    => $this->getPuntoVenta(),
                    'CbteTipo'  => $this->getTipoCbte()
            )
        );

        $this->_checkErrors($results, 'FECompUltimoAutorizado');
        $resultado =
                array(  "nro"   =>  $results->FECompUltimoAutorizadoResult->CbteNro,
                        "tipo"  =>  $results->FECompUltimoAutorizadoResult->CbteTipo,
                        "ptoVta"=>  $results->FECompUltimoAutorizadoResult->PtoVta );

        if(isset($results->FECompUltimoAutorizadoResult->Errors)){
            $resultado['Errors'] = intval($results->FECompUltimoAutorizadoResult->Errors->Err->Code);
            $resultado['Msg']    = $results->FECompUltimoAutorizadoResult->Errors->Err->Msg;
        }else{
            $resultado['Errors']=0;
        }
        return $resultado;
  }

    // Dado un lote de comprobantes retorna el mismo autorizado con el CAE otorgado.
    public function solicitarCAEA(){ // solo valido para lote de factura y debe venir con fechas
        $ano = 2017;
        $mes = 01;
        $dia = 20;
        $quincena = ($dia <= 15)?1:2;

        $results = $this->client->FECAEASolicitar(
            array(  'Auth'     =>  array(   'Token'    => $this->TA->credentials->token,
                'Sign'     => $this->TA->credentials->sign,
                'Cuit'     => $this->empresa->getCuit()
            ),
                'Periodo'    => ($ano *100) + $mes,
                'Orden'  => $quincena
            )
        );

        $e = $this->_checkErrors($results, 'FECAEASolicitar');
        //echo $e;
        $X=$results->FECAEASolicitarResult;
        var_dump($X);
        //return $this->TipostoTable("Monedas",$X->ResultGet->Moneda );
        return true;
    }
    public function solicitarCAE($registros){
        $i=0;
        $FeDetReq = array();
        foreach ($registros as $reg){
            foreach ($reg as $key=>$value){
                $FeDetReq[$i][$key] =  $value;
            }
            $i++;
        }
        $results = $this->client->FECAESolicitar(
            array(  'Auth'     =>  array(   'Token'    => $this->TA->credentials->token,
                                            'Sign'     => $this->TA->credentials->sign,
                                            'Cuit'     => $this->empresa->getCuit()
            ),
                'FeCAEReq' => array( "FeCabReq" => array(   'CantReg'  => count($FeDetReq),
                    'PtoVta'   => $this->getPuntoVenta(),
                    'CbteTipo' => $this->getTipoCbte()
                ),
                    "FeDetReq" => $FeDetReq
                )
            )
        );
        $e = $this->_checkErrors($results, 'FECAESolicitar');
        //var_dump($e);

        return $results->FECAESolicitarResult;
    }
    public function getInfo(){

        echo $this->getPuntosVentaHabilitados();
        echo "<br />";
        echo $this->getTiposComprobantes();
        echo "<br />";
        echo $this->getTiposIva();
        echo "<br />";
        echo $this->getTiposTributos();
        echo "<br />";
    }
  /*
   * Obtiene los tipos de Comprobantes
   */
  private function getTiposComprobantes(){

    $results = $this->client->FEParamGetTiposCbte(
        array(  'Auth'     =>  array(   'Token'    => $this->TA->credentials->token,
                                        'Sign'     => $this->TA->credentials->sign,
                                        'Cuit'     => $this->empresa->getCuit()
                                    )
            )
    );
    
    $e =  $this->_checkErrors($results, 'FEParamGetTiposCbte');
    //echo $e;
    $X=$results->FEParamGetTiposCbteResult;
    return $this->TipostoTable("Comprobantes",$X->ResultGet->CbteTipo );
  }
    /*
     * Obtiene los tipos de Monedas
     */
    private function getTiposMonedas(){
        $results = $this->client->FEParamGetTiposMonedas(
            array(  'Auth'     =>  array(   'Token'    => $this->TA->credentials->token,
                                            'Sign'     => $this->TA->credentials->sign,
                                            'Cuit'     => $this->empresa->getCuit()
                                        )
            )
        );

        $e =  $this->_checkErrors($results, 'FEParamGetTiposMonedas');
        //echo $e;
        $X=$results->FEParamGetTiposMonedasResult;
        return $this->TipostoTable("Monedas",$X->ResultGet->Moneda );
    }
    /*
     * Obtiene los tipos de IVA
     */
    private function getTiposIva(){

        $results = $this->client->FEParamGetTiposIva(
            array(  'Auth'     =>  array(   'Token'    => $this->TA->credentials->token,
                                            'Sign'     => $this->TA->credentials->sign,
                                            'Cuit'     => $this->empresa->getCuit()
                                        )
                )
        );

        $e =  $this->_checkErrors($results, 'FEParamGetTiposIva');
        //echo $e;
        $X=$results->FEParamGetTiposIvaResult;

        return $this->TipostoTable("IVA",$X->ResultGet->IvaTipo );
    }
    /*
     * Obtiene los puntos de ventas habilitados
     */
    private function getPuntosVentaHabilitados(){

        $results = $this->client->FEParamGetPtosVenta(
            array(  'Auth'     =>  array(   'Token'    => $this->TA->credentials->token,
                                            'Sign'     => $this->TA->credentials->sign,
                                            'Cuit'     => $this->empresa->getCuit()
                                        )
                )
        );

        $e =  $this->_checkErrors($results, 'FEParamGetPtosVenta');
        //echo $e;
        $X=$results->FEParamGetPtosVentaResult;
        //var_dump($X);
        return $this->TipostoTable("PtosVtas habilitados",$X->FEParamGetPtosVentaResult );
        //return true;
    }
    /*
     * Obtiene los tipos de Documentos
     */
    private function getTiposDoc(){
        $results = $this->client->FEParamGetTiposDoc(
            array(  'Auth'     =>  array(   'Token'    => $this->TA->credentials->token,
                                            'Sign'     => $this->TA->credentials->sign,
                                            'Cuit'     => $this->empresa->getCuit()
                                        )
                )
        );

        $e =  $this->_checkErrors($results, 'FEParamGetTiposDoc');
        $X=$results->FEParamGetTiposDocResult;
        return $this->TipostoTable("Documentos",$X->ResultGet->DocTipo );
    }

    /*
     * Obtiene los tipos de Documentos
     */
    private function getTiposTributos(){
        $results = $this->client->FEParamGetTiposTributos(
            array(  'Auth'     =>  array(   'Token'    => $this->TA->credentials->token,
                                            'Sign'     => $this->TA->credentials->sign,
                                            'Cuit'     => $this->empresa->getCuit()
                                        )
                )
        );

        $e =  $this->_checkErrors($results, 'FEParamGetTiposTributos');
        $X=$results->FEParamGetTiposTributosResult;
        //var_dump($X);
        return $this->TipostoTable("Tributos",$X->ResultGet->TributoTipo );
    }

    /**
     * Setea el tipo de comprobante
     * A = 1
     * B = 6
     * @param $tipo
     * @return bool
     */
  public function setTipoCbte($tipo) 
  {
    switch($tipo) {
      case 'a': case 'A': case '1':
        $this->tipo_cbte = 1;
      break;
      
      case 'b': case 'B': case 'c': case 'C': case '6':
        $this->tipo_cbte = 6;
      break;
      
      default:
          $this->tipo_cbte = $tipo;
    }

    return true;
  }

    /**
     * @return mixed
     */
  public function getTipoCbte(){
        return $this->tipo_cbte;
    }
  public function getFunciones(){
        return $this->client->__getFunctions();
    }
  public function getTipos($funcion=false){
        if($funcion){
            $e = $this->client->__getTypes();
            return $e->{$funcion};
        }else{
            return $this->client->__getTypes();
        }
    }
  /**
     * @return string
     */
  public function getPuntoVenta(){
      return $this->punto_venta;
  }

    /**
     * @param string $punto_venta
     */
  public function setPuntoVenta($punto_venta){
        $this->punto_venta = $punto_venta;
    }

  private function TipostoTable($titulo, $objeto){
        $Texto  ="<table><thead><tr><th colspan='4'>Tipos ".$titulo."</th></tr>";
        $Texto .="<tr><th>Id</th><th>Desc</th><th>FchDesde</th><th>FchHasta</th></tr></thead><tbody>";
        foreach ($objeto AS $Y) {
            $Texto .="<tr><td>".$Y->Id."</td><td>".$Y->Desc."</td><td>".$Y->FchDesde."</td><td>".$Y->FchHasta."</td></tr>";
        }
        $Texto .= "</tbody></table>";
        return $Texto;
    }


  public function ArmoRegistro($doctipo,$docnro,$desdeComprobante,
                               $hastaComprobante,                               $fecha,
                               $total,                               $imptotconc,
                               $neto,                               $impOpEx,
                               $impTrib,                               $impIva,
                               $fchServDesde,                               $fchServHasta,
                               $fchVtoPago,                               $moneda,
                               $cotizacion,                               $tributos,
                               $iva,                               $concepto=1){
        $regfac[0]['Concepto']     = $concepto; // 1 - producto
        $regfac[0]['DocTipo']      = $doctipo;
        $regfac[0]['DocNro']       = $docnro;
        $regfac[0]['CbteDesde']    = $desdeComprobante;
        $regfac[0]['CbteHasta']    = $hastaComprobante;
        $regfac[0]['CbteFch']      = $fecha;
        $regfac[0]['ImpTotal']     = $total;
        $regfac[0]['ImpTotConc']   = $imptotconc;
        $regfac[0]['ImpNeto']      = $neto;
        $regfac[0]['ImpOpEx']      = $impOpEx;
        $regfac[0]['ImpTrib']      = $impTrib;
        $regfac[0]['ImpIVA']       = $impIva;
        $regfac[0]['FchServDesd']  = $fchServDesde;
        $regfac[0]['FchServHasta'] = $fchServHasta;
        $regfac[0]['FchVtoPago']   = $fchVtoPago;
        $regfac[0]['MonId']        = $moneda;
        $regfac[0]['MonCotiz']     = $cotizacion;
        //$regfac[0]['CbtesAsoc']  = array();
        if ($impTrib!=0){
            $regfac[0]['Tributos']     = $tributos; //tributos
        }
        $regfac[0]['Iva']          = $iva; // array con nombres especificos
        return $regfac;
    }
} // END class