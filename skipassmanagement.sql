-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Gen 23, 2025 alle 08:33
-- Versione del server: 10.4.32-MariaDB
-- Versione PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `skipassmanagement`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `discounts`
--

CREATE TABLE `discounts` (
  `id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `category` varchar(50) NOT NULL,
  `age_group` varchar(50) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `discounts`
--

INSERT INTO `discounts` (`id`, `type`, `category`, `age_group`, `price`) VALUES
(1, 'fisi', 'Giornaliero', 'Adulti', 27.50),
(2, 'fisi', 'Giornaliero', 'Junior', 23.00),
(3, 'fisi', 'Giornaliero', 'Senior', 25.00),
(4, 'fisi', 'Giornaliero', 'Baby', 15.50),
(5, 'fisi', 'Stagionale', 'Adulti', 680.00),
(6, 'fisi', 'Stagionale', 'Junior', 680.00),
(7, 'fisi', 'Stagionale', 'Senior', 680.00),
(8, 'fisi', 'Stagionale', 'Baby', 680.00),
(9, 'maestro', 'Stagionale', 'Adulti', 450.00),
(10, 'maestro', 'Stagionale', 'Junior', 450.00),
(11, 'maestro', 'Stagionale', 'Senior', 450.00),
(12, 'maestro', 'Stagionale', 'Baby', 450.00),
(13, 'disabile', 'Giornaliero', 'Adulti', 38.50),
(14, 'disabile', 'Giornaliero', 'Junior', 32.20),
(15, 'disabile', 'Giornaliero', 'Senior', 35.00),
(16, 'disabile', 'Giornaliero', 'Baby', 21.70),
(17, 'disabile_accompagnatore', 'Accompagnatore', 'Any', 0.00),
(18, 'disabile', 'Stagionale', 'Adulti', 728.00),
(19, 'disabile', 'Stagionale', 'Junior', 595.00),
(20, 'disabile', 'Stagionale', 'Senior', 595.00),
(21, 'disabile', 'Stagionale', 'Baby', 364.00);

-- --------------------------------------------------------

--
-- Struttura della tabella `rewards`
--

CREATE TABLE `rewards` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `points_cost` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `rewards`
--

INSERT INTO `rewards` (`id`, `name`, `description`, `points_cost`) VALUES
(1, 'Sconto del 10% su Skipass', 'Ottieni uno sconto del 10% sull\'acquisto del prossimo skipass.', 100),
(2, 'Skipass Gratuito per un Giorno', 'Riscatta un giorno gratuito di utilizzo dello skipass.', 500),
(3, 'Accesso VIP alle Piste', 'Accesso prioritario alle piste durante i fine settimana.', 300),
(4, 'Merchandise Gratuito', 'Ricevi un gadget ufficiale del Gestionale SkiPass.', 150);

-- --------------------------------------------------------

--
-- Struttura della tabella `skipasses`
--

CREATE TABLE `skipasses` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `category` varchar(255) NOT NULL,
  `age_group` varchar(255) NOT NULL,
  `skipass_price_id` int(11) NOT NULL,
  `validity_start_date` date NOT NULL,
  `validity_end_date` date NOT NULL,
  `area` varchar(255) NOT NULL,
  `discount_type` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `status` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `skipasses`
--

INSERT INTO `skipasses` (`id`, `user_id`, `category`, `age_group`, `skipass_price_id`, `validity_start_date`, `validity_end_date`, `area`, `discount_type`, `price`, `status`, `created_at`) VALUES
(4, 4, 'Stagionale', 'Adulti', 33, '2024-11-20', '2025-04-25', 'Lombardia', 'fisi', 680.00, 'attivo', '2025-01-22 10:01:14'),
(5, 1, 'Stagionale', 'Adulti', 37, '2024-11-20', '2025-04-25', 'Lombardia', 'maestro', 450.00, 'attivo', '2025-01-22 10:01:14'),
(6, 1, 'Giornaliero', 'Adulti', 41, '2025-02-03', '2025-02-04', 'Lombardia', 'disabile', 38.50, 'attivo', '2025-01-22 10:01:14'),
(7, 4, 'Settimanale (6 Giorni)', 'Adulti', 13, '2025-02-01', '2025-02-07', 'Lombardia', 'none', 320.00, 'attivo', '2025-01-22 10:01:14'),
(8, 1, 'Giornaliero', 'Adulti', 29, '2025-03-01', '2025-03-02', 'Piemonte', 'fisi', 27.50, 'attivo', '2025-01-22 10:01:14'),
(9, 4, 'Stagionale', 'Junior', 34, '2024-11-20', '2025-04-24', 'Trentino Alto Adige', 'fisi', 680.00, 'attivo', '2025-01-22 10:01:14'),
(10, 4, 'Giornaliero', 'Adulti', 1, '2025-02-01', '2025-02-02', 'Valle d\\\'aosta', 'none', 55.00, 'attivo', '2025-01-23 07:20:31');

-- --------------------------------------------------------

--
-- Struttura della tabella `skipass_prices`
--

CREATE TABLE `skipass_prices` (
  `id` int(11) NOT NULL,
  `category` varchar(255) NOT NULL,
  `age_group` varchar(255) NOT NULL,
  `discount_type` varchar(50) NOT NULL DEFAULT 'none',
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `skipass_prices`
--

INSERT INTO `skipass_prices` (`id`, `category`, `age_group`, `discount_type`, `price`, `created_at`) VALUES
(1, 'Giornaliero', 'Adulti', 'none', 55.00, '2025-01-21 19:00:11'),
(2, 'Giornaliero', 'Junior (fino a 16 anni)', 'none', 46.00, '2025-01-21 19:00:11'),
(3, 'Giornaliero', 'Senior (oltre 65 anni)', 'none', 50.00, '2025-01-21 19:00:11'),
(4, 'Giornaliero', 'Baby (fino a 8 anni)', 'none', 31.00, '2025-01-21 19:00:11'),
(5, '2 Giorni', 'Adulti', 'none', 128.00, '2025-01-21 19:00:11'),
(6, '2 Giorni', 'Junior (fino a 16 anni)', 'none', 108.50, '2025-01-21 19:00:11'),
(7, '2 Giorni', 'Senior (oltre 65 anni)', 'none', 108.50, '2025-01-21 19:00:11'),
(8, '2 Giorni', 'Baby (fino a 8 anni)', 'none', 64.00, '2025-01-21 19:00:11'),
(9, '3 Giorni', 'Adulti', 'none', 189.50, '2025-01-21 19:00:11'),
(10, '3 Giorni', 'Junior (fino a 16 anni)', 'none', 161.00, '2025-01-21 19:00:11'),
(11, '3 Giorni', 'Senior (oltre 65 anni)', 'none', 161.00, '2025-01-21 19:00:11'),
(12, '3 Giorni', 'Baby (fino a 8 anni)', 'none', 94.50, '2025-01-21 19:00:11'),
(13, 'Settimanale (6 Giorni)', 'Adulti', 'none', 320.00, '2025-01-21 19:00:11'),
(14, 'Settimanale (6 Giorni)', 'Junior (fino a 16 anni)', 'none', 275.00, '2025-01-21 19:00:11'),
(15, 'Settimanale (6 Giorni)', 'Senior (oltre 65 anni)', 'none', 275.00, '2025-01-21 19:00:11'),
(16, 'Settimanale (6 Giorni)', 'Baby (fino a 8 anni)', 'none', 160.00, '2025-01-21 19:00:11'),
(17, 'Stagionale', 'Adulti', 'none', 1040.00, '2025-01-21 19:00:11'),
(18, 'Stagionale', 'Junior (fino a 16 anni)', 'none', 850.00, '2025-01-21 19:00:11'),
(19, 'Stagionale', 'Senior (oltre 65 anni)', 'none', 850.00, '2025-01-21 19:00:11'),
(20, 'Stagionale', 'Baby (fino a 8 anni)', 'none', 520.00, '2025-01-21 19:00:11'),
(21, 'Giornaliero', 'Adulti', 'none', 30.00, '2025-01-21 20:03:30'),
(22, 'Giornaliero', 'Junior', 'none', 25.00, '2025-01-21 20:03:30'),
(23, 'Giornaliero', 'Senior', 'none', 28.00, '2025-01-21 20:03:30'),
(24, 'Giornaliero', 'Baby', 'none', 18.00, '2025-01-21 20:03:30'),
(25, 'Stagionale', 'Adulti', 'none', 700.00, '2025-01-21 20:03:30'),
(26, 'Stagionale', 'Junior', 'none', 650.00, '2025-01-21 20:03:30'),
(27, 'Stagionale', 'Senior', 'none', 680.00, '2025-01-21 20:03:30'),
(28, 'Stagionale', 'Baby', 'none', 350.00, '2025-01-21 20:03:30'),
(29, 'Giornaliero', 'Adulti', 'fisi', 27.50, '2025-01-21 20:03:30'),
(30, 'Giornaliero', 'Junior', 'fisi', 23.00, '2025-01-21 20:03:30'),
(31, 'Giornaliero', 'Senior', 'fisi', 25.00, '2025-01-21 20:03:30'),
(32, 'Giornaliero', 'Baby', 'fisi', 15.50, '2025-01-21 20:03:30'),
(33, 'Stagionale', 'Adulti', 'fisi', 680.00, '2025-01-21 20:03:30'),
(34, 'Stagionale', 'Junior', 'fisi', 680.00, '2025-01-21 20:03:30'),
(35, 'Stagionale', 'Senior', 'fisi', 680.00, '2025-01-21 20:03:30'),
(36, 'Stagionale', 'Baby', 'fisi', 680.00, '2025-01-21 20:03:30'),
(37, 'Stagionale', 'Adulti', 'maestro', 450.00, '2025-01-21 20:03:30'),
(38, 'Stagionale', 'Junior', 'maestro', 450.00, '2025-01-21 20:03:30'),
(39, 'Stagionale', 'Senior', 'maestro', 450.00, '2025-01-21 20:03:30'),
(40, 'Stagionale', 'Baby', 'maestro', 450.00, '2025-01-21 20:03:30'),
(41, 'Giornaliero', 'Adulti', 'disabile', 38.50, '2025-01-21 20:03:30'),
(42, 'Giornaliero', 'Junior', 'disabile', 32.20, '2025-01-21 20:03:30'),
(43, 'Giornaliero', 'Senior', 'disabile', 35.00, '2025-01-21 20:03:30'),
(44, 'Giornaliero', 'Baby', 'disabile', 21.70, '2025-01-21 20:03:30'),
(45, 'Stagionale', 'Adulti', 'disabile', 728.00, '2025-01-21 20:03:30'),
(46, 'Stagionale', 'Junior', 'disabile', 595.00, '2025-01-21 20:03:30'),
(47, 'Stagionale', 'Senior', 'disabile', 595.00, '2025-01-21 20:03:30'),
(48, 'Stagionale', 'Baby', 'disabile', 364.00, '2025-01-21 20:03:30'),
(49, 'Accompagnatore', 'Any', 'disabile_accompagnatore', 0.00, '2025-01-21 20:03:30');

-- --------------------------------------------------------

--
-- Struttura della tabella `support_requests`
--

CREATE TABLE `support_requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `status` enum('in attesa','in lavorazione','risolto') DEFAULT 'in attesa',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `response` text DEFAULT NULL,
  `responded_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `description` varchar(255) NOT NULL,
  `points` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `surname` varchar(100) NOT NULL,
  `date_of_birth` date NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone_number` varchar(15) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `points` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `users`
--

INSERT INTO `users` (`id`, `name`, `surname`, `date_of_birth`, `username`, `email`, `phone_number`, `password`, `role`, `created_at`, `points`) VALUES
(1, 'Ludovico', 'Mariani', '2006-02-02', 'Ludo', 'mariani.ludovico@itispaleocapa.it', '3345567481', '$2y$10$TIO4HuPSN8HXKwYf9DQRh.UyBeOzaF2c2ybF6MWDC9uIOGFr1uyQ6', 'cliente', '2025-01-20 19:08:59', 0),
(4, 'admin', 'user', '2006-11-01', 'adminuser', 'riccardo.masserini@icloud.com', '3383991931', '$2y$10$CmJ2oxIJfs5zDCsD7H1BCevxonDqgzRbUAbua9TfoCvyR.qX.bs2q', 'admin', '2025-01-20 19:35:23', 12);

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `discounts`
--
ALTER TABLE `discounts`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `rewards`
--
ALTER TABLE `rewards`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `skipasses`
--
ALTER TABLE `skipasses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `skipass_price_id` (`skipass_price_id`);

--
-- Indici per le tabelle `skipass_prices`
--
ALTER TABLE `skipass_prices`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `support_requests`
--
ALTER TABLE `support_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indici per le tabelle `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indici per le tabelle `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `discounts`
--
ALTER TABLE `discounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT per la tabella `rewards`
--
ALTER TABLE `rewards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT per la tabella `skipasses`
--
ALTER TABLE `skipasses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT per la tabella `skipass_prices`
--
ALTER TABLE `skipass_prices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT per la tabella `support_requests`
--
ALTER TABLE `support_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT per la tabella `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT per la tabella `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `skipasses`
--
ALTER TABLE `skipasses`
  ADD CONSTRAINT `skipasses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `skipasses_ibfk_2` FOREIGN KEY (`skipass_price_id`) REFERENCES `skipass_prices` (`id`) ON DELETE CASCADE;

--
-- Limiti per la tabella `support_requests`
--
ALTER TABLE `support_requests`
  ADD CONSTRAINT `support_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Limiti per la tabella `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
