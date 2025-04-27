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
INSERT INTO transacoes (cliente_id, empresa_id, agendamento_id, data, descricao, valor, status, paypal_transaction_id) 
VALUES 
(1, NULL, 1, '2024-03-15', 'Pagamento de corrida - Agendamento 1', 850.00, 'Pago', 'PAYPAL12345'),
(NULL, 1, 2, '2024-03-14', 'Pagamento de corrida - Agendamento 2', 2350.00, 'Pendente', NULL);

-- Inserção de dados de faturamento mensal de exemplo
INSERT INTO faturamento_mensal (empresa_id, mes, ano, faturamento) 
VALUES 
(1, 1, 2024, 120000.00),
(1, 2, 2024, 135000.00),
(1, 3, 2024, 152000.00);

-- Inserção de métodos de pagamento de exemplo
INSERT INTO metodos_pagamento (cliente_id, tipo, detalhes_pagamento) 
VALUES 
(1, 'PayPal', '{"email": "cliente1@paypal.com"}'),
(2, 'Cartão de Crédito', '{"numero": "4111111111111111", "validade": "12/2025"}');

INSERT INTO metodos_pagamento (empresa_id, tipo, detalhes_pagamento) 
VALUES 
(1, 'PayPal', '{"email": "empresa1@paypal.com"}');