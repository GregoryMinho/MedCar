 CREATE DATABASE IF NOT EXISTS dashboard_medcar;
USE dashboard_medcar;

-- Tabela de Resumo Diário (Métricas do Dashboard)
CREATE TABLE resumo_diario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    data_resumo DATE NOT NULL,
    quantidade_servicos INT DEFAULT 0,    -- Número de serviços/agendamentos realizados no dia (Serviços Hoje)
    faturamento DECIMAL(10,2) DEFAULT 0.00,   -- Faturamento do dia
    avaliacao DECIMAL(3,1) DEFAULT 0.0,       -- Avaliação média (ex.: 4.8)
    pendencias INT DEFAULT 0                  -- Número de pendências
);

-- Inserindo dados na tabela resumo_diario
INSERT INTO resumo_diario (data_resumo, quantidade_servicos, faturamento, avaliacao, pendencias) VALUES 
('2024-03-14', 5, 1500.00, 4.5, 1),
('2024-03-15', 3, 900.00, 4.8, 0),
('2024-03-16', 4, 1200.00, 4.2, 2);