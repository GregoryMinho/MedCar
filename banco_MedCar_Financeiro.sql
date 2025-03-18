-- Criação do banco de dados
CREATE DATABASE MedCar_Financeiro;

-- Selecionando o banco de dados
USE MedCar_Financeiro;

-- Tabela para armazenar as transações
CREATE TABLE transacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    data DATE NOT NULL,
    descricao VARCHAR(255) NOT NULL,
    valor DECIMAL(10, 2) NOT NULL,
    status ENUM('Pago', 'Pendente') NOT NULL
);

-- Tabela para armazenar as métricas financeiras
CREATE TABLE metricas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo VARCHAR(50) NOT NULL,
    valor DECIMAL(10, 2) NOT NULL,
    descricao VARCHAR(255) NOT NULL
);

-- Tabela para armazenar os gráficos de faturamento mensal
CREATE TABLE faturamento_mensal (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mes INT NOT NULL,
    ano INT NOT NULL,
    faturamento DECIMAL(10, 2) NOT NULL
);

-- Inserção de dados de transações de exemplo
INSERT INTO transacoes (data, descricao, valor, status) 
VALUES 
('2024-03-15', 'Transporte Paciente - J. Silva', 850.00, 'Pago'),
('2024-03-14', 'Manutenção Veicular', 2350.00, 'Pendente');

-- Inserção de dados de métricas financeiras
INSERT INTO metricas (tipo, valor, descricao) 
VALUES 
('faturamento', 152000.00, 'Faturamento Total'),
('despesas', 107000.00, 'Despesas Totais'),
('lucroliquido', 45000.00, 'Lucro Líquido'),
('ticketmedio', 780.00, 'Ticket Médio'),
('transacoes', 194, 'Número Total de Transações'),
('pendentes', 15000.00, 'Total de Pendentes'),
('clientesativos', 48, 'Número de Clientes Ativos');

-- Inserção de dados de faturamento mensal
INSERT INTO faturamento_mensal (mes, ano, faturamento) 
VALUES 
(1, 2024, 120000.00),
(2, 2024, 135000.00),
(3, 2024, 152000.00),
(4, 2024, 142000.00),
(5, 2024, 160000.00),
(6, 2024, 175000.00);