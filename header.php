<?php
/* =============================================================================
   DOSYA: header.php
   AÇIKLAMA: Tüm sayfaların ortak başlık ve navigasyon yapısı.
   GÜNCELLEMELER: Oturum süresinin "Çıkış Yap" diyene kadar kalıcı olması için
                  çerez ve sunucu ayarları güçlendirildi. (Örn: 1 Yıl)
============================================================================= */

// =========================================================================
// KALICI OTURUM AYARLARI (Giriş Yapınca Çıkış Yapana Kadar Kalması İçin)
// =========================================================================
$session_lifetime = 60 * 60 * 24 * 365; // 1 Yıl (Süreyi çok uzun tutarak "kalıcı" hale getiriyoruz)

// 1. Çerez Ayarları: Tarayıcı kapansa dahi oturumun silinmemesini sağlar.
if (PHP_VERSION_ID >= 70300) {
    session_set_cookie_params([
        'lifetime' => $session_lifetime,
        'path' => '/',
        'secure' => false, // Eğer siteniz HTTPS ise burayı true yapın
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
} else {
    session_set_cookie_params($session_lifetime, '/');
}

// 2. Sunucu Ayarları: Oturum dosyalarının sunucudan vaktinden önce silinmesini önler.
ini_set('session.gc_maxlifetime', $session_lifetime);
ini_set('session.cookie_lifetime', $session_lifetime);

// Not: config.php içinde session_start() çağrılmadan önce bu ayarların yapılması kritiktir.
include_once 'config.php';

// Oturum kontrolü
$isLoggedIn = isset($_SESSION['user_id']);
$isAdmin = isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin';

$current_page_file = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Hızlı Fiyat Sistemi'; ?></title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<div class="container">
    <header class="main-header">
        <h1>
            <a href="index.php" style="text-decoration:none; color:inherit;" title="Ana Sayfa">
                <i class="fas fa-home"></i>
            </a>
        </h1>

        <?php if ($isLoggedIn && ($page_search_handler ?? false)): ?>
            <div class="header-search">
                <form id="filter-form"
                      action="<?php
                          if ($page_search_handler === 'admin') {
                              echo $current_page_file;
                          } else {
                              echo '';
                          }
                      ?>"
                      method="GET"
                      <?php echo ($page_search_handler === 'index') ? 'onsubmit="return false;"' : ''; ?>>

                    <?php if ($page_search_handler === 'admin' && $current_page_file === 'admin.php'): ?>
                        <input type="hidden" name="action" value="list">
                    <?php endif; ?>

                    <input type="text"
                           id="search-input"
                           name="search"
                           placeholder="Ürün ara..."
                           class="header-search-input"
                           value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                </form>
            </div>
        <?php endif; ?>

        <nav>
            <?php if ($isLoggedIn): ?>
                <?php if ($isAdmin): ?>
                    <a href="admin.php" class="button" title="Yönetim Paneli">
                        <i class="fas fa-tools"></i>
                    </a>
                <?php endif; ?>
                <a href="logout.php" class="button button-secondary" title="Çıkış Yap">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            <?php else: ?>
                <a href="login.php" class="button" title="Giriş Yap">
                    <i class="fas fa-sign-in-alt"></i>
                </a>
            <?php endif; ?>
        </nav>
    </header>
