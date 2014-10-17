-- phpMyAdmin SQL Dump
-- version 4.0.4.2deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 13, 2014 at 03:36 PM
-- Server version: 5.5.31-1
-- PHP Version: 5.5.1-2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `gulfexpress2013`
--
CREATE DATABASE IF NOT EXISTS `gulfexpress2013` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `gulfexpress2013`;

-- --------------------------------------------------------

--
-- Table structure for table `wp_sgc_actionRequests`
--

CREATE TABLE IF NOT EXISTS `wp_sgc_actionRequests` (
  `actionRequestId` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `date_entered` datetime DEFAULT NULL,
  `date_modified` datetime DEFAULT NULL,
  `modified_user_id` bigint(20) unsigned DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT '0',
  `assigned_user_id` bigint(20) unsigned DEFAULT NULL,
  `tiposolicitudsa` tinyint(2) DEFAULT NULL,
  `estadosa` tinyint(2) DEFAULT NULL,
  `sedesa` tinyint(2) DEFAULT NULL,
  `gestionsa` tinyint(2) DEFAULT NULL,
  `tipoproblemasa` text,
  `accionesinmediatassa` text,
  `unidadnegociosa` varchar(100) DEFAULT 'remove',
  PRIMARY KEY (`actionRequestId`),
  KEY `assigned_user_id` (`assigned_user_id`),
  KEY `modified_user_id` (`modified_user_id`),
  KEY `created_by` (`created_by`),
  KEY `FK_RequestType` (`tiposolicitudsa`),
  KEY `estadosa` (`estadosa`),
  KEY `sedesa` (`sedesa`),
  KEY `gestionsa` (`gestionsa`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=544 ;

-- --------------------------------------------------------

--
-- Table structure for table `wp_sgc_actionRequests_files`
--

CREATE TABLE IF NOT EXISTS `wp_sgc_actionRequests_files` (
  `actionRequestId` int(11) NOT NULL,
  `fileId` int(10) unsigned NOT NULL,
  PRIMARY KEY (`actionRequestId`,`fileId`),
  KEY `FK_Aux_actionRequests_Files_fileId` (`fileId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `wp_sgc_actionRequests_notes`
--

CREATE TABLE IF NOT EXISTS `wp_sgc_actionRequests_notes` (
  `actionRequestId` int(11) NOT NULL,
  `noteId` int(11) NOT NULL,
  PRIMARY KEY (`actionRequestId`,`noteId`),
  KEY `FK_Aux_actionRequests_notes_noteId` (`noteId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `wp_sgc_actionRequests_tasks`
--

CREATE TABLE IF NOT EXISTS `wp_sgc_actionRequests_tasks` (
  `actionRequestId` int(11) NOT NULL,
  `taskId` int(11) NOT NULL,
  PRIMARY KEY (`actionRequestId`,`taskId`),
  KEY `FK_Aux_actionRequests_tasks_taskId` (`taskId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `wp_sgc_actionRequestTypes`
--

CREATE TABLE IF NOT EXISTS `wp_sgc_actionRequestTypes` (
  `requestTypeId` tinyint(2) NOT NULL AUTO_INCREMENT,
  `requestType` varchar(50) NOT NULL,
  PRIMARY KEY (`requestTypeId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Table structure for table `wp_sgc_audit`
--

CREATE TABLE IF NOT EXISTS `wp_sgc_audit` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `table` varchar(255) NOT NULL,
  `PK` varchar(255) NOT NULL,
  `column` varchar(255) NOT NULL,
  `data` text NOT NULL,
  `action` varchar(5) NOT NULL,
  `date` datetime NOT NULL,
  `user` varchar(255) NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=385 ;

-- --------------------------------------------------------

--
-- Table structure for table `wp_sgc_classifications`
--

CREATE TABLE IF NOT EXISTS `wp_sgc_classifications` (
  `classificationId` tinyint(2) NOT NULL AUTO_INCREMENT,
  `classification` varchar(100) NOT NULL,
  PRIMARY KEY (`classificationId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Table structure for table `wp_sgc_customerTypes`
--

CREATE TABLE IF NOT EXISTS `wp_sgc_customerTypes` (
  `customerTypeId` tinyint(2) NOT NULL AUTO_INCREMENT,
  `customerType` varchar(255) NOT NULL,
  PRIMARY KEY (`customerTypeId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `wp_sgc_files`
--

CREATE TABLE IF NOT EXISTS `wp_sgc_files` (
  `fileId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT 'Untitled.txt',
  `fileName` varchar(500) NOT NULL,
  `ext` varchar(20) NOT NULL,
  `mime` varchar(50) NOT NULL DEFAULT 'text/plain',
  `size` bigint(20) unsigned NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_by` bigint(20) NOT NULL,
  PRIMARY KEY (`fileId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2973 ;

-- --------------------------------------------------------

--
-- Table structure for table `wp_sgc_files_data`
--

CREATE TABLE IF NOT EXISTS `wp_sgc_files_data` (
  `fileId` int(10) unsigned NOT NULL,
  `data` longblob NOT NULL,
  PRIMARY KEY (`fileId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `wp_sgc_generalities`
--

CREATE TABLE IF NOT EXISTS `wp_sgc_generalities` (
  `generalityId` tinyint(2) NOT NULL AUTO_INCREMENT,
  `generality` varchar(100) NOT NULL,
  PRIMARY KEY (`generalityId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17 ;

-- --------------------------------------------------------

--
-- Table structure for table `wp_sgc_managements`
--

CREATE TABLE IF NOT EXISTS `wp_sgc_managements` (
  `managementId` tinyint(2) NOT NULL AUTO_INCREMENT,
  `management` varchar(100) NOT NULL,
  PRIMARY KEY (`managementId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

-- --------------------------------------------------------

--
-- Table structure for table `wp_sgc_menus`
--

CREATE TABLE IF NOT EXISTS `wp_sgc_menus` (
  `IdMenu` int(11) NOT NULL AUTO_INCREMENT,
  `parentSlug` varchar(25) NOT NULL,
  `MenuType` tinyint(1) NOT NULL,
  `PageTitle` varchar(150) NOT NULL,
  `MenuTitle` varchar(150) NOT NULL,
  `Capability` varchar(50) NOT NULL,
  `MenuSlug` varchar(50) NOT NULL,
  `FunctionMenu` varchar(50) NOT NULL,
  `IconUrl` varchar(50) DEFAULT NULL,
  `PositionMenu` smallint(6) DEFAULT NULL,
  `MenuStatus` bit(1) NOT NULL,
  PRIMARY KEY (`IdMenu`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- Table structure for table `wp_sgc_nonConformities`
--

CREATE TABLE IF NOT EXISTS `wp_sgc_nonConformities` (
  `nonConformityId` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `date_entered` datetime DEFAULT NULL,
  `date_modified` datetime DEFAULT NULL,
  `modified_user_id` bigint(20) unsigned DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `description` text,
  `deleted` tinyint(1) DEFAULT '0',
  `assigned_user_id` char(36) DEFAULT NULL,
  `estadonc` tinyint(2) DEFAULT NULL,
  `nombre_del_clientenc` varchar(25) DEFAULT NULL,
  `telefononc` varchar(25) DEFAULT NULL,
  `fuentenc` tinyint(2) DEFAULT NULL,
  `generalidadnc` tinyint(2) DEFAULT NULL,
  `sedenc` tinyint(2) DEFAULT NULL,
  `unidaddenegocionc` varchar(100) DEFAULT 'remove',
  `gestion` tinyint(2) DEFAULT NULL,
  `clasificacion_nc_c` tinyint(2) DEFAULT NULL,
  `tipo_cliente_c` tinyint(2) NOT NULL,
  PRIMARY KEY (`nonConformityId`),
  KEY `modified_user_id` (`modified_user_id`),
  KEY `created_by` (`created_by`),
  KEY `estadonc` (`estadonc`),
  KEY `sedenc` (`sedenc`),
  KEY `gestion` (`gestion`),
  KEY `fuentenc` (`fuentenc`),
  KEY `generalidadnc` (`generalidadnc`),
  KEY `clasificacion_nc` (`clasificacion_nc_c`),
  KEY `tipo_cliente_c` (`tipo_cliente_c`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=407 ;

-- --------------------------------------------------------

--
-- Table structure for table `wp_sgc_nonConformities_files`
--

CREATE TABLE IF NOT EXISTS `wp_sgc_nonConformities_files` (
  `nonConformityId` int(11) NOT NULL,
  `fileId` int(10) unsigned NOT NULL,
  PRIMARY KEY (`nonConformityId`,`fileId`),
  KEY `FK_Aux_nonConformities_Files_fileId` (`fileId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `wp_sgc_nonConformities_notes`
--

CREATE TABLE IF NOT EXISTS `wp_sgc_nonConformities_notes` (
  `nonConformityId` int(11) NOT NULL,
  `noteId` int(11) NOT NULL,
  PRIMARY KEY (`nonConformityId`,`noteId`),
  KEY `FK_Aux_nonConformities_notes_noteId` (`noteId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `wp_sgc_nonConformities_tasks`
--

CREATE TABLE IF NOT EXISTS `wp_sgc_nonConformities_tasks` (
  `nonConformityId` int(11) NOT NULL,
  `taskId` int(11) NOT NULL,
  PRIMARY KEY (`nonConformityId`,`taskId`),
  KEY `FK_Aux_nonConformities_tasks_taskId` (`taskId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `wp_sgc_notes`
--

CREATE TABLE IF NOT EXISTS `wp_sgc_notes` (
  `noteId` int(11) NOT NULL AUTO_INCREMENT,
  `noteTypeId` tinyint(1) NOT NULL,
  `date_entered` datetime NOT NULL,
  `date_modified` datetime NOT NULL,
  `modified_user_id` bigint(20) unsigned DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` text,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`noteId`),
  KEY `noteTypeId` (`noteTypeId`),
  KEY `modified_user_id` (`modified_user_id`),
  KEY `created_by` (`created_by`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3333 ;

-- --------------------------------------------------------

--
-- Table structure for table `wp_sgc_notes_files`
--

CREATE TABLE IF NOT EXISTS `wp_sgc_notes_files` (
  `noteId` int(11) NOT NULL,
  `fileId` int(10) unsigned NOT NULL,
  PRIMARY KEY (`noteId`,`fileId`),
  KEY `FK_Aux_Notes_Files_fileId` (`fileId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `wp_sgc_noteTypes`
--

CREATE TABLE IF NOT EXISTS `wp_sgc_noteTypes` (
  `noteTypeId` tinyint(1) NOT NULL AUTO_INCREMENT,
  `noteType` varchar(100) NOT NULL,
  UNIQUE KEY `noteTypeId` (`noteTypeId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `wp_sgc_offices`
--

CREATE TABLE IF NOT EXISTS `wp_sgc_offices` (
  `officeId` tinyint(2) NOT NULL AUTO_INCREMENT,
  `office` varchar(100) NOT NULL,
  PRIMARY KEY (`officeId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Table structure for table `wp_sgc_priorities`
--

CREATE TABLE IF NOT EXISTS `wp_sgc_priorities` (
  `priorityId` tinyint(2) NOT NULL AUTO_INCREMENT,
  `priority` varchar(50) NOT NULL,
  PRIMARY KEY (`priorityId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Table structure for table `wp_sgc_sources`
--

CREATE TABLE IF NOT EXISTS `wp_sgc_sources` (
  `sourceId` tinyint(2) NOT NULL AUTO_INCREMENT,
  `source` varchar(100) NOT NULL,
  PRIMARY KEY (`sourceId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- Table structure for table `wp_sgc_status`
--

CREATE TABLE IF NOT EXISTS `wp_sgc_status` (
  `statusId` tinyint(2) NOT NULL AUTO_INCREMENT,
  `status` varchar(50) NOT NULL,
  `Type` enum('Default','Document','Task') NOT NULL,
  PRIMARY KEY (`statusId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

--
-- Table structure for table `wp_sgc_systemDocuments`
--

CREATE TABLE IF NOT EXISTS `wp_sgc_systemDocuments` (
  `systemDocumentId` int(11) NOT NULL AUTO_INCREMENT,
  `documentTypeId` tinyint(4) NOT NULL,
  `date_entered` datetime DEFAULT NULL,
  `date_modified` datetime DEFAULT NULL,
  `modified_user_id` bigint(20) unsigned DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `description` text,
  `deleted` tinyint(1) DEFAULT '0',
  `document_name` varchar(255) NOT NULL,
  `active_date` date DEFAULT NULL,
  `exp_date` date DEFAULT NULL,
  `status_id` tinyint(2) DEFAULT NULL,
  `version` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`systemDocumentId`),
  KEY `documentTypeId` (`documentTypeId`),
  KEY `modified_user_id` (`modified_user_id`),
  KEY `created_by` (`created_by`),
  KEY `status_id` (`status_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2216 ;

-- --------------------------------------------------------

--
-- Table structure for table `wp_sgc_systemDocuments_files`
--

CREATE TABLE IF NOT EXISTS `wp_sgc_systemDocuments_files` (
  `systemDocumentId` int(11) NOT NULL,
  `fileId` int(10) unsigned NOT NULL,
  PRIMARY KEY (`systemDocumentId`,`fileId`),
  KEY `FK_Aux_SystemDocuments_Files_fileId` (`fileId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `wp_sgc_systemDocumentTypes`
--

CREATE TABLE IF NOT EXISTS `wp_sgc_systemDocumentTypes` (
  `documentTypeId` tinyint(4) NOT NULL AUTO_INCREMENT,
  `documentType` varchar(255) NOT NULL,
  PRIMARY KEY (`documentTypeId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Table structure for table `wp_sgc_tasks`
--

CREATE TABLE IF NOT EXISTS `wp_sgc_tasks` (
  `taskId` int(11) NOT NULL AUTO_INCREMENT,
  `taskTypeId` tinyint(2) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `date_entered` datetime DEFAULT NULL,
  `date_modified` datetime DEFAULT NULL,
  `modified_user_id` bigint(20) unsigned DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `description` text,
  `deleted` tinyint(1) DEFAULT '0',
  `assigned_user_id` bigint(20) unsigned DEFAULT NULL,
  `status` tinyint(2) DEFAULT NULL,
  `date_due_flag` tinyint(1) DEFAULT '1',
  `date_due` datetime DEFAULT NULL,
  `date_start_flag` tinyint(1) DEFAULT '1',
  `date_start` datetime DEFAULT NULL,
  `priority` tinyint(2) DEFAULT NULL,
  PRIMARY KEY (`taskId`),
  KEY `modified_user_id` (`modified_user_id`),
  KEY `created_by` (`created_by`),
  KEY `assigned_user_id` (`assigned_user_id`),
  KEY `status` (`status`),
  KEY `priority` (`priority`),
  KEY `taskTypeId` (`taskTypeId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `wp_sgc_tasktypes`
--

CREATE TABLE IF NOT EXISTS `wp_sgc_tasktypes` (
  `taskTypeId` tinyint(2) NOT NULL AUTO_INCREMENT,
  `taskType` varchar(25) NOT NULL,
  PRIMARY KEY (`taskTypeId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Volcado de datos para la tabla `wp_sgc_tasktypes`
--

INSERT INTO `wp_sgc_tasktypes` (`taskTypeId`, `taskType`) VALUES
(0, 'noType'),
(1, 'actionrequests'),
(2, 'nonconformities');

-- --------------------------------------------------------

--
-- Table structure for table `wp_sgc_tasks_files`
--

CREATE TABLE IF NOT EXISTS `wp_sgc_tasks_files` (
  `taskId` int(11) NOT NULL,
  `fileId` int(10) unsigned NOT NULL,
  PRIMARY KEY (`taskId`,`fileId`),
  KEY `FK_Aux_tasks_Files_fileId` (`fileId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `wp_sgc_tasks_notes`
--

CREATE TABLE IF NOT EXISTS `wp_sgc_tasks_notes` (
  `taskId` int(11) NOT NULL,
  `noteId` int(11) NOT NULL,
  PRIMARY KEY (`taskId`,`noteId`),
  KEY `FK_Aux_tasks_notes_noteId` (`noteId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Constraints for dumped tables
--

-- --------------------------------------------------------

--
-- Estructura para la vista `timetasksstatus`
--

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `timetasksstatus` AS select `t`.`taskId` AS `taskId`,'Sin fechas' AS `Expired` from `wp_sgc_tasks` `t` where (isnull(`t`.`date_due`) or isnull(`t`.`date_start`)) union select `t`.`taskId` AS `taskId`,'Vencida' AS `Expired` from `wp_sgc_tasks` `t` where ((`t`.`date_due` < curdate()) and (`t`.`date_due` is not null) and (`t`.`date_start` is not null)) union select `t`.`taskId` AS `taskId`,if(isnull(((to_days(curdate()) - to_days(`t`.`date_start`)) / (to_days(`t`.`date_due`) - to_days(`t`.`date_start`)))),'Vence hoy',if((((to_days(curdate()) - to_days(`t`.`date_start`)) / (to_days(`t`.`date_due`) - to_days(`t`.`date_start`))) >= 0.9),'proxima a vencer','Con tiempo')) AS `Expired` from `wp_sgc_tasks` `t` where ((`t`.`date_due` >= curdate()) and (`t`.`date_due` is not null) and (`t`.`date_start` is not null));

--
-- Constraints for table `wp_sgc_actionRequests`
--
ALTER TABLE `wp_sgc_actionRequests`
  ADD CONSTRAINT `FK_actionRequests_Users_assigned` FOREIGN KEY (`assigned_user_id`) REFERENCES `wp_users` (`ID`),
  ADD CONSTRAINT `FK_actionRequests_Users_Created` FOREIGN KEY (`created_by`) REFERENCES `wp_users` (`ID`),
  ADD CONSTRAINT `FK_actionRequests_Users_Mod` FOREIGN KEY (`modified_user_id`) REFERENCES `wp_users` (`ID`),
  ADD CONSTRAINT `FK_RequestType` FOREIGN KEY (`tiposolicitudsa`) REFERENCES `wp_sgc_actionRequestTypes` (`requestTypeId`),
  ADD CONSTRAINT `wp_sgc_actionRequests_ibfk_1` FOREIGN KEY (`estadosa`) REFERENCES `wp_sgc_status` (`statusId`),
  ADD CONSTRAINT `wp_sgc_actionRequests_ibfk_2` FOREIGN KEY (`sedesa`) REFERENCES `wp_sgc_offices` (`officeId`),
  ADD CONSTRAINT `wp_sgc_actionRequests_ibfk_3` FOREIGN KEY (`gestionsa`) REFERENCES `wp_sgc_managements` (`managementId`);

--
-- Constraints for table `wp_sgc_actionRequests_files`
--
ALTER TABLE `wp_sgc_actionRequests_files`
  ADD CONSTRAINT `FK_Aux_actionRequests_Files_actionRequestId` FOREIGN KEY (`actionRequestId`) REFERENCES `wp_sgc_actionRequests` (`actionRequestId`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_Aux_actionRequests_Files_fileId` FOREIGN KEY (`fileId`) REFERENCES `wp_sgc_files` (`fileId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `wp_sgc_actionRequests_notes`
--
ALTER TABLE `wp_sgc_actionRequests_notes`
  ADD CONSTRAINT `FK_Aux_actionRequests_notes_actionRequestId` FOREIGN KEY (`actionRequestId`) REFERENCES `wp_sgc_actionRequests` (`actionRequestId`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_Aux_actionRequests_notes_noteId` FOREIGN KEY (`noteId`) REFERENCES `wp_sgc_notes` (`noteId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `wp_sgc_actionRequests_tasks`
--
ALTER TABLE `wp_sgc_actionRequests_tasks`
  ADD CONSTRAINT `FK_Aux_actionRequests_tasks_actionRequestId` FOREIGN KEY (`actionRequestId`) REFERENCES `wp_sgc_actionRequests` (`actionRequestId`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_Aux_actionRequests_tasks_taskId` FOREIGN KEY (`taskId`) REFERENCES `wp_sgc_tasks` (`taskId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `wp_sgc_files_data`
--
ALTER TABLE `wp_sgc_files_data`
  ADD CONSTRAINT `FK_FileData` FOREIGN KEY (`fileId`) REFERENCES `wp_sgc_files` (`fileId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `wp_sgc_nonConformities`
--
ALTER TABLE `wp_sgc_nonConformities`
  ADD CONSTRAINT `wp_sgc_nonConformities_ibfk_7` FOREIGN KEY (`tipo_cliente_c`) REFERENCES `wp_sgc_customerTypes` (`customerTypeId`),
  ADD CONSTRAINT `FK_nonConformities_Users_Created` FOREIGN KEY (`created_by`) REFERENCES `wp_users` (`ID`),
  ADD CONSTRAINT `FK_nonConformities_Users_Mod` FOREIGN KEY (`modified_user_id`) REFERENCES `wp_users` (`ID`),
  ADD CONSTRAINT `wp_sgc_nonConformities_ibfk_1` FOREIGN KEY (`estadonc`) REFERENCES `wp_sgc_status` (`statusId`),
  ADD CONSTRAINT `wp_sgc_nonConformities_ibfk_2` FOREIGN KEY (`sedenc`) REFERENCES `wp_sgc_offices` (`officeId`),
  ADD CONSTRAINT `wp_sgc_nonConformities_ibfk_3` FOREIGN KEY (`gestion`) REFERENCES `wp_sgc_managements` (`managementId`),
  ADD CONSTRAINT `wp_sgc_nonConformities_ibfk_4` FOREIGN KEY (`fuentenc`) REFERENCES `wp_sgc_sources` (`sourceId`),
  ADD CONSTRAINT `wp_sgc_nonConformities_ibfk_5` FOREIGN KEY (`generalidadnc`) REFERENCES `wp_sgc_generalities` (`generalityId`),
  ADD CONSTRAINT `wp_sgc_nonConformities_ibfk_6` FOREIGN KEY (`clasificacion_nc_c`) REFERENCES `wp_sgc_classifications` (`classificationId`);

--
-- Constraints for table `wp_sgc_nonConformities_files`
--
ALTER TABLE `wp_sgc_nonConformities_files`
  ADD CONSTRAINT `FK_Aux_nonConformities_Files_fileId` FOREIGN KEY (`fileId`) REFERENCES `wp_sgc_files` (`fileId`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_Aux_nonConformities_Files_nonConformityId` FOREIGN KEY (`nonConformityId`) REFERENCES `wp_sgc_nonConformities` (`nonConformityId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `wp_sgc_nonConformities_notes`
--
ALTER TABLE `wp_sgc_nonConformities_notes`
  ADD CONSTRAINT `FK_Aux_nonConformities_notes_nonConformityId` FOREIGN KEY (`nonConformityId`) REFERENCES `wp_sgc_nonConformities` (`nonConformityId`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_Aux_nonConformities_notes_noteId` FOREIGN KEY (`noteId`) REFERENCES `wp_sgc_notes` (`noteId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `wp_sgc_nonConformities_tasks`
--
ALTER TABLE `wp_sgc_nonConformities_tasks`
  ADD CONSTRAINT `FK_Aux_nonConformities_tasks_nonConformityId` FOREIGN KEY (`nonConformityId`) REFERENCES `wp_sgc_nonConformities` (`nonConformityId`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_Aux_nonConformities_tasks_taskId` FOREIGN KEY (`taskId`) REFERENCES `wp_sgc_tasks` (`taskId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `wp_sgc_notes`
--
ALTER TABLE `wp_sgc_notes`
  ADD CONSTRAINT `FK_noe_noteTypes` FOREIGN KEY (`noteTypeId`) REFERENCES `wp_sgc_noteTypes` (`noteTypeId`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_notes_Users_Created` FOREIGN KEY (`created_by`) REFERENCES `wp_users` (`ID`),
  ADD CONSTRAINT `FK_notes_Users_Mod` FOREIGN KEY (`modified_user_id`) REFERENCES `wp_users` (`ID`);

--
-- Constraints for table `wp_sgc_notes_files`
--
ALTER TABLE `wp_sgc_notes_files`
  ADD CONSTRAINT `FK_Aux_Notes_Files_fileId` FOREIGN KEY (`fileId`) REFERENCES `wp_sgc_files` (`fileId`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_Aux_Notes_Files_noetId` FOREIGN KEY (`noteId`) REFERENCES `wp_sgc_notes` (`noteId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `wp_sgc_systemDocuments`
--
ALTER TABLE `wp_sgc_systemDocuments`
  ADD CONSTRAINT `FK_systemDocuments_Users_Created` FOREIGN KEY (`created_by`) REFERENCES `wp_users` (`ID`),
  ADD CONSTRAINT `FK_systemDocuments_Users_Mod` FOREIGN KEY (`modified_user_id`) REFERENCES `wp_users` (`ID`),
  ADD CONSTRAINT `wp_sgc_systemDocuments_ibfk_1` FOREIGN KEY (`status_id`) REFERENCES `wp_sgc_status` (`statusId`),
  ADD CONSTRAINT `wp_sgc_systemDocuments_Type` FOREIGN KEY (`documentTypeId`) REFERENCES `wp_sgc_systemDocumentTypes` (`documentTypeId`);

--
-- Constraints for table `wp_sgc_systemDocuments_files`
--
ALTER TABLE `wp_sgc_systemDocuments_files`
  ADD CONSTRAINT `FK_Aux_SystemDocuments_Files_fileId` FOREIGN KEY (`fileId`) REFERENCES `wp_sgc_files` (`fileId`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_Aux_SystemDocuments_Files_systemDocumentId` FOREIGN KEY (`systemDocumentId`) REFERENCES `wp_sgc_systemDocuments` (`systemDocumentId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `wp_sgc_tasks`
--
ALTER TABLE `wp_sgc_tasks`
  ADD CONSTRAINT `wp_sgc_tasks_ibfk_3` FOREIGN KEY (`taskTypeId`) REFERENCES `wp_sgc_tasktypes` (`taskTypeId`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_tasks_Users_assigned` FOREIGN KEY (`assigned_user_id`) REFERENCES `wp_users` (`ID`),
  ADD CONSTRAINT `FK_tasks_Users_Created` FOREIGN KEY (`created_by`) REFERENCES `wp_users` (`ID`),
  ADD CONSTRAINT `FK_tasks_Users_Mod` FOREIGN KEY (`modified_user_id`) REFERENCES `wp_users` (`ID`),
  ADD CONSTRAINT `wp_sgc_tasks_ibfk_1` FOREIGN KEY (`status`) REFERENCES `wp_sgc_status` (`statusId`),
  ADD CONSTRAINT `wp_sgc_tasks_ibfk_2` FOREIGN KEY (`priority`) REFERENCES `wp_sgc_priorities` (`priorityId`);

--
-- Constraints for table `wp_sgc_tasks_files`
--
ALTER TABLE `wp_sgc_tasks_files`
  ADD CONSTRAINT `FK_Aux_tasks_Files_fileId` FOREIGN KEY (`fileId`) REFERENCES `wp_sgc_files` (`fileId`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_Aux_tasks_Files_taskId` FOREIGN KEY (`taskId`) REFERENCES `wp_sgc_tasks` (`taskId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `wp_sgc_tasks_notes`
--
ALTER TABLE `wp_sgc_tasks_notes`
  ADD CONSTRAINT `FK_Aux_tasks_notes_noteId` FOREIGN KEY (`noteId`) REFERENCES `wp_sgc_notes` (`noteId`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_Aux_tasks_notes_taskId` FOREIGN KEY (`taskId`) REFERENCES `wp_sgc_tasks` (`taskId`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
