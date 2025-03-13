CREATE DATABASE medcar_cadastro_login;
USE medcar_cadastro_login;

CREATE TABLE clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL, -- A senha será armazenada criptografada
    cpf VARCHAR(14) UNIQUE NOT NULL, -- add um novo campo cpf que não tinha
    telefone VARCHAR(20),
     tipo VARCHAR(20) DEFAULT 'cliente', -- não usei isso aqui ainda 
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP --  ^ a mesma coisa pq não sei se isso vai ser viável 
);

CREATE TABLE empresas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL, -- A senha será armazenada criptografada
    telefone VARCHAR(20),
    cnpj VARCHAR(20) UNIQUE NOT NULL,
     tipo VARCHAR(20) DEFAULT 'empresa',
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- p database medcar_clientes;