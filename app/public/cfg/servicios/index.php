<?php
    require_once '../../../conf/include.all.php';
    require_once ("../../../modelos/Servicios.php");
    require_once ("../../../modelos/Entornos.php");
    $servicios = new \Servicio\Servicios();
    $data= $servicios->getServicios();

    $entornos =  new \Config\Entornos();

    ?>
<!doctype html>
<html lang="es">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="stylesheet" href="/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/all.min.css">
    <title>Servicios</title>
</head>
<body>
<div class="container">
    <h1>Servicios del WS Configurados</h1>
    <div class="row">
        <div class="col-sm">
            <table class="table">
                <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Nombre</th>
                    <th scope="col">Entorno</th>
                    <th scope="col">Archivo WSDL</th>
                    <th scope="col">Version</th>
                    <th scope="col">Archivo Documentacion</th>
                    <th scope="col">URL Servicio</th>
                    <th scope="col">Acciones</th>
                </tr>
                </thead>
                <tbody>
                    <?php if(is_object($data)):?>
                        <?php foreach ($data as $d):?>
                        <tr>
                            <th scope="row"><?=$d->getId()?></th>
                            <td><?=$d->getNombre()?></td>
                            <td><?=$entornos->getById($d->getIdentorno())->getNombre()?></td>
                            <td><?=$d->getFileWsdl()?></td>
                            <td><?=$d->getVersion()?></td>
                            <td><?=$d->getFileDoc()?></td>
                            <td><?=$d->getUrl()?></td>
                            <td>
                                <a href="view.php?idservicio=<?=$d->getId()?>" class="btn btn-xs btn-outline-primary"><i class="fa fa-eye"></i> </a>
                                <a href="view.php?accion=edit&idservicio=<?=$d->getId()?>" class="btn btn-xs btn-outline-info"><i class="fa fa-edit"></i> </a>
                                <a href="view.php?accion=del&idservicio=<?=$d->getId()?>" class="btn btn-xs btn-outline-danger"><i class="fa fa-trash-alt"></i> </a>
                            </td>
                        </tr>
                        <?php endforeach;?>
                    <?php endif;?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4"><a href="/cfg/servicios/view.php" class="btn btn-outline-success"><i class="fa fa-plus-circle"></i> Agregar </a></td>
                        <td colspan="4"><a href="/index.php" class="btn btn-outline-danger"><i class="fa fa-window-close"></i> Volver</a></td>

                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="/assets/js/jquery-3.4.1.slim.min.js" ></script>
<script src="/assets/js/popper.min.js"></script>
<script src="/assets/js/bootstrap.min.js"></script>
</body>
</html>