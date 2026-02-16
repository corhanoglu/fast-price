<?php
/* =============================================================================
   DOSYA: admin_header.php
   AÇIKLAMA: Yönetim paneli sayfaları için alt navigasyon ve ana aksiyon butonu.
   GÜNCELLEMELER: Kullanıcılar sekmesi için aksiyon butonu eklendi.
============================================================================= */
$current_page = basename($_SERVER['PHP_SELF']);
$action = $_GET['action'] ?? 'list'; // Action'ı al

// Admin panelinde navigasyon ve aksiyon butonlarını içeren bar
?>
<div class="admin-panel-bar">

    <nav class="admin-nav">
        <a href="admin.php" class="button button-admin-nav <?php echo ($current_page == 'admin.php' && ($action != 'new_product' && $action != 'edit')) ? 'active' : ''; ?>">Ürünler</a>

        <a href="admin_users.php" class="button button-admin-nav <?php echo ($current_page == 'admin_users.php' && ($action != 'new_user' && $action != 'edit')) ? 'active' : ''; ?>">Kullanıcılar</a>
    </nav>

    <div class="admin-actions-bar">
        <?php if ($current_page === 'admin.php' && $action === 'list'): ?>
            <a href="?action=new_product" class="button button-admin-action">Yeni Ürün Ekle</a>
        <?php elseif ($current_page === 'admin_users.php' && $action === 'list'): ?>
             <a href="?action=new_user" class="button button-admin-action">Yeni Kullanıcı Ekle</a>
        <?php endif; ?>
    </div>
</div>
