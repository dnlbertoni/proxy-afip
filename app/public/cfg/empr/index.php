<?php
    require_once '../../../conf/include.all.php';
    require_once ("../../../modelos/Empresas.php");
    $empresas = new \Empresa\Empresas();
    $data= $empresas->getEmpresas();
    $titulo="Empresas";
    ?>
<!doctype html>
<html lang="es">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="stylesheet" href="/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/all.min.css">
    <title>Empresa</title>
</head>
<body>
    <?php include('../../nav.php');?>
<div class="container">
    <div class="row">
        <div class="col-sm">
            <table class="table">
                <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Razon Social</th>
                    <th scope="col">CUIT</th>
                    <th scope="col">Estado</th>
                    <th scope="col">Acciones</th>
                </tr>
                </thead>
                <tbody>
                    <?php if(is_object($data)):?>
                        <?php foreach ($data as $d):?>
                        <tr>
                            <th scope="row"><?=$d->getId()?></th>
                            <td><?=$d->getRazonSocial()?></td>
                            <td><?=$d->getCuit()?></td>
                            <td><i class="fa fa-thumbs-<?=($d->getActivo()==1)?'up':'down';?>"></i> </td>
                            <td>
                                <a href="view.php?idempresa=<?=$d->getId()?>" class="btn btn-xs btn-outline-primary"><i class="fa fa-eye"></i> </a>
                                <a href="view.php?accion=edit&idempresa=<?=$d->getId()?>" class="btn btn-xs btn-outline-info"><i class="fa fa-edit"></i> </a>
                                <a href="view.php?accion=del&idempresa=<?=$d->getId()?>" class="btn btn-xs btn-outline-danger"><i class="fa fa-trash-alt"></i> </a>
                            </td>
                        </tr>
                        <?php endforeach;?>
                    <?php endif;?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2"><a href="/app/empresa/view.php" class="btn btn-outline-success"><i class="fa fa-plus-circle"></i> Agregar </a></td>
                        <td>&nbsp;</td>
                        <td colspan="2"><a href="/index.php" class="btn btn-outline-danger"><i class="fa fa-window-close"></i> Volver</a></td>

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