CREATE DATABASE IF NOT EXISTS medcar;
USE medcar;

-- Cria a tabela "pacientes" para armazenar os dados dos pacientes
CREATE TABLE IF NOT EXISTS pacientes_registros (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    data_consulta DATE NOT NULL,
    horario TIME NOT NULL,
    hospital VARCHAR(255) NOT NULL,
    status ENUM('Agendado', 'Concluído', 'Cancelado') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insere dados de exemplo (os mesmos exibidos na página)
INSERT INTO pacientes_registros (nome, data_consulta, horario, hospital, status) VALUES
('Maria Oliveira', '2024-03-22', '10:30:00', 'Clínica Saúde Total', 'Agendado'),
('João Silva', '2024-03-15', '14:00:00', 'Hospital Santa Maria', 'Concluído'),
('Carlos Magnos', '2024-03-25', '15:30:00', 'Hospital São Rafael', 'Cancelado');

-- select * from pacientes_registros;