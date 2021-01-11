CREATE TABLE `errores_api` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `errorcode` int NOT NULL,
  `error_mesaje` varchar(255) NOT NULL,
  `error_explicacion` varchar(255) NOT NULL  
);
