CREATE TABLE `parametros` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `nombre` int NOT NULL,
  `tipo` enum('CAMPO','LISTA','SINO') NOT NULL,
  `valor` int NOT NULL
) ENGINE='InnoDB';