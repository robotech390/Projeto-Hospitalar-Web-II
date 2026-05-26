-- 1. Tabelas Base (Sem FKs externas ou que são pais de outras)
CREATE TABLE `endereco` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `logradouro` varchar(255),
  `cidade` varchar(100),
  `estado` char(2),
  `numero` varchar(10),
  `cep` varchar(9)
);

CREATE TABLE `tipo_medicamento` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `descricao` varchar(255)
);

CREATE TABLE `tipo_cobranca` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `descricao` varchar(100)
);

-- 2. Entidades de Pessoas e Usuários
CREATE TABLE `pessoa` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `nome` varchar(255),
  `cpf` varchar(14),
  `data_nascimento` date,
  `email` varchar(100),
  `telefone` varchar(20),
  `id_endereco` int,
  FOREIGN KEY (`id_endereco`) REFERENCES `endereco`(`id`)
);

CREATE TABLE `usuario` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `usuario` varchar(50),
  `senha` varchar(255),
  `funcao` varchar(50),
  `id_pessoa` int,
  FOREIGN KEY (`id_pessoa`) REFERENCES `pessoa`(`id`)
);

CREATE TABLE `usuario_log` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `id_usuario` int,
  `log` longtext,
  `data` datetime,
  FOREIGN KEY (`id_usuario`) REFERENCES `usuario`(`id`)
);

CREATE TABLE `medico` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `id_pessoa` int,
  `tipo` enum('Geral', 'Especialista'),
  `CRM` varchar(20),
  FOREIGN KEY (`id_pessoa`) REFERENCES `pessoa`(`id`)
);

-- 3. Tabelas de Convênio e Plano
CREATE TABLE `convenio` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `nome` varchar(100),
  `cnpj` varchar(18),
  `telefone` varchar(20),
  `email` varchar(100),
  `id_endereco` int,
  FOREIGN KEY (`id_endereco`) REFERENCES `endereco`(`id`)
);

CREATE TABLE `plano` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `descricao` varchar(255),
  `id_tipo_cobranca` int,
  `id_convenio` int,
  FOREIGN KEY (`id_tipo_cobranca`) REFERENCES `tipo_cobranca`(`id`),
  FOREIGN KEY (`id_convenio`) REFERENCES `convenio`(`id`)
);

-- 4. Consultas e Diagnósticos
CREATE TABLE `tipo_consulta` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `descricao` varchar(100),
  `valor` decimal(10,2)
);

CREATE TABLE `consulta` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `descricao` varchar(255),
  `data` date,
  `hora_inicio` datetime,
  `hora_fim` datetime,
  `data_check_in` datetime,
  `status` varchar(50),
  `id_tipo_consulta` int,
  `id_paciente` int,
  `id_medico` int,
  FOREIGN KEY (`id_tipo_consulta`) REFERENCES `tipo_consulta`(`id`),
  FOREIGN KEY (`id_paciente`) REFERENCES `pessoa`(`id`),
  FOREIGN KEY (`id_medico`) REFERENCES `medico`(`id`)
);

CREATE TABLE `diagnostico` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `cid` varchar(10),
  `descricao` varchar(255),
  `id_consulta` int,
  FOREIGN KEY (`id_consulta`) REFERENCES `consulta`(`id`)
);

-- 5. Exames
CREATE TABLE `tipo_exame` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `nome` varchar(100),
  `tipo` enum('Laboratorial', 'Imagem'),
  `preco` decimal(10,2),
  `preparo` varchar(255)
);

CREATE TABLE `solicitacao_exame` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `data` datetime,
  `justificativa` varchar(255),
  `prioridade` int,
  `id_consulta` int,
  FOREIGN KEY (`id_consulta`) REFERENCES `consulta`(`id`)
);

CREATE TABLE `itens_exame` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `id_solicitacao` int,
  `id_tipo_exame` int,
  `status` enum('Pendente', 'Concluído', 'Cancelado'),
  `laudo` text,
  `arquivo` varchar(255),
  `data_resultado` date,
  FOREIGN KEY (`id_tipo_exame`) REFERENCES `tipo_exame`(`id`),
  FOREIGN KEY (`id_solicitacao`) REFERENCES `solicitacao_exame`(`id`)
);

-- 6. Medicamentos e Notas Fiscais
CREATE TABLE `medicamento` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `nome` varchar(100),
  `dosagem` varchar(50),
  `principio_ativo` varchar(100),
  `id_tipo_medicamento` int,
  `preco` decimal(10,2),
  FOREIGN KEY (`id_tipo_medicamento`) REFERENCES `tipo_medicamento`(`id`)
);

CREATE TABLE `lote` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `id_medicamento` int,
  `numero` int,
  `data_validade` date,
  `quantidade_produtos` int,
  `ativo` boolean,
  FOREIGN KEY (`id_medicamento`) REFERENCES `medicamento`(`id`)
);

CREATE TABLE `nota_fiscal` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `numero` varchar(50),
  `cpf_cnpj` varchar(18),
  `destinatario` int,
  `data` datetime,
  `tipo` char(1)
);

CREATE TABLE `lote_notafiscal` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `id_nota_fiscal` int,
  `icms` decimal(10,2),
  `cfop` char(4),
  `quantidade` int,
  `id_lote` int,
  FOREIGN KEY (`id_nota_fiscal`) REFERENCES `nota_fiscal`(`id`),
  FOREIGN KEY (`id_lote`) REFERENCES `lote`(`id`)
);

-- 7. Receitas e Coberturas
CREATE TABLE `receita` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `observacoes` varchar(255),
  `farmacia` varchar(100),
  `data_emissao` date,
  `id_consulta` int,
  FOREIGN KEY (`id_consulta`) REFERENCES `consulta`(`id`)
);

CREATE TABLE `medicamento_receita` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `id_receita` int,
  `id_medicamento` int,
  FOREIGN KEY (`id_receita`) REFERENCES `receita`(`id`),
  FOREIGN KEY (`id_medicamento`) REFERENCES `medicamento`(`id`)
);

CREATE TABLE `plano_cobertura_exame` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `id_plano` int,
  `id_tipo_exame` int,
  FOREIGN KEY (`id_plano`) REFERENCES `plano`(`id`),
  FOREIGN KEY (`id_tipo_exame`) REFERENCES `tipo_exame`(`id`)
);

CREATE TABLE `plano_cobertura_consulta` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `id_plano` int,
  `id_tipo_consulta` int,
  FOREIGN KEY (`id_plano`) REFERENCES `plano`(`id`),
  FOREIGN KEY (`id_tipo_consulta`) REFERENCES `tipo_consulta`(`id`)
);

CREATE TABLE `plano_cobertura_medicamento` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `id_plano` int,
  `id_tipo_medicamento` int,
  FOREIGN KEY (`id_plano`) REFERENCES `plano`(`id`),
  FOREIGN KEY (`id_tipo_medicamento`) REFERENCES `tipo_medicamento`(`id`)
);