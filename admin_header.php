<?php
/* =============================================================================
   DOSYA: admin_header.php
   AÇIKLAMA: Yönetim paneli sayfaları için alt navigasyon ve ana aksiyon butonu.
   GÜNCELLEMELER: Navigasyon ve aksiyon butonu tek bir barda gruplandı.
============================================================================= */
$current_page = basename($_SERVER['PHP_SELF']);
$action = $_GET['action'] ?? 'list'; // Action'ı al

// Admin panelinde navigasyon ve aksiyon butonlarını içeren bar
?>
<div class="admin-panel-bar">

    <nav class="admin-nav">
        <a href="admin.php" class="button button-admin-nav <?php echo ($current_page == 'admin.php' && ($action == 'list' || $action == 'delete' || $action == 'update' || $action == 'add' || $action == 'edit' || $action == 'new_product')) ? 'active' : ''; ?>">Ürünler</a>

        <a href="admin_users.php" class="button button-admin-nav <?php echo ($current_page == 'admin_users.php') ? 'active' : ''; ?>">Kullanıcılar</a>
    </nav>

    <div class="admin-actions-bar">
        <?php if ($current_page === 'admin.php' && $action === 'list'): ?>
            <a href="?action=new_product" class="button button-admin-action">Yeni Ürün Ekle</a>
        <?php endif; ?>
    </div>
</div>
