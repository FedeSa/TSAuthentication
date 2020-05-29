-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Mag 28, 2020 alle 12:19
-- Versione del server: 10.4.8-MariaDB
-- Versione PHP: 7.3.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tsauth`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `images`
--

CREATE TABLE `images` (
  `image` varchar(255) CHARACTER SET latin1 NOT NULL,
  `author` varchar(255) CHARACTER SET latin1 NOT NULL,
  `arrow` varchar(255) CHARACTER SET latin1 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `images`
--

INSERT INTO `images` (`image`, `author`, `arrow`) VALUES
('Image1', 'Aaron Burden', 'left'),
('Image10', 'Ruvim Noga', 'left'),
('Image11', 'Samuel Scrimshaw', 'left'),
('Image12', 'Scott Webb', 'left'),
('Image13', 'Sergio Rola', 'left'),
('Image14', 'Yannis Papanastasopoulos', 'left'),
('Image2', 'Etienne', 'left'),
('Image3', 'Alex Wigan', 'left'),
('Image4', 'Brianna Fairhurst', 'left'),
('Image5', 'Christian Seeling', 'left'),
('Image6', 'George Fitzmaurice', 'left'),
('Image7', 'Geran De Klerk', 'left'),
('Image8', 'Jason Blackeye', 'left'),
('Image9', 'Kizwan Chronos', 'left');

-- --------------------------------------------------------

--
-- Struttura della tabella `percorsi`
--

CREATE TABLE `percorsi` (
  `partenze` varchar(255) NOT NULL,
  `arrivi` varchar(255) NOT NULL,
  `totale` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `percorsi`
--

INSERT INTO `percorsi` (`partenze`, `arrivi`, `totale`) VALUES
('AL', 'BB', 1),
('BB', 'DD', 2),
('DD', 'EE', 2),
('EE', 'FF', 0),
('FF', 'KK', 4);

-- --------------------------------------------------------

--
-- Struttura della tabella `prenotazioni`
--

CREATE TABLE `prenotazioni` (
  `utente` varchar(255) NOT NULL,
  `partenze` varchar(255) NOT NULL,
  `arrivi` varchar(255) NOT NULL,
  `passeggeri` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `prenotazioni`
--

INSERT INTO `prenotazioni` (`utente`, `partenze`, `arrivi`, `passeggeri`) VALUES
('u1@p.it', 'FF', 'KK', 4),
('u2@p.it', 'BB', 'EE', 1),
('u3@p.it', 'DD', 'EE', 1),
('u4@p.it', 'AL', 'DD', 1);

-- --------------------------------------------------------

--
-- Struttura della tabella `utenti`
--

CREATE TABLE `utenti` (
  `utente` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `utenti`
--

INSERT INTO `utenti` (`utente`, `password`) VALUES
('fede@hhh.it', 'Images/sergio-rola-wud-eV6Vpwo-unsplash.jpg,Images/brianna-fairhurst-4I6-DAdDC6A-unsplash.jpg,Images/Abstract_Etienne.jpg,Images/jason-blackeye-ap3LXI0fPJY-unsplash.jpg,Images/nature_kizwan chronos.jpg'),
('sss-kkio@dddd.com', 'Images/brianna-fairhurst-4I6-DAdDC6A-unsplash.jpg,Images/Abstract_Etienne.jpg,Images/Abstract_Etienne.jpg,Images/george-fitzmaurice-k-wRtwkF27k-unsplash.jpg,Images/ruvim-noga-pazM9TQJ2Ck-unsplash.jpg'),
('u1@p.it', 'ec6ef230f1828039ee794566b9c58adc'),
('u2@p.it', '1d665b9b1467944c128a5575119d1cfd'),
('u3@p.it', '7bc3ca68769437ce986455407dab2a1f'),
('u4@p.it', '13207e3d5722030f6c97d69b4904d39d');

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `images`
--
ALTER TABLE `images`
  ADD PRIMARY KEY (`image`);

--
-- Indici per le tabelle `percorsi`
--
ALTER TABLE `percorsi`
  ADD PRIMARY KEY (`partenze`,`arrivi`);

--
-- Indici per le tabelle `prenotazioni`
--
ALTER TABLE `prenotazioni`
  ADD PRIMARY KEY (`utente`,`partenze`,`arrivi`);

--
-- Indici per le tabelle `utenti`
--
ALTER TABLE `utenti`
  ADD PRIMARY KEY (`utente`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
