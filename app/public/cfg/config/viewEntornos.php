<?php
require_once ("../../lib/mysql/mysql.class.php");
require_once ("../../modelos/Entornos.php");
require_once ("../../modelos/Empresas.php");

$empresas =  new \Empresa\Empresas();
$empresasSel = $empresas->getEmpresas();


if(isset($_GET['identorno'])){
    $entornos = new \Config\Entornos();
    $data= $entornos->getById($_GET['identorno']);
    $accion=(isset($_GET['accion']))?$_GET['accion']:'view';
}else{
    $data= new \Config\Entorno();
    $accion='add';
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

    <title>Configuracion - Entorno - Agregar</title>
</head>
<body>
<div class="container">
    <h1>Entorno</h1>
    <div class="row">
        <div class="col-12">
            <form method="post" action="crudEntornos.php" enctype="multipart/form-data" id="form-ajax">
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
                    <label for="razon_social" class="col-5 col-form-label">Nombre</label>
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
                    <label for="idempresa" class="col-5 col-form-label">Empresa</label>
                    <div class="col-7">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="fa fa-qrcode"></i>
                                </div>
                            </div>
                            <select name="idempresa" id="idempresa" lass="form-control" required="required">
                                <?php foreach ($empresasSel as $s):?>
                                    <option value="<?=$s->getId()?>" <?=($s->getId()==$data->getIdempresa())?"selected='selected'":""?>><?=$s->getRazonSocial()?></option>
                                <?php endforeach;?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-5">Debug</label>
                    <div class="col-7">
                        <div class="custom-control custom-radio custom-control-inline">
                            <input name="debug_activo" id="debug_activo_0" type="radio" class="custom-control-input" value="1" <?=($data->getDebugActivo()==1)?'checked="checked"':'';?>>
                            <label for="debug_activo_0" class="custom-control-label">Si</label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input name="debug_activo" id="debug_activo_1" type="radio" class="custom-control-input" value="0"<?=($data->getDebugActivo()==0)?'checked="checked"':'';?>>
                            <label for="debug_activo_1" class="custom-control-label">No</label>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-5">Actual</label>
                    <div class="col-7">
                        <div class="custom-control custom-radio custom-control-inline">
                            <input name="actual" id="actual_0" type="radio" class="custom-control-input" value="1" <?=($data->getActual()==1)?'checked="checked"':'';?>>
                            <label for="actual_0" class="custom-control-label">Si</label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input name="actual" id="actual_1" type="radio" class="custom-control-input" value="0"<?=($data->getActual()==0)?'checked="checked"':'';?>>
                            <label for="actual_1" class="custom-control-label">No</label>
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