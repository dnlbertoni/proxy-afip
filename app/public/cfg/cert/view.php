<?php
    require_once '../../../conf/include.all.php';
require_once ("../../../modelos/Certificados.php");
require_once ("../../../modelos/Entornos.php");
require_once ("../../../modelos/Empresas.php");

$entornos =  new \Config\Entornos();
$entornosSel = $entornos->getEntornos();

$empresas =  new \Empresa\Empresas();
$empresasSel = $empresas->getEmpresas();

if(isset($_GET['idcertificado'])){
    $servicios = new \Certificado\Certificados();
    $data= $servicios->getById($_GET['idcertificado']);
    $accion=(isset($_GET['accion']))?$_GET['accion']:'view';
}else{
    $data= new \Certificado\Certificado();
    $accion='add';
}
$titulo="Certificados";
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

    <title>Certificados - Agregar</title>
</head>
<body>
<?php include('../../nav.php');?>
<div class="container">
    <h1>Certificados</h1>
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
                            <select name="identorno" id="identono" lass="form-control" required="required">
                                <?php foreach ($entornosSel as $s):?>
                                    <option value="<?=$s->getId()?>"<?=($s->getId()==$data->getIdentorno())?"selected='selected'":""?> ><?=$s->getNombre()?></option>
                                <?php endforeach;?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="filename" class="col-5 col-form-label">Archivo </label>
                    <div class="col-7">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="fa fa-file-archive"></i>
                                </div>
                            </div>
                            <input id="filename" name="filename" placeholder="Archivo de certificado " type="text" class="form-control" required="required" value="<?=$data->getFilename();?>">
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="tipo" class="col-5 col-form-label">Tipo</label>
                    <div class="col-7">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="fa fa-qrcode"></i>
                                </div>
                            </div>
                            <select name="tipo" id="tipo" lass="form-control" required="required">
                                    <option value="CERT"<?=($data->getTipo()=='CERT')?"selected='selected'":""?> >Certificado Bajado de AFIP</option>
                                    <option value="PRIVATEKEY"<?=($data->getTipo()=='PRIVATEKEY')?"selected='selected'":""?> >Certificado del Server</option>
                                    <option value="CSR"<?=($data->getTipo()=='CSR')?"selected='selected'":""?> >Certificado para que la AFIP genere</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="password_certificado" class="col-5 col-form-label">Password del Certificado (si tiene)</label>
                    <div class="col-7">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="fa fa-passport"></i>
                                </div>
                            </div>
                            <input id="password_certificado" name="password_certificado" placeholder="Password del Certificado (si tiene)" type="text" class="form-control" required="required" value="<?=$data->getPasswordCertificado();?>">
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
                    <label for="fechaemision" class="col-5 col-form-label">Fecha emision</label>
                    <div class="col-7">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="fa fa-house-user"></i>
                                </div>
                            </div>
                            <input id="fechaemision" name="fechaemision" placeholder="Fecha Emision" type="text" class="form-control" value="<?=$data->getFechaemision();?>">
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="fechavencimiento" class="col-5 col-form-label">Fecha Vto</label>
                    <div class="col-7">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="fa fa-house-user"></i>
                                </div>
                            </div>
                            <input id="fechavencimiento" name="fechavencimiento" placeholder="Fecha Vencimiento " type="text" class="form-control" required="required" value="<?=$data->getFechavencimiento();?>">
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="certificado_raw" class="col-5 col-form-label">Certificado (texto)</label>
                    <div class="col-7">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="fa fa-certificate"></i>
                                </div>
                            </div>
                            <textarea id="certificado_raw" name="certificado_raw" placeholder="Certificado" type="text" class="form-control" required="required" 
                            value="<?=$data->getCertificadoRaw();?>" ></textarea>
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
                    //document.location='index.php';
                });
        });
    })
</script>
</body>
</html>