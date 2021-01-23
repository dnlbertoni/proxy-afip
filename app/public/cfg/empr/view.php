<?php
require_once ("../../lib/mysql/mysql.class.php");
require_once ("../../modelos/Empresas.php");

if(isset($_GET['idempresa'])){
    $empresas = new \Empresa\Empresas();
    $data= $empresas->getById($_GET['idempresa']);
    $accion=(isset($_GET['accion']))?$_GET['accion']:'view';
}else{
    $data= new Empresa\Empresa();
    $accion='add';
}
switch ($accion){
    case "add":
        $nombreAccion="Agregar";
        break;
    case "edit":
        $nombreAccion="Editar";
        break;
    case "view":
        $nombreAccion="Ver";
        break;
    case "del":
        $nombreAccion="Borrar";
        break;
}
?>
<!doctype html>
<html lang="es">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/all.min.css">

    <title>Empresa - <?= $nombreAccion?></title>
</head>
<body>
<div class="container">
    <h1>Empresas - <?= $nombreAccion?></h1>
    <div class="row">
        <div class="col-12">
            <form method="post" action="crud.php" enctype="multipart/form-data" id="form-ajax">
                <input type="hidden" value="<?=$accion?>" name="accion"/>
                <div class="form-group row">
                    <label for="id" class="col-5 col-form-label">ID</label>
                    <div class="col-7">
                        <div class="input-group">
                            <input id="id" name="id" placeholder="ID" type="text" class="form-control" readonly value="<?=$data->getId();?>">
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="razon_social" class="col-5 col-form-label">Razon Social</label>
                    <div class="col-7">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="fa fa-house-user"></i>
                                </div>
                            </div>
                            <input id="razon_social" name="razon_social" placeholder="Razon Social" type="text" class="form-control" required="required" value="<?=$data->getRazonSocial();?>">
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="cuit" class="col-5 col-form-label">CUIT</label>
                    <div class="col-7">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="fa fa-qrcode"></i>
                                </div>
                            </div>
                            <input id="cuit" name="cuit" placeholder="CUIT" type="text" class="form-control" required="required" value="<?=$data->getCuit(); ?>">
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-5">Activo</label>
                    <div class="col-7">
                        <div class="custom-control custom-radio custom-control-inline">
                            <input name="activo" id="activo_0" type="radio" class="custom-control-input" value="1" <?=($data->getActivo()==1)?'checked="checked"':'';?>>
                            <label for="activo_0" class="custom-control-label">Si</label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input name="activo" id="activo_1" type="radio" class="custom-control-input" value="0"<?=($data->getActivo()==0)?'checked="checked"':'';?>>
                            <label for="activo_1" class="custom-control-label">No</label>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="offset-5 col-7">
                        <button name="submit" type="submit" class="btn btn-outline-success"><i class="fa fa-save"></i> Grabar</button>
                        <a href="index.php" class="btn btn-outline-danger"><i class="fa fa-window-close"></i> Volver</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="/assets/js/jquery-3.4.1.slim.min.js" ></script>
<script src="/assets/js/popper.min.js"></script>
<script src="/assets/js/bootstrap.min.js"></script>
<script>
    $(document).ready(function () {
        $("#form-ajax").on("submit", function(e){
            e.preventDefault();
            var f = $(this);
            var url = $(this).attr('action');
            var formData = new FormData(document.getElementById("form-ajax"));
            $.ajax({
                url: url,
                type: "post",
                dataType: "json",
                data: formData,
                cache: false,
                contentType: false,
                processData: false
            })
                .done(function(data){
                    //alert(data);
                    document.location='index.php';
                });
        });
    })
</script>
</body>
</html>