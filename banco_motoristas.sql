CREATE DATABASE Motoristas_MedCar;
USE Motoristas_MedCar;

-- Tabela de Motoristas
CREATE TABLE Motoristas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    cnh VARCHAR(20) UNIQUE NOT NULL,
    status ENUM('Ativo', 'Inativo', 'Em Serviço') NOT NULL,
    cidade VARCHAR(50) NOT NULL,
    estado VARCHAR(2) NOT NULL,
    foto_url VARCHAR(255)
);

-- Tabela de Veículos
CREATE TABLE Veiculos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    motorista_id INT NOT NULL,
    placa VARCHAR(10) UNIQUE NOT NULL,
    modelo VARCHAR(50) NOT NULL,
    tipo VARCHAR(50),
    status ENUM('disponivel', 'em_uso', 'manutencao') DEFAULT 'disponivel',
    ultima_manutencao DATE,
    proxima_manutencao DATE,
    FOREIGN KEY (motorista_id) REFERENCES Motoristas(id) ON DELETE CASCADE
);

-- Inserção de dados de exemplo
INSERT INTO Motoristas (nome, cnh, status, cidade, estado, foto_url) VALUES
('João Silva', '123456789', 'Ativo', 'São Paulo', 'SP', 'https://source.unsplash.com/random/80x80/?person'),
('Maria Oliveira', '987654321', 'Em Serviço', 'Rio de Janeiro', 'RJ', 'https://source.unsplash.com/random/80x80/?person');

INSERT INTO Veiculos (motorista_id, placa, modelo) VALUES
(1, 'ABC-1234', 'Sedan'),




(2, 'XYZ-5678', 'SUV');