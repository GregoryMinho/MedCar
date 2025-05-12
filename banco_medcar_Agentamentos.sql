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
    valor DECIMAL(10, 2), -- Campo para armazenar o valor do agendamento
    data_cancelamento TIMESTAMP NULL, -- Campo para armazenar a data de cancelamento, se aplicável
    data_conclusao TIMESTAMP NULL, -- Campo para armazenar a data de conclusão, se aplicável
    agendado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES medcar_cadastro_login.clientes(id) ON DELETE CASCADE,
    FOREIGN KEY (empresa_id) REFERENCES medcar_cadastro_login.empresas(id) ON DELETE CASCADE
);


INSERT INTO agendamentos (cliente_id, empresa_id, data_consulta, horario, rua_origem, numero_origem, complemento_origem, cidade_origem, cep_origem, rua_destino, numero_destino, complemento_destino, cidade_destino, cep_destino, condicao_medica, precisa_oxigenio, precisa_assistencia, precisa_monitor, medicamentos, alergias, contato_emergencia, informacoes_adicionais, acompanhante, tipo_transporte, situacao) VALUES
(1, 1, CURDATE(), '10:30:00', 'Rua A', '123', 'Apto 1', 'Cidade A', '12345-678', 'Rua B', '456', 'Sala 2', 'Cidade B', '87654-321', 'Condição A', 1, 0, 0, 'Medicamento A', 'Alergia A', 'Contato A', 'Info A', 1, 'Padrão', 'Pendente'),
(1, 2, CURDATE(), '14:00:00', 'Rua C', '789', 'Apto 3', 'Cidade C', '23456-789', 'Rua D', '012', 'Sala 4', 'Cidade D', '98765-432', 'Condição B', 0, 1, 0, 'Medicamento B', 'Alergia B', 'Contato B', 'Info B', 0, 'Cadeirante', 'Agendado'),
(1, 3, CURDATE(), '15:30:00', 'Rua E', '345', 'Apto 5', 'Cidade E', '34567-890', 'Rua F', '678', 'Sala 6', 'Cidade F', '09876-543', 'Condição C', 0, 0, 1, 'Medicamento C', 'Alergia C', 'Contato C', 'Info C', 2, 'Maca', 'Concluído'),
(1, 1, CURDATE(), '09:00:00', 'Rua G', '111', 'Apto 7', 'Cidade G', '45678-123', 'Rua H', '222', 'Sala 8', 'Cidade H', '65432-987', 'Condição D', 1, 1, 0, 'Medicamento D', 'Alergia D', 'Contato D', 'Info D', 1, 'Padrão', 'Cancelado'),
(1, 2, '2023-07-05', '11:45:00', 'Rua I', '333', 'Apto 9', 'Cidade I', '56789-234', 'Rua J', '444', 'Sala 10', 'Cidade J', '76543-876', 'Condição E', 0, 0, 1, 'Medicamento E', 'Alergia E', 'Contato E', 'Info E', 0, 'Cadeirante', 'Pendente'),
(1, 3, '2024-08-20', '13:15:00', 'Rua K', '555', 'Apto 11', 'Cidade K', '67890-345', 'Rua L', '666', 'Sala 12', 'Cidade L', '87654-765', 'Condição F', 1, 1, 1, 'Medicamento F', 'Alergia F', 'Contato F', 'Info F', 2, 'Maca', 'Agendado'),
(1, 1, '2012-09-30', '16:00:00', 'Rua M', '777', 'Apto 13', 'Cidade M', '78901-456', 'Rua N', '888', 'Sala 14', 'Cidade N', '98765-654', 'Condição G', 0, 0, 0, 'Medicamento G', 'Alergia G', 'Contato G', 'Info G', 1, 'Padrão', 'Concluído');

SELECT * 
FROM medcar_agendamentos.agendamentos a
RIGHT JOIN medcar_cadastro_login.clientes c ON a.cliente_id = c.id
WHERE c.id = 1;

select * from agendamentos;