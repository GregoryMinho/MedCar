-- Criação do banco de dados
CREATE DATABASE medq_avaliacoes;
USE medcar_avaliacoes;

-- Tabela de Motoristas
CREATE TABLE motoristas (
    motorista_id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    cpf VARCHAR(14) UNIQUE NOT NULL,
    cnh VARCHAR(20) NOT NULL,
    data_nascimento DATE NOT NULL,
    telefone VARCHAR(20) NOT NULL,
    email VARCHAR(100),
    foto_perfil VARCHAR(255),
    data_cadastro DATETIME DEFAULT CURRENT_TIMESTAMP,
    ativo BOOLEAN DEFAULT TRUE
);

-- Tabela de Veículos
CREATE TABLE veiculos (
    veiculo_id INT AUTO_INCREMENT PRIMARY KEY,
    placa VARCHAR(10) UNIQUE NOT NULL,
    modelo VARCHAR(50) NOT NULL,
    ano INT NOT NULL,
    capacidade INT NOT NULL,
    tipo_veiculo ENUM('Padrão', 'Adaptado', 'UTI') NOT NULL
);

-- Tabela de Motorista_Veículo (Relacionamento N:N)
CREATE TABLE motorista_veiculo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    motorista_id INT NOT NULL,
    veiculo_id INT NOT NULL,
    data_vinculo DATE NOT NULL,
    ativo BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (motorista_id) REFERENCES motoristas(motorista_id),
    FOREIGN KEY (veiculo_id) REFERENCES veiculos(veiculo_id)
);

-- Tabela de Pacientes
CREATE TABLE pacientes (
    paciente_id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    cpf VARCHAR(14) UNIQUE NOT NULL,
    data_nascimento DATE NOT NULL,
    telefone VARCHAR(20) NOT NULL,
    email VARCHAR(100),
    tipo_sanguineo VARCHAR(3),
    alergias TEXT,
    data_cadastro DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de Transportes (Viagens)
CREATE TABLE transportes (
    transporte_id INT AUTO_INCREMENT PRIMARY KEY,
    paciente_id INT NOT NULL,
    motorista_id INT NOT NULL,
    veiculo_id INT NOT NULL,
    origem VARCHAR(255) NOT NULL,
    destino VARCHAR(255) NOT NULL,
    data_hora_saida DATETIME NOT NULL,
    data_hora_chegada DATETIME,
    status ENUM('Agendado', 'Em Andamento', 'Concluído', 'Cancelado') DEFAULT 'Agendado',
    observacoes TEXT,
    FOREIGN KEY (paciente_id) REFERENCES pacientes(paciente_id),
    FOREIGN KEY (motorista_id) REFERENCES motoristas(motorista_id),
    FOREIGN KEY (veiculo_id) REFERENCES veiculos(veiculo_id)
);

-- Tabela de Avaliações
CREATE TABLE avaliacoes (
    avaliacao_id INT AUTO_INCREMENT PRIMARY KEY,
    transporte_id INT NOT NULL,
    paciente_id INT NOT NULL,
    motorista_id INT NOT NULL,
    nota INT NOT NULL CHECK (nota BETWEEN 1 AND 5),
    comentario TEXT,
    data_avaliacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    lida BOOLEAN DEFAULT FALSE,
    resposta TEXT,
    data_resposta DATETIME,
    FOREIGN KEY (transporte_id) REFERENCES transportes(transporte_id),
    FOREIGN KEY (paciente_id) REFERENCES pacientes(paciente_id),
    FOREIGN KEY (motorista_id) REFERENCES motoristas(motorista_id)
);

-- Tabela de Critérios de Avaliação
CREATE TABLE criterios_avaliacao (
    criterio_id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT NOT NULL,
    peso INT DEFAULT 1
);

-- Tabela de Avaliação por Critério
CREATE TABLE avaliacao_criterios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    avaliacao_id INT NOT NULL,
    criterio_id INT NOT NULL,
    nota INT NOT NULL CHECK (nota BETWEEN 1 AND 5),
    FOREIGN KEY (avaliacao_id) REFERENCES avaliacoes(avaliacao_id),
    FOREIGN KEY (criterio_id) REFERENCES criterios_avaliacao(criterio_id)
);

-- Inserção de dados iniciais
INSERT INTO motoristas (nome, cpf, cnh, data_nascimento, telefone, email, foto_perfil) VALUES
('João Silva', '123.456.789-01', '12345678901', '1980-05-15', '(11) 99999-9999', 'joao.silva@email.com', 'joao.jpg'),
('Maria Souza', '987.654.321-09', '98765432109', '1985-08-20', '(11) 98888-8888', 'maria.souza@email.com', 'maria.jpg');

INSERT INTO veiculos (placa, modelo, ano, capacidade, tipo_veiculo) VALUES
('ABC-1234', 'Fiat Ducato', 2022, 3, 'Padrão'),
('XYZ-9876', 'Mercedes Sprinter', 2021, 2, 'Adaptado');

INSERT INTO motorista_veiculo (motorista_id, veiculo_id, data_vinculo) VALUES
(1, 1, '2023-01-10'),
(2, 2, '2023-01-15');

INSERT INTO pacientes (nome, cpf, data_nascimento, telefone, email, tipo_sanguineo, alergias) VALUES
('Ana Clara Rodrigues', '111.222.333-44', '1975-03-25', '(11) 97777-7777', 'ana.rodrigues@email.com', 'A+', 'Penicilina'),
('Carlos Eduardo', '555.666.777-88', '1982-11-12', '(11) 96666-6666', 'carlos.eduardo@email.com', 'O-', NULL);

INSERT INTO transportes (paciente_id, motorista_id, veiculo_id, origem, destino, data_hora_saida, data_hora_chegada, status, observacoes) VALUES
(1, 1, 1, 'Hospital São Paulo', 'Rua das Flores, 123', '2023-05-15 14:00:00', '2023-05-15 14:45:00', 'Concluído', 'Paciente com dificuldade de locomoção'),
(2, 2, 2, 'Clínica Médica Central', 'Avenida Paulista, 1000', '2023-05-16 09:30:00', '2023-05-16 10:15:00', 'Concluído', 'Necessidade de cadeira de rodas');

INSERT INTO criterios_avaliacao (nome, descricao, peso) VALUES
('Pontualidade', 'O motorista chegou no horário agendado?', 1),
('Atendimento', 'O motorista foi cortês e profissional?', 1),
('Condução', 'O motorista dirigiu com segurança?', 2),
('Estado do Veículo', 'O veículo estava limpo e em boas condições?', 1),
('Cuidado com Paciente', 'O motorista demonstrou cuidado com o paciente?', 2);

INSERT INTO avaliacoes (transporte_id, paciente_id, motorista_id, nota, comentario, lida) VALUES
(1, 1, 1, 5, 'Motorista muito atencioso, dirigiu com cuidado e me ajudou com os pertences.', TRUE),
(2, 2, 2, 4, 'Bom serviço, mas o veículo poderia estar mais limpo.', FALSE);

INSERT INTO avaliacao_criterios (avaliacao_id, criterio_id, nota) VALUES
(1, 1, 5), (1, 2, 5), (1, 3, 5), (1, 4, 5), (1, 5, 5),
(2, 1, 4), (2, 2, 5), (2, 3, 4), (2, 4, 3), (2, 5, 4);