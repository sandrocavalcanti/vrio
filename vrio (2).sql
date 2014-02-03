-- phpMyAdmin SQL Dump
-- version 3.5.7
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 03, 2014 at 11:02 PM
-- Server version: 5.5.29
-- PHP Version: 5.4.10

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `vrio`
--

-- --------------------------------------------------------

--
-- Table structure for table `tb_banheiro`
--

CREATE TABLE `tb_banheiro` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `descricao` varchar(100) NOT NULL,
  `logradouro` varchar(100) DEFAULT NULL,
  `numero` varchar(10) DEFAULT NULL,
  `bairro` varchar(100) DEFAULT NULL,
  `cep` varchar(20) DEFAULT NULL,
  `cidade` varchar(100) DEFAULT NULL,
  `uf` char(2) DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `tipo` enum('B','P') NOT NULL DEFAULT 'B' COMMENT 'B:banheiro,P:pontoderesgate',
  `ativo` tinyint(4) NOT NULL DEFAULT '0',
  `data_cadastro` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `tipo` (`tipo`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `tb_banheiro`
--

INSERT INTO `tb_banheiro` (`id`, `descricao`, `logradouro`, `numero`, `bairro`, `cep`, `cidade`, `uf`, `latitude`, `longitude`, `tipo`, `ativo`, `data_cadastro`) VALUES
(2, 'Olinda 1', 'Rua das Pedras', '443', 'Alto da Se', '50909-090', 'Olinda', 'PE', -8.13195660, -34.90600750, 'B', 1, '2013-12-09 08:42:52'),
(3, 'Olinda 2', 'Rua das Igrejas', '332', 'Varadouro', '50050-000', 'Olinda', 'PE', -8.01962530, -34.85157850, 'P', 1, '2013-12-09 08:44:59');

-- --------------------------------------------------------

--
-- Table structure for table `tb_banheiro_foto`
--

CREATE TABLE `tb_banheiro_foto` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `imagem` varchar(100) NOT NULL,
  `id_banheiro` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_tb_banheiro_foto_tb_banheiro_idx` (`id_banheiro`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tb_customer`
--

CREATE TABLE `tb_customer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(50) NOT NULL,
  `sobrenome` varchar(50) NOT NULL,
  `email` varchar(150) NOT NULL,
  `senha` varchar(100) NOT NULL,
  `sexo` enum('F','M') NOT NULL DEFAULT 'M',
  `celular` varchar(20) NOT NULL,
  `cpf` varchar(45) NOT NULL,
  `data_nascimento` date DEFAULT NULL,
  `uf` char(2) DEFAULT NULL,
  `data_cadastro` datetime NOT NULL,
  `ativo` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `tb_customer`
--

INSERT INTO `tb_customer` (`id`, `nome`, `sobrenome`, `email`, `senha`, `sexo`, `celular`, `cpf`, `data_nascimento`, `uf`, `data_cadastro`, `ativo`) VALUES
(2, 'Ricardo', 'Mota', 'ricardo@banheirosvrio.com.br', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', 'M', '123', '11122233344', '1970-01-17', 'PE', '2013-12-09 00:00:00', 1),
(10, '123', '123', 'suporte@cxc.vom', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', 'F', '1212', '1212', '1969-12-31', 'BA', '2014-01-19 17:48:57', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tb_produto`
--

CREATE TABLE `tb_produto` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `descricao` varchar(50) NOT NULL,
  `valor` decimal(12,2) DEFAULT NULL,
  `ativo` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `tb_produto`
--

INSERT INTO `tb_produto` (`id`, `descricao`, `valor`, `ativo`) VALUES
(1, 'Carnaval 2014', 10.00, 1);

-- --------------------------------------------------------

--
-- Table structure for table `tb_user`
--

CREATE TABLE `tb_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(50) NOT NULL,
  `email` varchar(150) NOT NULL,
  `senha` varchar(100) NOT NULL,
  `data_cadastro` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `tb_user`
--

INSERT INTO `tb_user` (`id`, `nome`, `email`, `senha`, `data_cadastro`) VALUES
(1, 'admin', 'admin@vrio.com.br', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', '2013-12-01 18:07:27');

-- --------------------------------------------------------

--
-- Table structure for table `tb_venda`
--

CREATE TABLE `tb_venda` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `valor_total` decimal(12,2) DEFAULT NULL,
  `data_cadastro` datetime NOT NULL,
  `id_customer` int(11) NOT NULL,
  `negociacao_id` varchar(50) DEFAULT NULL,
  `forma_pgto` varchar(45) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_tb_venda_tb_customer1_idx` (`id_customer`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `tb_venda`
--

INSERT INTO `tb_venda` (`id`, `valor_total`, `data_cadastro`, `id_customer`, `negociacao_id`, `forma_pgto`) VALUES
(1, 100.00, '2014-01-19 00:00:00', 2, '999', 'Master'),
(2, 200.00, '2014-01-13 00:00:00', 2, '333', 'Visa');

-- --------------------------------------------------------

--
-- Table structure for table `tb_venda_produto`
--

CREATE TABLE `tb_venda_produto` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `qtde` decimal(5,2) DEFAULT NULL,
  `valor_unit` decimal(12,2) DEFAULT NULL,
  `id_produto` int(11) NOT NULL,
  `id_venda` int(11) NOT NULL,
  `resgatado` tinyint(4) NOT NULL DEFAULT '0',
  `data_resgate` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_tb_venda_produto_tb_produto1_idx` (`id_produto`),
  KEY `fk_tb_venda_produto_tb_venda1_idx` (`id_venda`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `tb_venda_produto`
--

INSERT INTO `tb_venda_produto` (`id`, `qtde`, `valor_unit`, `id_produto`, `id_venda`, `resgatado`, `data_resgate`) VALUES
(1, 10.00, 10.00, 1, 1, 0, NULL),
(2, 20.00, 10.00, 1, 2, 0, NULL);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tb_banheiro_foto`
--
ALTER TABLE `tb_banheiro_foto`
  ADD CONSTRAINT `fk_tb_banheiro_foto_tb_banheiro` FOREIGN KEY (`id_banheiro`) REFERENCES `tb_banheiro` (`id`) ON UPDATE NO ACTION;

--
-- Constraints for table `tb_venda`
--
ALTER TABLE `tb_venda`
  ADD CONSTRAINT `fk_tb_venda_tb_customer1` FOREIGN KEY (`id_customer`) REFERENCES `tb_customer` (`id`) ON UPDATE NO ACTION;

--
-- Constraints for table `tb_venda_produto`
--
ALTER TABLE `tb_venda_produto`
  ADD CONSTRAINT `fk_tb_venda_produto_tb_produto1` FOREIGN KEY (`id_produto`) REFERENCES `tb_produto` (`id`) ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_tb_venda_produto_tb_venda1` FOREIGN KEY (`id_venda`) REFERENCES `tb_venda` (`id`) ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
