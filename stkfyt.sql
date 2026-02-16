-- phpMyAdmin SQL Dump
-- version 5.1.2
-- https://www.phpmyadmin.net/
--
-- Anamakine: localhost:3306
-- Üretim Zamanı: 16 Şub 2026, 14:08:10
-- Sunucu sürümü: 5.7.24
-- PHP Sürümü: 8.3.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `stkfyt`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `kategoriler`
--

CREATE TABLE `kategoriler` (
  `id` int(11) NOT NULL,
  `ad` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `kullanicilar`
--

CREATE TABLE `kullanicilar` (
  `id` int(11) NOT NULL,
  `kullanici_adi` varchar(50) NOT NULL,
  `sifre` varchar(255) NOT NULL,
  `rol` enum('admin','kullanici') NOT NULL DEFAULT 'kullanici'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Tablo döküm verisi `kullanicilar`
--

INSERT INTO `kullanicilar` (`id`, `kullanici_adi`, `sifre`, `rol`) VALUES
(1, 'admin', '$2y$10$ANxXIYH2oYe9dAJy070y7OJ68dm5VjlXB2/Qah57cIvnPlH3s1Fmy', 'admin'),
(5, 'satis', '$2y$10$ALq47ooqIak1eBgEX0U4Y.rj/zVUYbKNstAg8nzcObylHVEcg1d7.', 'kullanici'),
(6, 'test', '$2y$10$mMEOhPGIUTOtDTLo4q.roOu5aGfNX0SUEkmMTeXi5i/zFt7WLJFuW', 'kullanici');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `urunler`
--

CREATE TABLE `urunler` (
  `id` int(11) NOT NULL,
  `ad` varchar(255) NOT NULL,
  `alis_fiyati` decimal(10,2) NOT NULL,
  `satis_fiyati` decimal(10,2) NOT NULL,
  `birim` varchar(50) NOT NULL,
  `para_birimi` enum('TRY','USD','EUR') NOT NULL DEFAULT 'TRY',
  `kategori_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Tablo döküm verisi `urunler`
--

INSERT INTO `urunler` (`id`, `ad`, `alis_fiyati`, `satis_fiyati`, `birim`, `para_birimi`, `kategori_id`) VALUES
(135, 'test1', '10.00', '20.00', 'Adet', 'TRY', NULL),
(136, 'test2', '100.00', '150.00', 'kg', 'TRY', NULL),
(137, 'test3', '100.00', '150.00', 'm2', 'TRY', NULL),
(138, 'test4', '150.00', '200.00', 'm3', 'TRY', NULL),
(139, 'test5', '80.00', '150.00', 'm', 'TRY', NULL),
(140, 'test6', '5.00', '15.00', 'cm', 'TRY', NULL),
(141, 'test7', '1.00', '2.00', 'mm', 'TRY', NULL),
(142, 'test8', '50.00', '75.00', 'mtül', 'TRY', NULL);

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
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kullanici_adi` (`kullanici_adi`);

--
-- Tablo için indeksler `urunler`
--
ALTER TABLE `urunler`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kategori_id` (`kategori_id`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `kategoriler`
--
ALTER TABLE `kategoriler`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `kullanicilar`
--
ALTER TABLE `kullanicilar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Tablo için AUTO_INCREMENT değeri `urunler`
--
ALTER TABLE `urunler`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=143;

--
-- Dökümü yapılmış tablolar için kısıtlamalar
--

--
-- Tablo kısıtlamaları `urunler`
--
ALTER TABLE `urunler`
  ADD CONSTRAINT `urunler_ibfk_1` FOREIGN KEY (`kategori_id`) REFERENCES `kategoriler` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
