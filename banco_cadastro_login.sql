CREATE DATABASE medcar_cadastro_login;
USE medcar_cadastro_login;

CREATE TABLE clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL, -- A senha será armazenada criptografada
    cpf VARCHAR(20) UNIQUE NOT NULL,
    telefone VARCHAR(20),
    tipo VARCHAR(20) DEFAULT 'cliente', 
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP --  ^ a mesma coisa pq não sei se isso vai ser viável 
);

CREATE TABLE empresas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL, -- A senha será armazenada criptografada
    telefone VARCHAR(20),
    cnpj VARCHAR(20) UNIQUE NOT NULL,
    endereco VARCHAR(255) NOT NULL,
    cidade VARCHAR(100) NOT NULL,
    tipo VARCHAR(20) DEFAULT 'empresa',
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE detalhe_medico (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_cliente INT NOT NULL,
    tipo_sanguineo ENUM('A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-', 'NÃO IDENTIFICADO') NOT NULL,
    alergias TEXT DEFAULT('NÃO IDENTIFICADO/POSSUO'),
    remedio_recorrente TEXT DEFAULT('NÃO USO'),
    FOREIGN KEY (id_cliente) REFERENCES clientes(id) ON DELETE CASCADE
);

CREATE TABLE login_google_cliente(
    id int auto_increment primary key,
    id_cliente int not null,
    foto_perfil varchar(255) not null,
    FOREIGN KEY (id_cliente) REFERENCES clientes(id) ON DELETE CASCADE
    
)