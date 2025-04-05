-- steveshop.sql
-- Estrutura do banco de dados para o SteveShop

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- --------------------------------------------------------

--
-- Banco de dados: `steveshop`
--
CREATE DATABASE IF NOT EXISTS `steveshop` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `steveshop`;

-- --------------------------------------------------------

--
-- Estrutura da tabela `clients_accounts`
--

CREATE TABLE `clients_accounts` (
  `account_ID` int(11) NOT NULL AUTO_INCREMENT,
  `account_NAME` varchar(255) NOT NULL,
  `account_EMAIL` varchar(255) NOT NULL,
  `account_PASSWORD` varchar(255) NOT NULL,
  `account_CREATED_AT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `account_UPDATED_AT` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`account_ID`),
  UNIQUE KEY `account_EMAIL` (`account_EMAIL`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `recover_hash`
--

CREATE TABLE `recover_hash` (
  `hash_ID` int(11) NOT NULL AUTO_INCREMENT,
  `hash` varchar(255) NOT NULL,
  `hash_CLIENT_ID` int(11) NOT NULL,
  `hash_CREATED_AT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`hash_ID`),
  KEY `hash_CLIENT_ID` (`hash_CLIENT_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `system_accounts_blocked`
--

CREATE TABLE `system_accounts_blocked` (
  `block_ID` int(11) NOT NULL AUTO_INCREMENT,
  `block_CLIENT_ID` int(11) NOT NULL,
  `block_END_IN` datetime NOT NULL,
  `block_CREATED_AT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`block_ID`),
  KEY `block_CLIENT_ID` (`block_CLIENT_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `system_confirmation_email`
--

CREATE TABLE `system_confirmation_email` (
  `email_ID` int(11) NOT NULL AUTO_INCREMENT,
  `email_CLIENT_ID` int(11) NOT NULL,
  `email_HASH` varchar(255) NOT NULL,
  `email_CREATED_AT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`email_ID`),
  KEY `email_CLIENT_ID` (`email_CLIENT_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `webstores`
--

CREATE TABLE `webstores` (
  `webstore_ID` int(11) NOT NULL AUTO_INCREMENT,
  `webstore_CLIENT_ID` int(11) NOT NULL,
  `webstore_NAME` varchar(255) NOT NULL,
  `webstore_SUBDOMAIN` varchar(255) NOT NULL,
  `webstore_DOMAIN` varchar(255) DEFAULT NULL,
  `webstore_TOKEN` varchar(255) NOT NULL,
  `webstore_PLAN` int(11) NOT NULL DEFAULT '1',
  `webstore_CREATED_AT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `webstore_UPDATED_AT` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`webstore_ID`),
  UNIQUE KEY `webstore_SUBDOMAIN` (`webstore_SUBDOMAIN`),
  UNIQUE KEY `webstore_DOMAIN` (`webstore_DOMAIN`),
  KEY `webstore_CLIENT_ID` (`webstore_CLIENT_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `webstores_categories`
--

CREATE TABLE `webstores_categories` (
  `category_ID` int(11) NOT NULL AUTO_INCREMENT,
  `category_WID` int(11) NOT NULL,
  `category_SID` int(11) NOT NULL,
  `category_NAME` varchar(255) NOT NULL,
  `category_CREATED_AT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `category_UPDATED_AT` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`category_ID`),
  KEY `category_WID` (`category_WID`),
  KEY `category_SID` (`category_SID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `webstores_dispense`
--

CREATE TABLE `webstores_dispense` (
  `dispense_ID` int(11) NOT NULL AUTO_INCREMENT,
  `dispense_WID` int(11) NOT NULL,
  `dispense_SID` int(11) NOT NULL,
  `dispense_PID` int(11) NOT NULL,
  `dispense_ACTIVED` tinyint(1) NOT NULL DEFAULT '0',
  `dispense_CREATED_AT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`dispense_ID`),
  KEY `dispense_WID` (`dispense_WID`),
  KEY `dispense_SID` (`dispense_SID`),
  KEY `dispense_PID` (`dispense_PID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `webstores_gateways`
--

CREATE TABLE `webstores_gateways` (
  `gateway_ID` int(11) NOT NULL AUTO_INCREMENT,
  `gateway_WID` int(11) NOT NULL,
  `gateway_MERCADOPAGO_CLIENT` varchar(255) DEFAULT NULL,
  `gateway_MERCADOPAGO_SECRET` varchar(255) DEFAULT NULL,
  `gateway_CREATED_AT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `gateway_UPDATED_AT` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`gateway_ID`),
  KEY `gateway_WID` (`gateway_WID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `webstores_gifts`
--

CREATE TABLE `webstores_gifts` (
  `gift_ID` int(11) NOT NULL AUTO_INCREMENT,
  `gift_WID` int(11) NOT NULL,
  `gift_CUPOM` varchar(255) NOT NULL,
  `gift_VALUE` decimal(10,2) NOT NULL,
  `gift_CREATED_AT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`gift_ID`),
  KEY `gift_WID` (`gift_WID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `webstores_maturity`
--

CREATE TABLE `webstores_maturity` (
  `maturity_ID` int(11) NOT NULL AUTO_INCREMENT,
  `maturity_WID` int(11) NOT NULL,
  `maturity_EXPIRE` date NOT NULL,
  `maturity_CREATED_AT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`maturity_ID`),
  KEY `maturity_WID` (`maturity_WID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `webstores_packages`
--

CREATE TABLE `webstores_packages` (
  `package_ID` int(11) NOT NULL AUTO_INCREMENT,
  `package_WID` int(11) NOT NULL,
  `package_SERVER` int(11) NOT NULL,
  `package_CATEGORY` int(11) DEFAULT NULL,
  `package_NAME` varchar(255) NOT NULL,
  `package_DESCRIPTION` text,
  `package_PRICE` decimal(10,2) NOT NULL,
  `package_CREATED_AT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `package_UPDATED_AT` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`package_ID`),
  KEY `package_WID` (`package_WID`),
  KEY `package_SERVER` (`package_SERVER`),
  KEY `package_CATEGORY` (`package_CATEGORY`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `webstores_packages_commands`
--

CREATE TABLE `webstores_packages_commands` (
  `command_ID` int(11) NOT NULL AUTO_INCREMENT,
  `command_PID` int(11) NOT NULL,
  `command_CONTENT` text NOT NULL,
  `command_CREATED_AT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`command_ID`),
  KEY `command_PID` (`command_PID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `webstores_pages`
--

CREATE TABLE `webstores_pages` (
  `page_ID` int(11) NOT NULL AUTO_INCREMENT,
  `page_WID` int(11) NOT NULL,
  `page_TITLE` varchar(255) NOT NULL,
  `page_LINK` varchar(255) NOT NULL,
  `page_CONTENT` text NOT NULL,
  `page_CREATED_AT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `page_UPDATED_AT` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`page_ID`),
  KEY `page_WID` (`page_WID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `webstores_preconfigs`
--

CREATE TABLE `webstores_preconfigs` (
  `config_ID` int(11) NOT NULL AUTO_INCREMENT,
  `config_DOMAIN` varchar(255) DEFAULT NULL,
  `config_CURRENCY` varchar(10) NOT NULL DEFAULT 'R$',
  `config_CREATED_AT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`config_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `webstores_references`
--

CREATE TABLE `webstores_references` (
  `reference_ID` int(11) NOT NULL AUTO_INCREMENT,
  `reference_WID` int(11) NOT NULL,
  `reference_TYPE` varchar(50) NOT NULL,
  `reference_VALUE` text NOT NULL,
  `reference_CREATED_AT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`reference_ID`),
  KEY `reference_WID` (`reference_WID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `webstores_seo`
--

CREATE TABLE `webstores_seo` (
  `seo_ID` int(11) NOT NULL AUTO_INCREMENT,
  `seo_WID` int(11) NOT NULL,
  `seo_TITLE` varchar(255) DEFAULT NULL,
  `seo_DESCRIPTION` text,
  `seo_KEYWORDS` text,
  `seo_CREATED_AT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `seo_UPDATED_AT` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`seo_ID`),
  KEY `seo_WID` (`seo_WID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `webstores_servers`
--

CREATE TABLE `webstores_servers` (
  `server_ID` int(11) NOT NULL AUTO_INCREMENT,
  `server_WID` int(11) NOT NULL,
  `server_NAME` varchar(255) NOT NULL,
  `server_IP` varchar(255) NOT NULL,
  `server_PORT` int(11) DEFAULT '22',
  `server_RCON_PORT` int(11) DEFAULT NULL,
  `server_RCON_PASSWORD` varchar(255) DEFAULT NULL,
  `server_CREATED_AT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `server_UPDATED_AT` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`server_ID`),
  KEY `server_WID` (`server_WID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `webstores_template_config`
--

CREATE TABLE `webstores_template_config` (
  `config_ID` int(11) NOT NULL AUTO_INCREMENT,
  `config_WID` int(11) NOT NULL,
  `config_LOGO` varchar(255) DEFAULT NULL,
  `config_BACKGROUND` varchar(255) DEFAULT NULL,
  `config_PRIMARY_COLOR` varchar(20) DEFAULT '#3498db',
  `config_CREATED_AT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `config_UPDATED_AT` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`config_ID`),
  KEY `config_WID` (`config_WID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `webstore_template`
--

CREATE TABLE `webstore_template` (
  `template_ID` int(11) NOT NULL AUTO_INCREMENT,
  `wid` int(11) NOT NULL,
  `template_NAME` varchar(255) NOT NULL DEFAULT 'default_template',
  `template_CREATED_AT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`template_ID`),
  KEY `wid` (`wid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `webstores_transactions`
--

CREATE TABLE `webstores_transactions` (
  `transaction_ID` int(11) NOT NULL AUTO_INCREMENT,
  `transaction_WID` int(11) NOT NULL,
  `transaction_CODE` varchar(255) NOT NULL,
  `transaction_EMAIL` varchar(255) NOT NULL,
  `transaction_NAME` varchar(255) DEFAULT NULL,
  `transaction_PRODUCT_NAME` varchar(255) NOT NULL,
  `transaction_PRODUCT_ID` int(11) NOT NULL,
  `transaction_AMOUNT` decimal(10,2) NOT NULL,
  `transaction_NET_AMOUNT` decimal(10,2) NOT NULL,
  `transaction_PAID` tinyint(1) NOT NULL DEFAULT '0',
  `transaction_PAYMENT_METHOD` varchar(50) DEFAULT NULL,
  `transaction_DATE` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `transaction_UPDATED_AT` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`transaction_ID`),
  UNIQUE KEY `transaction_CODE` (`transaction_CODE`),
  KEY `transaction_WID` (`transaction_WID`),
  KEY `transaction_PRODUCT_ID` (`transaction_PRODUCT_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `webstores_widgets`
--

CREATE TABLE `webstores_widgets` (
  `widget_ID` int(11) NOT NULL AUTO_INCREMENT,
  `widget_WID` int(11) NOT NULL,
  `widget_UNIQUE` varchar(255) NOT NULL,
  `widget_DATA` text,
  `widget_ACTIVED` tinyint(1) NOT NULL DEFAULT '1',
  `widget_CREATED_AT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `widget_UPDATED_AT` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`widget_ID`),
  KEY `widget_WID` (`widget_WID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Restrições de chave estrangeira
--

ALTER TABLE `recover_hash`
  ADD CONSTRAINT `recover_hash_ibfk_1` FOREIGN KEY (`hash_CLIENT_ID`) REFERENCES `clients_accounts` (`account_ID`) ON DELETE CASCADE;

ALTER TABLE `system_accounts_blocked`
  ADD CONSTRAINT `system_accounts_blocked_ibfk_1` FOREIGN KEY (`block_CLIENT_ID`) REFERENCES `clients_accounts` (`account_ID`) ON DELETE CASCADE;

ALTER TABLE `system_confirmation_email`
  ADD CONSTRAINT `system_confirmation_email_ibfk_1` FOREIGN KEY (`email_CLIENT_ID`) REFERENCES `clients_accounts` (`account_ID`) ON DELETE CASCADE;

ALTER TABLE `webstores`
  ADD CONSTRAINT `webstores_ibfk_1` FOREIGN KEY (`webstore_CLIENT_ID`) REFERENCES `clients_accounts` (`account_ID`) ON DELETE CASCADE;

ALTER TABLE `webstores_categories`
  ADD CONSTRAINT `webstores_categories_ibfk_1` FOREIGN KEY (`category_WID`) REFERENCES `webstores` (`webstore_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `webstores_categories_ibfk_2` FOREIGN KEY (`category_SID`) REFERENCES `webstores_servers` (`server_ID`) ON DELETE CASCADE;

ALTER TABLE `webstores_dispense`
  ADD CONSTRAINT `webstores_dispense_ibfk_1` FOREIGN KEY (`dispense_WID`) REFERENCES `webstores` (`webstore_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `webstores_dispense_ibfk_2` FOREIGN KEY (`dispense_SID`) REFERENCES `webstores_servers` (`server_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `webstores_dispense_ibfk_3` FOREIGN KEY (`dispense_PID`) REFERENCES `webstores_packages` (`package_ID`) ON DELETE CASCADE;

ALTER TABLE `webstores_gateways`
  ADD CONSTRAINT `webstores_gateways_ibfk_1` FOREIGN KEY (`gateway_WID`) REFERENCES `webstores` (`webstore_ID`) ON DELETE CASCADE;

ALTER TABLE `webstores_gifts`
  ADD CONSTRAINT `webstores_gifts_ibfk_1` FOREIGN KEY (`gift_WID`) REFERENCES `webstores` (`webstore_ID`) ON DELETE CASCADE;

ALTER TABLE `webstores_maturity`
  ADD CONSTRAINT `webstores_maturity_ibfk_1` FOREIGN KEY (`maturity_WID`) REFERENCES `webstores` (`webstore_ID`) ON DELETE CASCADE;

ALTER TABLE `webstores_packages`
  ADD CONSTRAINT `webstores_packages_ibfk_1` FOREIGN KEY (`package_WID`) REFERENCES `webstores` (`webstore_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `webstores_packages_ibfk_2` FOREIGN KEY (`package_SERVER`) REFERENCES `webstores_servers` (`server_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `webstores_packages_ibfk_3` FOREIGN KEY (`package_CATEGORY`) REFERENCES `webstores_categories` (`category_ID`) ON DELETE SET NULL;

ALTER TABLE `webstores_packages_commands`
  ADD CONSTRAINT `webstores_packages_commands_ibfk_1` FOREIGN KEY (`command_PID`) REFERENCES `webstores_packages` (`package_ID`) ON DELETE CASCADE;

ALTER TABLE `webstores_pages`
  ADD CONSTRAINT `webstores_pages_ibfk_1` FOREIGN KEY (`page_WID`) REFERENCES `webstores` (`webstore_ID`) ON DELETE CASCADE;

ALTER TABLE `webstores_references`
  ADD CONSTRAINT `webstores_references_ibfk_1` FOREIGN KEY (`reference_WID`) REFERENCES `webstores` (`webstore_ID`) ON DELETE CASCADE;

ALTER TABLE `webstores_seo`
  ADD CONSTRAINT `webstores_seo_ibfk_1` FOREIGN KEY (`seo_WID`) REFERENCES `webstores` (`webstore_ID`) ON DELETE CASCADE;

ALTER TABLE `webstores_servers`
  ADD CONSTRAINT `webstores_servers_ibfk_1` FOREIGN KEY (`server_WID`) REFERENCES `webstores` (`webstore_ID`) ON DELETE CASCADE;

ALTER TABLE `webstores_template_config`
  ADD CONSTRAINT `webstores_template_config_ibfk_1` FOREIGN KEY (`config_WID`) REFERENCES `webstores` (`webstore_ID`) ON DELETE CASCADE;

ALTER TABLE `webstore_template`
  ADD CONSTRAINT `webstore_template_ibfk_1` FOREIGN KEY (`wid`) REFERENCES `webstores` (`webstore_ID`) ON DELETE CASCADE;

ALTER TABLE `webstores_transactions`
  ADD CONSTRAINT `webstores_transactions_ibfk_1` FOREIGN KEY (`transaction_WID`) REFERENCES `webstores` (`webstore_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `webstores_transactions_ibfk_2` FOREIGN KEY (`transaction_PRODUCT_ID`) REFERENCES `webstores_packages` (`package_ID`) ON DELETE CASCADE;

ALTER TABLE `webstores_widgets`
  ADD CONSTRAINT `webstores_widgets_ibfk_1` FOREIGN KEY (`widget_WID`) REFERENCES `webstores` (`webstore_ID`) ON DELETE CASCADE; 