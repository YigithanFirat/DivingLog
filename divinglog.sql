-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: 127.0.0.1
-- Üretim Zamanı: 07 Tem 2025, 23:52:28
-- Sunucu sürümü: 10.4.32-MariaDB
-- PHP Sürümü: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `divinglog`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `certificate`
--

CREATE TABLE `certificate` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `tc` varchar(11) NOT NULL,
  `certificate_name` varchar(100) NOT NULL,
  `issuing_organization` varchar(100) DEFAULT NULL,
  `issue_date` date DEFAULT NULL,
  `expiration_date` date DEFAULT NULL,
  `certificate_level` varchar(50) DEFAULT NULL,
  `certificate_number` varchar(50) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `certificate`
--

INSERT INTO `certificate` (`id`, `full_name`, `tc`, `certificate_name`, `issuing_organization`, `issue_date`, `expiration_date`, `certificate_level`, `certificate_number`, `notes`, `created_at`) VALUES
(1, 'Yiğithan Fırat', '32641454324', 'Dalış Sertifikası', 'İskenderun Teknik Üniversitesi', '2025-07-06', '2026-07-06', 'Uzman', '1', 'Sertifika verildi.', '2025-07-06 07:44:32'),
(2, 'Atakan Fırat', '10110110111', 'Dalış Sertifikası', 'İSTE', '2026-06-07', '2027-06-07', 'Master', '2', 'Sertifika verildi.', '2025-07-06 15:41:46');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `diving_places`
--

CREATE TABLE `diving_places` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `latitude` double NOT NULL,
  `longitude` double NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `diving_places`
--

INSERT INTO `diving_places` (`id`, `name`, `latitude`, `longitude`, `created_at`) VALUES
(1, 'İzmir Körfezi', 38.4192, 27.1287, '2025-07-06 15:19:06'),
(2, 'Gökova Körfezi', 36.95, 28, '2025-07-06 15:19:06'),
(3, 'Edremit Körfezi', 39.55, 26.8, '2025-07-06 15:19:06'),
(4, 'Antalya Körfezi', 36.85, 30.7, '2025-07-06 15:19:06'),
(5, 'Saros Körfezi', 40.4, 26.8, '2025-07-06 15:19:06'),
(6, 'Mersin Körfezi', 36.8, 34.6, '2025-07-06 15:19:06'),
(7, 'İskenderun Körfezi', 36.6, 36.2, '2025-07-06 15:19:06'),
(9, 'İSTE', 2, 2, '2025-07-06 15:23:40');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `diving_plans`
--

CREATE TABLE `diving_plans` (
  `id` int(11) NOT NULL,
  `tcno` varchar(11) NOT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `bottom_time` varchar(10) DEFAULT NULL,
  `avg_depth` varchar(10) DEFAULT NULL,
  `max_depth` varchar(10) DEFAULT NULL,
  `temperature` varchar(10) DEFAULT NULL,
  `minutes` varchar(10) DEFAULT NULL,
  `diving_location` varchar(255) DEFAULT NULL,
  `water_type` varchar(50) DEFAULT NULL,
  `depth_feet` varchar(10) DEFAULT NULL,
  `depth_meter` varchar(10) DEFAULT NULL,
  `respiration` varchar(50) DEFAULT NULL,
  `clothing` varchar(50) DEFAULT NULL,
  `diving_purpose` varchar(255) DEFAULT NULL,
  `tools` varchar(255) DEFAULT NULL,
  `tools_devices` varchar(100) DEFAULT NULL,
  `supervisor` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `diving_plans`
--

INSERT INTO `diving_plans` (`id`, `tcno`, `start_time`, `end_time`, `bottom_time`, `avg_depth`, `max_depth`, `temperature`, `minutes`, `diving_location`, `water_type`, `depth_feet`, `depth_meter`, `respiration`, `clothing`, `diving_purpose`, `tools`, `tools_devices`, `supervisor`, `created_at`) VALUES
(1, '32641454324', NULL, NULL, NULL, NULL, NULL, NULL, '50', 'Antalya Körfezi', 'Tuzlu Su', NULL, '100', 'Hava', 'Islak', 'Eğitim', 'BOŞ', 'MK-17', 'Necdet Uyğur', '2025-07-06 07:36:24'),
(2, '10110110111', NULL, NULL, NULL, NULL, NULL, NULL, '60', 'Antalya Körfezi', 'Tuzlu Su', NULL, '200', 'Hava', 'Islak', 'Eğitim', 'BOŞ', 'MK-17', 'Necdet Uyğur', '2025-07-06 15:56:15'),
(3, '32641454324', NULL, NULL, NULL, NULL, NULL, NULL, '60', 'Mersin Körfezi', 'Tuzlu Su', NULL, '180', 'Hava', 'İslak', 'Eğitim', 'BOŞ', 'MK-17', 'Necdet Uyğur', '2025-07-06 16:29:47'),
(4, '32641454324', NULL, NULL, NULL, NULL, NULL, NULL, '50', 'İzmir Körfezi', 'Tuzlu Su', NULL, '250', 'Hava', 'İslak', 'Eğitim', 'Kaynak', 'Scuba', 'Necdet Uyğur', '2025-07-06 16:31:52'),
(5, '32641454324', '23:09:00', '00:00:00', '51', '100', '200', '32', '60', 'İskenderun Körfezi', 'Tuzlu Su', NULL, '100', 'Hava', 'İslak', 'Eğitim', 'Şnorkel', 'MK-17', 'Necdet Uyğur', '2025-07-07 20:10:48');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `health_inspections`
--

CREATE TABLE `health_inspections` (
  `id` int(11) NOT NULL,
  `muayene_tarihi` date NOT NULL,
  `onaylayan` varchar(255) NOT NULL,
  `onaylanan` varchar(255) NOT NULL,
  `tcno` varchar(11) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `health_inspections`
--

INSERT INTO `health_inspections` (`id`, `muayene_tarihi`, `onaylayan`, `onaylanan`, `tcno`, `created_at`) VALUES
(2, '2025-07-06', 'Dr. Atakan FIRAT', 'Yiğithan Fırat', '32641454324', '2025-07-06 19:26:37'),
(3, '2025-07-06', 'Dr. Atakan FIRAT', 'Önder Fırat', '10110110111', '2025-07-06 19:27:07'),
(4, '0225-07-06', 'Dr. Atakan FIRAT', 'Yiğithan Fırat', '', '2025-07-06 21:08:00'),
(5, '2025-07-06', 'Dr. Atakan FIRAT', 'Önder Fırat', '', '2025-07-06 21:11:01'),
(6, '2025-07-06', 'Dr. Erdi Baş', 'Atakan FIRAT', '10110110110', '2025-07-06 21:13:22');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `ad` varchar(100) NOT NULL,
  `soyad` varchar(100) NOT NULL,
  `dogum_tarihi` date NOT NULL,
  `milliyet` varchar(50) NOT NULL,
  `adres` text NOT NULL,
  `kaza_haber_kişi_ad_soyad` varchar(100) DEFAULT NULL,
  `telefon` varchar(15) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `sifre` varchar(255) NOT NULL,
  `tcno` varchar(11) DEFAULT NULL,
  `login` int(2) NOT NULL DEFAULT 0,
  `email` varchar(255) NOT NULL,
  `reset_token` varchar(64) DEFAULT NULL,
  `token_expiry` datetime DEFAULT NULL,
  `admin` int(2) NOT NULL DEFAULT 0,
  `fotograf` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `users`
--

INSERT INTO `users` (`id`, `ad`, `soyad`, `dogum_tarihi`, `milliyet`, `adres`, `kaza_haber_kişi_ad_soyad`, `telefon`, `created_at`, `sifre`, `tcno`, `login`, `email`, `reset_token`, `token_expiry`, `admin`, `fotograf`) VALUES
(1, 'Yiğithan', 'Fırat', '2002-01-11', 'TÜRK', 'Agah Ateş Mah. Muhsin Alataş Cad. No:36/1 HEREKE/KÖRFEZ/KOCAELİ', 'Önder FIRAT', '+905355255187', '2025-07-06 07:26:58', '$2y$10$nuN7abTjD68QucxhTCch2O998MqPyz7F8YtarPVOQZkpX56Y.I9j6', '32641454324', 1, 'yigithanfirat@gmail.com', NULL, NULL, 1, 'YOK'),
(2, 'Atakan', 'FIRAT', '1991-06-01', 'TÜRK', 'Agah Ateş mah. Muhsin Alataş Cad.', 'Önder Fırat', '+905061364385', '2025-07-06 15:10:51', '$2y$10$qxTtty/HkQz2ghaomp7RA.sr4edUSYACDpTP9s1iOXhJ91VV.IP0q', '10110110110', 0, 'atakanfirat@windowslive.com', NULL, NULL, 0, '../uploads/1751814651_mezuniyet1.jpg'),
(3, 'Önder', 'Fırat', '1967-11-29', 'TÜRK', 'Agah Ateş mah. Muhsin Alataş Cad.', 'Atakan FIRAT', '+905539249998', '2025-07-06 15:48:21', '$2y$10$2JTFa4Wgo.YvXLOfa1PER.Tfje6Ez0I74ccLkSkwPisK2iS.v1yeC', '10110110111', 0, 'onderfirat@gmail.com', NULL, NULL, 0, '../uploads/1751816901_cat.jpg');

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `certificate`
--
ALTER TABLE `certificate`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_certificate_number` (`certificate_number`),
  ADD KEY `idx_tc` (`tc`);

--
-- Tablo için indeksler `diving_places`
--
ALTER TABLE `diving_places`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `diving_plans`
--
ALTER TABLE `diving_plans`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `health_inspections`
--
ALTER TABLE `health_inspections`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `certificate`
--
ALTER TABLE `certificate`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Tablo için AUTO_INCREMENT değeri `diving_places`
--
ALTER TABLE `diving_places`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Tablo için AUTO_INCREMENT değeri `diving_plans`
--
ALTER TABLE `diving_plans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Tablo için AUTO_INCREMENT değeri `health_inspections`
--
ALTER TABLE `health_inspections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Tablo için AUTO_INCREMENT değeri `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
