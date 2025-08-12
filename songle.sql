-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: 127.0.0.1
-- Üretim Zamanı: 12 Ağu 2025, 04:11:00
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
-- Tablo için tablo yapısı `ayarlar`
--

CREATE TABLE `ayarlar` (
  `id` int(11) NOT NULL,
  `kullanici_id` int(11) DEFAULT NULL,
  `tema` varchar(20) DEFAULT 'dark',
  `sayfa_boyutu` int(11) DEFAULT 20,
  `bildirim_sesi` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `ayarlar`
--

INSERT INTO `ayarlar` (`id`, `kullanici_id`, `tema`, `sayfa_boyutu`, `bildirim_sesi`, `created_at`, `updated_at`) VALUES
(1, 1, 'dark', 10, 0, '2025-08-05 11:48:38', '2025-08-09 19:12:21');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `islem_kayitlari`
--

CREATE TABLE `islem_kayitlari` (
  `id` int(11) NOT NULL,
  `islem_tipi` enum('sarki_ekleme','sarki_silme','sarki_degistirme','kategori_ekleme','kategori_silme','kategori_degistirme','yetkili_ekleme','yetkili_silme','yetkili_guncelleme','sifre_sifirlama','rol_degistirme') NOT NULL,
  `kaynak` enum('deezer','mp3','manuel','admin_panel') NOT NULL,
  `kullanici_id` int(11) NOT NULL,
  `kullanici_adi` varchar(100) NOT NULL,
  `tarih` timestamp NOT NULL DEFAULT current_timestamp(),
  `detay` text NOT NULL,
  `sarki_adi` varchar(255) DEFAULT NULL,
  `sanatci` varchar(255) DEFAULT NULL,
  `kategori` varchar(255) DEFAULT NULL,
  `kategori_adi` varchar(255) DEFAULT NULL,
  `eski_deger` text DEFAULT NULL,
  `yeni_deger` text DEFAULT NULL,
  `hedef_kullanici_id` int(11) DEFAULT NULL,
  `hedef_kullanici_adi` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `islem_kayitlari`
--

INSERT INTO `islem_kayitlari` (`id`, `islem_tipi`, `kaynak`, `kullanici_id`, `kullanici_adi`, `tarih`, `detay`, `sarki_adi`, `sanatci`, `kategori`, `kategori_adi`, `eski_deger`, `yeni_deger`) VALUES
(58, 'sarki_degistirme', 'manuel', 1, 'admin', '2025-08-08 14:05:08', 'Kardan Adam şarkısı güncellendi', 'Kardan Adam', ' Kardan Adam', 'türkçe-pop,türkçe-diğer', NULL, 'Kategori: türkçe-pop,türkçe-diğer, Şarkı Adı: Kardan Adam', 'Kategori: türkçe-pop,türkçe-diğer, Şarkı Adı: Kardan Adam'),
(59, 'kategori_ekleme', 'manuel', 1, 'admin', '2025-08-08 14:05:22', '\'ghkıh\' kategorisi eklendi', NULL, NULL, NULL, 'ghkıh', NULL, NULL),
(60, 'kategori_silme', 'manuel', 1, 'admin', '2025-08-08 14:05:28', '\'ghkıh\' kategorisi silindi', NULL, NULL, NULL, 'ghkıh', NULL, NULL),
(61, 'kategori_ekleme', 'manuel', 1, 'admin', '2025-08-09 12:49:19', '\'asdasds\' kategorisi eklendi', NULL, NULL, NULL, 'asdasds', NULL, NULL),
(62, 'kategori_degistirme', 'manuel', 1, 'admin', '2025-08-09 12:49:26', '\'asdasds\' kategorisi güncellendi', NULL, NULL, NULL, 'asdasds', 'İsim: asdasds', 'İsim: dur'),
(63, 'sarki_degistirme', 'manuel', 1, 'admin', '2025-08-09 12:49:56', 'Kardan Adam şarkısı güncellendi', 'Kardan Adam', ' Kardan Adam', 'türkçe-pop,türkçe-diğer', NULL, 'Kategori: türkçe-pop,türkçe-diğer, Şarkı Adı: Kardan Adam', 'Kategori: türkçe-pop,türkçe-diğer, Şarkı Adı: Kardan Adam o'),
(64, 'kategori_silme', 'manuel', 1, 'admin', '2025-08-09 13:36:42', '\'dur\' kategorisi silindi', NULL, NULL, NULL, 'dur', NULL, NULL),
(65, 'sarki_degistirme', 'manuel', 1, 'admin', '2025-08-09 13:37:08', 'Kardan Adam o şarkısı güncellendi', 'Kardan Adam o', ' Kardan Adam', 'türkçe-pop,türkçe-diğer', NULL, 'Kategori: türkçe-pop,türkçe-diğer, Şarkı Adı: Kardan Adam o', 'Kategori: türkçe-pop,türkçe-diğer, Şarkı Adı: Kardan Adam'),
(66, 'sarki_ekleme', 'manuel', 1, 'admin', '2025-08-09 13:53:36', 'Holocaust -  Holocaust şarkısı eklendi', 'Holocaust', ' Holocaust', 'türkçe-hiphop,türkçe-diğer', NULL, NULL, NULL),
(67, 'sarki_degistirme', 'manuel', 13, 'asd', '2025-08-09 18:24:24', 'Kardan Adam şarkısı güncellendi', 'Kardan Adam', ' Kardan Adam', 'türkçe-pop,türkçe-diğer', NULL, 'Kategori: türkçe-pop,türkçe-diğer, Şarkı Adı: Kardan Adam', 'Kategori: türkçe-pop,türkçe-diğer, Şarkı Adı: Kardan Adam'),
(68, 'sarki_degistirme', 'manuel', 1, 'admin', '2025-08-09 19:10:39', 'Kardan Adam şarkısı güncellendi', 'Kardan Adam', ' Kardan Adam', 'türkçe-pop,türkçe-diğer', NULL, 'Kategori: türkçe-pop,türkçe-diğer, Şarkı Adı: Kardan Adam', 'Kategori: türkçe-pop,türkçe-diğer, Şarkı Adı: Kardan Adam'),
(69, 'kategori_ekleme', 'manuel', 1, 'admin', '2025-08-09 19:10:58', '\'asjhdaksujd\' kategorisi eklendi', NULL, NULL, NULL, 'asjhdaksujd', NULL, NULL),
(70, 'kategori_ekleme', 'manuel', 1, 'admin', '2025-08-09 19:11:12', '\'asdhjas\' kategorisi eklendi', NULL, NULL, NULL, 'asdhjas', NULL, NULL),
(71, 'kategori_silme', 'manuel', 1, 'admin', '2025-08-09 19:11:34', '\'asdhjas\' kategorisi silindi', NULL, NULL, NULL, 'asdhjas', NULL, NULL),
(72, 'kategori_silme', 'manuel', 1, 'admin', '2025-08-09 19:11:38', '\'asjhdaksujd\' kategorisi silindi', NULL, NULL, NULL, 'asjhdaksujd', NULL, NULL),
(73, 'sarki_degistirme', 'manuel', 14, 'doğa', '2025-08-09 19:14:34', 'Yankı şarkısı güncellendi', 'Yankı', ' Yankı', 'türkçe-pop,türkçe-diğer', NULL, 'Kategori: türkçe-pop,türkçe-diğer, Şarkı Adı: Yankı', 'Kategori: türkçe-pop,türkçe-diğer, Şarkı Adı: Yankı'),
(74, 'kategori_ekleme', 'manuel', 1, 'admin', '2025-08-10 13:19:07', '\'asd\' kategorisi eklendi', NULL, NULL, NULL, 'asd', NULL, NULL),
(75, 'kategori_silme', 'manuel', 1, 'admin', '2025-08-10 13:19:09', '\'asd\' kategorisi silindi', NULL, NULL, NULL, 'asd', NULL, NULL),
(76, 'kategori_ekleme', 'manuel', 1, 'admin', '2025-08-10 13:22:14', '\'asd\' kategorisi eklendi', NULL, NULL, NULL, 'asd', NULL, NULL),
(77, 'kategori_silme', 'manuel', 1, 'admin', '2025-08-10 13:22:17', '\'asd\' kategorisi silindi', NULL, NULL, NULL, 'asd', NULL, NULL),
(79, 'kategori_ekleme', 'manuel', 21, 'asd', '2025-08-12 00:11:01', '\'asdasdasd\' kategorisi eklendi', NULL, NULL, NULL, 'asdasdasd', NULL, NULL),
(80, 'kategori_ekleme', 'manuel', 21, 'asd', '2025-08-12 00:11:09', '\'asdfasdf\' kategorisi eklendi', NULL, NULL, NULL, 'asdfasdf', NULL, NULL),
(83, 'sarki_silme', 'manuel', 1, 'admin', '2025-08-12 00:17:07', 'LKHAYAL şarkısı silindi', 'LKHAYAL', ' LKHAYAL', 'türkçe-diğer', NULL, NULL, NULL);

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
(14, 'yabancı', 10, '2025-07-25 16:05:09'),
(28, 'diğer', 1, '2025-08-04 13:14:03'),
(29, 'diğer', 3, '2025-08-04 13:14:12');

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
(1, 'admin', '$2y$10$4cwDDc3PakkG.jUGpPHd.eySGxAykl2G73iPFnLltQeuHkw.tpGH.', 1, '2025-07-25 16:27:54');

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
  `kapak` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `sarkilar`
--

INSERT INTO `sarkilar` (`id`, `kategori`, `cevap`, `sarki`, `dosya`, `kapak`, `created_at`) VALUES
(210, 'türkçe-pop,türkçe-diğer', 'Kardan Adam', ' Kardan Adam', 'songs/Glen-Kardan_Adam.mp3', 'kapaklar/song_66127911.jpg', '2025-07-31 11:26:44'),
(211, 'türkçe-pop,türkçe-diğer', 'Yankı', ' Yankı', 'songs/Simge-Yank.mp3', 'kapaklar/song_118116196.jpg', '2025-07-31 11:26:53'),
(213, 'türkçe-pop,türkçe-diğer', 'Yaralı', ' Yaralı', 'songs/Beng-Yaral.mp3', 'kapaklar/song_66127891.jpg', '2025-07-31 11:27:45'),
(217, 'türkçe-rock,türkçe-diğer', 'Issızlığın Ortasında', ' Issızlığın Ortasında', 'songs/Hayko_Cepkin-Isszln_Ortasnda.mp3', 'kapaklar/song_116845862.jpg', '2025-07-31 11:30:17'),
(218, 'türkçe-rock,türkçe-diğer', 'Aman Aman', ' Aman Aman', 'songs/Duman-Aman_Aman.mp3', 'kapaklar/song_531223621.jpg', '2025-07-31 11:30:35'),
(219, 'türkçe-rock,türkçe-diğer', 'Bak', ' Bak', 'songs/Pilli_Bebek-Bak.mp3', 'kapaklar/song_80734162.jpg', '2025-07-31 11:30:44'),
(222, 'türkçe-rock,türkçe-diğer', 'Çakıl Taşları', ' Çakıl Taşları', 'songs/ebnem_Ferah-akl_Talar.mp3', 'kapaklar/song_68053892.jpg', '2025-07-31 11:31:29'),
(224, 'türkçe-rock,türkçe-diğer', 'Serseri', ' Serseri', 'songs/Teoman-Serseri.mp3', 'kapaklar/song_96128642.jpg', '2025-07-31 11:32:19'),
(226, 'türkçe-rock,türkçe-diğer', 'Cambaz', ' Cambaz', 'songs/Mor_ve_tesi-Cambaz.mp3', 'kapaklar/song_77595995.jpg', '2025-07-31 11:34:10'),
(227, 'türkçe-hiphop,türkçe-diğer', 'Med Cezir', ' Med Cezir', 'songs/Ceza-Med_Cezir.mp3', 'kapaklar/song_1914175957.jpg', '2025-07-31 11:34:46'),
(228, 'türkçe-hiphop,türkçe-diğer', 'Ateşten Gömlek', ' Ateşten Gömlek', 'songs/Sagopa_Kajmer-Ateten_Gmlek.mp3', 'kapaklar/song_96576696.jpg', '2025-07-31 11:34:56'),
(230, 'türkçe-hiphop,türkçe-diğer', 'Koptu Kayış', ' Koptu Kayış', 'songs/Sansar_Salvo-Koptu_Kay.mp3', 'kapaklar/song_2234712957.jpg', '2025-07-31 11:35:37'),
(232, 'türkçe-hiphop,türkçe-diğer', 'Böyle İyi', ' Böyle İyi', 'songs/No.1-Byle_yi.mp3', 'kapaklar/song_1429951212.jpg', '2025-07-31 11:36:21'),
(233, 'türkçe-hiphop,türkçe-diğer', 'Çıktık Yine Yollara', ' Çıktık Yine Yollara', 'songs/Norm_Ender-ktk_Yine_Yollara.mp3', 'kapaklar/song_601517332.jpg', '2025-07-31 11:36:38'),
(234, 'türkçe-hiphop,türkçe-diğer', 'Sağı Solu Kes', ' Sağı Solu Kes', 'songs/Gazapizm-Sa_Solu_Kes.mp3', 'kapaklar/song_871055242.jpg', '2025-07-31 11:36:51'),
(235, 'türkçe-hiphop,türkçe-diğer', 'HARMAN', ' HARMAN', 'songs/Bege-HARMAN.mp3', 'kapaklar/song_2049514977.jpg', '2025-07-31 11:37:04'),
(236, 'türkçe-hiphop,türkçe-diğer', 'Günleri Geride Bırak', ' Günleri Geride Bırak', 'songs/aner-Gnleri_Geride_Brak.mp3', 'kapaklar/song_2627880632.jpg', '2025-07-31 11:37:23'),
(237, 'türkçe-pop,türkçe-diğer', 'Poşet', ' Poşet', 'songs/Serdar_Orta-Poet.mp3', 'kapaklar/song_66133622.jpg', '2025-08-01 14:59:23'),
(238, 'türkçe-pop,türkçe-diğer', 'Dudu', ' Dudu', 'songs/Tarkan-Dudu.mp3', 'kapaklar/song_113218880.jpg', '2025-08-03 13:13:29'),
(239, 'türkçe-pop,türkçe-diğer', 'Bakıcaz Artık', ' Bakıcaz Artık', 'songs/Hande_Yener-Bakcaz_Artk.mp3', 'kapaklar/song_3289995651.jpg', '2025-08-03 13:13:37'),
(261, 'türkçe-hiphop,türkçe-diğer', 'Neden', ' Neden', 'songs/Ceg-Neden.mp3', 'kapaklar/song_1982108137.jpg', '2025-08-05 15:50:53'),
(262, 'türkçe-pop,türkçe-diğer', 'Ben Bazen', ' Ben Bazen', 'songs/Simge-Ben_Bazen.mp3', 'kapaklar/song_502960492.jpg', '2025-08-05 15:51:26'),
(263, 'türkçe-pop,türkçe-diğer', 'Karabiberim', ' Karabiberim', 'songs/Serdar_Orta-Karabiberim.mp3', 'kapaklar/song_66157441.jpg', '2025-08-05 15:53:20'),
(269, 'türkçe-pop,türkçe-diğer', 'Saz mı, Caz mı', ' Saz mı, Caz mı', 'songs/Glen-Saz_m_Caz_m.mp3', 'kapaklar/song_66195686.jpg', '2025-08-05 16:26:14'),
(270, 'türkçe-pop,türkçe-diğer', 'Sana Çıkıyor Yollar', ' Sana Çıkıyor Yollar', 'songs/Derya_Ulu-Sana_kyor_Yollar.mp3', 'kapaklar/song_1568184712.jpg', '2025-08-05 16:50:46'),
(271, 'türkçe-rock,türkçe-diğer', 'Alışırım Gözlerimi Kapamaya', ' Alışırım Gözlerimi Kapamaya', 'songs/maNga-Alrm_Gzlerimi_Kapamaya.mp3', 'kapaklar/song_4765148.jpg', '2025-08-05 18:22:12'),
(272, 'türkçe-diğer', 'Gülümse', ' Gülümse', 'songs/Sezen_Aksu-Glmse.mp3', 'kapaklar/song_63969081.jpg', '2025-08-05 18:24:40'),
(273, 'türkçe-pop,türkçe-diğer', 'Bazen', ' Bazen', 'songs/MF-Bazen.mp3', 'kapaklar/song_1041911402.jpg', '2025-08-05 18:26:16'),
(277, 'türkçe-rock,türkçe-diğer', 'Falan Filan', ' Falan Filan', 'songs/Redd-Falan_Filan.mp3', 'kapaklar/song_68055262.jpg', '2025-08-08 12:47:15'),
(278, 'türkçe-pop,türkçe-diğer', 'Kırmızı', ' Kırmızı', 'songs/Hande_Yener-Krmz.mp3', 'kapaklar/song_79246919.jpg', '2025-08-08 13:03:14'),
(279, 'türkçe-hiphop,türkçe-diğer', 'Kaçak Göçek', ' Kaçak Göçek', 'songs/Sefo-Kaak_Gek.mp3', 'kapaklar/song_2975705701.jpg', '2025-08-08 13:04:31'),
(281, 'türkçe-hiphop,türkçe-diğer', 'Holocaust', ' Holocaust', 'songs/Ceza-Holocaust.mp3', 'kapaklar/song_98910182.jpg', '2025-08-09 13:53:35');

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `ayarlar`
--
ALTER TABLE `ayarlar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_ayarlar_kullanici` (`kullanici_id`);

--
-- Tablo için indeksler `islem_kayitlari`
--
ALTER TABLE `islem_kayitlari`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_islem_tipi` (`islem_tipi`),
  ADD KEY `idx_kaynak` (`kaynak`),
  ADD KEY `idx_tarih` (`tarih`),
  ADD KEY `idx_kullanici` (`kullanici_id`);

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
-- Tablo için AUTO_INCREMENT değeri `ayarlar`
--
ALTER TABLE `ayarlar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Tablo için AUTO_INCREMENT değeri `islem_kayitlari`
--
ALTER TABLE `islem_kayitlari`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;

--
-- Tablo için AUTO_INCREMENT değeri `kategoriler`
--
ALTER TABLE `kategoriler`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- Tablo için AUTO_INCREMENT değeri `kullanicilar`
--
ALTER TABLE `kullanicilar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Tablo için AUTO_INCREMENT değeri `sarkilar`
--
ALTER TABLE `sarkilar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=282;

--
-- Dökümü yapılmış tablolar için kısıtlamalar
--

--
-- Tablo kısıtlamaları `ayarlar`
--
ALTER TABLE `ayarlar`
  ADD CONSTRAINT `fk_ayarlar_kullanici` FOREIGN KEY (`kullanici_id`) REFERENCES `kullanicilar` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
