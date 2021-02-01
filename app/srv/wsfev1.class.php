<?php
require_once ("../modelos/Empresas.php");
require_once ("../modelos/Servicios.php");
require_once ("../modelos/Entornos.php");
require_once ("../modelos/Certificados.php");
require_once ("wsaa.class.php");

class WsFEv1 {
    /*
    * manejo de errores
    */
    public $error = array();

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
    private $wsaa;
    private $registroComprobante=array();

	/*
	* Constructor
	*/
	public function __construct($cuit=null, $servicioDeNegocio='wsfev1') {
	  $this->service = $servicioDeNegocio;
	  $this->Archivos = new ArrayObject();
	  $pathCert       = __DIR__ ."/../data/cert/";
	  $pathXML        = __DIR__ ."/../data/xml/";
	  $pathDebug      = __DIR__ ."/../data/debug/";
	  $pathWsdl       = __DIR__ ."/../data/wsdl/";


	  if($cuit !=null){
	      /*** levanto los modelos ***/
	      $empresas = new \Empresa\Empresas();
	      $entornos = new \Config\Entornos();
	      $certificados = new \Certificado\Certificados();
	      $servicios = new \Servicio\Servicios();

	      $this->empresa=$empresas->getByCuit($cuit);
	      $this->servicio = $servicios->getByName($this->service);
	      // seteos en php
	      ini_set("soap.wsdl_cache_enabled", "0");
	      ini_set('soap.wsdl_cache_ttl',0);
	      ini_set("default_socket_timeout", 120);

	      /*** WS para loguear ***/
	      $this->wsaa = new WSAA($cuit, $this->service);
	      
	      if(is_object($this->servicio)){
	        if(is_object($this->empresa)){
	            // Busco el entorno activo que tiene la empresa
	            $this->entorno=$entornos->getActual($this->empresa->getId());
	            if(is_object($this->entorno)){
	              // seteo el directorio de XML para loguear
	              $this->LOG_XMLS = ($this->entorno->getDebugActivo()==1);
	              // busco los datos cargados del certificado generado en la afip
	              $this->certificadoAFIP = $certificados->getCertificadoEntorno( $this->entorno->getId(),'CERT', $this->empresa->getId());
	              if(is_object($this->certificadoAFIP)){
	                //busco el certificado local, el que se subio a la afip para generar
	                $this->certificadoLocal = $certificados->getCertificadoEntorno( $this->entorno->getId(),'PRIVATEKEY', $this->empresa->getId());

	                if (is_object($this->certificadoLocal)){
	                  $this->passphrase = $this->certificadoLocal->getPasswordCertificado();

	                  /**** defino los nombres de los archivos en funcion de la configuracion ***/
	                  $this->Archivos = array('cert'       => $pathCert . $this->certificadoAFIP->getFilename(),
	                                          'privatekey' => $pathCert . $this->certificadoLocal->getFilename(),
	                                          'wsdl'       => $pathWsdl . $this->servicio->getFileWsdl(),
	                                          'ta'         => $pathXML."TA_".$this->entorno->getNombre().$this->empresa->getCuit().".xml",
	                                          'tra'        => $pathXML."TRA_".$this->entorno->getNombre().$this->empresa->getCuit().".xml",
	                                          'traTMP'     => $pathXML."TRA_".$this->entorno->getNombre().$this->empresa->getCuit().".tmp",
	                                          'debugOUT'   => $pathDebug."request-".$this->service."_".$this->entorno->getNombre().$this->empresa->getCuit().".xml",
	                                          'debugIN'    => $pathDebug."response-".$this->service."_".$this->entorno->getNombre().$this->empresa->getCuit().".xml");

	                  // validar archivos necesarios
	                  if (!file_exists($this->Archivos['cert'])){
	                    $this->error['ErrorCode']    = 1007; // 
	                    $this->error['ErrorMessage'] = " Error de Apertura ". $this->Archivos['cert'];
	                  } else{
	                    if (!file_exists($this->Archivos['privatekey'])){
	                      $this->error['ErrorCode']    = 1008; // 
	                      $this->error['ErrorMessage'] = " Error de Apertura ".$this->Archivos['privatekey'];
	                    } else{
	                      if (!file_exists($this->Archivos['wsdl'])){                         
	                        $this->error['ErrorCode']    = 1009; // 
	                        $this->error['ErrorMessage'] = " Error de Apertura ->". $this->Archivos['wsdl'];
	                      } else{
	                        $this->client = new SoapClient($this->Archivos['wsdl'] , array(
	                                'soap_version'   => SOAP_1_2,
	                                'location'       => $this->servicio->getUrl(),
	                                'trace'          => 1,
	                                'exceptions'     => 0
	                            )
	                        );                                      
	                      }
	                    }
	                  }
	                }else{
	                  $this->error['ErrorCode']=1006; // 
	                  $this->error['ErrorMessage']= 'El certificado Local no esta configurado para esa empresa y entorno';                  
	                }
	              }else{
	                $this->error['ErrorCode']=1005; // 
	                $this->error['ErrorMessage']= 'El certificado de la AFIP no esta configurado para esa empresa y entorno';                  
	              };
	            }else{
	              $this->error['ErrorCode']=1004; // 
	              $this->error['ErrorMessage']= 'Entornos nos configurados para esa Empresa';
	            };
	        }else{
	              $this->error['ErrorCode']=1003; // 
	              $this->error['ErrorMessage']= 'El CUIT ingresado no esta configurado.';
	        };
	      }else{
	              $this->error['ErrorCode']=1002; // 
	              $this->error['ErrorMessage']= 'El servicio ingresado no esta configurado.';            
	      }
	  }else{
	      $this->error['ErrorCode']=1001; // 
	      $this->error['ErrorMessage']= 'Falta CUIT.';
	  };
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
	$XXX=$method.'Result';
	if ($this->LOG_XMLS) {
	  file_put_contents(sprintf($this->Archivos['debugOUT'],$method),$this->client->__getLastRequest());
	  file_put_contents(sprintf($this->Archivos['debugIN'],$method),$this->client->__getLastResponse());
	}

	if (is_soap_fault($results)) {
	      $this->error['ErrorCode']    = $results->faultcode;
	      $this->error['ErrorMessage'] = $results->faultstring;
	      return false;   
	}
	if(isset($results->$XXX->Errors)){
	    foreach($results->$XXX->Errors as $e){
	      $this->error['ErrorCode']    = $e->Code;
	      $this->error['ErrorMessage'] = $e->Msg;        	
	    }
	    return false;
	}else{
	  return true;
	}
	}

	/**
	* Abre el archivo de TA xml,
	* si hay algun problema devuelve false
	*/
	private function openTA() {
	$vto=$this->wsaa->Token();

	if(empty($this->error)){
	  $this->TA = simplexml_load_file($this->Archivos['ta']);     
	  if($this->TA == false){
	    $this->error['ErrorCode']=1021; // 
	    $this->error['ErrorMessage']= 'Error al abrir el Archivo TA.';
	    return false;
	  }else{
	    return true;
	  }
	}else{
	  return false;
	}
	}

	private function getAuth(){ //veo cuando no abri TA
	$auth =array(   'Token'    => $this->TA->credentials->token,
	                 'Sign'     => $this->TA->credentials->sign,
	                 'Cuit'     => $this->empresa->getCuit()
	            );
	return $auth;
	}


	/****************** a partir de ahora son el parseo de cada metodo del WS **********************/
	private function _getTiposComprobantes(){
	$this->openTA();
	if(empty($this->error)){
	  $results = $this->client->FEParamGetTiposCbte(
	      array(  'Auth'     =>  $this->getAuth() )
	  );
	  
	  return $results->FEParamGetTiposCbteResult->ResultGet->CbteTipo;
	}else{
	  return $this->error;
	}
	}
	  /*
	 * Obtiene los puntos de ventas habilitados
	 */
	private function _getPuntosVentaHabilitados(){
	    $this->openTA();
	    if(empty($this->error)){
		    $results = $this->client->FEParamGetPtosVenta(
		        array(  'Auth'     =>  $this->getAuth()
		            )
		    );
		  $this->_checkErrors($results, 'FEParamGetPtosVenta');
		  if(empty($this->error)){
		    return $results;	  	
		  }else{
	      	return $this->error;	  	
		  }
	    }else{
	      return $this->error;
	    }
	}


	public function Dummy(){
		if(empty($this->error)){
		  $results = $this->client->FEDummy();
		  return (array) $results->FEDummyResult;        
		}else{
		  return $this->error;
		}        
	}

	/** armo el registo a enviar como comprobante **/
	public function setRegistroComprobante(	$doctipo,			$docnro,	$desdeComprobante,
	                           				$hastaComprobante,  $fecha,		$total,
	                           				$imptotconc,		$neto,		$impOpEx,
	                           				$impTrib,			$impIva,	$fchServDesde,
	                           				$fchServHasta,		$fchVtoPago,$moneda,
	                           				$cotizacion,		$tributos,	$iva,
	                           				$concepto=1,		$cbtesAsoc){
    	$regfac[0]['Concepto']     	= $concepto;
	    $regfac[0]['DocTipo']      	= $doctipo;
	    $regfac[0]['DocNro']       	= $docnro;
	    $regfac[0]['CbteDesde']    	= $desdeComprobante;
	    $regfac[0]['CbteHasta']    	= $hastaComprobante;
	    $regfac[0]['CbteFch']      	= $fecha;
	    $regfac[0]['ImpTotal']     	= $total;
	    $regfac[0]['ImpTotConc']   	= $imptotconc;
	    $regfac[0]['ImpNeto']      	= $neto;
	    $regfac[0]['ImpOpEx']      	= $impOpEx;
	    $regfac[0]['ImpTrib']      	= $impTrib;
	    $regfac[0]['ImpIVA']       	= $impIva;
	    $regfac[0]['FchServDesd'] 	= $fchServDesde;
	    $regfac[0]['FchServHasta']	= $fchServHasta;
	    $regfac[0]['FchVtoPago']   	= $fchVtoPago;
	    $regfac[0]['MonId']        	= $moneda;
	    $regfac[0]['MonCotiz']     	= $cotizacion;
	    if (is_array($cbtesAsoc)){
	    	$struct = array('Tipo','PtoVta', 'Nro','Cuit','CbteFch');
	    	$val=0;
	    	foreach ($struct as $key => $value) {
	    		if(isset($cbtesAsoc[0][$key])){
	    			$val++;
	    		}
	    	}
	    	if($val==count($cbtesAsoc[0])){
	    		$regfac[0]['CbtesAsoc']  	= $cbtesAsoc;	    		    		
	    	};
	    }
	    if ($impTrib!=0){
	        if (is_array($tributos)){
		    	$struct = array('Id','BaseImp', 'Importe', 'Desc', 'Alic');
		    	$val=0;
		    	foreach ($struct as $key => $value) {
		    		if(isset($tributos['Tributo'][0][$key])){
		    			$val++;
		    		}
		    	}
		    	if($val==count($tributos['Tributo'][0])){
		    		$regfac[0]['Tributos']  	= $tributos;	    		    		
		    	}else{
				    $this->error['ErrorCode']=2002; // 
				    $this->error['ErrorMessage']= 'No se enviaron los tributos';
				    return false;
		    	}; // ver si es obligatorio
		    }
	    }	
	    /** verifico array IVA **/
	    if (is_array($iva)){
	    	$struct = array('Id','BaseImp', 'Importe');
	    	$val=0;
	    	foreach ($struct as $key => $value) {
	    		if(isset($iva['AlicIva'][0][$key])){
	    			$val++;
	    		}
	    	}
	    	if($val==count($iva['AlicIva'][0])){
	    		$regfac[0]['Iva']  	= $iva;	    		    		
	    	}else{
			    $this->error['ErrorCode']=2001; // 
			    $this->error['ErrorMessage']= 'No se enviaron las alicuotas de IVA';
			    return false;
	    	}; // ver si es obligatorio
	    }
	    //$regfac[0]['Opcionales']   = $opcionales;
	    //$regfac[0]['Compradores']  = $compradores;
	    //$regfac[0]['PeriodoAsoc']  = $periodoasoc;
	    $this->registroComprobante = $regfac;
	    return true;
    }	

    public function solicitarCAE($puntoVenta=null,$tipoCbte=null){
	    $this->openTA();	    
	    if($puntoVenta===null){
			$this->error['ErrorCode']=2003; // 
			$this->error['ErrorMessage']= 'No se envio un Punto de venta';	    	
    		return $this->error;			
	    };
	    if($tipoCbte===null){
			$this->error['ErrorCode']=2004; // 
			$this->error['ErrorMessage']= 'No se envio un Tipo de Comprobante';	    	
    		return $this->error;			
	    };	    
   	    if(empty($this->registroComprobante)){
			$this->error['ErrorCode']=2005; // 
			$this->error['ErrorMessage']= 'No se seteo el Comprobante';	    	
    		return $this->error;			
	    };
    	if(empty($this->error)){
	        $i=0;
	        $FeDetReq = array();
	        foreach ($this->registroComprobante as $reg){
	            foreach ($reg as $key=>$value){
	                $FeDetReq[$i][$key] =  $value;
	            }
	            $i++;
	        }
	        $results = $this->client->FECAESolicitar(
	            array(  'Auth'     =>  $this->getAuth(),
	                	'FeCAEReq' => array( "FeCabReq" => array(	'CantReg'  => count($FeDetReq),
	                    											'PtoVta'   => $puntoVenta,
	                    											'CbteTipo' => $tipoCbte
	                											),
	                    "FeDetReq" => $FeDetReq
	                )
	            )
	        );

	        $this->_checkErrors($results, 'FECAESolicitar');

			if(empty($this->error)){
		        return $results->FECAESolicitarResult;
			}else{
		    	return $this->error;	  	
			}
    	}else{
    		return $this->error;
    	}
    }    
}