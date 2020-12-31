CREATE TABLE `empresas` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `razon_social` varchar(255) NOT NULL,
  `cuit` int NOT NULL,
  `apitoken` varchar(255) NOT NULL,
  `activo` int NOT NULL,
  `lastupdate` timestamp NOT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE='InnoDB';