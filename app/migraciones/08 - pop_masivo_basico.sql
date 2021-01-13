
-- inserto la emrpesa 
INSERT INTO `empresas` (`id`,`razon_social`, `cuit`, `apitoken`, `activo`, `lastupdate`)
VALUES (1,'danielbertoni', '20268667033', 'A2WMR3CFdmrdJN7BdPUupzGU', '1', now());

-- inserto el entorno
INSERT INTO `entornos` (`id`, `nombre`, `debug_activo`, `actual`, `idempresa`) VALUES
(1,	'homolagacion_daniel',	1,	1,	1);

-- inserto el servicio de validacion

INSERT INTO `servicios` (`nombre`, `descripcion`, `identorno`, `file_wsdl`, `version`, `file_doc`, `url`, `idempresa`)
VALUES ('wsaa', 'validacion y obtencion del token para transacciones', '1', 'wsaa.wsdl', '1', '', 'https://wsaahomo.afip.gov.ar/ws/services/LoginCms', '1');


--inserto en el certificado
INSERT INTO `certificados` (`identorno`, `idempresa`, `filename`, `tipo`, `password_certificado`, `activo`, `fechaemision`, `fechavencimiento`, `certifcado_raw`)
VALUES ('1', '1', 'certificadoAFIP2686667033.crt', 1, '', '1', '2020-12-20', '2022-12-20', '');

INSERT INTO `certificados` (`identorno`, `idempresa`, `filename`, `tipo`, `password_certificado`, `activo`, `fechaemision`, `fechavencimiento`, `certifcado_raw`)
VALUES ('1', '1', 'danielbertoniproxyafip20268667033.csr', 2, '', '1', '2020-12-20', '2022-12-20', '');

