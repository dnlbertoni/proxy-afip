CREATE TABLE `certificados` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `identorno` int NOT NULL,
  `idempresa` int NOT NULL,
  `filename` varchar(255) NOT NULL,
  `tipo` int NOT NULL,
  `password_certificado` varchar(255) NOT NULL,
  `activo` int NOT NULL,
  `fechaemision` date NOT NULL,
  `fechavencimiento` date NOT NULL,
  `certifcado_raw` longtext NOT NULL,
  FOREIGN KEY (`identorno`) REFERENCES `entornos` (`id`),
  FOREIGN KEY (`idempresa`) REFERENCES `empresas` (`id`)
) ENGINE='InnoDB';