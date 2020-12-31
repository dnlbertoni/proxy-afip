CREATE TABLE `entornos` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `nombre` varchar(255) NOT NULL,
  `debug_activo` int NOT NULL,
  `actual` int NOT NULL,
  `idempresa` int NOT NULL,
  FOREIGN KEY (`idempresa`) REFERENCES `empresas` (`id`)
) ENGINE='InnoDB';
