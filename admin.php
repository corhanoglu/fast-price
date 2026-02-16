<?php
$page_title = 'Ürün Yönetimi';
$page_search_handler = 'admin';
include 'header.php';

// Oturum ve Yetki Kontrolü
if (!$isLoggedIn || !$isAdmin) { header('Location: index.php'); exit; }

$action = $_GET['action'] ?? 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// --- VERİTABANI İŞLEMLERİ ---
if ($action == 'delete' && $id > 0) {
    $db->prepare("DELETE FROM urunler WHERE id = ?")->execute([$id]);
    header('Location: admin.php'); exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ad = $_POST['ad']; $birim = $_POST['birim']; $alis = $_POST['alis_fiyati']; $satis = $_POST['satis_fiyati']; $para = $_POST['para_birimi'];
    if ($action == 'add') {
        $db->prepare("INSERT INTO urunler (ad, birim, alis_fiyati, satis_fiyati, para_birimi) VALUES (?, ?, ?, ?, ?)")->execute([$ad, $birim, $alis, $satis, $para]);
    } elseif ($action == 'edit' && $id > 0) {
        $db->prepare("UPDATE urunler SET ad=?, birim=?, alis_fiyati=?, satis_fiyati=?, para_birimi=? WHERE id=?")->execute([$ad, $birim, $alis, $satis, $para, $id]);
    }
    header('Location: admin.php'); exit;
}
?>

<div class="container">
    <div class="admin-nav-tabs">
        <a href="admin.php" class="tab-link active"><i class="fas fa-boxes"></i> Ürünler</a>
        <a href="admin_users.php" class="tab-link"><i class="fas fa-users"></i> Kullanıcılar</a>
    </div>

    <?php if ($action == 'add' || $action == 'edit'):
        $item = ['ad' => '', 'birim' => '', 'alis_fiyati' => '', 'satis_fiyati' => '', 'para_birimi' => 'TRY'];
        if ($action == 'edit') {
            $stmt = $db->prepare("SELECT * FROM urunler WHERE id = ?");
            $stmt->execute([$id]);
            $item = $stmt->fetch(PDO::FETCH_ASSOC);
        }
    ?>
        <div class="form-card" style="margin-bottom: 2rem;">
            <h2><?php echo ($action == 'add' ? 'Yeni Ürün' : 'Ürünü Düzenle'); ?></h2>
            <form method="POST">
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label>Ürün Adı</label>
                    <input type="text" name="ad" value="<?php echo htmlspecialchars($item['ad']); ?>" required>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Birim</label>
                        <input type="text" name="birim" value="<?php echo htmlspecialchars($item['birim']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Para Birimi</label>
                        <select name="para_birimi">
                            <option value="TRY" <?php if($item['para_birimi']=='TRY') echo 'selected'; ?>>TRY (₺)</option>
                            <option value="USD" <?php if($item['para_birimi']=='USD') echo 'selected'; ?>>USD ($)</option>
                            <option value="EUR" <?php if($item['para_birimi']=='EUR') echo 'selected'; ?>>EUR (€)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Alış Fiyatı</label>
                        <input type="number" step="0.01" name="alis_fiyati" value="<?php echo $item['alis_fiyati']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Satış Fiyatı</label>
                        <input type="number" step="0.01" name="satis_fiyati" value="<?php echo $item['satis_fiyati']; ?>" required>
                    </div>
                </div>
                <div style="display: flex; gap: 10px; margin-top: 1rem;">
                    <button type="submit" class="button button-primary">Kaydet</button>
                    <a href="admin.php" class="button button-secondary">İptal</a>
                </div>
            </form>
        </div>
    <?php endif; ?>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <h2 style="font-size: 1.1rem; margin: 0; font-weight: 700;">Ürün Listesi</h2>
        <a href="admin.php?action=add" class="button button-primary"><i class="fas fa-plus"></i> Yeni Ürün</a>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>ÜRÜN DETAYI</th>
                    <th class="text-right">ALIŞ</th>
                    <th class="text-right">SATIŞ</th>
                    <th style="text-align:right;">İŞLEM</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $products = $db->query("SELECT * FROM urunler ORDER BY id DESC")->fetchAll(PDO::FETCH_OBJ);
                foreach ($products as $p):
                    $symbol = ($p->para_birimi == 'USD' ? '$' : ($p->para_birimi == 'EUR' ? '€' : '₺'));
                ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($p->ad); ?></strong> <small style="color:#888;">(<?php echo htmlspecialchars($p->birim); ?>)</small></td>
                    <td class="text-right"><?php echo number_format($p->alis_fiyati, 2, ',', '.'); ?> <?php echo $symbol; ?></td>
                    <td class="text-right"><strong><?php echo number_format($p->satis_fiyati, 2, ',', '.'); ?> <?php echo $symbol; ?></strong></td>
                    <td style="text-align:right;">
                        <a href="?action=edit&id=<?php echo $p->id; ?>" style="color:var(--accent); margin-right:15px;"><i class="fas fa-pen"></i></a>
                        <a href="?action=delete&id=<?php echo $p->id; ?>" style="color:red;" onclick="return confirm('Silinsin mi?')"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include 'footer.php'; ?>
