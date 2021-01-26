<?php
require_once ("../../lib/mysql/mysql.class.php");
require_once ("../../modelos/Parametros.php");

if(isset($_GET['idparametro'])){
    $parametros = new \Config\Parametros();
    $data= $parametros->getById($_GET['idparametro']);
    $accion=(isset($_GET['accion']))?$_GET['accion']:'view';
}else{
    $data= new \Config\Parametro();
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

    <title>Configuracion - Parametros - <?=$nombreAccion?></title>
</head>
<body>
<div class="container">
    <h1>Parametro - <?=$nombreAccion?></h1>
    <div class="row">
        <div class="col-12">
            <form method="post" action="crudParametros.php" enctype="multipart/form-data" id="form-ajax">
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
                    <label for="nombre" class="col-5 col-form-label">Nombre</label>
                    <div class="col-7">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="fa fa-engine"></i>
                                </div>
                            </div>
                            <input id="nombre" name="nombre" placeholder="Nombre" type="text" class="form-control" required="required" value="<?=$data->getNombre();?>">
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="valor" class="col-5 col-form-label">Valor</label>
                    <div class="col-7">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="fa fa-engine"></i>
                                </div>
                            </div>
                            <input id="valor" name="valor" placeholder="Valor" type="text" class="form-control" required="required" value="<?=$data->getValor();?>">
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