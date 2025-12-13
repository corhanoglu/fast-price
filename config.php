<?php
/* =============================================================================
   DOSYA: config.php
   AÇIKLAMA: Veritabanı bağlantısı ve genel ayarlar bu dosyada yapılır.
============================================================================= */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Veritabanı bağlantı bilgileri
define('DB_HOST', 'localhost');
define('DB_NAME', 'db'); // Veritabanı adınız
define('DB_USER', 'root');      // Veritabanı kullanıcı adınız
define('DB_PASS', 'root');          // Veritabanı şifreniz

// Saat dilimini ayarla
date_default_timezone_set('Europe/Istanbul');

// Veritabanı bağlantısını PDO ile kur
try {
    $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
} catch (PDOException $e) {
    die("Veritabanı bağlantı hatası: " . $e->getMessage());
}

// Para birimi sembolleri için yardımcı fonksiyon (GÜNCELLENDİ)
function getCurrencySymbol($currency) {
    switch ($currency) {
        case 'TL': return '₺'; // Türk Lirası
        case 'USD': return '$';
        case 'EUR': return '€';
        default: return '';
    }
}

// İskontolu fiyat hesaplama fonksiyonu
if (!function_exists('calculateDiscountedPrice')) {
    function calculateDiscountedPrice($price, $discountPercentage) {
        $discountPercentage = floatval($discountPercentage);
        if ($discountPercentage > 0) {
            return $price * (1 - $discountPercentage / 100);
        }
        return $price;
    }
}
?>
