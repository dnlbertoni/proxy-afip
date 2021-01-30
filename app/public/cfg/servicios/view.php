<?php
    require_once '../../../conf/include.all.php';
require_once ("../../../modelos/Servicios.php");
require_once ("../../../modelos/Entornos.php");
require_once ("../../../modelos/Empresas.php");

$entornos =  new \Config\Entornos();
$entornosSel = $entornos->getEntornos();

$empresas =  new \Empresa\Empresas();
$empresasSel = $empresas->getEmpresas();

if(isset($_GET['idservicio'])){
    $servicios = new \Servicio\Servicios();
    $data= $servicios->getById($_GET['idservicio']);
    $accion=(isset($_GET['accion']))?$_GET['accion']:'view';
}else{
    $data= new Servicio\Servicio();
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

    <title>Servicios </title>
</head>
<body>
<div class="container">
    <h1>Servicios - <?=$nombreAccion?></h1>
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
                    <label for="nombre" class="col-5 col-form-label">Nombre</label>
                    <div class="col-7">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="fa fa-house-user"></i>
                                </div>
                            </div>
                            <input id="nombre" name="nombre" placeholder="Nombre" type="text" class="form-control" <?=($accion=="add")?'required="required"':'';?> value="<?=$data->getNombre();?>">
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="descripcion" class="col-5 col-form-label">Descripcion</label>
                    <div class="col-7">
                        <div class="input-group">
                            <textarea id="descripcion" name="descripcion" class="form-control" cols="40" rows="10" aria-describedby="textareaHelpBlock"> <?=$data->getDescripcion();?></textarea>
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
                    <label for="identorno" class="col-5 col-form-label">Entorno</label>
                    <div class="col-7">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="fa fa-qrcode"></i>
                                </div>
                            </div>
                            <select name="identorno" id="identorno" lass="form-control" required="required">
                                <?php foreach ($entornosSel as $s):?>
                                    <option value="<?=$s->getId()?>"><?=$s->getNombre()?></option>
                                <?php endforeach;?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="file_wsdl" class="col-5 col-form-label">Archivo WSDL</label>
                    <div class="col-7">
                        <div class="input-group">
                            <input id="file_wsdl" name="file_wsdl" placeholder="Archivo WSDL" type="file" class="form-control"  <?=($accion=="add")?'required="required"':'';?> value="<?=$data->getFileWsdl();?>">
                            <div class="input-group-append" id="btn-chg-wsdl">
                                <div class="input-group-text">
                                    <i class="fa fa-house-user"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="version" class="col-5 col-form-label">Version</label>
                    <div class="col-7">
                        <div class="input-group">
                            <div class="input-group-prepend" >
                                <div class="input-group-text">
                                    <i class="fa fa-house-user"></i>
                                </div>
                            </div>
                            <input id="version" name="version" placeholder="Version" type="text" class="form-control"  <?=($accion="add")?'required="required"':'';?> value="<?=$data->getVersion();?>">
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="file_doc" class="col-5 col-form-label">Archivo Documentacion</label>
                    <div class="col-7">
                        <div class="input-group">
                            <input id="file_doc" name="file_doc" placeholder="Archivo Documentacion" type="file" class="form-control"  value="<?=$data->getFileDoc();?>">
                            <div class="input-group-append" id="btn-chg-doc">
                                <div class="input-group-text">
                                    <i class="fa fa-file"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="url" class="col-5 col-form-label">Url Servicio</label>
                    <div class="col-7">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="fa fa-house-user"></i>
                                </div>
                            </div>
                            <input id="url" name="url" placeholder="URL servicio" type="text" class="form-control" required="required" value="<?=$data->getUrl();?>">
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
        $('#btn-chg-wsdl').click(function () {
            var sel=$("#file_wsdl");
            var valor = sel.attr('type');
            if(valor==="file"){
                valor="text";
            }else{
                valor="file";
            }
            sel.attr('type',valor);
        })
        $('#btn-chg-doc').click(function () {
            var sel=$("#file_doc");
            var valor = sel.attr('type');
            if(valor==="file"){
                valor="text";
            }else{
                valor="file";
            }
            sel.attr('type',valor);
        })
        $(function() {
            var entorno = function () {
                var selected = $('#idempresa').val();
                $('#identorno').empty();
                $.getJSON("/api/select.php?entidad=entorno&empresa=" + selected, null, function (data) {
                    data.forEach(function (element, index) {
                        $('#identorno').append('<option value="' + element.key + '">' + element.label + '</option>');
                    });
                });
            };
            $('#idempresa').change(entorno);
            entorno();
        });
    })
</script>
</body>
</html>


