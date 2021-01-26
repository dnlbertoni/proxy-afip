ALTER TABLE `certificados`
CHANGE `tipo` `tipo` enum('CERT','PRIVATEKEY','CSR') COLLATE 'utf8mb4_0900_ai_ci' NOT NULL AFTER `filename`;