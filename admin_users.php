<?php
$page_title = 'Kullanıcı Yönetimi';
include 'header.php'; // Ortak başlık

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    echo "</div><p>Bu sayfaya erişim yetkiniz yok.</p></body></html>";
    exit;
}

$action = $_POST['action'] ?? $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;
$error = ''; $success = '';

if ($action === 'delete' && $id) {
    if ($id == $_SESSION['user_id']) {
        $error = "Kendi hesabınızı silemezsiniz.";
    } else {
        $stmt = $db->prepare("DELETE FROM kullanicilar WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $success = "Kullanıcı başarıyla silindi.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'add') {
    $username = $_POST['kullanici_adi'];
    $password = $_POST['sifre'];
    if (empty($username) || empty($password)) {
        $error = "Kullanıcı adı ve şifre boş bırakılamaz.";
    } else {
        $stmt_check = $db->prepare("SELECT id FROM kullanicilar WHERE kullanici_adi = :username");
        $stmt_check->execute(['username' => $username]);
        if ($stmt_check->fetch()) {
            $error = "Bu kullanıcı adı zaten mevcut.";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("INSERT INTO kullanicilar (kullanici_adi, sifre, rol) VALUES (:username, :password, 'kullanici')");
            $stmt->execute(['username' => $username, 'password' => $hashedPassword]);
            $success = "Yeni kullanıcı başarıyla eklendi.";
        }
    }
}

$users = $db->query("SELECT id, kullanici_adi, rol FROM kullanicilar ORDER BY kullanici_adi ASC")->fetchAll();
?>

<!-- Admin Menüsünü Dahil Et -->
<?php include 'admin_header.php'; ?>

<?php if ($error): ?><div class="message error-message"><?php echo $error; ?></div><?php endif; ?>
<?php if ($success): ?><div class="message success-message"><?php echo $success; ?></div><?php endif; ?>

<div class="table-header"><h2>Yeni Kullanıcı Ekle</h2></div>
<form action="admin_users.php" method="POST" class="admin-form">
    <input type="hidden" name="action" value="add">
    <div class="form-row">
        <div class="form-group"><label>Kullanıcı Adı</label><input type="text" name="kullanici_adi" required></div>
        <div class="form-group"><label>Şifre</label><input type="password" name="sifre" required></div>
    </div>
    <div class="form-actions"><button type="submit" class="button">Yeni Kullanıcı Ekle</button></div>
</form>

<div class="table-header" style="margin-top: 2rem;"><h2>Mevcut Kullanıcılar</h2></div>
<table>
    <thead><tr><th>Kullanıcı Adı</th><th>Rol</th><th>İşlemler</th></tr></thead>
    <tbody>
        <?php foreach ($users as $user): ?>
        <tr>
            <td><?php echo htmlspecialchars($user->kullanici_adi); ?></td>
            <td><?php echo htmlspecialchars($user->rol); ?></td>
            <td class="actions">
                <?php if ($user->rol !== 'admin'): ?>
                    <a href="?action=delete&id=<?php echo $user->id; ?>" class="button button-small button-danger" onclick="return confirm('Bu kullanıcıyı silmek istediğinizden emin misiniz?');">Sil</a>
                <?php else: ?>
                    <span style="color:#999; font-size:0.8rem;">Silinemez</span>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</div><!-- container kapanış -->
</body>
</html>
