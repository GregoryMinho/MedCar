CREATE DATABASE medcar_cadastro_login;
USE medcar_cadastro_login;

CREATE TABLE clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL, -- A senha será armazenada criptografada
    telefone VARCHAR(20),
<<<<<<< Updated upstream
    tipo DEFAULT 'cliente',
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
=======
    tipo VARCHAR(20) DEFAULT 'cliente', 
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP --  ^ a mesma coisa pq não sei se isso vai ser viável 
>>>>>>> Stashed changes
);

CREATE TABLE empresas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL, -- A senha será armazenada criptografada
    telefone VARCHAR(20),
    cnpj VARCHAR(20) UNIQUE NOT NULL,
<<<<<<< Updated upstream
    tipo DEFAULT 'empresa',
=======
    tipo VARCHAR(20) DEFAULT 'empresa',
>>>>>>> Stashed changes
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- p database medcar_clientes;