<?php
require_once ("../modelos/Empresas.php");
require_once ("../modelos/Servicios.php");
require_once ("../modelos/Entornos.php");
require_once ("../modelos/Certificados.php");
require_once ("wsaa.class.php");

class WsBasic {
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

  /*
   * Constructor
   */
  public function __construct($cuit=null, $servicioDeNegocio=null) {
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
                                              'debugOUT'   => $pathDebug."request-loginCms".$this->entorno->getNombre().$this->empresa->getCuit().".xml",
                                              'debugIN'    => $pathDebug."response-loginCms".$this->entorno->getNombre().$this->empresa->getCuit().".xml");

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
    $vto=$wsaa->Token();
    if(empty($wsaa->error)){
      $this->TA = simplexml_load_file($this->Archivos['ta']);     
      return $this->TA == false ? false : true;
    }else{
      return false;
    }
  }

  /****************** a partir de ahora son el parseo de cada metodo del WS **********************/


  /**
     * Retorna la cantidad maxima de registros de detalle que
     * puede tener una invocacion al FEAutorizarRequest
     */
  public function Dummy(){
        if(empty($this->error)){
          $results = $this->client->FEDummy();
          try {
              $e = $this->_checkErrors($results, 'FEDummy');
          } catch (Exception $e) {
          }
          return array('App' => $results->FEDummyResult->AppServer, 'DB' => $results->FEDummyResult->DbServer, 'Auth' => $results->FEDummyResult->AuthServer);        
        }else{
          return $this->error;
        }
    }
} // END class