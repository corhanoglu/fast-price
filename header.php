<?php
include_once 'config.php';
$isLoggedIn = isset($_SESSION['user_id']);
$isAdmin = isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin';
$current_page_file = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Hızlı Fiyat'; ?></title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<div class="container">
    <header class="main-header">
        <h1><a href="index.php">FASTPRICE</a></h1>

        <?php if ($isLoggedIn && ($page_search_handler ?? false)): ?>
            <div class="header-search">
                <form onsubmit="return false;">
                    <input type="text" id="search-input" placeholder="Ara..." class="header-search-input">
                </form>
            </div>
        <?php endif; ?>

        <nav>
            <?php if ($isLoggedIn): ?>
                <?php if ($isAdmin): ?>
                    <a href="admin.php" class="button button-secondary" title="Yönetim Paneli">
                        <i class="fas fa-layer-group"></i> <span>Yönetim</span>
                    </a>
                <?php endif; ?>
                <a href="logout.php" class="button button-primary" title="Güvenli Çıkış">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            <?php endif; ?>
        </nav>
    </header>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('search-input');
        if (!searchInput) return;

        // Mevcut sayfadaki tabloyu bul (index, admin veya admin_users)
        const getTableBody = () => {
            return document.getElementById('product-table-body') ||
                   document.querySelector('.table-wrapper tbody');
        };

        let timer;
        searchInput.addEventListener('input', () => {
            clearTimeout(timer);
            timer = setTimeout(async () => {
                const targetTable = getTableBody();
                if (!targetTable) return;

                // Sayfaya göre ilgili AJAX dosyasını seç
                let ajaxFile = 'ajax_search.php';
                const path = window.location.pathname;

                if (path.includes('admin.php')) {
                    ajaxFile = 'ajax_admin_list.php';
                } else if (path.includes('admin_users.php')) {
                    ajaxFile = 'ajax_admin_users.php';
                }

                try {
                    const response = await fetch(`${ajaxFile}?search=${encodeURIComponent(searchInput.value)}`);
                    const data = await response.text();
                    targetTable.innerHTML = data;

                    // Eğer index sayfasındaysak blur efektini tazele
                    if (typeof applyBlur === "function") {
                        applyBlur();
                    }
                } catch (error) {
                    console.error("Arama işlemi sırasında bir hata oluştu:", error);
                }
            }, 300);
        });
    });
    </script>
