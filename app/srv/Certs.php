<?php

class Certs{

	public function Generar(){
		$path=__dir__ . "../cert/";
		$claveServer=$path. $_SERVER['SERVER_NAME'] ."_2048".".ppk";
		$comando_cert_local="openssl genrsa -out %s 2048";
		$cmd=sprintf($comando_cert_local,$claveServer);
		exec($cmd);
		$nombreEmpresa="autosersantaslucia";
		$nombreSistema="citrus";
		$cuit="20085805755";
		$claveSistema=$path . $nombreEmpresa .'_'. $nombreSistema.".csr";
		$comando_cert_export='openssl req -new -key %s  -subj "/C=AR/O=%s/CN=%s/serialNumber=CUIT %s"  -out %s';
		$cmd = sprintf($comando_cert_export,$claveServer,$nombreEmpresa, $nombreSistema, $cuit, $claveSistema);
		exec($cmd);		
		return $claveServer;
	}
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author 
	 **/
	public function genssh(){
		$cmd = __dir__ . "../cert/generarCert.sh";
		exec($cmd, $op);		
		return $op;
	}
}
