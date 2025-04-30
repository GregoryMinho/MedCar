-- Criação do banco de dados
CREATE DATABASE medcar_avaliacoes;
USE medcar_avaliacoes;

-- Tabela de Motoristas (mantida igual)
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

-- Tabela de Veículos (mantida igual)
CREATE TABLE veiculos (
    veiculo_id INT AUTO_INCREMENT PRIMARY KEY,
    placa VARCHAR(10) UNIQUE NOT NULL,
    modelo VARCHAR(50) NOT NULL,
    ano INT NOT NULL,
    capacidade INT NOT NULL,
    tipo_veiculo ENUM('Padrão', 'Adaptado', 'UTI') NOT NULL
);

-- Tabela de Motorista_Veículo (mantida igual)
CREATE TABLE motorista_veiculo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    motorista_id INT NOT NULL,
    veiculo_id INT NOT NULL,
    data_vinculo DATE NOT NULL,
    ativo BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (motorista_id) REFERENCES motoristas(motorista_id),
    FOREIGN KEY (veiculo_id) REFERENCES veiculos(veiculo_id)
);

-- Tabela de Pacientes (mantida igual)
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

-- Tabela de Transportes (Viagens) (mantida igual)
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

-- Tabela de Avaliações MODIFICADA (adicionado empresa_id)
CREATE TABLE avaliacoes (
    avaliacao_id INT AUTO_INCREMENT PRIMARY KEY,
    transporte_id INT NOT NULL,
    paciente_id INT NOT NULL,
    motorista_id INT NOT NULL,
    empresa_id INT NOT NULL, -- NOVA COLUNA
    nota INT NOT NULL CHECK (nota BETWEEN 1 AND 5),
    comentario TEXT,
    data_avaliacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    lida BOOLEAN DEFAULT FALSE,
    resposta TEXT,
    data_resposta DATETIME,
    FOREIGN KEY (transporte_id) REFERENCES transportes(transporte_id),
    FOREIGN KEY (paciente_id) REFERENCES pacientes(paciente_id),
    FOREIGN KEY (motorista_id) REFERENCES motoristas(motorista_id),
    FOREIGN KEY (empresa_id) REFERENCES medcar_cadastro_login.empresas(id) -- REFERÊNCIA EXTERNA
);

-- Tabela de Critérios de Avaliação (mantida igual)
CREATE TABLE criterios_avaliacao (
    criterio_id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT NOT NULL,
    peso INT DEFAULT 1
);

-- Tabela de Avaliação por Critério (mantida igual)
CREATE TABLE avaliacao_criterios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    avaliacao_id INT NOT NULL,
    criterio_id INT NOT NULL,
    nota INT NOT NULL CHECK (nota BETWEEN 1 AND 5),
    FOREIGN KEY (avaliacao_id) REFERENCES avaliacoes(avaliacao_id),
    FOREIGN KEY (criterio_id) REFERENCES criterios_avaliacao(criterio_id)
);

-- Inserção de dados MODIFICADA (adicionado empresa_id)
INSERT INTO avaliacoes (transporte_id, paciente_id, motorista_id, empresa_id, nota, comentario, lida) VALUES
(1, 1, 1, 1, 5, 'Motorista muito atencioso, dirigiu com cuidado e me ajudou com os pertences.', TRUE),
(2, 2, 2, 2, 4, 'Bom serviço, mas o veículo poderia estar mais limpo.', FALSE);

-- Restante das inserções mantido igual...