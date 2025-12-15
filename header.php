<?php
/* =============================================================================
   DOSYA: header.php
   AÇIKLAMA: Tüm sayfaların ortak başlık ve navigasyon yapısı.
   GÜNCELLEMELER: Dinamik arama çubuğu (header-search) eklendi.
============================================================================= */
include_once 'config.php';

// Oturum kontrolü
$isLoggedIn = isset($_SESSION['user_id']);
$isAdmin = isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin';

// $page_search_handler: 'admin' veya 'index' olarak dahil eden sayfalar tarafından set edilir.
// $_GET['search'] değeri, arama çubuğunun içerideki değeri koruması için kullanılır.
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Hızlı Fiyat Sistemi'; ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <header class="main-header">
        <h1><a href="index.php" style="text-decoration:none; color:inherit;">Hızlı Fiyat</a></h1>

        <?php if ($isLoggedIn && ($page_search_handler ?? false)): ?>
            <div class="header-search">
                <form id="filter-form"
                      action="<?php echo ($page_search_handler === 'admin') ? 'admin.php' : ''; ?>"
                      method="GET"
                      <?php echo ($page_search_handler === 'index') ? 'onsubmit="return false;"' : ''; ?>>

                    <?php if ($page_search_handler === 'admin'): ?>
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
                    <a href="admin.php" class="button">Yönetim Paneli</a>
                <?php endif; ?>
                <a href="logout.php" class="button button-secondary">Çıkış Yap</a>
            <?php else: ?>
                <a href="login.php" class="button">Giriş Yap</a>
            <?php endif; ?>
        </nav>
    </header>
