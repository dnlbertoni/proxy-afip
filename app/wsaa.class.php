<?php

class WSAA {
  const TA           =    "../data/xmlgenerados/TA_%s.xml";         # Archivo con el Token y Sign
  const WSDL         = "wsaa.wsdl";      # The WSDL corresponding to WSAA
  const CERT_CE      = "../data/keys/certificadoCentro_6442083e8fa4a204.crt";  # The X.509 certificate in PEM format
  const CERT_CR      = "../data/keys/certificadoRizzi_1b481cf0105eb17b.crt";  # The X.509 certificate in PEM format
  const PRIVATEKEY_CE   = "../data/keys/privadaCE.key";  # The private key correspoding to CERT (PEM)
  const PRIVATEKEY_CR   = "../data/keys/privadaRizzi.key";  # The private key correspoding to CERT (PEM)
  const PASSPHRASE   = "";         # The passphrase (if any) to sign
  const PROXY_ENABLE = false;

  const URL             = "https://wsaa.afip.gov.ar/ws/services/LoginCms"; // produccion
  const TA_test         = "../data/xmlgenerados/TA_test_%s.xml";         # Archivo con el Token y Sign
  const CERT_test       = "../data/keys/certificadoDNL.crt";  # The X.509 certificate in PEM format
  const PRIVATEKEY_test = "../data/keys/privadaDNL.key";  # The private key correspoding to CERT (PEM)
  const PASSPHRASE_test = "";         # The passphrase (if any) to sign
  const PROXY_ENABLE_test = false;
  const URL_test = "https://wsaahomo.afip.gov.ar/ws/services/LoginCms"; // testing

  
    /*
     * el path relativo, terminado en /
     */
  private $path = './';
  
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
  private $archivoTA;
  private $empresa;
  private $cert;
  private $privatekey;
  private $urlWSS;
  private $passphrase;
  private $ta;
  private $pathTRA;
  
  /*
   * Constructor
   */
  public function __construct($path = './', $empresa="CE",$entorno="produccion",$service = 'wsfe' ){
      $this->setEmpresa($empresa);
      //echo $this->getEmpresa();die();

    if($entorno=="test"){
		$this->cert=self::CERT_test;
		$this->privatekey=self::PRIVATEKEY_test;
		$this->urlWSS=self::URL_test;
		$this->passphrase = self::PASSPHRASE_test;
		$this->ta         = self::TA_test;
		$this->pathTRA    = '../data/xmlgenerados/TRA_TEST_';
	}else{
        if($this->getEmpresa()=="CR"){
            $this->cert=self::CERT_CR;
            $this->privatekey=self::PRIVATEKEY_CR;
        }else{
            $this->cert=self::CERT_CE;
            $this->privatekey=self::PRIVATEKEY_CE;
        }
		$this->urlWSS=self::URL;
		$this->passphrase = self::PASSPHRASE;
		$this->ta         = self::TA;
		$this->pathTRA    = '../data/xmlgenerados/TRA_';
	}


  //$this->path = __DIR__ .'/'. $path;
    $this->path = __DIR__ .'/';
    $this->service = $service;
    $this->setArchivoTA();
    
    // seteos en php
    ini_set("soap.wsdl_cache_enabled", "0");    

    // validar archivos necesarios
    if (!file_exists($this->path. $this->cert)) $this->error .= " Error de Apertura ".$this->cert;
    if (!file_exists($this->path. $this->privatekey)) $this->error .= " Error de Apertura ".$this->privatekey;
    if (!file_exists($this->path. self::WSDL)) $this->error .= " Error de Apertura ".self::WSDL;
    
    if(!empty($this->error)) {
      throw new Exception('WSAA class. Faltan archivos necesarios para el funcionamiento.' . $this->error);
    }
    
    $this->client = new SoapClient($this->path.self::WSDL, array(
              'soap_version'   => SOAP_1_2,
              'location'       => $this->urlWSS,
              'trace'          => 1,
              'exceptions'     => 0
            )
    );
  }

    /**
     * @return mixed
     */
    public function getEmpresa(){
        return $this->empresa;
    }

    /**
     * @param mixed $empresa
     */
    public function setEmpresa($empresa){
        $this->empresa = $empresa;
    }

    /**
     * @return mixed
     */
    public function getArchivoTA(){
        return $this->archivoTA;
    }

    /**
     * @param mixed $archivoTA
     */
    public function setArchivoTA(){
        $this->archivoTA = sprintf($this->path.$this->ta,$this->getEmpresa());
    }

  /**
   * Crea el archivo xml de TRA
   */
  private function create_TRA(){
    unlink($this->path.$this->pathTRA.$this->getEmpresa().'.xml');
    $TRA = new SimpleXMLElement(
      '<?xml version="1.0" encoding="UTF-8"?>' .
      '<loginTicketRequest version="1.0">'.
      '</loginTicketRequest>');
    $TRA->addChild('header');
    $TRA->header->addChild('uniqueId', date('U'));
    $TRA->header->addChild('generationTime', date('c',date('U')-60));
    $TRA->header->addChild('expirationTime', date('c',date('U')+60));
    $TRA->addChild('service', $this->service);
    $TRA->asXML($this->path.$this->pathTRA.$this->getEmpresa().'.xml');
  }
  
   /**
   * Borra el archivo xml de TRA
   */
  public function delete_TRA(){
      unlink($this->path.$this->pathTRA.$this->getEmpresa().'.xml');
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
      $fileTRA = $this->path.$this->pathTRA.$this->getEmpresa();
      $STATUS = openssl_pkcs7_sign($fileTRA . ".xml", $fileTRA . ".tmp", "file://".$this->path.$this->cert, array("file://".$this->path.$this->privatekey, $this->passphrase), array(),!PKCS7_DETACHED );
      //echo $fileTRA . ".xml" . "<br/>";
      if (!$STATUS){
          throw new Exception("ERROR generando firma PKCS#7 ");
      }
      $inf = fopen($this->path.$this->pathTRA.$this->getEmpresa().".tmp", "r");
      $i = 0;
      $CMS = "";
      while (!feof($inf)){
          $buffer = fgets($inf);
          if ( $i++ >= 4 ) $CMS .= $buffer;
      }
      fclose($inf);
      unlink($this->path.$this->pathTRA.$this->getEmpresa().".tmp");
      return $CMS;      
    }else{
      return $this->error;
    }
  }
  
  /**
   * Conecta con el web service y obtiene el token y sign
   */
  private function call_WSAA(){

    $results = $this->client->loginCms(array('in0' => $this->sign_TRA()));
    
    // para logueo
    file_put_contents($this->path."request-loginCms.xml", $this->client->__getLastRequest());
    file_put_contents($this->path."response-loginCms.xml", $this->client->__getLastResponse());
  
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
  public function generar_TA()
  {
    $this->create_TRA();
    $TA = $this->call_WSAA( $this->sign_TRA() );
                    
    if (!file_put_contents($this->getArchivoTA(), $TA))
      throw new Exception("Error al generar al archivo TA.xml");
    
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
      $TA_file = file($this->getArchivoTA(), FILE_IGNORE_NEW_LINES);
      
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
