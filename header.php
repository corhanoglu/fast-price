<?php
/* =============================================================================
   DOSYA: header.php
   AÇIKLAMA: Tüm sayfaların ortak başlık ve navigasyon yapısı.
============================================================================= */
include_once 'config.php';

// Oturum kontrolü
$isLoggedIn = isset($_SESSION['user_id']);
$isAdmin = isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin';
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
  
