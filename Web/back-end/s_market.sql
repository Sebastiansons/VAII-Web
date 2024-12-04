-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hostiteľ: 127.0.0.1
-- Čas generovania: St 04.Dec 2024, 21:46
-- Verzia serveru: 10.4.32-MariaDB
-- Verzia PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databáza: `s_market`
--

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `shopcategories`
--

CREATE TABLE `shopcategories` (
  `CategoryID` int(11) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Description` varchar(255) DEFAULT NULL,
  `Icon` varchar(255) DEFAULT NULL,
  `IsNew` tinyint(1) DEFAULT 0,
  `IsUnavailable` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Sťahujem dáta pre tabuľku `shopcategories`
--

INSERT INTO `shopcategories` (`CategoryID`, `Name`, `Description`, `Icon`, `IsNew`, `IsUnavailable`) VALUES
(1, 'Sport', 'Sporting goods', 'bi-trophy', 0, 0),
(2, 'Cars', 'Automotive items', 'bi-truck', 0, 0),
(3, 'Books', 'Various books', 'bi-book', 0, 0),
(4, 'Clothes', 'Apparel and fashion', 'bi-handbag', 0, 0),
(5, 'House and garden', 'Home essentials', 'bi-house', 0, 0),
(6, 'Electro', 'Electronic devices', 'bi-tv', 0, 0),
(7, 'Mobiles', 'Smartphones', 'bi-phone', 0, 0),
(8, 'Furniture', 'Home furnishings', 'bi-lamp', 0, 0),
(9, 'PC', 'Computing devices', 'bi-laptop', 0, 0),
(10, 'Machines', 'Industrial tools', 'bi-wrench', 0, 0),
(11, 'Services', 'Various services', 'bi-tools', 0, 0),
(12, 'Music', 'Musical instruments', 'bi-music-note-beamed', 0, 0),
(13, 'Work', 'Work essentials', 'bi-briefcase', 0, 0),
(14, 'Animals', 'Pet supplies', 'bi-bug', 0, 0),
(15, 'Kids', 'Childrens items', 'bi-balloon', 0, 0),
(16, 'Others', 'Miscellaneous', 'bi-box-seam', 0, 0);

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `shopitems`
--

CREATE TABLE `shopitems` (
  `ItemID` int(11) NOT NULL,
  `CategoryID` int(11) DEFAULT NULL,
  `Name` varchar(255) NOT NULL,
  `Description` text DEFAULT NULL,
  `Created` timestamp NOT NULL DEFAULT current_timestamp(),
  `Price` decimal(10,2) NOT NULL,
  `Image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Sťahujem dáta pre tabuľku `shopitems`
--

INSERT INTO `shopitems` (`ItemID`, `CategoryID`, `Name`, `Description`, `Created`, `Price`, `Image`) VALUES
(13, 4, 'cwecw', 'ecew', '2024-12-04 20:36:55', 11.00, NULL),
(15, 1, 'cwe', 'cwecw', '2024-12-04 20:45:18', 1.00, NULL);

--
-- Kľúče pre exportované tabuľky
--

--
-- Indexy pre tabuľku `shopcategories`
--
ALTER TABLE `shopcategories`
  ADD PRIMARY KEY (`CategoryID`);

--
-- Indexy pre tabuľku `shopitems`
--
ALTER TABLE `shopitems`
  ADD PRIMARY KEY (`ItemID`),
  ADD KEY `CategoryID` (`CategoryID`);

--
-- AUTO_INCREMENT pre exportované tabuľky
--

--
-- AUTO_INCREMENT pre tabuľku `shopcategories`
--
ALTER TABLE `shopcategories`
  MODIFY `CategoryID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT pre tabuľku `shopitems`
--
ALTER TABLE `shopitems`
  MODIFY `ItemID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Obmedzenie pre exportované tabuľky
--

--
-- Obmedzenie pre tabuľku `shopitems`
--
ALTER TABLE `shopitems`
  ADD CONSTRAINT `shopitems_ibfk_1` FOREIGN KEY (`CategoryID`) REFERENCES `shopcategories` (`CategoryID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
