-- 1. Criar o banco de dados
CREATE DATABASE medq_transporte;

-- 2. Usar o banco de dados criado
USE medq_transporte;

-- 3. Criar a tabela de empresas de transporte
CREATE TABLE empresas_transporte (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome_empresa VARCHAR(255) NOT NULL,
    email VARCHAR(191) NOT NULL UNIQUE,
    senha_hash VARCHAR(255) NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
