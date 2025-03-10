-- Criando o banco de dados
CREATE DATABASE IF NOT EXISTS medcar_agendamentos;

-- Selecionando o banco de dados
USE medcar_agendamentos;

-- Estrutura para tabela `empresas`
CREATE TABLE `empresas` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Estrutura para tabela `pacientes_registros`
CREATE TABLE `pacientes_registros` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `data_consulta` date NOT NULL,
  `horario` time NOT NULL,
  `destino` varchar(255) NOT NULL,
  `status` enum('agendado','concluido','cancelado') NOT NULL,
  `data_agendamento` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `usuario_id` int(11) NOT NULL,
  `empresa_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Despejando dados para a tabela `pacientes_registros`
INSERT INTO `pacientes_registros` (`id`, `nome`, `data_consulta`, `horario`, `destino`, `status`, `data_agendamento`, `usuario_id`, `empresa_id`) VALUES
(1, 'Maria Oliveira', '2024-03-22', '10:30:00', 'Clínica Saúde Total', 'Agendado', '2025-03-10 15:43:00', 1, 1),
(2, 'João Silva', '2024-03-15', '14:00:00', 'Hospital Santa Maria', 'concluido', '2025-03-10 15:43:00', 2, 2),
(3, 'Carlos Magnos', '2024-03-25', '15:30:00', 'Hospital São Rafael', 'Cancelado', '2025-03-10 15:43:00', 3, 3);

-- Acionadores `pacientes_registros`
DELIMITER $$
CREATE TRIGGER `before_insert_pacientes_registros` BEFORE INSERT ON `pacientes_registros` FOR EACH ROW BEGIN
    DECLARE usuario_exists INT;
    DECLARE empresa_exists INT;

    -- Verifica se o usuario_id existe na tabela de usuários
    SELECT COUNT(*) INTO usuario_exists FROM usuarios WHERE id = NEW.usuario_id;
    IF usuario_exists = 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Usuario ID não existe';
    END IF;

    -- Verifica se o empresa_id existe na tabela de empresas
    SELECT COUNT(*) INTO empresa_exists FROM empresas WHERE id = NEW.empresa_id;
    IF empresa_exists = 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Empresa ID não existe';
    END IF;
END
$$
DELIMITER ;

DELIMITER $$
CREATE TRIGGER `before_update_pacientes_registros` BEFORE UPDATE ON `pacientes_registros` FOR EACH ROW BEGIN
    DECLARE usuario_exists INT;
    DECLARE empresa_exists INT;

    -- Verifica se o usuario_id existe na tabela de usuários
    SELECT COUNT(*) INTO usuario_exists FROM usuarios WHERE id = NEW.usuario_id;
    IF usuario_exists = 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Usuario ID não existe';
    END IF;

    -- Verifica se o empresa_id existe na tabela de empresas
    SELECT COUNT(*) INTO empresa_exists FROM empresas WHERE id = NEW.empresa_id;
    IF empresa_exists = 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Empresa ID não existe';
    END IF;
END
$$
DELIMITER ;

-- Estrutura para tabela `usuarios`
CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Índices para tabelas despejadas

-- Índices de tabela `empresas`
ALTER TABLE `empresas`
  ADD PRIMARY KEY (`id`);

-- Índices de tabela `pacientes_registros`
ALTER TABLE `pacientes_registros`
  ADD PRIMARY KEY (`id`);

-- Índices de tabela `usuarios`
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

-- AUTO_INCREMENT para tabelas despejadas

-- AUTO_INCREMENT de tabela `empresas`
ALTER TABLE `empresas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- AUTO_INCREMENT de tabela `pacientes_registros`
ALTER TABLE `pacientes_registros`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

-- AUTO_INCREMENT de tabela `usuarios`
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;
