-- Criação do banco de dados
CREATE DATABASE medcar_financeiro;

-- Selecionando o banco de dados
USE medcar_financeiro;

-- Tabela para armazenar as transações, agora relacionadas a clientes, empresas e agendamentos
CREATE TABLE transacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NULL,
    empresa_id INT NULL,
    agendamento_id INT NULL, -- Relaciona a transação a um agendamento
    data DATE NOT NULL,
    descricao VARCHAR(255) NOT NULL,
    valor DECIMAL(10, 2) NOT NULL,
    status ENUM('Pago', 'Pendente', 'Cancelado') NOT NULL,
    paypal_transaction_id VARCHAR(255) DEFAULT NULL, -- ID da transação no PayPal
    FOREIGN KEY (cliente_id) REFERENCES medcar_cadastro_login.clientes(id) ON DELETE SET NULL,
    FOREIGN KEY (empresa_id) REFERENCES medcar_cadastro_login.empresas(id) ON DELETE SET NULL,
    FOREIGN KEY (agendamento_id) REFERENCES medcar_agendamentos.agendamentos(id) ON DELETE SET NULL
);

-- Tabela para armazenar os gráficos de faturamento mensal, agora relacionados a empresas
CREATE TABLE faturamento_mensal (
    id INT AUTO_INCREMENT PRIMARY KEY,
    empresa_id INT NOT NULL,
    mes INT NOT NULL,
    ano INT NOT NULL,
    faturamento DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (empresa_id) REFERENCES medcar_cadastro_login.empresas(id) ON DELETE CASCADE
);

-- Tabela para armazenar métodos de pagamento associados a clientes e empresas
CREATE TABLE metodos_pagamento (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NULL,
    empresa_id INT NULL,
    tipo ENUM('PayPal', 'Cartão de Crédito') NOT NULL,
    detalhes_pagamento TEXT NOT NULL, -- Ex.: JSON com informações do PayPal ou cartão
    FOREIGN KEY (cliente_id) REFERENCES medcar_cadastro_login.clientes(id) ON DELETE CASCADE,
    FOREIGN KEY (empresa_id) REFERENCES medcar_cadastro_login.empresas(id) ON DELETE CASCADE
);

-- Inserção de dados de transações de exemplo associadas a agendamentos
-- Transações mais completas (10 registros)
INSERT INTO transacoes (cliente_id, empresa_id, agendamento_id, data, descricao, valor, status, paypal_transaction_id) 
VALUES 
-- Transações de clientes
(1, NULL, 1, '2024-03-15', 'Pagamento de corrida - Agendamento 1', 850.00, 'Pago', 'PAYPAL12345'),
(2, NULL, 3, '2024-03-16', 'Pagamento de corrida - Agendamento 3', 1200.00, 'Pago', 'PAYPAL67890'),
(3, NULL, 5, '2024-03-17', 'Pagamento de corrida - Agendamento 5', 950.00, 'Pendente', NULL),
(4, NULL, 7, '2024-03-18', 'Pagamento de corrida - Agendamento 7', 1500.00, 'Pago', 'PAYPAL54321'),
(5, NULL, 9, '2024-03-19', 'Pagamento de corrida - Agendamento 9', 750.00, 'Cancelado', NULL),

-- Transações de empresas
(NULL, 1, 2, '2024-03-14', 'Pagamento de corrida - Agendamento 2', 2350.00, 'Pendente', NULL),
(NULL, 2, 4, '2024-03-15', 'Pagamento de corrida - Agendamento 4', 1800.00, 'Pago', 'PAYPAL98765'),
(NULL, 1, 6, '2024-03-16', 'Pagamento de corrida - Agendamento 6', 3200.00, 'Pago', 'PAYPAL24680'),
(NULL, 3, 8, '2024-03-17', 'Pagamento de corrida - Agendamento 8', 1450.00, 'Pendente', NULL),
(NULL, 2, 10, '2024-03-18', 'Pagamento de corrida - Agendamento 10', 2100.00, 'Pago', 'PAYPAL13579');


-- Inserção de dados de faturamento mensal de exemplo
-- Faturamento mensal mais completo (12 meses para 3 empresas)
INSERT INTO faturamento_mensal (empresa_id, mes, ano, faturamento) 
VALUES 
-- Empresa 1 (12 meses)
(1, 1, 2024, 120000.00),
(1, 2, 2024, 135000.00),
(1, 3, 2024, 152000.00),
(1, 4, 2024, 128000.00),
(1, 5, 2024, 142000.00),
(1, 6, 2024, 155000.00),
(1, 7, 2024, 148000.00),
(1, 8, 2024, 162000.00),
(1, 9, 2024, 138000.00),
(1, 10, 2024, 145000.00),
(1, 11, 2024, 158000.00),
(1, 12, 2024, 172000.00),

-- Empresa 2 (6 meses)
(2, 7, 2024, 85000.00),
(2, 8, 2024, 92000.00),
(2, 9, 2024, 88000.00),
(2, 10, 2024, 95000.00),
(2, 11, 2024, 102000.00),
(2, 12, 2024, 115000.00),

-- Empresa 3 (3 meses)
(3, 10, 2024, 48000.00),
(3, 11, 2024, 52000.00),
(3, 12, 2024, 58000.00);

-- Inserção de métodos de pagamento de exemplo
-- Métodos de pagamento mais variados (8 registros)
INSERT INTO metodos_pagamento (cliente_id, empresa_id, tipo, detalhes_pagamento) 
VALUES 
-- Clientes
(1, NULL, 'PayPal', '{"email": "cliente1@paypal.com", "conta_verificada": true}'),
(2, NULL, 'Cartão de Crédito', '{"numero": "4111111111111111", "validade": "12/2025", "bandeira": "Visa"}'),
(3, NULL, 'PayPal', '{"email": "cliente3@paypal.com", "conta_verificada": false}'),
(4, NULL, 'Cartão de Crédito', '{"numero": "5555555555554444", "validade": "06/2026", "bandeira": "Mastercard"}'),
(5, NULL, 'Cartão de Crédito', '{"numero": "378282246310005", "validade": "09/2024", "bandeira": "American Express"}'),

-- Empresas
(NULL, 1, 'PayPal', '{"email": "financeiro@empresa1.com", "conta_business": true}'),
(NULL, 2, 'Cartão de Crédito', '{"numero": "4222222222222", "validade": "03/2027", "bandeira": "Visa", "titular": "Empresa 2 LTDA"}'),
(NULL, 3, 'PayPal', '{"email": "contato@empresa3.com", "conta_business": false}');

SELECT 
    t.id,
    t.data,
    t.descricao,
    t.valor,
    t.status,
    c.nome AS cliente_nome,
    e.nome AS empresa_nome
FROM 
    transacoes t
LEFT JOIN 
    medcar_cadastro_login.clientes c ON t.cliente_id = c.id
LEFT JOIN 
    medcar_cadastro_login.empresas e ON t.empresa_id = e.id;