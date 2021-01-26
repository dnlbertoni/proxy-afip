<?php
    require_once '../../../conf/include.all.php';
    require_once ("../../../modelos/Empresas.php");
    require_once ("../../../modelos/Certificados.php");
    require_once ("../../../modelos/Entornos.php");
    $certificados = new \Certificado\Certificados();
    $data= $certificados->getCertificados();

    $entornos =  new \Config\Entornos();
    $empresas = new \Empresa\Empresas();

    ?>
<!doctype html>
<html lang="es">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="stylesheet" href="/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/all.min.css">
    <title>Certificados</title>
</head>
<body>
<div class="container">
    <h1>Certificados de los WS Configurados</h1>
    <div class="row">
        <div class="col-sm">
            <table class="table">
                <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Empresa</th>
                    <th scope="col">Entorno</th>
                    <th scope="col">Nombre Archivo</th>
                    <th scope="col">Tipo</th>
                    <th scope="col">Vencimiento</th>
                    <th scope="col">Activo</th>
                    <th scope="col">Acciones</th>
                </tr>
                </thead>
                <tbody>
                    <?php if(is_object($data)):?>
                        <?php foreach ($data as $d):?>
                        <tr>
                            <th scope="row"><?=$d->getId()?></th>
                            <td><?=$empresas->getById($d->getIdempresa())->getRazonSocial()?></td>
                            <td><?=$entornos->getById($d->getIdentorno())->getNombre()?></td>
                            <td><?=$d->getFilename()?></td>
                            <td><?=$d->getTipo()?></td>
                            <td><?=$d->getFechavencimiento()?></td>
                            <td><?=$d->getActivo()?></td>
                            <td>
                                <a href="view.php?idcertificado=<?=$d->getId()?>" class="btn btn-xs btn-outline-primary"><i class="fa fa-eye"></i> </a>
                                <a href="view.php?accion=edit&idcertificado=<?=$d->getId()?>" class="btn btn-xs btn-outline-info"><i class="fa fa-edit"></i> </a>
                                <a href="view.php?accion=del&idcertificado=<?=$d->getId()?>" class="btn btn-xs btn-outline-danger"><i class="fa fa-trash-alt"></i> </a>
                            </td>
                        </tr>
                        <?php endforeach;?>
                    <?php endif;?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4"><a href="/app/certificados/view.php" class="btn btn-outline-success"><i class="fa fa-plus-circle"></i> Agregar </a></td>
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