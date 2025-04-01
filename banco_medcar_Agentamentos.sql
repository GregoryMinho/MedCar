CREATE DATABASE IF NOT EXISTS medcar_agendamentos;
USE medcar_agendamentos;

-- Cria a tabela "agendamentos_registros" para armazenar os dados dos agendamentos
CREATE TABLE agendamentos(
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    empresa_id INT NOT NULL,
    data_consulta DATE NOT NULL,
    horario TIME NOT NULL,
    rua_origem VARCHAR(255),
    numero_origem VARCHAR(10),
    complemento_origem VARCHAR(255),
    cidade_origem VARCHAR(100),
    cep_origem VARCHAR(10),
    rua_destino VARCHAR(255),
    numero_destino VARCHAR(10),
    complemento_destino VARCHAR(255),
    cidade_destino VARCHAR(100),
    cep_destino VARCHAR(10),
    condicao_medica TEXT,
    precisa_oxigenio TINYINT,
    precisa_assistencia TINYINT,
    precisa_monitor TINYINT,
    medicamentos TEXT,
    alergias TEXT,
    contato_emergencia VARCHAR(255),
    informacoes_adicionais TEXT,
    acompanhante TINYINT,
    tipo_transporte VARCHAR(50),
    situacao ENUM('Pendente','Agendado', 'Concluido', 'Cancelado') NOT NULL default('Pendente'),
    observacoes VARCHAR(255), -- Campo para observações adicionais, como motivo do cancelamento
    agendado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


select * from agendamentos; 

-- Insere dados de exemplo (os mesmos exibidos na página)
INSERT INTO agendamentos (cliente_id, empresa_id, data_consulta, horario, rua_origem, numero_origem, complemento_origem, cidade_origem, cep_origem, rua_destino, numero_destino, complemento_destino, cidade_destino, cep_destino, condicao_medica, precisa_oxigenio, precisa_assistencia, precisa_monitor, medicamentos, alergias, contato_emergencia, informacoes_adicionais, acompanhante, tipo_transporte, situacao) VALUES
(1, 1, '2024-03-22', '10:30:00', 'Rua A', '123', 'Apto 1', 'Cidade A', '12345-678', 'Rua B', '456', 'Sala 2', 'Cidade B', '87654-321', 'Condição A', 1, 0, 0, 'Medicamento A', 'Alergia A', 'Contato A', 'Info A', 1, 'Padrão', 'Pendente'),
(2, 2, '2024-03-15', '14:00:00', 'Rua C', '789', 'Apto 3', 'Cidade C', '23456-789', 'Rua D', '012', 'Sala 4', 'Cidade D', '98765-432', 'Condição B', 0, 1, 0, 'Medicamento B', 'Alergia B', 'Contato B', 'Info B', 0, 'Cadeirante', 'Agendado'),
(3, 3, '2024-03-25', '15:30:00', 'Rua E', '345', 'Apto 5', 'Cidade E', '34567-890', 'Rua F', '678', 'Sala 6', 'Cidade F', '09876-543', 'Condição C', 0, 0, 1, 'Medicamento C', 'Alergia C', 'Contato C', 'Info C', 2, 'Maca', 'Concluído');

INSERT INTO agendamentos (cliente_id, empresa_id, data_consulta, horario, rua_origem, numero_origem, complemento_origem, cidade_origem, cep_origem, rua_destino, numero_destino, complemento_destino, cidade_destino, cep_destino, condicao_medica, precisa_oxigenio, precisa_assistencia, precisa_monitor, medicamentos, alergias, contato_emergencia, informacoes_adicionais, acompanhante, tipo_transporte, situacao) VALUES
(1, 1, '2012-03-22', '10:30:00', 'Rua A', '123', 'Apto 1', 'Cidade A', '12345-678', 'Rua B', '456', 'Sala 2', 'Cidade B', '87654-321', 'Condição A', 1, 0, 0, 'Medicamento A', 'Alergia A', 'Contato A', 'Info A', 1, 'Padrão', 'Pendente'),
(1, 2, '2023-04-15', '14:00:00', 'Rua C', '789', 'Apto 3', 'Cidade C', '23456-789', 'Rua D', '012', 'Sala 4', 'Cidade D', '98765-432', 'Condição B', 0, 1, 0, 'Medicamento B', 'Alergia B', 'Contato B', 'Info B', 0, 'Cadeirante', 'Agendado'),
(1, 3, '2024-05-25', '15:30:00', 'Rua E', '345', 'Apto 5', 'Cidade E', '34567-890', 'Rua F', '678', 'Sala 6', 'Cidade F', '09876-543', 'Condição C', 0, 0, 1, 'Medicamento C', 'Alergia C', 'Contato C', 'Info C', 2, 'Maca', 'Concluído'),
(1, 1, '2012-06-10', '09:00:00', 'Rua G', '111', 'Apto 7', 'Cidade G', '45678-123', 'Rua H', '222', 'Sala 8', 'Cidade H', '65432-987', 'Condição D', 1, 1, 0, 'Medicamento D', 'Alergia D', 'Contato D', 'Info D', 1, 'Padrão', 'Cancelado'),
(1, 2, '2023-07-05', '11:45:00', 'Rua I', '333', 'Apto 9', 'Cidade I', '56789-234', 'Rua J', '444', 'Sala 10', 'Cidade J', '76543-876', 'Condição E', 0, 0, 1, 'Medicamento E', 'Alergia E', 'Contato E', 'Info E', 0, 'Cadeirante', 'Pendente'),
(1, 3, '2024-08-20', '13:15:00', 'Rua K', '555', 'Apto 11', 'Cidade K', '67890-345', 'Rua L', '666', 'Sala 12', 'Cidade L', '87654-765', 'Condição F', 1, 1, 1, 'Medicamento F', 'Alergia F', 'Contato F', 'Info F', 2, 'Maca', 'Agendado'),
(1, 1, '2012-09-30', '16:00:00', 'Rua M', '777', 'Apto 13', 'Cidade M', '78901-456', 'Rua N', '888', 'Sala 14', 'Cidade N', '98765-654', 'Condição G', 0, 0, 0, 'Medicamento G', 'Alergia G', 'Contato G', 'Info G', 1, 'Padrão', 'Concluído');

SELECT * 
FROM medcar_agendamentos.agendamentos a
RIGHT JOIN medcar_cadastro_login.clientes c ON a.cliente_id = c.id
WHERE c.id = 1;



-- Cria o TRIGGER para verificar a existência de cliente_id e empresa_id antes de inserir
DELIMITER //
CREATE TRIGGER before_insert_agendamentos_registros
BEFORE INSERT ON agendamentos
FOR EACH ROW
BEGIN
    DECLARE cliente_exists INT;
    DECLARE empresa_exists INT;

      -- Verifica se o cliente_id existe no banco de dados de usuários
    SELECT COUNT(*) INTO cliente_exists FROM  medcar_cadastro_login.clientes WHERE id = NEW.cliente_id;
    IF cliente_exists = 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Cliente ID não existe';
    END IF;

    -- Verifica se o empresa_id existe na tabela de empresas
    SELECT COUNT(*) INTO empresa_exists FROM  medcar_cadastro_login.empresas WHERE id = NEW.empresa_id;
    IF empresa_exists = 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Empresa ID não existe';
    END IF;
END;
//
DELIMITER ;

-- Cria o TRIGGER para verificar a existência de cliente_id e empresa_id antes de atualizar
DELIMITER //
CREATE TRIGGER before_update_agendamentos_registros
BEFORE UPDATE ON agendamentos
FOR EACH ROW
BEGIN
    DECLARE cliente_exists INT;
    DECLARE empresa_exists INT;

    -- Verifica se o cliente_id existe no banco de dados de usuários
    SELECT COUNT(*) INTO cliente_exists FROM usuarios_db.usuarios WHERE id = NEW.cliente_id;
    IF cliente_exists = 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Cliente ID não existe';
    END IF;

    -- Verifica se o empresa_id existe no banco de dados de empresas
    SELECT COUNT(*) INTO empresa_exists FROM empresas_db.empresas WHERE id = NEW.empresa_id;
    IF empresa_exists = 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Empresa ID não existe';
    END IF;
END;
//
DELIMITER ;