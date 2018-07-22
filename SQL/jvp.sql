-- phpMyAdmin SQL Dump
-- version 4.6.6deb5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 22, 2018 at 10:09 AM
-- Server version: 5.7.22-0ubuntu18.04.1
-- PHP Version: 7.2.7-1+ubuntu18.04.1+deb.sury.org+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `jvp`
--

-- --------------------------------------------------------

--
-- Table structure for table `cvp_ecg`
--

CREATE TABLE `cvp_ecg` (
  `idscreenshot` mediumint(9) NOT NULL,
  `number` int(11) NOT NULL,
  `PQRSTwave` char(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `cvp_examination`
--

CREATE TABLE `cvp_examination` (
  `cvpID` mediumint(9) NOT NULL,
  `studyInstanceUID` varchar(64) NOT NULL,
  `patientName` varchar(100) NOT NULL,
  `patientFamilyName` varchar(100) NOT NULL,
  `patientID` varchar(100) NOT NULL,
  `dataOraEsame` datetime NOT NULL,
  `dataEntryDateTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `cvp_sampling`
--

CREATE TABLE `cvp_sampling` (
  `idscreenshot` mediumint(9) NOT NULL,
  `number` int(11) NOT NULL,
  `pressure` decimal(5,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `cvp_waves`
--

CREATE TABLE `cvp_waves` (
  `idscreenshot` mediumint(9) NOT NULL,
  `number` int(11) NOT NULL,
  `acxvyWave` char(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `doppler_ecg`
--

CREATE TABLE `doppler_ecg` (
  `iddoppler` mediumint(9) NOT NULL,
  `number` int(11) NOT NULL,
  `PQRSTwave` char(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `doppler_sampling`
--

CREATE TABLE `doppler_sampling` (
  `iddoppler` mediumint(9) NOT NULL,
  `number` int(11) NOT NULL,
  `meanVelocity` decimal(5,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `orph_sonogram`
--

CREATE TABLE `orph_sonogram` (
  `PID` varchar(100) NOT NULL,
  `time` float NOT NULL,
  `CSA` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `research_project`
--

CREATE TABLE `research_project` (
  `researchID` varchar(100) NOT NULL,
  `description` varchar(10000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `screenshot`
--

CREATE TABLE `screenshot` (
  `idscreenshot` mediumint(9) NOT NULL,
  `cvpID` mediumint(9) NOT NULL,
  `data_shot` datetime DEFAULT NULL,
  `fileName` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sonogram`
--

CREATE TABLE `sonogram` (
  `videoclipID` mediumint(9) NOT NULL,
  `processID` varchar(64) NOT NULL,
  `number` float NOT NULL,
  `csa` float NOT NULL,
  `perimeter` int(11) NOT NULL,
  `CSAcm` float DEFAULT '0',
  `times` float NOT NULL DEFAULT '0',
  `realtime` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `us_doppler`
--

CREATE TABLE `us_doppler` (
  `iddoppler` mediumint(9) NOT NULL,
  `studyInstanceUID` varchar(64) NOT NULL,
  `data_shot` datetime DEFAULT NULL,
  `RightOrLeftIJV` char(1) NOT NULL,
  `Jposition123` int(1) NOT NULL,
  `fileName` varchar(300) NOT NULL,
  `pixelTocms` decimal(5,2) NOT NULL,
  `baseLine` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `us_ecg`
--

CREATE TABLE `us_ecg` (
  `videoclipID` mediumint(9) NOT NULL,
  `number` int(11) NOT NULL,
  `PQRSTwave` char(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `us_jvp`
--

CREATE TABLE `us_jvp` (
  `videoclipID` mediumint(9) NOT NULL,
  `number` int(11) NOT NULL,
  `acxvyWave` char(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `us_report`
--

CREATE TABLE `us_report` (
  `reportID` mediumint(9) NOT NULL,
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
  `cognome` varchar(100) DEFAULT NULL,
  `Indirizzo` varchar(300) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `us_respiration`
--

CREATE TABLE `us_respiration` (
  `videoclipID` mediumint(9) NOT NULL,
  `number` int(11) NOT NULL,
  `ieWaves` char(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `us_study`
--

CREATE TABLE `us_study` (
  `studyInstanceUID` varchar(64) NOT NULL,
  `patientName` varchar(100) NOT NULL,
  `patientFamilyName` varchar(100) NOT NULL,
  `patientID` varchar(100) NOT NULL,
  `studyDateTime` datetime DEFAULT NULL,
  `dataEntryDateTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `researchID` varchar(100) NOT NULL,
  `sex` varchar(20) DEFAULT NULL,
  `address` varchar(150) DEFAULT NULL,
  `primarycode` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `us_videoclip`
--

CREATE TABLE `us_videoclip` (
  `videoclipID` mediumint(9) NOT NULL,
  `instanceNumber` int(5) NOT NULL,
  `studyInstanceUID` varchar(64) NOT NULL,
  `dataOraVideo` time DEFAULT NULL,
  `dataEntryDateTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `RightOrLeftIJV` char(1) NOT NULL,
  `Jposition123` int(1) NOT NULL,
  `phdx` decimal(12,10) NOT NULL,
  `umx` int(11) NOT NULL,
  `phsx` decimal(12,10) NOT NULL,
  `umy` int(11) NOT NULL,
  `effectiveDuration` decimal(5,2) NOT NULL,
  `numberOfFrames` int(11) NOT NULL,
  `fileName` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cvp_ecg`
--
ALTER TABLE `cvp_ecg`
  ADD PRIMARY KEY (`idscreenshot`,`number`);

--
-- Indexes for table `cvp_examination`
--
ALTER TABLE `cvp_examination`
  ADD PRIMARY KEY (`cvpID`),
  ADD UNIQUE KEY `studyInstanceUID` (`studyInstanceUID`);

--
-- Indexes for table `cvp_sampling`
--
ALTER TABLE `cvp_sampling`
  ADD PRIMARY KEY (`idscreenshot`,`number`);

--
-- Indexes for table `cvp_waves`
--
ALTER TABLE `cvp_waves`
  ADD PRIMARY KEY (`idscreenshot`,`number`);

--
-- Indexes for table `doppler_ecg`
--
ALTER TABLE `doppler_ecg`
  ADD PRIMARY KEY (`iddoppler`,`number`);

--
-- Indexes for table `doppler_sampling`
--
ALTER TABLE `doppler_sampling`
  ADD PRIMARY KEY (`iddoppler`,`number`);

--
-- Indexes for table `research_project`
--
ALTER TABLE `research_project`
  ADD PRIMARY KEY (`researchID`);

--
-- Indexes for table `screenshot`
--
ALTER TABLE `screenshot`
  ADD PRIMARY KEY (`idscreenshot`),
  ADD KEY `cvpID` (`cvpID`);

--
-- Indexes for table `sonogram`
--
ALTER TABLE `sonogram`
  ADD PRIMARY KEY (`videoclipID`,`processID`,`number`);

--
-- Indexes for table `us_doppler`
--
ALTER TABLE `us_doppler`
  ADD PRIMARY KEY (`iddoppler`),
  ADD KEY `studyInstanceUID` (`studyInstanceUID`);

--
-- Indexes for table `us_ecg`
--
ALTER TABLE `us_ecg`
  ADD PRIMARY KEY (`videoclipID`,`number`);

--
-- Indexes for table `us_jvp`
--
ALTER TABLE `us_jvp`
  ADD PRIMARY KEY (`videoclipID`,`number`);

--
-- Indexes for table `us_report`
--
ALTER TABLE `us_report`
  ADD PRIMARY KEY (`reportID`),
  ADD KEY `studyInstanceUID` (`studyInstanceUID`);

--
-- Indexes for table `us_respiration`
--
ALTER TABLE `us_respiration`
  ADD PRIMARY KEY (`videoclipID`,`number`);

--
-- Indexes for table `us_study`
--
ALTER TABLE `us_study`
  ADD PRIMARY KEY (`studyInstanceUID`),
  ADD KEY `researchID` (`researchID`);

--
-- Indexes for table `us_videoclip`
--
ALTER TABLE `us_videoclip`
  ADD PRIMARY KEY (`videoclipID`),
  ADD UNIQUE KEY `instanceNumber` (`instanceNumber`,`studyInstanceUID`),
  ADD KEY `studyInstanceUID` (`studyInstanceUID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cvp_examination`
--
ALTER TABLE `cvp_examination`
  MODIFY `cvpID` mediumint(9) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `screenshot`
--
ALTER TABLE `screenshot`
  MODIFY `idscreenshot` mediumint(9) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `us_doppler`
--
ALTER TABLE `us_doppler`
  MODIFY `iddoppler` mediumint(9) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `us_report`
--
ALTER TABLE `us_report`
  MODIFY `reportID` mediumint(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `us_videoclip`
--
ALTER TABLE `us_videoclip`
  MODIFY `videoclipID` mediumint(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=133;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `cvp_ecg`
--
ALTER TABLE `cvp_ecg`
  ADD CONSTRAINT `cvp_ecg_ibfk_1` FOREIGN KEY (`idscreenshot`) REFERENCES `screenshot` (`idscreenshot`);

--
-- Constraints for table `cvp_examination`
--
ALTER TABLE `cvp_examination`
  ADD CONSTRAINT `cvp_examination_ibfk_1` FOREIGN KEY (`studyInstanceUID`) REFERENCES `us_study` (`studyInstanceUID`);

--
-- Constraints for table `cvp_sampling`
--
ALTER TABLE `cvp_sampling`
  ADD CONSTRAINT `cvp_sampling_ibfk_1` FOREIGN KEY (`idscreenshot`) REFERENCES `screenshot` (`idscreenshot`);

--
-- Constraints for table `cvp_waves`
--
ALTER TABLE `cvp_waves`
  ADD CONSTRAINT `cvp_waves_ibfk_1` FOREIGN KEY (`idscreenshot`) REFERENCES `screenshot` (`idscreenshot`);

--
-- Constraints for table `doppler_ecg`
--
ALTER TABLE `doppler_ecg`
  ADD CONSTRAINT `doppler_ecg_ibfk_1` FOREIGN KEY (`iddoppler`) REFERENCES `us_doppler` (`iddoppler`);

--
-- Constraints for table `doppler_sampling`
--
ALTER TABLE `doppler_sampling`
  ADD CONSTRAINT `doppler_sampling_ibfk_1` FOREIGN KEY (`iddoppler`) REFERENCES `us_doppler` (`iddoppler`);

--
-- Constraints for table `screenshot`
--
ALTER TABLE `screenshot`
  ADD CONSTRAINT `screenshot_ibfk_1` FOREIGN KEY (`cvpID`) REFERENCES `cvp_examination` (`cvpID`);

--
-- Constraints for table `sonogram`
--
ALTER TABLE `sonogram`
  ADD CONSTRAINT `sonogram_ibfk_1` FOREIGN KEY (`videoclipID`) REFERENCES `us_videoclip` (`videoclipID`);

--
-- Constraints for table `us_doppler`
--
ALTER TABLE `us_doppler`
  ADD CONSTRAINT `us_doppler_ibfk_1` FOREIGN KEY (`studyInstanceUID`) REFERENCES `us_study` (`studyInstanceUID`);

--
-- Constraints for table `us_ecg`
--
ALTER TABLE `us_ecg`
  ADD CONSTRAINT `us_ecg_ibfk_1` FOREIGN KEY (`videoclipID`) REFERENCES `us_videoclip` (`videoclipID`);

--
-- Constraints for table `us_jvp`
--
ALTER TABLE `us_jvp`
  ADD CONSTRAINT `us_jvp_ibfk_1` FOREIGN KEY (`videoclipID`) REFERENCES `us_videoclip` (`videoclipID`);

--
-- Constraints for table `us_report`
--
ALTER TABLE `us_report`
  ADD CONSTRAINT `us_report_ibfk_1` FOREIGN KEY (`studyInstanceUID`) REFERENCES `us_study` (`studyInstanceUID`);

--
-- Constraints for table `us_respiration`
--
ALTER TABLE `us_respiration`
  ADD CONSTRAINT `us_respiration_ibfk_1` FOREIGN KEY (`videoclipID`) REFERENCES `us_videoclip` (`videoclipID`);

--
-- Constraints for table `us_study`
--
ALTER TABLE `us_study`
  ADD CONSTRAINT `us_study_ibfk_1` FOREIGN KEY (`researchID`) REFERENCES `research_project` (`researchID`);

--
-- Constraints for table `us_videoclip`
--
ALTER TABLE `us_videoclip`
  ADD CONSTRAINT `us_videoclip_ibfk_1` FOREIGN KEY (`studyInstanceUID`) REFERENCES `us_study` (`studyInstanceUID`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
