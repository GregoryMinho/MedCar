CREATE DATABASE IF NOT EXISTS medcar_agendamentos;
USE medcar_agendamentos;

-- Cria a tabela "pacientes_registros" para armazenar os dados dos pacientes
CREATE TABLE IF NOT EXISTS agentamentos_registros (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    data_consulta DATE NOT NULL,
    horario TIME NOT NULL,
    destino VARCHAR(255) NOT NULL,
    status ENUM('Agendado', 'Concluído', 'Cancelado') NOT NULL,
    data_agendamento TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    usuario_id INT NOT NULL,
    empresa_id INT NOT NULL
);

-- Insere dados de exemplo (os mesmos exibidos na página)
INSERT INTO pacientes_registros (nome, data_consulta, horario, destino, status, usuario_id, empresa_id) VALUES
('Maria Oliveira', '2024-03-22', '10:30:00', 'Clínica Saúde Total', 'Agendado', 1, 1),
('João Silva', '2024-03-15', '14:00:00', 'Hospital Santa Maria', 'Concluído', 2, 2),
('Carlos Magnos', '2024-03-25', '15:30:00', 'Hospital São Rafael', 'Cancelado', 3, 3);

-- Cria o TRIGGER para verificar a existência de usuario_id e empresa_id antes de inserir
DELIMITER //
CREATE TRIGGER before_insert_pacientes_registros
BEFORE INSERT ON pacientes_registros
FOR EACH ROW
BEGIN
    DECLARE usuario_exists INT;
    DECLARE empresa_exists INT;

    -- Verifica se o usuario_id existe no banco de dados de usuários
    SELECT COUNT(*) INTO usuario_exists FROM usuarios_db.usuarios WHERE id = NEW.usuario_id;
    IF usuario_exists = 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Usuario ID não existe';
    END IF;

    -- Verifica se o empresa_id existe no banco de dados de empresas
    SELECT COUNT(*) INTO empresa_exists FROM empresas_db.empresas WHERE id = NEW.empresa_id;
    IF empresa_exists = 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Empresa ID não existe';
    END IF;
END;
//
DELIMITER ;

-- Cria o TRIGGER para verificar a existência de usuario_id e empresa_id antes de atualizar
DELIMITER //
CREATE TRIGGER before_update_pacientes_registros
BEFORE UPDATE ON pacientes_registros
FOR EACH ROW
BEGIN
    DECLARE usuario_exists INT;
    DECLARE empresa_exists INT;

    -- Verifica se o usuario_id existe no banco de dados de usuários
    SELECT COUNT(*) INTO usuario_exists FROM usuarios_db.usuarios WHERE id = NEW.usuario_id;
    IF usuario_exists = 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Usuario ID não existe';
    END IF;

    -- Verifica se o empresa_id existe no banco de dados de empresas
    SELECT COUNT(*) INTO empresa_exists FROM empresas_db.empresas WHERE id = NEW.empresa_id;
    IF empresa_exists = 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Empresa ID não existe';
    END IF;
END;
//
DELIMITER ;