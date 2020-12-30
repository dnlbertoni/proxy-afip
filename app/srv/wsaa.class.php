<?php

require_once ("../modelos/Empresas.php");
require_once ("../modelos/Servicios.php");
require_once ("../modelos/Entornos.php");
require_once ("../modelos/Certificados.php");

class WSAA {
  /*
   * manejo de errores
   */
  public $error = '';
  
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
  

  public function __construct($cuit=null,$servicioDeNegocio=null){
      $this->service = $servicioDeNegocio;
      $this->Archivos = new ArrayObject();
      $pathCert       = __DIR__ ."../data/cert/";
      $pathXML        = __DIR__ ."../data/xml/";
      $pathDebug      = __DIR__ ."../data/debug/";

      if($cuit !=null){
          /*** levanto los modelos ***/
          $empresas = new \Empresa\Empresas();
          $entornos = new \Config\Entornos();
          $certificados = new \Servicio\Certificados();
          $servicios = new \Servicio\Servicios();

          $this->servicioLogin = $servicios->getByName('wsaa');
          $this->empresa=$empresas->getByCuit($cuit);

          // seteos en php
          ini_set("soap.wsdl_cache_enabled", "0");

          if(is_object($this->empresa)){
              $this->entorno=$entornos->getActual($this->empresa->getId());
              $this->LOG_XMLS = ($this->entorno->getDebugActivo()==1);

              $this->certificadoAFIP = $certificados->getCertificadoEntorno( $entornos->getActual()->getId(),'CERT', $this->empresa->getId());
              $this->certificadoLocal = $certificados->getCertificadoEntorno( $entornos->getActual()->getId(),'PRIVATEKEY', $this->empresa->getId());
              $this->passphrase = $this->certificadoLocal->getPasswordCErtificado();
              /**** defino los nombres de los archivos en funcion de la configuracion ***/

              $this->Archivos->append(array('cert'       => $pathCert.$this->certificadoAFIP->getFilename()));
              $this->Archivos->append(array('privatekey' => $pathCert.$this->certificadoLocal->getFilename()));
              $this->Archivos->append(array('wdsl'       => $this->servicioLogin->getFileWsdl()));
              $this->Archivos->append(array('ta'         => $pathXML."TA_".$this->entorno->getNombre().$this->empresa->getCuit().".xml"));
              $this->Archivos->append(array('tra'        => $pathXML."TRA_".$this->entorno->getNombre().$this->empresa->getCuit().".xml"));
              $this->Archivos->append(array('traTMP'     => $pathXML."TRA_".$this->entorno->getNombre().$this->empresa->getCuit().".tmp"));
              $this->Archivos->append(array('debugOUT'   => $pathDebug."request-loginCms".$this->entorno->getNombre().$this->empresa->getCuit().".xml"));
              $this->Archivos->append(array('debugIN'    => $pathDebug."response-loginCms".$this->entorno->getNombre().$this->empresa->getCuit().".xml"));

              // validar archivos necesarios
              if (!file_exists($this->Archivos['cert'])) $this->error .= " Error de Apertura ". $this->Archivos['cert'];
              if (!file_exists($this->Archivos['privatekey'])) $this->error .= " Error de Apertura ".$this->Archivos['privatekey'];
              if (!file_exists($this->Archivos['wsdl'])) $this->error .= " Error de Apertura ".$this->Archivos['wsdl'];
              if(!empty($this->error)) {
                  throw new Exception('WSAA class. Faltan archivos necesarios para el funcionamiento.' . $this->error);
              }
              $this->client = new SoapClient($this->Archivos['wsdl'] , array(
                      'soap_version'   => SOAP_1_2,
                      'location'       => $this->servicioLogin->getUrl(),
                      'trace'          => 1,
                      'exceptions'     => 0
                  )
              );
          }else{
              throw new Exception('WSAA class. El CUIT ingresado no esta configurado.');
          }
      }else{
          throw new Exception('WSAA class. Falta CUIT.');
      }
  }

  /**
   * Crea el archivo xml de TRA
   */
  private function create_TRA(){
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
  }
  
   /**
   * Borra el archivo xml de TRA
   */
  public function delete_TRA(){
      unlink($this->Archivos['tra']);
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
    $STATUS = openssl_pkcs7_sign($this->Archivos['tra'], $this->Archivos['traTMP'], "file://".$this->Archivos['cert'], array("file://".$this->Archivos['privatekey'], $this->passphrase), array(),!PKCS7_DETACHED );
    //echo $fileTRA . ".xml" . "<br/>";
    if (!$STATUS){
        throw new Exception("ERROR generando firma PKCS#7 ");
    }
    $inf = fopen($this->Archivos['traTMP'], "r");
    $i = 0;
    $CMS = "";
    while (!feof($inf)){
        $buffer = fgets($inf);
        if ( $i++ >= 4 ) $CMS .= $buffer;
    }
    fclose($inf);
    unlink($this->Archivos['traTMP']);
    return $CMS;
  }

    /**
     * Conecta con el web service y obtiene el token y sign
     * @param $cms
     * @return
     * @throws Exception
     */
  private function call_WSAA($cms){
    $results = $this->client->loginCms(array('in0' => $cms));
    
    // para logueo
      if($this->LOG_XMLS){
          file_put_contents($this->Archivos['debugOUT'], $this->client->__getLastRequest());
          file_put_contents($this->Archivos['debugIN'], $this->client->__getLastResponse());
      }

    if (is_soap_fault($results)) 
      throw new Exception("SOAP Fault: ".$results->faultcode.': '.$results->faultstring);
      
    return $results->loginCmsReturn;
  }
  
  /*
   * Convertir un XML a Array
   */
  private function xml2array($xml) {    
    $json = json_encode( simplexml_load_string($xml));
    return json_decode($json, TRUE);
  }    
  
  /**
   * funcion principal que llama a las demas para generar el archivo TA.xml
   * que contiene el token y sign
   */
  public function generar_TA(){
    $this->create_TRA();
    $TA = $this->call_WSAA( $this->sign_TRA() );
                    
    if (!file_put_contents($this->Archivos['ta'], $TA))
      throw new Exception("Error al generar al archivo ".$this->Archivos['ta']);
    
    $this->TA = $this->xml2Array($TA);
      
    return true;
  }
  
  /**
   * Obtener la fecha de expiracion del TA
   * si no existe el archivo, devuelve false
   */
  public function get_expiration() {
    // si no esta en memoria abrirlo
    if(empty($this->TA)) {             
      $TA_file = file($this->Archivos['ta'], FILE_IGNORE_NEW_LINES);
      
      if($TA_file) {
        $TA_xml = '';
        for($i=0; $i < sizeof($TA_file); $i++)
          $TA_xml.= $TA_file[$i];        
        $this->TA = $this->xml2Array($TA_xml);
        $r = $this->TA['header']['expirationTime'];
      } else {
        $r = false;
      }      
    } else {
      $r = $this->TA['header']['expirationTime'];
    }
     
    return $r;
  }
   
}
