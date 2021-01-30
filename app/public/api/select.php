<?php
    require_once '../../conf/include.all.php';
require_once ("../../modelos/Entornos.php");
require_once ("../../modelos/Parametros.php");
require_once ("../../modelos/Empresas.php");
$data=array();
if(isset($_GET['entidad'])){
    switch ($_GET['entidad']){
        case 'entorno':
            $entornos = new \Config\Entornos();
            $empresa = (isset($_GET['empresa']))?$_GET['empresa']:null;
            foreach ($entornos->getEntornos($empresa) as $e){
                $data[] = array('key'=>$e->getId(),'label'=>$e->getNombre());
            }
            break;
    }
}else{
    $data[] = array('key'=>0,'label'=>'Seleccione...');
}
header('Content-Type: application/json');
echo json_encode($data);