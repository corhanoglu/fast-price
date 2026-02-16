<?php
$page_title = 'Kullanıcı Yönetimi';
$page_search_handler = 'admin';
include 'header.php';

// Oturum ve Yetki Kontrolü
if (!$isLoggedIn || !$isAdmin) { header('Location: index.php'); exit; }

$action = $_POST['action'] ?? $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? $_POST['id'] ?? null;
$error = ''; $success = '';

// --- VERİTABANI İŞLEMLERİ ---
if ($action === 'delete' && $id) {
    if (intval($id) === $_SESSION['user_id']) {
        $error = "Kendi hesabınızı silemezsiniz.";
    } else {
        $db->prepare("DELETE FROM kullanicilar WHERE id = ?")->execute([$id]);
        header('Location: admin_users.php'); exit;
    }
}

if ($action === 'update_user' && $_SERVER['REQUEST_METHOD'] === 'POST' && $id) {
    $k_adi = trim($_POST['kullanici_adi']);
    $rol = $_POST['rol'];
    if (!empty($_POST['sifre'])) {
        $sifre = password_hash($_POST['sifre'], PASSWORD_DEFAULT);
        $db->prepare("UPDATE kullanicilar SET kullanici_adi = ?, rol = ?, sifre = ? WHERE id = ?")->execute([$k_adi, $rol, $sifre, $id]);
    } else {
        $db->prepare("UPDATE kullanicilar SET kullanici_adi = ?, rol = ? WHERE id = ?")->execute([$k_adi, $rol, $id]);
    }
    header('Location: admin_users.php'); exit;
}

if ($action === 'add_user' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $db->prepare("INSERT INTO kullanicilar (kullanici_adi, sifre, rol) VALUES (?, ?, ?)")
       ->execute([$_POST['kullanici_adi'], password_hash($_POST['sifre'], PASSWORD_DEFAULT), $_POST['rol']]);
    header('Location: admin_users.php'); exit;
}

$current_user = null;
if ($action === 'edit' && $id) {
    $stmt = $db->prepare("SELECT * FROM kullanicilar WHERE id = ?");
    $stmt->execute([$id]);
    $current_user = $stmt->fetch(PDO::FETCH_OBJ);
}
?>

<div class="container">
    <div class="admin-nav-tabs">
        <a href="admin.php" class="tab-link"><i class="fas fa-boxes"></i> Ürünler</a>
        <a href="admin_users.php" class="tab-link active"><i class="fas fa-users"></i> Kullanıcılar</a>
    </div>

    <?php if ($action === 'new_user' || ($action === 'edit' && $current_user)): ?>
        <div class="form-card" style="margin-bottom: 2rem;">
            <h2 style="margin-top:0;"><?php echo $action === 'edit' ? 'Kullanıcıyı Düzenle' : 'Yeni Kullanıcı'; ?></h2>
            <form method="POST">
                <input type="hidden" name="action" value="<?php echo $action === 'edit' ? 'update_user' : 'add_user'; ?>">
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label>Kullanıcı Adı</label>
                    <input type="text" name="kullanici_adi" value="<?php echo htmlspecialchars($current_user->kullanici_adi ?? ''); ?>" required>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Yetki Rolü</label>
                        <select name="rol">
                            <option value="kullanici" <?php echo (($current_user->rol ?? '') == 'kullanici') ? 'selected' : ''; ?>>Kullanıcı</option>
                            <option value="admin" <?php echo (($current_user->rol ?? '') == 'admin') ? 'selected' : ''; ?>>Yönetici</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Şifre <?php if($action == 'edit') echo '<small>(Boş bırakılırsa değişmez)</small>'; ?></label>
                        <input type="password" name="sifre" <?php echo $action === 'new_user' ? 'required' : ''; ?>>
                    </div>
                </div>
                <div style="display: flex; gap: 10px; margin-top: 1rem;">
                    <button type="submit" class="button button-primary">Kaydet</button>
                    <a href="admin_users.php" class="button button-secondary">İptal</a>
                </div>
            </form>
        </div>
    <?php endif; ?>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <h2 style="font-size: 1.1rem; margin: 0; font-weight: 700;">Kullanıcı Listesi</h2>
        <a href="?action=new_user" class="button button-primary"><i class="fas fa-plus"></i> Yeni Kullanıcı</a>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>KULLANICI ADI</th>
                    <th>YETKİ</th>
                    <th style="text-align:right;">İŞLEM</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $users = $db->query("SELECT * FROM kullanicilar ORDER BY rol = 'admin' DESC, kullanici_adi ASC")->fetchAll(PDO::FETCH_OBJ);
                foreach ($users as $u):
                ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($u->kullanici_adi); ?></strong></td>
                    <td>
                        <span style="font-size: 0.75rem; font-weight: 800; color: <?php echo $u->rol == 'admin' ? 'var(--accent)' : 'var(--text-muted)'; ?>;">
                            <?php echo strtoupper($u->rol); ?>
                        </span>
                    </td>
                    <td style="text-align:right;">
                        <a href="?action=edit&id=<?php echo $u->id; ?>" style="color:var(--accent); margin-right:15px;"><i class="fas fa-pen"></i></a>
                        <?php if ($u->id != $_SESSION['user_id']): ?>
                            <a href="?action=delete&id=<?php echo $u->id; ?>" style="color:red;" onclick="return confirm('Silinsin mi?')"><i class="fas fa-trash"></i></a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include 'footer.php'; ?>
