<?php
require_once '../../../conf/include.all.php';
require_once ("../../../modelos/Servicios.php");
require_once ("../../../lib/archivos/upload.class.php");

/** parseo los inputs */
$accion = $_POST['accion'];
$servicio = new \Servicio\Servicio();
$servicio->setId(isset($_POST['id'])?$_POST['id']:null);
$servicio->setNombre(isset($_POST['nombre'])?$_POST['nombre']:null);
$servicio->setDescripcion(isset($_POST['descripcion'])?$_POST['descripcion']:null);
$servicio->setIdentorno(isset($_POST['identorno'])?$_POST['identorno']:null);
$servicio->setFileWsdl(isset($_POST['file_wsdl'])?$_POST['file_wsdl']:null);
$servicio->setFileDoc(isset($_POST['file_doc'])?$_POST['file_doc']:null);
$servicio->setVersion(isset($_POST['version'])?$_POST['version']:null);
$servicio->setUrl(isset($_POST['url'])?$_POST['url']:null);

$servicios = new \Servicio\Servicios();

$data['res']['code']=99;
$data['res']['message']='Sin Accion definida';
$error=false;
switch ($accion){
    case 'add':
        $rta_file=0;
        foreach ($_FILES as $idx=>$file ){
            switch ($idx){
                case "file_wsdl":
                    $dir=__DIR__.'/../../../data/wsdl'; // ver como levantarlo de configuraciones
                    $fileExtension = 'wsdl';
                    break;
                case "file_doc":
                    $dir=__DIR__.'/../../../data/doc'; // ver como levantarlo de configuraciones
                    $fileNameCmps = explode(".", $file['name']);
                    $fileExtension = strtolower(end($fileNameCmps));
                    break;
                default:
                    $error = true;
                    break;
            }
            if(!$error){
                $fileTmpPath = $_FILES[$idx]['tmp_name'];
                $fileName = $_FILES[$idx]['name'];
                $fileSize = $_FILES[$idx]['size'];
                $fileType = $_FILES[$idx]['type'];
                $fileNameCmps = explode(".", $fileName);

                $subir = new Upload($idx);
                $nombre = $servicio->getNombre() . '_'.$servicio->getIdentorno().'.' .$fileExtension;
                $subir->setDir($dir);
                $subir->setExtensions('.'.$fileExtension);
                $rta_file += $subir->copyFile($nombre);
                $error_archivo=$subir->getErrorMessage();
                switch ($idx){
                    case "file_wsdl":
                        $servicio->setFileWsdl($subir->getFilename());
                        break;
                    case "file_doc":
                        $servicio->setFileDoc($subir->getFilename());
                        break;
                }
            }
        }
        if($rta_file==0){
            $id=$servicios->insert($servicio);
            if($id){
                $data['res']['code']=10;
                $data['res']['message']=$id;
            }else{
                $data['res']['code']=11;
                $data['res']['message']='Nose pudo Insertar';
            }            
        }else{
                $data['res']['code']=10 + $rta_file;
                $data['res']['message']=$error_archivo;            
        }
        break;
    case "edit":
        foreach ($_FILES as $idx=>$file ){
            switch ($idx){
                case "file_wsdl":
                    $dir=__DIR__.'/../../../data/wsdl'; // ver como levantarlo de configuraciones
                    break;
                case "file_doc":
                    $dir=__DIR__.'/../../../data/doc'; // ver como levantarlo de configuraciones
                    break;
                default:
                    $error = true;
                    break;
            }
            if(!$error){
                $fileTmpPath = $_FILES[$idx]['tmp_name'];
                $fileName = $_FILES[$idx]['name'];
                $fileSize = $_FILES[$idx]['size'];
                $fileType = $_FILES[$idx]['type'];
                $fileNameCmps = explode(".", $fileName);
                $fileExtension = strtolower(end($fileNameCmps));

                $subir = new Upload($idx);
                $nombre = $servicio->getNombre() . $fileExtension;
                $subir->setDir($dir);
                $subir->setExtensions('.'.$fileExtension);
                $subir->copyFile($nombre);
                switch ($idx){
                    case "file_wsdl":
                        $servicio->setFileWsdl($subir->getFilename());
                        break;
                    case "file_doc":
                        $servicio->setFileDoc($subir->getFilename());
                        break;
                }
            }
        }
        $id=$servicios->update($servicio);
        if($id){
            $data['res']['code']=10;
            $data['res']['message']=$id;
        }else{
            $data['res']['code']=11;
            $data['res']['message']='No se pudo actualizar';
        }
        break;
    case "del":
        $id=$servicios->delete($servicio);
        if($id){
            $data['res']['code']=10;
            $data['res']['message']=$id;
        }else{
            $data['res']['code']=11;
            $data['res']['message']='No se pudo borrar';
        }
        break;
}
header('Content-Type: application/json');
echo json_encode($data);