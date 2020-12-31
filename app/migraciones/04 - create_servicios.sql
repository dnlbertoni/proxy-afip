CREATE TABLE `servicios` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `nombre` varchar(255) NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `identorno` int NOT NULL,
  `file_wsdl` varchar(255) NOT NULL,
  `version` varchar(255) NOT NULL,
  `file_doc` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `idempresa` int NOT NULL,
  FOREIGN KEY (`identorno`) REFERENCES `entornos` (`id`),
  FOREIGN KEY (`idempresa`) REFERENCES `empresas` (`id`)
) ENGINE='InnoDB';