-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hostiteľ: 127.0.0.1
-- Čas generovania: Št 06.Feb 2025, 23:38
-- Verzia serveru: 10.4.32-MariaDB
-- Verzia PHP: 8.2.12

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
-- Štruktúra tabuľky pre tabuľku `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `client_id` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `countries`
--

CREATE TABLE `countries` (
  `country_id` int(11) NOT NULL,
  `country_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Sťahujem dáta pre tabuľku `countries`
--

INSERT INTO `countries` (`country_id`, `country_name`) VALUES
(1, 'Slovakia');

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `orders`
--

CREATE TABLE `orders` (
  `orderID` varchar(10) NOT NULL,
  `clientID` int(11) DEFAULT NULL,
  `itemIDs` varchar(255) DEFAULT NULL,
  `quantities` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `statusID` int(11) DEFAULT NULL,
  `deliveryAddress` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Sťahujem dáta pre tabuľku `orders`
--

INSERT INTO `orders` (`orderID`, `clientID`, `itemIDs`, `quantities`, `created_at`, `statusID`, `deliveryAddress`) VALUES
('VTKdaAOmoR', 4, '45', '3', '2025-02-06 22:27:59', 3, 'Brodnanska 210, Zilina, 12345, Slovakia');

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `order_status`
--

CREATE TABLE `order_status` (
  `statusID` int(11) NOT NULL,
  `statusName` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Sťahujem dáta pre tabuľku `order_status`
--

INSERT INTO `order_status` (`statusID`, `statusName`) VALUES
(1, 'Created'),
(2, 'Processing'),
(3, 'Sent'),
(4, 'Canceled');

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
(6, 'Electro', 'Electronic devices', 'bi-tv', 0, 0),
(7, 'Mobiles', 'Smartphones', 'bi-phone', 0, 0),
(8, 'Furniture', 'Home furnishings', 'bi-lamp', 0, 0),
(9, 'PC', 'Computing devices', 'bi-laptop', 0, 0),
(10, 'Machines', 'Industrial tools', 'bi-wrench', 0, 0),
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
(45, 6, 'BEKO EWUE86261CSH1W +10 rokov záruka na motor po registrácii', 'Práčka - s predným plnením, energetická trieda A, kapacita práčky 8 kg, váž. spotreba energie 47 kWh/100 cyklov, váž. spotreba vody na cyklus 37 l, parný program, invertorový motor, odložený štart, rýchly program, displej a zvukový signál ukončenia programu, 1200 ot./min pri odstreďovaní, rozmery 60 × 84 × 55 cm (Š×V×H)', '2025-02-06 22:20:21', 321.80, '../../images/products/67a535e56d4bf.png,../../images/products/67a535e56d583.png');

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `users`
--

CREATE TABLE `users` (
  `Id` int(11) NOT NULL,
  `Username` varchar(30) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `Session_id` varchar(255) DEFAULT NULL,
  `Session_updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Role_id` int(11) NOT NULL DEFAULT 1,
  `Balance` double(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Sťahujem dáta pre tabuľku `users`
--

INSERT INTO `users` (`Id`, `Username`, `Email`, `Password`, `Created_at`, `Session_id`, `Session_updated_at`, `Role_id`, `Balance`) VALUES
(2, 'sepkoadmin', 'sepkosidor@gmail.com', '$2y$10$xYqLEKQiVKs4VXur8Val..8RL64WGCiSb486zivuQZ5pUl3UbtBHS', '2025-01-13 22:41:34', 's9nu3blgqf9b6p20qmr3b1s9g4', '2025-02-06 23:32:53', 2, 0.00),
(4, 'sepkocustomer', 'sepko@gmail.com', '$2y$10$LcIUcxzroIMvnBUGog.t1uxMkpR9ddQ1PdnGwsg0FXXkeKdRMtGqy', '2025-02-06 13:20:50', '53mrjoq2a2p0vo4ed81gtnlmv5', '2025-02-06 23:37:08', 1, 0.00),
(5, 'sepkosupport', 'support@gmail.com', '$2y$10$9RQtQdKPKPIDWJA6Gv31geKSfLFutKPhET6Q6miLUrtSpjUWiIEn2', '2025-02-06 18:42:15', '52bmtosbeeejvh1hl4324goumh', '2025-02-06 23:36:52', 3, 0.00);

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `user_addresses`
--

CREATE TABLE `user_addresses` (
  `user_id` int(11) NOT NULL,
  `street` varchar(255) NOT NULL,
  `house_number` varchar(50) NOT NULL,
  `city` varchar(100) NOT NULL,
  `postal_code` varchar(20) NOT NULL,
  `country_id` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Sťahujem dáta pre tabuľku `user_addresses`
--

INSERT INTO `user_addresses` (`user_id`, `street`, `house_number`, `city`, `postal_code`, `country_id`) VALUES
(2, 'brodno', '1234', 'zilina', '01014', 1),
(4, 'Brodnanska', '210', 'Zilina', '12345', 1),
(5, 'ntghn', 'tzhntz', 'Zilina', '12345', 1);

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `user_roles`
--

CREATE TABLE `user_roles` (
  `Id` int(11) NOT NULL,
  `Name` varchar(50) NOT NULL,
  `Note` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Sťahujem dáta pre tabuľku `user_roles`
--

INSERT INTO `user_roles` (`Id`, `Name`, `Note`) VALUES
(1, 'Customer', 'Regular customer role'),
(2, 'Admin', 'Administrator role with full access'),
(3, 'Support', 'Support role for customer service');

--
-- Kľúče pre exportované tabuľky
--

--
-- Indexy pre tabuľku `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexy pre tabuľku `countries`
--
ALTER TABLE `countries`
  ADD PRIMARY KEY (`country_id`);

--
-- Indexy pre tabuľku `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`orderID`),
  ADD KEY `clientID` (`clientID`),
  ADD KEY `statusID` (`statusID`);

--
-- Indexy pre tabuľku `order_status`
--
ALTER TABLE `order_status`
  ADD PRIMARY KEY (`statusID`);

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
-- Indexy pre tabuľku `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`Id`),
  ADD UNIQUE KEY `Username` (`Username`),
  ADD UNIQUE KEY `Email` (`Email`),
  ADD KEY `FK_Role` (`Role_id`);

--
-- Indexy pre tabuľku `user_addresses`
--
ALTER TABLE `user_addresses`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `country_id` (`country_id`);

--
-- Indexy pre tabuľku `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`Id`);

--
-- AUTO_INCREMENT pre exportované tabuľky
--

--
-- AUTO_INCREMENT pre tabuľku `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT pre tabuľku `countries`
--
ALTER TABLE `countries`
  MODIFY `country_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pre tabuľku `order_status`
--
ALTER TABLE `order_status`
  MODIFY `statusID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pre tabuľku `shopcategories`
--
ALTER TABLE `shopcategories`
  MODIFY `CategoryID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT pre tabuľku `shopitems`
--
ALTER TABLE `shopitems`
  MODIFY `ItemID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT pre tabuľku `users`
--
ALTER TABLE `users`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pre tabuľku `user_roles`
--
ALTER TABLE `user_roles`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Obmedzenie pre exportované tabuľky
--

--
-- Obmedzenie pre tabuľku `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `users` (`Id`),
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `shopitems` (`ItemID`);

--
-- Obmedzenie pre tabuľku `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`clientID`) REFERENCES `users` (`Id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`statusID`) REFERENCES `order_status` (`statusID`);

--
-- Obmedzenie pre tabuľku `shopitems`
--
ALTER TABLE `shopitems`
  ADD CONSTRAINT `shopitems_ibfk_1` FOREIGN KEY (`CategoryID`) REFERENCES `shopcategories` (`CategoryID`);

--
-- Obmedzenie pre tabuľku `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `FK_Role` FOREIGN KEY (`Role_id`) REFERENCES `user_roles` (`Id`);

--
-- Obmedzenie pre tabuľku `user_addresses`
--
ALTER TABLE `user_addresses`
  ADD CONSTRAINT `user_addresses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`Id`),
  ADD CONSTRAINT `user_addresses_ibfk_2` FOREIGN KEY (`country_id`) REFERENCES `countries` (`country_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
