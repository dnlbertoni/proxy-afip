<?php
class Upload {
	var $cls_upload_dir; 			// Directory to upload to
	var $cls_max_filesize; 			// max file size (must be set in form)
	var $cls_filename;				// Name of the uploaded file
	var $cls_filesize; 				// file size
	var $cls_file;					// file
    var $cls_file_type;             // file type (mime)
	var $cls_copyfile; 				// Final filename to copy, after change
	var $cls_referer_domain;		// domain from script is called from
	var $cls_arr_ext_accepted; 		// Type of file we will accept.
	var $cls_rename_file; 			// must rename uploaded file or not
	var $cls_errorCode;				// error code
	var $cls_domain;				// our domain
	var $cls_domain_check;			// domain check flag: 1 check referrer domain, 0 not.
	var $cls_overWrite;	 			//Overwrite the file if exists
    var $cls_originalname;          // Original Name of the Uploaded File
    var $cls_tipo_archivo;           // file type (application)
    var $cls_accepted;
    var $cls_file_ext;

	/** upload()
    ** Constructor de la Clase upload.
    ** @param inputName Nombre del Campo del Formulario que contiene el Nombre de Archivo a Subir
    ** @param rename Renombrar el Archivo si el valor es 1.
    ** @param ow Sobreescribir el Archivo si existe.
    **/
	function __construct($inputName = "file" , $rename = 0 , $ow = 1) {

		$myfile= $inputName;
       
		global  $MAX_FILE_SIZE, $_FILES;
        

        //var_dump($_SERVER);die();
		$this->cls_domain = "localhost";
		$this->cls_domain_check = 0;
		$this->cls_overWrite = $ow;
		$this->cls_rename_file = $rename;
        $this->cls_referer_domain = $_SERVER['HTTP_ORIGIN'];
		$this->cls_max_filesize = $MAX_FILE_SIZE;
        $this->cls_tipo_archivo = $this->esDesconocido;
        $this->cls_file_ext = '';
        
        if (!isset($_FILES) || !is_array($_FILES[$myfile]) || empty($_FILES[$myfile]['name'])) {
            $this->cls_errorCode = 9;
        } else {
            $this->cls_file = $_FILES[$myfile]['tmp_name'];
    		$this->cls_filename =  $_FILES[$myfile]['name'];
            $this->cls_file_type =  $_FILES[$myfile]['type'];
    		$this->cls_filesize = $_FILES[$myfile]['size'];             
        }
      
  	}
    /** getFileType()
    ** Obtener el tipo de Archivo (No MIME).
    ** @return tipoArchivo 0 si es desconocido, 1 si es Imagen, 2 si es Flash, 3 si es Adjunto
    **/
    function getFileType () {
        return $this->cls_tipo_archivo;
    }

    /** setExtensions()
    ** Establece las extensiones v�lidas para hacer upload.
    ** @param String Lista de extensiones separadas por punto y coma.
    ** @return void
    **/
	function setExtensions($ext){        
        $this->cls_arr_ext_accepted = explode(";",$ext);        
    }

    /** checkExtension()
     ** Metodo para verificar que la extension del archivo es valida.
     ** @return boolean
     **/
    function checkExtension() {
        // corregir el nombre del archivo solo en estos casos
        $this->cls_filename = preg_replace("/ /", "_", $this->cls_filename);
        $this->cls_filename = preg_replace("/%20/", "_", $this->cls_filename);
        
        $extension = strtolower($this->getExtension($this->getFilename()));        
        
        // Chequear si la extension es v�lida
        $this->cls_accepted = in_array($extension, $this->cls_arr_ext_accepted);        
        $this->cls_errorCode = (!$this->cls_accepted) ? 1 : 0;
        $this->cls_file_ext = (!$this->cls_accepted) ? '' : $extension;
        // Retornar true si el tipo es valido        
        return $this->cls_accepted;
        //return true;
    }

    /** getExtension()
    ** Retorna la extension, incluyendo el punto, del nombre del archivo a hacer upload.
    ** @param String Nombre del archivo a obtener la extension
    ** @return String
    **/
    function getExtension($filename = ''){
        if(empty($filename))
           return $this->cls_file_ext;
        else
           return strrchr($filename, ".");
    }

	/** setDir()
	** Establece el Directorio donde se subira el archivo.
	** @param String Nombre del directorio contenedor
	** @return void
	**/
	function setDir($dir) {
    		$this->cls_upload_dir = $dir;
  	}

    /** getDir()
    ** Obtiene el directorio donde se hara el upload.
    ** @return void
    **/
    function getDir() {
            return $this->cls_upload_dir;
    }

    /** setDomain()
    ** Estable el nombre de dominio y el chequeo de url
    ** @returns void
    **/
	function setDomain($domain, $refer = 0) {
        $this->cls_domain = $domain;
        $this->cls_domain_check = $refer;
    }

    /** getDomain()
    ** Obtiene el nombre de dominio
    ** @return String Nombre del dominio
    **/
    function getDomain() {
        return $this->cls_domain;
    }

  	/** checkFileSize()
	** Metodo para chequear que el tama�o del Archivo no exceda el Tama�o Maximo.
	** @returns boolean
	**/
	function checkFileSize() {              
		$this->cls_accepted = ($this->cls_filesize <= $this->cls_max_filesize);
        $this->cls_errorCode = (!$this->cls_accepted) ? 2 : 0;
        return $this->cls_accepted;
	}

    /** setMaxFileSize()
    ** Metodo que asigna el tama�o m�ximo de Archivo para subir
    ** @param int Tama�o del Archivo
    **/
    function setMaxFileSize($size) {
        $this->cls_max_filesize = ($size > 0) ? $size : MAX_FILE_SIZE;
    }

    /** getFileSize()
    ** Metodo que retorna el tama�o del Archivo a subir.
    ** @return int Tama�o del Archivo
    **/
    function getFileSize() {
        return $this->cls_filesize;
    }


	/** filenameExist()
	** Function to check if a file with the same filename
	** exist in the directory we upload to.
	** @returns Boolean
	*/
	function filenameExist() {
		return (file_exists($this->cls_copyfile));
	}

	/** copyFile()
	** Metodo para copiar el archivo.
    ** @param String Nombre del Archivo
	** @return boolean true Si fue copiado con Exito, false si ocurrio un error.
	**
	**/
  	function copyFile($uploaded_name = "")	{
		global $HTTP_REFERER;        
		$url=parse_url($HTTP_REFERER);
		//$this_domain = $url["scheme"]."://".$url["host"]; //get domain script is called from
        $this_domain = $_SERVER['HTTP_ORIGIN'];
        $this->changeFilename($uploaded_name);

		if(!empty($this->cls_file)) {
			if(!$this->checkExtension()) {    
				$this->cls_errorCode = 1; // extension not accepted
			} else { 
				$this->setCompleteFilename();

				if($this->filenameExist($this->cls_copyfile)) {
					if ($this->cls_overWrite) {
						if (!unlink($this->cls_copyfile)) {
							$this->cls_errorCode = 3; // can't delete remote file
						} else {
							$this->cls_errorCode = $this->resumeCopy($this_domain,$uploaded_name);
						}
					} else {
						$this->cls_errorCode = 4; // file exists and OverWrite not set
					}
				} else {  
					$this->cls_errorCode = $this->resumeCopy($this_domain,$uploaded_name);
				}
			}
		} else {
			$this->cls_errorCode = (!empty($this->cls_filename)) ? 2: 8;
		}
		return (!$this->cls_errorCode);
	}

	/** resumeCopy()
	** Funcion utilizada para reanudar la copia de un archivo.
	** No es necesario invocar a esta funcion, el metodo copyFile lo hace autom�ticamente
	** @returns 0 Si la copia fue exitosa, un valor distinto de cero si hubo un error
	*/
	function resumeCopy($this_domain,$uploaded_name) {        
		if ($this->cls_domain_check) {
            if($this_domain != $this->cls_referer_domain) {
				return 7;
			}
		}
        if(copy($this->cls_file, $this->cls_copyfile)) {            
			if($this->cls_rename_file) {
				$temp_name = ($uploaded_name)? $this->changeFilename($uploaded_name) : $this->changeFilename();
				if(!rename($this->cls_copyfile, $temp_name)) {
					return 5;
				} else {
			        $this->cls_copyfile = $temp_name;
				}
          	}
			return 0;
       	} else	{
			return 6;
		}
	}

	/** changeFilename()
	* Cambia el nombre del Archivo antes de copiarlo a su destino.
	*
	* @returns String El nuevo nombre del Archivo
	*/
	function changeFilename($new_name = null) {
		if(empty($new_name)) {
			$extension = $this->getExtension($this->cls_copyfile);
			$new_name = rndName(30);
			$new_name.= strtolower($extension);
		}
		$this->cls_filename = $new_name;
		return ($this->cls_upload_dir ."/$new_name");
	}

  	/** getFilename()
  	** Obtener el Nombre y la ruta del archivo subido
  	** @return String El nombre del Archivo
    **/
  	function getFilename () {
  		return ($this->cls_filename);
  	}

    /** removeFile()
    ** Borra el Archivo en el directorio donde hacemos upload
    ** @return boolean true si fue borrado, false si hubo un error
    **/
    function removeFile($file) {
        if (file_exists($this->cls_upload_dir."/".$file)) {
            if(@unlink($this->cls_upload_dir."/".$file)) {
                $this->cls_errorCode = 0;
                return true;
            } else {
                $this->cls_errorCode = 3;
                return false;
            }
        } else {
            $this->cls_errorCode = 0;
            return false;
        }
    }

    /** setCompleteFilename()
    ** Establece el path absoluto para poder subir el archivo.
    ** @return void
    **/
    function setCompleteFilename() {
        $this->cls_copyfile = $this->cls_upload_dir ."/". $this->cls_filename;
    }


    /** getErrorCode()
    **  Obtiene el codigo del Error ocurrido.
    ** @return int Codigo del Error
    **/
    function getErrorCode () {
        return $this->cls_errorCode;
    }

    /** getErrorMessage()
    ** Traduce el Codigo de Error en un Mensaje Formateado.
    ** @return String Mensaje con el motivo del Error.
    **/
    function getErrorMessage () {
        switch($this->cls_errorCode) {
            case 0: $msg = "�Operacion Exitosa!"; break;
            case 1: $msg = "<b>".$this->getFilename()."</b> no fue enviado. La extensi�n: <b>".$this->getExtension()."</b> no se acepta!"; break;
            case 2: $msg = "El archivo <b>".$this->cls_filename."</b> es demasiado grande o no existe!"; break;
            case 3: $msg = "El archivo remoto no pudo borrarse!"; break;
            case 4: $msg = "El archivo <b>".$this->cls_filename."</b> existe pero est� prohibido su borrado!"; break;
            case 5: $msg = "Env�o exitoso, pero fall� el renombrado del archivo!"; break;
            case 6: $msg = "Imposible enviar el archivo :("; break;
            case 7: $msg = "Ud. no tiene permiso para usar este script!"; break;
            case 8: $msg = "Debe elegir un archivo para enviar"; break;
            case 9: $msg = "No se ha enviado el archivo"; break;
            default:$msg = "Error desconocido!";
        };
        return $msg;
    }

    /** prepareUpload()
    **  Verifica si la Extension, el Tama�o y el Tipo de Archivo es Valido.
    **/
    function prepareUpload() {        
        $this->cls_accepted = ($this->checkExtension() and $this->checkFileSize()) or $this->checkFileType();           
    }

    /** mayUpload()
    **  Verifica si esta todo listo para subir el archivo.
    ** @return boolean true si esta todo bien, false en caso contrario.
    **/
    function mayUpload() {
         return $this->cls_accepted;
    }

}; // class ends

/**************************************************************
  Funcion utilizada para elegir un nombre en forma aleatoria
   con una longitud de 15 caracteres si no se indica otra
***************************************************************/
function rndName($name_len = 15) {
	$allchar = "ABCDEFGHIJKLNMOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz01234567890_";
	$str = "" ;
	mt_srand ((double) microtime() * 1000000);
	for ($i = 0; $i < $name_len ; $i++)
		$str .= substr($allchar, mt_rand (0,strlen($allchar)),1) ;
	return $str ;
}

?>
