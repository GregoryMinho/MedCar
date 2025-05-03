CREATE DATABASE medcar_cadastro_login;
USE medcar_cadastro_login;

CREATE TABLE clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL, -- A senha será armazenada criptografada
    cpf VARCHAR(20) UNIQUE NOT NULL,
    telefone VARCHAR(20),
    foto varchar(255) DEFAULT null , -- atualizado a cada login com o google
    tipo VARCHAR(20) DEFAULT 'cliente', 
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP, --  ^ a mesma coisa pq não sei se isso vai ser viável 
    data_nascimento DATE,
    contato_emergencia VARCHAR(255) DEFAULT ('não informado'),
);

 CREATE TABLE empresas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL, -- A senha será armazenada criptografada
    telefone VARCHAR(20),
    cnpj VARCHAR(20) UNIQUE NOT NULL,
    cep VARCHAR(9) NOT NULL, -- Adicionado o campo cep
    endereco VARCHAR(255) NOT NULL,
    cidade VARCHAR(100) NOT NULL,
    tipo VARCHAR(20) DEFAULT 'empresa',
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- nova Tabela de empresa_especialidades
CREATE TABLE empresa_especialidades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    empresa_id INT NOT NULL,
    especialidade VARCHAR(100) NOT NULL,
    FOREIGN KEY (empresa_id) REFERENCES empresas(id) ON DELETE CASCADE
);

-- nova Tabela de empresa_veiculos
CREATE TABLE empresa_veiculos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    empresa_id INT NOT NULL,
    tipo_veiculo VARCHAR(100) NOT NULL,
    FOREIGN KEY (empresa_id) REFERENCES empresas(id) ON DELETE CASCADE
);

CREATE TABLE detalhe_medico (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_cliente INT NOT NULL,
    tipo_sanguineo ENUM('A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-', 'NÃO IDENTIFICADO') NOT NULL,
    alergias TEXT DEFAULT('NÃO IDENTIFICADO/POSSUO'),
    doencas_cronicas TEXT DEFAULT('NÃO IDENTIFICADO/POSSUO'),
    remedio_recorrente TEXT DEFAULT('NÃO USO'),
    FOREIGN KEY (id_cliente) REFERENCES clientes(id) ON DELETE CASCADE
);

CREATE TABLE enderecos_clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_cliente INT NOT NULL,
    rua VARCHAR(255) NOT NULL,
    numero VARCHAR(20) NOT NULL,
    complemento VARCHAR(255),
    bairro VARCHAR(100),
    cidade VARCHAR(100) NOT NULL,
    estado VARCHAR(2) NOT NULL,
    cep VARCHAR(20) NOT NULL,
    FOREIGN KEY (id_cliente) REFERENCES clientes(id) ON DELETE CASCADE
);

INSERT INTO clientes (nome, email, senha, cpf, telefone) VALUES
('João Silva', 'joao.silva@email.com', '$2a$10$Xp1Q4J9z7JQZJZJZJZJZJO', '123.456.789-01', '(11) 98765-4321'),
('Maria Oliveira', 'maria.oliveira@email.com', '$2a$10$Xp1Q4J9z7JQZJZJZJZJZJO', '987.654.321-09', '(21) 99876-5432'),
('Carlos Pereira', 'carlos.pereira@email.com', '$2a$10$Xp1Q4J9z7JQZJZJZJZJZJO', '456.789.123-45', '(31) 98765-1234'),
('Ana Santos', 'ana.santos@email.com', '$2a$10$Xp1Q4J9z7JQZJZJZJZJZJO', '789.123.456-78', '(41) 98765-6789'),
('Pedro Costa', 'pedro.costa@email.com', '$2a$10$Xp1Q4J9z7JQZJZJZJZJZJO', '321.654.987-32', '(51) 98765-9876');

INSERT INTO empresas (nome, email, senha, telefone, cnpj, endereco, cidade) VALUES
('AutoMecânica Speed', 'speed@email.com', '$2a$10$Xp1Q4J9z7JQZJZJZJZJZJO', '(11) 3456-7890', '12.345.678/0001-01', 'Rua das Oficinas, 100', 'São Paulo'),
('Mecânica Master', 'master@email.com', '$2a$10$Xp1Q4J9z7JQZJZJZJZJZJO', '(21) 3344-5566', '23.456.789/0001-02', 'Avenida dos Carros, 200', 'Rio de Janeiro'),
('CarService Express', 'express@email.com', '$2a$10$Xp1Q4J9z7JQZJZJZJZJZJO', '(31) 9876-5432', '34.567.890/0001-03', 'Rua dos Motores, 300', 'Belo Horizonte'),
('AutoCenter Total', 'total@email.com', '$2a$10$Xp1Q4J9z7JQZJZJZJZJZJO', '(41) 3456-1234', '45.678.901/0001-04', 'Avenida das Peças, 400', 'Curitiba'),
('Mecânica Premium', 'premium@email.com', '$2a$10$Xp1Q4J9z7JQZJZJZJZJZJO', '(51) 3344-7788', '56.789.012/0001-05', 'Rua dos Veículos, 500', 'Porto Alegre');

