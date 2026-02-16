<?php
/* =============================================================================
   DOSYA: config.php
   GÜNCELLEME: Oturum yolu ve PDO hataları düzeltildi.
============================================================================= */

if (basename($_SERVER['PHP_SELF']) == 'config.php') { die('Erişim yasak.'); }

// 1. OTURUM AYARLARI
$session_lifetime = 60 * 60 * 24 * 30; // 30 Gün

// Oturum klasörünü otomatik oluştur ve izinleri kontrol et
$custom_session_path = __DIR__ . '/sessions';
if (!is_dir($custom_session_path)) {
    @mkdir($custom_session_path, 0755, true);
}

// Klasör yazılabilirse kullan, değilse varsayılanı kullan (Hata oluşmaması için)
if (is_writable($custom_session_path)) {
    session_save_path($custom_session_path);
}

ini_set('session.gc_maxlifetime', $session_lifetime);
ini_set('session.cookie_lifetime', $session_lifetime);

if (session_status() === PHP_SESSION_NONE) {
    if (PHP_VERSION_ID >= 70300) {
        session_set_cookie_params([
            'lifetime' => $session_lifetime, 'path' => '/',
            'secure' => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on'),
            'httponly' => true, 'samesite' => 'Lax'
        ]);
    } else {
        session_set_cookie_params($session_lifetime, '/');
    }
    session_start();
}

// 2. VERİTABANI BİLGİLERİ (Lütfen bilgilerin doğruluğunu kontrol edin)
define('DB_HOST', 'localhost');
define('DB_NAME', 'veritabani-adi');
define('DB_USER', 'veritabani-user');
define('DB_PASS', 'veritabani-password');

try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ, // Nesne (Object) modu
        PDO::ATTR_EMULATE_PREPARES   => true,          // Uyumluluk için true yapıldı
    ];
    $db = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    error_log($e->getMessage());
    die("Veritabanı bağlantısı kurulamadı.");
}

date_default_timezone_set('Europe/Istanbul');

