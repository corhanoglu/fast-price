<?php
include_once 'config.php';
// Güvenlik: Giriş yapmamış veya admin olmayan göremez
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') exit;

$search = $_GET['search'] ?? '';
$query = "SELECT * FROM kullanicilar WHERE kullanici_adi LIKE ? ORDER BY rol = 'admin' DESC, kullanici_adi ASC";
$stmt = $db->prepare($query);
$stmt->execute(["%$search%"]);
$users = $stmt->fetchAll(PDO::FETCH_OBJ);

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
