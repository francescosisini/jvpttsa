-- phpMyAdmin SQL Dump
-- version 3.4.7.1
-- http://www.phpmyadmin.net
--
-- Host: 89.46.111.41
-- Generato il: Lug 01, 2018 alle 18:07
-- Versione del server: 5.6.39
-- Versione PHP: 5.3.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `Sql1087530_1`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `cvp_ecg`
--

CREATE TABLE IF NOT EXISTS `cvp_ecg` (
  `idscreenshot` mediumint(9) NOT NULL,
  `number` int(11) NOT NULL,
  `PQRSTwave` char(1) NOT NULL,
  PRIMARY KEY (`idscreenshot`,`number`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `cvp_examination`
--

CREATE TABLE IF NOT EXISTS `cvp_examination` (
  `cvpID` mediumint(9) NOT NULL AUTO_INCREMENT,
  `studyInstanceUID` varchar(64) NOT NULL,
  `patientName` varchar(100) NOT NULL,
  `patientFamilyName` varchar(100) NOT NULL,
  `patientID` varchar(100) NOT NULL,
  `dataOraEsame` datetime NOT NULL,
  `dataEntryDateTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`cvpID`),
  UNIQUE KEY `studyInstanceUID` (`studyInstanceUID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=14 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `cvp_sampling`
--

CREATE TABLE IF NOT EXISTS `cvp_sampling` (
  `idscreenshot` mediumint(9) NOT NULL,
  `number` int(11) NOT NULL,
  `pressure` decimal(5,2) NOT NULL,
  PRIMARY KEY (`idscreenshot`,`number`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `cvp_waves`
--

CREATE TABLE IF NOT EXISTS `cvp_waves` (
  `idscreenshot` mediumint(9) NOT NULL,
  `number` int(11) NOT NULL,
  `acxvyWave` char(1) NOT NULL,
  PRIMARY KEY (`idscreenshot`,`number`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `doppler_ecg`
--

CREATE TABLE IF NOT EXISTS `doppler_ecg` (
  `iddoppler` mediumint(9) NOT NULL,
  `number` int(11) NOT NULL,
  `PQRSTwave` char(1) NOT NULL,
  PRIMARY KEY (`iddoppler`,`number`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `doppler_sampling`
--

CREATE TABLE IF NOT EXISTS `doppler_sampling` (
  `iddoppler` mediumint(9) NOT NULL,
  `number` int(11) NOT NULL,
  `meanVelocity` decimal(5,2) NOT NULL,
  PRIMARY KEY (`iddoppler`,`number`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `research_project`
--

CREATE TABLE IF NOT EXISTS `research_project` (
  `researchID` varchar(100) NOT NULL,
  `description` varchar(10000) NOT NULL,
  PRIMARY KEY (`researchID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `screenshot`
--

CREATE TABLE IF NOT EXISTS `screenshot` (
  `idscreenshot` mediumint(9) NOT NULL AUTO_INCREMENT,
  `cvpID` mediumint(9) NOT NULL,
  `data_shot` datetime DEFAULT NULL,
  `fileName` varchar(300) NOT NULL,
  PRIMARY KEY (`idscreenshot`),
  KEY `cvpID` (`cvpID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=16 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `sonogram`
--

CREATE TABLE IF NOT EXISTS `sonogram` (
  `videoclipID` mediumint(9) NOT NULL,
  `processID` varchar(64) NOT NULL,
  `number` int(11) NOT NULL,
  `csa` int(11) NOT NULL,
  `perimeter` int(11) NOT NULL,
  PRIMARY KEY (`videoclipID`,`processID`,`number`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `us_doppler`
--

CREATE TABLE IF NOT EXISTS `us_doppler` (
  `iddoppler` mediumint(9) NOT NULL AUTO_INCREMENT,
  `studyInstanceUID` varchar(64) NOT NULL,
  `data_shot` datetime DEFAULT NULL,
  `RightOrLeftIJV` char(1) NOT NULL,
  `Jposition123` int(1) NOT NULL,
  `fileName` varchar(300) NOT NULL,
  `pixelTocms` decimal(5,2) NOT NULL,
  `baseLine` int(11) NOT NULL,
  PRIMARY KEY (`iddoppler`),
  KEY `studyInstanceUID` (`studyInstanceUID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=61 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `us_ecg`
--

CREATE TABLE IF NOT EXISTS `us_ecg` (
  `videoclipID` mediumint(9) NOT NULL,
  `number` int(11) NOT NULL,
  `PQRSTwave` char(1) DEFAULT NULL,
  PRIMARY KEY (`videoclipID`,`number`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `us_jvp`
--

CREATE TABLE IF NOT EXISTS `us_jvp` (
  `videoclipID` mediumint(9) NOT NULL,
  `number` int(11) NOT NULL,
  `acxvyWave` char(1) DEFAULT NULL,
  PRIMARY KEY (`videoclipID`,`number`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `us_report`
--

CREATE TABLE IF NOT EXISTS `us_report` (
  `reportID` mediumint(9) NOT NULL AUTO_INCREMENT,
  `studyInstanceUID` varchar(64) NOT NULL,
  `storia` varchar(2000) NOT NULL,
  `quesito` varchar(2000) NOT NULL,
  `cca_csa_d` decimal(5,2) NOT NULL,
  `ica_csa_d` decimal(5,2) NOT NULL,
  `eca_csa_d` decimal(5,2) NOT NULL,
  `av_csa_d` decimal(5,2) NOT NULL,
  `cca_v_d` decimal(5,2) NOT NULL,
  `ica_v_d` decimal(5,2) NOT NULL,
  `eca_v_d` decimal(5,2) NOT NULL,
  `av_v_d` decimal(5,2) NOT NULL,
  `j1_csa_d` decimal(5,2) NOT NULL,
  `j2_csa_d` decimal(5,2) NOT NULL,
  `j3_csa_d` decimal(5,2) NOT NULL,
  `j1_v_d` decimal(5,2) NOT NULL,
  `j2_v_d` decimal(5,2) NOT NULL,
  `j3_v_d` decimal(5,2) NOT NULL,
  `j1_bloccoFlusso_d` tinyint(1) NOT NULL,
  `j1_flussoBi_d` tinyint(1) NOT NULL,
  `j1_valvolaIpoMobile_d` tinyint(1) NOT NULL,
  `j1_compressioni_d` tinyint(1) NOT NULL,
  `j2_bloccoFlusso_d` tinyint(1) NOT NULL,
  `j2_flussoBi_d` tinyint(1) NOT NULL,
  `j2_valvolaIpoMobile_d` tinyint(1) NOT NULL,
  `j2_compressioni_d` tinyint(1) NOT NULL,
  `j3_bloccoFlusso_d` tinyint(1) NOT NULL,
  `j3_flussoBi_d` tinyint(1) NOT NULL,
  `j3_valvolaIpoMobile_d` tinyint(1) NOT NULL,
  `j3_compressioni_d` tinyint(1) NOT NULL,
  `cca_csa_s` decimal(5,2) NOT NULL,
  `ica_csa_s` decimal(5,2) NOT NULL,
  `eca_csa_s` decimal(5,2) NOT NULL,
  `av_csa_s` decimal(5,2) NOT NULL,
  `cca_v_s` decimal(5,2) NOT NULL,
  `ica_v_s` decimal(5,2) NOT NULL,
  `eca_v_s` decimal(5,2) NOT NULL,
  `av_v_s` decimal(5,2) NOT NULL,
  `j1_csa_s` decimal(5,2) NOT NULL,
  `j2_csa_s` decimal(5,2) NOT NULL,
  `j3_csa_s` decimal(5,2) NOT NULL,
  `j1_v_s` decimal(5,2) NOT NULL,
  `j2_v_s` decimal(5,2) NOT NULL,
  `j3_v_s` decimal(5,2) NOT NULL,
  `j1_bloccoFlusso_s` tinyint(1) NOT NULL,
  `j1_flussoBi_s` tinyint(1) NOT NULL,
  `j1_valvolaIpoMobile_s` tinyint(1) NOT NULL,
  `j1_compressioni_s` tinyint(1) NOT NULL,
  `j2_bloccoFlusso_s` tinyint(1) NOT NULL,
  `j2_flussoBi_s` tinyint(1) NOT NULL,
  `j2_valvolaIpoMobile_s` tinyint(1) NOT NULL,
  `j2_compressioni_s` tinyint(1) NOT NULL,
  `j3_bloccoFlusso_s` tinyint(1) NOT NULL,
  `j3_flussoBi_s` tinyint(1) NOT NULL,
  `j3_valvolaIpoMobile_s` tinyint(1) NOT NULL,
  `j3_compressioni_s` tinyint(1) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `cognome` varchar(100) NOT NULL,
  `Indirizzo` varchar(300) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  PRIMARY KEY (`reportID`),
  KEY `studyInstanceUID` (`studyInstanceUID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=60 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `us_respiration`
--

CREATE TABLE IF NOT EXISTS `us_respiration` (
  `videoclipID` mediumint(9) NOT NULL,
  `number` int(11) NOT NULL,
  `ieWaves` char(1) DEFAULT NULL,
  PRIMARY KEY (`videoclipID`,`number`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `us_study`
--

CREATE TABLE IF NOT EXISTS `us_study` (
  `studyInstanceUID` varchar(64) NOT NULL,
  `patientName` varchar(100) NOT NULL,
  `patientFamilyName` varchar(100) NOT NULL,
  `patientID` varchar(100) NOT NULL,
  `studyDateTime` datetime DEFAULT NULL,
  `dataEntryDateTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `researchID` varchar(100) NOT NULL,
  PRIMARY KEY (`studyInstanceUID`),
  KEY `researchID` (`researchID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `us_videoclip`
--

CREATE TABLE IF NOT EXISTS `us_videoclip` (
  `videoclipID` mediumint(9) NOT NULL AUTO_INCREMENT,
  `instanceNumber` int(5) NOT NULL,
  `studyInstanceUID` varchar(64) NOT NULL,
  `dataOraVideo` datetime DEFAULT NULL,
  `dataEntryDateTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `RightOrLeftIJV` char(1) NOT NULL,
  `Jposition123` int(1) NOT NULL,
  `phdx` decimal(12,10) NOT NULL,
  `umx` int(11) NOT NULL,
  `phsx` decimal(12,10) NOT NULL,
  `umy` int(11) NOT NULL,
  `effectiveDuration` decimal(5,2) NOT NULL,
  `numberOfFrames` int(11) NOT NULL,
  `fileName` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`videoclipID`),
  UNIQUE KEY `instanceNumber` (`instanceNumber`,`studyInstanceUID`),
  KEY `studyInstanceUID` (`studyInstanceUID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=106 ;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `cvp_ecg`
--
ALTER TABLE `cvp_ecg`
  ADD CONSTRAINT `cvp_ecg_ibfk_1` FOREIGN KEY (`idscreenshot`) REFERENCES `screenshot` (`idscreenshot`);

--
-- Limiti per la tabella `cvp_examination`
--
ALTER TABLE `cvp_examination`
  ADD CONSTRAINT `cvp_examination_ibfk_1` FOREIGN KEY (`studyInstanceUID`) REFERENCES `us_study` (`studyInstanceUID`);

--
-- Limiti per la tabella `cvp_sampling`
--
ALTER TABLE `cvp_sampling`
  ADD CONSTRAINT `cvp_sampling_ibfk_1` FOREIGN KEY (`idscreenshot`) REFERENCES `screenshot` (`idscreenshot`);

--
-- Limiti per la tabella `cvp_waves`
--
ALTER TABLE `cvp_waves`
  ADD CONSTRAINT `cvp_waves_ibfk_1` FOREIGN KEY (`idscreenshot`) REFERENCES `screenshot` (`idscreenshot`);

--
-- Limiti per la tabella `doppler_ecg`
--
ALTER TABLE `doppler_ecg`
  ADD CONSTRAINT `doppler_ecg_ibfk_1` FOREIGN KEY (`iddoppler`) REFERENCES `us_doppler` (`iddoppler`);

--
-- Limiti per la tabella `doppler_sampling`
--
ALTER TABLE `doppler_sampling`
  ADD CONSTRAINT `doppler_sampling_ibfk_1` FOREIGN KEY (`iddoppler`) REFERENCES `us_doppler` (`iddoppler`);

--
-- Limiti per la tabella `screenshot`
--
ALTER TABLE `screenshot`
  ADD CONSTRAINT `screenshot_ibfk_1` FOREIGN KEY (`cvpID`) REFERENCES `cvp_examination` (`cvpID`);

--
-- Limiti per la tabella `sonogram`
--
ALTER TABLE `sonogram`
  ADD CONSTRAINT `sonogram_ibfk_1` FOREIGN KEY (`videoclipID`) REFERENCES `us_videoclip` (`videoclipID`);

--
-- Limiti per la tabella `us_doppler`
--
ALTER TABLE `us_doppler`
  ADD CONSTRAINT `us_doppler_ibfk_1` FOREIGN KEY (`studyInstanceUID`) REFERENCES `us_study` (`studyInstanceUID`);

--
-- Limiti per la tabella `us_ecg`
--
ALTER TABLE `us_ecg`
  ADD CONSTRAINT `us_ecg_ibfk_1` FOREIGN KEY (`videoclipID`) REFERENCES `us_videoclip` (`videoclipID`);

--
-- Limiti per la tabella `us_jvp`
--
ALTER TABLE `us_jvp`
  ADD CONSTRAINT `us_jvp_ibfk_1` FOREIGN KEY (`videoclipID`) REFERENCES `us_videoclip` (`videoclipID`);

--
-- Limiti per la tabella `us_report`
--
ALTER TABLE `us_report`
  ADD CONSTRAINT `us_report_ibfk_1` FOREIGN KEY (`studyInstanceUID`) REFERENCES `us_study` (`studyInstanceUID`);

--
-- Limiti per la tabella `us_respiration`
--
ALTER TABLE `us_respiration`
  ADD CONSTRAINT `us_respiration_ibfk_1` FOREIGN KEY (`videoclipID`) REFERENCES `us_videoclip` (`videoclipID`);

--
-- Limiti per la tabella `us_study`
--
ALTER TABLE `us_study`
  ADD CONSTRAINT `us_study_ibfk_1` FOREIGN KEY (`researchID`) REFERENCES `research_project` (`researchID`);

--
-- Limiti per la tabella `us_videoclip`
--
ALTER TABLE `us_videoclip`
  ADD CONSTRAINT `us_videoclip_ibfk_1` FOREIGN KEY (`studyInstanceUID`) REFERENCES `us_study` (`studyInstanceUID`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
