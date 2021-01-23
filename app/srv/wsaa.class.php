<?php


require_once ("../modelos/Empresas.php");
require_once ("../modelos/Entornos.php");
require_once ("../modelos/Servicios.php");
require_once ("../modelos/Certificados.php");

class WSAA {
  /*
   * manejo de errores
   * Codigos de errores 1000
   */
  public $error = array();
  
  /**
   * Cliente SOAP
   */
  private $client;
     
  /*
   * servicio del cual queremos obtener la autorizacion
   */
  private $service;

  private $empresa;
  private $servicioLogin;
  private $certificadoAFIP;
  private $certificadoLocal;
  private $entorno;
  private $passphrase;
  

  public function __construct($cuit=null,$servicioDeNegocio){
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

          $this->servicioLogin = $servicios->getByName('wsaa');
          $this->empresa=$empresas->getByCuit($cuit);

          // seteos en php
          ini_set("soap.wsdl_cache_enabled", "0");

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
                                            'wsdl'       => $pathWsdl . $this->servicioLogin->getFileWsdl(),
                                            'ta'         => $pathXML."TA_".$this->entorno->getNombre().$this->empresa->getCuit().".xml",
                                            'tra'        => $pathXML."TRA_".$this->entorno->getNombre().$this->empresa->getCuit().".xml",
                                            'traTMP'     => $pathXML."TRA_".$this->entorno->getNombre().$this->empresa->getCuit().".tmp",
                                            'debugOUT'   => $pathDebug."request-loginCms".$this->entorno->getNombre().$this->empresa->getCuit().".xml",
                                            'debugIN'    => $pathDebug."response-loginCms".$this->entorno->getNombre().$this->empresa->getCuit().".xml");

                    // validar archivos necesarios
                    if (!file_exists($this->Archivos['cert'])){
                      $this->error['ErrorCode']    = 1009; // 
                      $this->error['ErrorMessage'] = " Error de Apertura ". $this->Archivos['cert'];
                    } else{
                      if (!file_exists($this->Archivos['privatekey'])){
                        $this->error['ErrorCode']    = 1010; // 
                        $this->error['ErrorMessage'] = " Error de Apertura ".$this->Archivos['privatekey'];
                      } else{
                        if (!file_exists($this->Archivos['wsdl'])){                         
                          $this->error['ErrorCode']    = 1011; // 
                          $this->error['ErrorMessage'] = " Error de Apertura ->". $this->Archivos['wsdl'];
                        } else{
                          $this->client = new SoapClient($this->Archivos['wsdl'] , array(
                                  'soap_version'   => SOAP_1_2,
                                  'location'       => $this->servicioLogin->getUrl(),
                                  'trace'          => 1,
                                  'exceptions'     => 0
                              )
                          );                                      
                        }
                      }
                    }
                  }else{
                    $this->error['ErrorCode']=1005; // 
                    $this->error['ErrorMessage']= 'El certificado Local no esta configurado para esa empresa y entorno';                  
                  }
                }else{
                  $this->error['ErrorCode']=1004; // 
                  $this->error['ErrorMessage']= 'El certificado de la AFIP no esta configurado para esa empresa y entorno';                  
                };
              }else{
                $this->error['ErrorCode']=1003; // 
                $this->error['ErrorMessage']= 'Entornos nos configurados para esa Empresa';
              };
          }else{
                $this->error['ErrorCode']=1002; // 
                $this->error['ErrorMessage']= 'El CUIT ingresado no esta configurado.';
          };
      }else{
          $this->error['ErrorCode']=1001; // 
          $this->error['ErrorMessage']= 'Falta CUIT.';
      };
  }

  /**
   * Crea el archivo xml de TRA
   */
  private function create_TRA(){
    if(empty($this->error)){
      $this->delete_TRA();
      $TRA = new SimpleXMLElement(
        '<?xml version="1.0" encoding="UTF-8"?>' .
        '<loginTicketRequest version="1.0">'.
        '</loginTicketRequest>');
      $TRA->addChild('header');
      $TRA->header->addChild('uniqueId', date('U'));
      $TRA->header->addChild('generationTime', date('c',date('U')-60));
      $TRA->header->addChild('expirationTime', date('c',date('U')+60));
      $TRA->addChild('service', $this->service);
      $TRA->asXML($this->Archivos['tra']);      
    }else{
      return false;
    }
  }
  
   /**
   * Borra el archivo xml de TRA
   */
  private function delete_TRA(){
      if(file_exists($this->Archivos['tra'])){
        unlink($this->Archivos['tra']);      
      }
      return true;
  }
   
  
  /*
   * This functions makes the PKCS#7 signature using TRA as input file, CERT and
   * PRIVATEKEY to sign. Generates an intermediate file and finally trims the 
   * MIME heading leaving the final CMS required by WSAA.
   * 
   * devuelve el CMS
   */
  private function sign_TRA(){
    if(empty($this->error)){
      if(file_exists($this->Archivos['tra'])){
        $STATUS = openssl_pkcs7_sign( $this->Archivos['tra'], 
                                      $this->Archivos['traTMP'], 
                                      "file://".realpath($this->Archivos['cert']), 
                                      array(  "file://".realpath($this->Archivos['privatekey']), 
                                              ''), 
                                      array(),
                                      !PKCS7_DETACHED );
        if (!$STATUS){
            $this->error['ErrorCode']=1007; // 
            $this->error['ErrorMessage']= "EERROR generando firma PKCS#7 ";     
            return false;          
        }
        $inf = fopen($this->Archivos['traTMP'], "r");
        if($inf){
          $i = 0;
          $CMS = "";
          while (!feof($inf)){
              $buffer = fgets($inf);
              if ( $i++ >= 4 ) $CMS .= $buffer;
          }
          fclose($inf);
          unlink($this->Archivos['traTMP']);
          return $CMS;                      
        }else{
            $this->error['ErrorCode']=1008; // 
            $this->error['ErrorMessage']= "Error al abrir el archivo ".$this->Archivos['traTMP'];     
            return false;                  
        }
      }else{
            $this->error['ErrorCode']=1013; // 
            $this->error['ErrorMessage']= "Error al abrir el archivo ".$this->Archivos['tra'];     
            return false;                          
      }
    }else{
      return false;
    }
  }

    /**
     * Conecta con el web service y obtiene el token y sign
     * @param $cms
     * @return
     * @throws Exception
     */
  private function call_WSAA(){
    $CMS=$this->sign_TRA();
    if(empty($this->error)){
      $results = $this->client->loginCms(array('in0' => $CMS));
      
      // para logueo
        if($this->LOG_XMLS){
            file_put_contents($this->Archivos['debugOUT'], $this->client->__getLastRequest());
            file_put_contents($this->Archivos['debugIN'], $this->client->__getLastResponse());
        }

      if (is_soap_fault($results)){
          $this->error['ErrorCode']=$results->faultcode;
          $this->error['ErrorMessage']= $results->faultstring;
          return false;   
        //throw new Exception("SOAP Fault: ".$results->faultcode.': '.$results->faultstring);
      }else{
        return $results->loginCmsReturn;
      }      
    }else{
      return false;                        
    }
  }
  
  /**
   * funcion principal que llama a las demas para generar el archivo TA.xml
   * que contiene el token y sign
   */
  private function generar_TA(){
    if(empty($this->error)){
      //creo el archivo ta
      $this->create_TRA();

      if(empty($this->error)){
        //llamo al WS
        $TA = $this->call_WSAA( );
        if(empty($this->error)){
          //verifico contenido del respuesta                      
          if (!file_put_contents($this->Archivos['ta'], $TA)){
            $this->error['ErrorCode']=1006; // 
            $this->error['ErrorMessage']= "Error al generar al archivo ".$this->Archivos['ta'];                
            return false;     
          }else{
            if(empty($this->error)){
              // almaceno el xml devulto
              $this->TA = $this->xml2Array($TA);
              return $this->TA['header']['expirationTime'];      
            }else{
              return false;                                     
            }
          }                  
        }else{
          return false;                        
        }
      }else{
        return false;              
      }
    }else{
      return false;      
    }
  }
  
  /**
   * Obtener la fecha de expiracion del TA
   * si no existe el archivo, devuelve false
   */
  private function get_expiration() {
    if(empty($this->error)){
      // si no esta en memoria abrirlo
      if(empty($this->TA)) { 
          if(empty($this->error)){
            if(file_exists($this->Archivos['ta'])){
              $TA_file = file($this->Archivos['ta'], FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);            
            }else{
              $TA_file = false;
            }
            if($TA_file) {
              $TA_xml = '';
              for($i=0; $i < sizeof($TA_file); $i++)
                $TA_xml.= $TA_file[$i];  
              if(empty($this->error)){
                $this->TA = $this->xml2Array($TA_xml);
              }else{
                $r = false;                 
              }
              if(empty($this->error)){
                $r = $this->TA['header']['expirationTime'];              
              }else{
                $r = false; 
              }
            } else {
              $r = false;
            } 
          }else{
            $r=false;            
          }
      } else {
        $r = $this->TA['header']['expirationTime'];
      }      
    }else{
      $r=false;
    }
    return $r;     
  }
   
  public function Token(){
    if(empty($this->error)){
      if( $this->get_expiration() < date("Y-m-d h:m:i") ) {
        if(empty($this->error)){        
          return $this->generar_TA();
        }else{
          return false;         
        }
      } else {
        if(empty($this->error)){
          return $this->get_expiration();          
        }else{
          return false;
        }
      };
    }else{
      return false;
    }
  }

  /*
   * Convertir un XML a Array
   */
  private function xml2array($xml) {    
    if(empty($this->error)){
      $json = json_encode( simplexml_load_string($xml));
      return json_decode($json, TRUE);      
    }else{
      return false;
    }
  }    
  

}
