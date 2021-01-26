<?php
    require_once ("../../lib/mysql/mysql.class.php");
    require_once ("../../modelos/Entornos.php");
    require_once ("../../modelos/Parametros.php");
    require_once ("../../modelos/Empresas.php");
    $entornos = new \Config\Entornos();
    $dataEntorno= $entornos->getEntornos();
    $parametros = new \Config\Parametros();
    $dataParametro= $parametros->getParametros();
    $empresas=new \Empresa\Empresas();
    ?>
<!doctype html>
<html lang="es">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="stylesheet" href="/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/all.min.css">
    <title>Configuraciones</title>
</head>
<body>
<div class="container">
    <h1>Configuraciones</h1>
    <div class="row">
        <div class="col-12">
            <div class="panel panel-danger">
                <div class="panel-header">Entornos</div>
                <div class="panel-body">
                    <table class="table">
                        <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Empresa</th>
                            <th scope="col">Nombre</th>
                            <th scope="col">Debug</th>
                            <th scope="col">Vigente</th>
                            <th scope="col">Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if(is_object($dataEntorno)):?>
                            <?php foreach ($dataEntorno as $d):?>
                                <tr>
                                    <th scope="row"><?=$d->getId()?></th>
                                    <td><?=(is_object($empresas->getById($d->getIdempresa())))?$empresas->getById($d->getIdempresa())->getRazonSocial():'';?></td>
                                    <td><?=$d->getNombre()?></td>
                                    <td><i class="fa fa-thumbs-<?=($d->getDebugActivo()==1)?'up':'down';?>"></i></td>
                                    <td><i class="fa fa-thumbs-<?=($d->getActual()==1)?'up':'down';?>"></i></td>
                                    <td>
                                        <a href="viewEntornos.php?identorno=<?=$d->getId()?>" class="btn btn-xs btn-outline-primary"><i class="fa fa-eye"></i> </a>
                                        <a href="viewEntornos.php?accion=edit&identorno=<?=$d->getId()?>" class="btn btn-xs btn-outline-info"><i class="fa fa-edit"></i> </a>
                                        <a href="viewEntornos.php?accion=del&identorno=<?=$d->getId()?>" class="btn btn-xs btn-outline-danger"><i class="fa fa-trash-alt"></i> </a>
                                    </td>
                                </tr>
                            <?php endforeach;?>
                        <?php endif;?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="2"><a href="/app/config/viewEntornos.php" class="btn btn-outline-success"><i class="fa fa-plus-circle"></i> Agregar </a></td>
                            <td>&nbsp;</td>
                            <td colspan="2"><a href="/index.php" class="btn btn-outline-danger"><i class="fa fa-window-close"></i> Volver</a></td>

                        </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="panel-footer">

                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="panel panel-danger">
                <div class="panel-header">Parametros</div>
                <div class="panel-body">
                    <table class="table">
                        <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Nombre</th>
                            <th scope="col">Valor</th>
                            <th scope="col">Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if(is_object($dataParametro)):?>
                            <?php foreach ($dataParametro as $d):?>
                                <tr>
                                    <th scope="row"><?=$d->getId()?></th>
                                    <td><?=$d->getNombre()?></td>
                                    <td><?=$d->getValor()?></td>
                                    <td>
                                        <a href="viewParametros.php?idparametro=<?=$d->getId()?>" class="btn btn-xs btn-outline-primary"><i class="fa fa-eye"></i> </a>
                                        <a href="viewParametros.php?accion=edit&idparametro=<?=$d->getId()?>" class="btn btn-xs btn-outline-info"><i class="fa fa-edit"></i> </a>
                                        <a href="viewParametros.php?accion=del&idparametro=<?=$d->getId()?>" class="btn btn-xs btn-outline-danger"><i class="fa fa-trash-alt"></i> </a>
                                    </td>
                                </tr>
                            <?php endforeach;?>
                        <?php endif;?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="2"><a href="/app/config/viewParametros.php" class="btn btn-outline-success"><i class="fa fa-plus-circle"></i> Agregar </a></td>
                            <td>&nbsp;</td>
                            <td colspan="2"><a href="/index.php" class="btn btn-outline-danger"><i class="fa fa-window-close"></i> Volver</a></td>

                        </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="panel-footer">

                </div>
            </div>
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