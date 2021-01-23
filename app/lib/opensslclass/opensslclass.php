
<?php

class OpensslClass{

	private $dn;
	private $privkey;
	private $csr;

	public function __construct(){

			// Normalmente querrá crear un certificado autofirmado en este
			// punto hasta que su AC satisfaga su petición.
			// Esto crea un certificado autofirmado que es válido por 365 días
			$sscert = openssl_csr_sign($csr, null, $privkey, 365);

			// Ahora querrá preservar su clave privada, CSR y certificado
			// autofirmado por lo que pueden ser instalados en su servidor web, servidor de correo
			// o cliente de correo (dependiendo del uso previsto para el certificado).
			// Este ejemplo muestra cómo obtener estas cosas con variables, pero
			// también puede almacenarlas directamente en archivos.
			// Normalmente, enviará la CSR a su AC, la cuál se la emitirá después
			// con el certificado "real".
			openssl_csr_export($csr, $csrout) and var_dump($csrout);
			openssl_x509_export($sscert, $certout) and var_dump($certout);
			openssl_pkey_export($privkey, $pkeyout, "mypassword") and var_dump($pkeyout);

			// Mostrar cualquier error que ocurra
			while (($e = openssl_error_string()) !== false) {
			    echo $e . "\n";
			}
	}

	public function setPrivateKey($pkey=null){
		if($pkey===null){
			// Generar una nueva pareja de clave privada (y pública)
			$this->privkey = openssl_pkey_new();			
			return true;
		}else{
			$this->privkey = $pkey;
			return true;
		};
	}

	public function getPrivateKey(){
		return $this->privkey;
	}

	public function setDn($dn){
			// Rellenar la información del nombre distinguido que se va a usar en el certificado
			// Debe cambiar los valores de estas claves para que coincidan con su nombre y
			// compañía, o para se más exactos, el nombre y la compañía de la persona/sitio
			// para el que va a generar el certificado.
			// Para los certificados SSL, el nombre común (commonName) normalmente es el nombre de
			// dominio que va a usar el certificado, pero para certificados S/MIME,
			// el nombre común será el nombre de la persona que usará el
			// certificado.

			/*
			$dn = array(
			    "countryName" => "UK",
			    "stateOrProvinceName" => "Somerset",
			    "localityName" => "Glastonbury",
			    "organizationName" => "The Brain Room Limited",
			    "organizationalUnitName" => "PHP Documentation Team",
			    "commonName" => "Wez Furlong",
			    "emailAddress" => "wez@example.com"
			);
			*/

			if(is_array($dn)){
				$this->dn=$dn;
				return true;
			}else{
				throw new Exception("Error Processing Request", 1);
			}
	}

	public function getDn(){
		return $this->dn;
	}

	public function setCSR($csr=null){	
		if($csr===null){	
			// Generar una petición de firma de certificado
			$this->csr = openssl_csr_new($this->getDn(), $privkey);
		}else{
			$this->csr = $csr;
		}
	}

	public function getCSR(){
		return $this->csr;
	}


}

