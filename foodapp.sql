-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Cze 13, 2025 at 11:32 PM
-- Wersja serwera: 10.4.32-MariaDB
-- Wersja PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `foodapp`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `locations`
--

CREATE TABLE `locations` (
  `id` int(11) NOT NULL,
  `restaurant_id` int(11) NOT NULL,
  `restaurant_name` varchar(150) NOT NULL,
  `city` varchar(100) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `opening_hours` varchar(255) DEFAULT NULL,
  `contact_email` varchar(150) DEFAULT NULL,
  `phone` varchar(32) DEFAULT NULL,
  `lat` double DEFAULT NULL,
  `lng` double DEFAULT NULL,
  `order_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `locations`
--

INSERT INTO `locations` (`id`, `restaurant_id`, `restaurant_name`, `city`, `address`, `opening_hours`, `contact_email`, `phone`, `lat`, `lng`, `order_url`) VALUES
(1, 1, 'Pierogarnia Stary Młyn', 'Częstochowa', 'ul. NMP 24', '10:00-22:00', 'kontakt@starymlyn.pl', '+48343211234', 50.81182, 19.12031, 'https://glovoapp.com/pl/czestochowa/pierogarnia-stary-mlyn-cze/'),
(2, 2, 'Restauracja Jurajska', 'Częstochowa', 'ul. Jurajska 5', '11:00-23:00', 'info@jurajska.pl', '+48343211235', 50.813, 19.1209, NULL),
(3, 3, 'Bar Mleczny Częstochowa', 'Częstochowa', 'ul. Piłsudskiego 10', '08:00-20:00', 'bar@czestochowa.pl', '+48343211236', 50.81, 19.122, NULL),
(4, 4, 'Restauracja Wawel', 'Kraków', 'ul. Wawelska 1', '09:00-23:00', 'kontakt@wawel.pl', '+48123456789', 50.06143, 19.93658, 'https://glovoapp.com/pl/krakow/restauracja-wawel-krk/'),
(5, 5, 'Pierogarnia Krakowska', 'Kraków', 'ul. Grodzka 15', '10:00-22:00', 'info@krakowska.pl', '+48123456780', 50.062, 19.937, NULL),
(6, 6, 'Bar Mleczny Kraków', 'Kraków', 'ul. Dietla 50', '07:00-19:00', 'bar@krakow.pl', '+48123456781', 50.06, 19.94, NULL),
(7, 7, 'Restauracja Panorama', 'Warszawa', 'ul. Marszałkowska 100', '11:00-23:00', 'kontakt@panorama.pl', '+48221234567', 52.22977, 21.01178, 'https://glovoapp.com/pl/warszawa/restauracja-panorama-waw/'),
(8, 8, 'Pierogarnia Warszawska', 'Warszawa', 'ul. Nowy Świat 20', '10:00-22:00', 'info@warszawska.pl', '+48221234568', 52.23, 21.012, NULL),
(9, 9, 'Bar Mleczny Warszawa', 'Warszawa', 'ul. Świętokrzyska 30', '07:00-19:00', 'bar@warszawa.pl', '+48221234569', 52.228, 21.01, NULL),
(10, 10, 'Karczma Polska', 'Wrocław', 'ul. Rynek 1', '10:00-22:00', 'kontakt@karczma.pl', '+48713456789', 51.10789, 17.03854, 'https://glovoapp.com/pl/wroclaw/karczma-polska-wro/'),
(11, 11, 'Restauracja Odra', 'Wrocław', 'ul. Odrzańska 2', '11:00-23:00', 'info@odra.pl', '+48713456780', 51.1085, 17.039, NULL),
(12, 12, 'Bar Mleczny Wrocław', 'Wrocław', 'ul. Piłsudskiego 20', '07:00-19:00', 'bar@wroclaw.pl', '+48713456781', 51.107, 17.04, NULL),
(13, 13, 'Bar Mleczny Neptun', 'Gdańsk', 'ul. Długa 50', '08:00-20:00', 'kontakt@neptun.pl', '+48583456789', 54.35205, 18.64637, 'https://glovoapp.com/pl/gdansk/bar-mleczny-neptun-gdn/'),
(14, 14, 'Restauracja Motława', 'Gdańsk', 'ul. Motławska 10', '11:00-23:00', 'info@motlawa.pl', '+48583456780', 54.353, 18.647, NULL),
(15, 15, 'Pierogarnia Gdańska', 'Gdańsk', 'ul. Piwna 5', '10:00-22:00', 'pierogi@gdansk.pl', '+48583456781', 54.351, 18.648, NULL),
(16, 16, 'Pizzeria Restauracja Arczi', 'Częstochowa', 'ul. Arczi 1', '10:00-22:00', 'kontakt@arczi.pl', '+48343211237', 50.8125, 19.121, 'https://glovoapp.com/pl/en/czestochowa/pizzeria-restauracja-arczi-czw/'),
(17, 17, 'Thang Long', 'Częstochowa', 'ul. Wietnamska 2', '11:00-23:00', 'info@thanglong.pl', '+48343211238', 50.8135, 19.1225, 'https://glovoapp.com/pl/en/czestochowa/thang-long-czw/'),
(18, 18, 'McDonald’s', 'Częstochowa', 'al. Wojska Polskiego 207', '07:00-23:00', 'kontakt@mcdonalds.pl', '+48343211239', 50.814, 19.123, 'https://glovoapp.com/pl/en/czestochowa/mcdonald-s-czw/');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `restaurants`
--

CREATE TABLE `restaurants` (
  `id` int(11) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `google_id` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `restaurants`
--

INSERT INTO `restaurants` (`id`, `email`, `password`, `google_id`) VALUES
(1, 'czest1@foodapp.pl', '$2y$10$iFugm8CYpb9zUKRxtllZTe0v1.GauQy5Pe71FRbtHBq9qaAdTEA/C', NULL),
(2, 'czest2@foodapp.pl', '$2y$10$iFugm8CYpb9zUKRxtllZTe0v1.GauQy5Pe71FRbtHBq9qaAdTEA/C', NULL),
(3, 'czest3@foodapp.pl', '$2y$10$iFugm8CYpb9zUKRxtllZTe0v1.GauQy5Pe71FRbtHBq9qaAdTEA/C', NULL),
(4, 'krakow1@foodapp.pl', '$2y$10$iFugm8CYpb9zUKRxtllZTe0v1.GauQy5Pe71FRbtHBq9qaAdTEA/C', NULL),
(5, 'krakow2@foodapp.pl', '$2y$10$iFugm8CYpb9zUKRxtllZTe0v1.GauQy5Pe71FRbtHBq9qaAdTEA/C', NULL),
(6, 'krakow3@foodapp.pl', '$2y$10$iFugm8CYpb9zUKRxtllZTe0v1.GauQy5Pe71FRbtHBq9qaAdTEA/C', NULL),
(7, 'warszawa1@foodapp.pl', '$2y$10$iFugm8CYpb9zUKRxtllZTe0v1.GauQy5Pe71FRbtHBq9qaAdTEA/C', NULL),
(8, 'warszawa2@foodapp.pl', '$2y$10$iFugm8CYpb9zUKRxtllZTe0v1.GauQy5Pe71FRbtHBq9qaAdTEA/C', NULL),
(9, 'warszawa3@foodapp.pl', '$2y$10$iFugm8CYpb9zUKRxtllZTe0v1.GauQy5Pe71FRbtHBq9qaAdTEA/C', NULL),
(10, 'wroclaw1@foodapp.pl', '$2y$10$iFugm8CYpb9zUKRxtllZTe0v1.GauQy5Pe71FRbtHBq9qaAdTEA/C', NULL),
(11, 'wroclaw2@foodapp.pl', '$2y$10$iFugm8CYpb9zUKRxtllZTe0v1.GauQy5Pe71FRbtHBq9qaAdTEA/C', NULL),
(12, 'wroclaw3@foodapp.pl', '$2y$10$iFugm8CYpb9zUKRxtllZTe0v1.GauQy5Pe71FRbtHBq9qaAdTEA/C', NULL),
(13, 'gdansk1@foodapp.pl', '$2y$10$iFugm8CYpb9zUKRxtllZTe0v1.GauQy5Pe71FRbtHBq9qaAdTEA/C', NULL),
(14, 'gdansk2@foodapp.pl', '$2y$10$iFugm8CYpb9zUKRxtllZTe0v1.GauQy5Pe71FRbtHBq9qaAdTEA/C', NULL),
(15, 'gdansk3@foodapp.pl', '$2y$10$iFugm8CYpb9zUKRxtllZTe0v1.GauQy5Pe71FRbtHBq9qaAdTEA/C', NULL),
(16, 'arczi@foodapp.pl', '$2y$10$iFugm8CYpb9zUKRxtllZTe0v1.GauQy5Pe71FRbtHBq9qaAdTEA/C', NULL),
(17, 'thanglong@foodapp.pl', '$2y$10$iFugm8CYpb9zUKRxtllZTe0v1.GauQy5Pe71FRbtHBq9qaAdTEA/C', NULL),
(18, 'mcdonalds@foodapp.pl', '$2y$10$iFugm8CYpb9zUKRxtllZTe0v1.GauQy5Pe71FRbtHBq9qaAdTEA/C', NULL);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `google_id` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `activation_token` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `google_id`, `is_active`, `activation_token`) VALUES
(1, 'cody.urlik@gmail.com', 'cody.urlik@gmail.com', '$2y$10$frlYbbGCpLqnu06OeN1fWuIVMTB3CF6EHE4Qc1kdks8AVddaeBR1G', NULL, 0, NULL),
(4, 'DishPatch', 'dishpatch.sapport@gmail.com', '$2y$10$aSyhdoPDwhlzjTABqTCtnOekWJZrngIVgwvkgbymeLYfDtfSanLX6', NULL, 1, NULL);

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `restaurant_id` (`restaurant_id`);

--
-- Indeksy dla tabeli `restaurants`
--
ALTER TABLE `restaurants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indeksy dla tabeli `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `locations`
--
ALTER TABLE `locations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `restaurants`
--
ALTER TABLE `restaurants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `locations`
--
ALTER TABLE `locations`
  ADD CONSTRAINT `locations_ibfk_1` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
