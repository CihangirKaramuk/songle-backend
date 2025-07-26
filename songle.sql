-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: 127.0.0.1
-- Üretim Zamanı: 26 Tem 2025, 14:12:14
-- Sunucu sürümü: 10.4.32-MariaDB
-- PHP Sürümü: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `songle`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `kategoriler`
--

CREATE TABLE `kategoriler` (
  `id` int(11) NOT NULL,
  `isim` varchar(100) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `kategoriler`
--

INSERT INTO `kategoriler` (`id`, `isim`, `parent_id`, `created_at`) VALUES
(1, 'türkçe', NULL, '2025-07-25 16:02:43'),
(2, 'rock', 1, '2025-07-25 16:03:12'),
(3, 'yabancı', NULL, '2025-07-25 16:04:57'),
(4, 'rock', 3, '2025-07-25 16:05:09'),
(5, 'pop', 1, '2025-07-25 16:03:12'),
(6, 'hip hop', 1, '2025-07-25 16:03:12'),
(7, 'pop', 3, '2025-07-25 16:05:09'),
(8, 'hip hop', 3, '2025-07-25 16:05:09'),
(9, 'dizi', NULL, '2025-07-25 16:04:57'),
(10, 'film', NULL, '2025-07-25 16:04:57'),
(11, 'türkçe', 9, '2025-07-25 16:05:09'),
(12, 'yabancı', 9, '2025-07-25 16:05:09'),
(13, 'türkçe', 10, '2025-07-25 16:05:09'),
(14, 'yabancı', 10, '2025-07-25 16:05:09');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `kullanicilar`
--

CREATE TABLE `kullanicilar` (
  `id` int(11) NOT NULL,
  `kullanici_adi` varchar(100) NOT NULL,
  `sifre` varchar(255) NOT NULL,
  `yetki` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `kullanicilar`
--

INSERT INTO `kullanicilar` (`id`, `kullanici_adi`, `sifre`, `yetki`, `created_at`) VALUES
(1, 'admin', '21232f297a57a5a743894a0e4a801fc3', 1, '2025-07-25 16:27:54');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `sarkilar`
--

CREATE TABLE `sarkilar` (
  `id` int(11) NOT NULL,
  `kategori` varchar(100) NOT NULL,
  `cevap` varchar(255) NOT NULL,
  `sarki` text NOT NULL,
  `dosya` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `kategoriler`
--
ALTER TABLE `kategoriler`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `kullanicilar`
--
ALTER TABLE `kullanicilar`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `sarkilar`
--
ALTER TABLE `sarkilar`
  ADD PRIMARY KEY (`id`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `kategoriler`
--
ALTER TABLE `kategoriler`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Tablo için AUTO_INCREMENT değeri `kullanicilar`
--
ALTER TABLE `kullanicilar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Tablo için AUTO_INCREMENT değeri `sarkilar`
--
ALTER TABLE `sarkilar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
