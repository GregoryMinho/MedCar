-- Criação do banco de dados
CREATE DATABASE IF NOT EXISTS medcar_chat
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

-- Usar o banco de dados criado
USE medcar_chat;

-- Criação da tabela de mensagens
CREATE TABLE IF NOT EXISTS `mensagens_chat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `empresa_id` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `sala` varchar(100) DEFAULT NULL,
  `remetente` varchar(50) DEFAULT NULL,
  `mensagem` text DEFAULT NULL,
  `data_envio` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `empresa_id` (`empresa_id`),
  KEY `cliente_id` (`cliente_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
