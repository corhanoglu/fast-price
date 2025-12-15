<?php
/* =============================================================================
   DOSYA: admin.php
   AÇIKLAMA: Ürün yönetim paneli.
   GÜNCELLEMELER: Arama header.php'ye, Yeni Ürün Ekle butonu admin_header.php'ye taşındı.
============================================================================= */
$page_title = 'Ürün Yönetimi';
// KRİTİK: header.php'ye arama formunun admin.php için olduğunu bildir.
$page_search_handler = 'admin';
include 'header.php';
include 'admin_header.php'; // Yönetim Navigasyonunu dahil et

if (!$isAdmin) {
    echo "</div><p>Bu sayfaya erişim yetkiniz yok.</p></body></html>";
    exit;
}

$action = $_POST['action'] ?? $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? $_POST['id'] ?? null;
$search = $_GET['search'] ?? ''; // Arama inputu header'dan gelse de burada yakalanır.
$error = ''; $success = '';

// Helper fonksiyonları (Normalde config.php'de tanımlı olmalı, burada yedeklendi)
if (!function_exists('getCurrencySymbol')) {
    function getCurrencySymbol($currency) {
        switch ($currency) {
            case 'USD': return '$';
            case 'EUR': return '€';
            default: return '₺';
        }
    }
}

// Para birimleri listesi
$currencies = ['TRY', 'USD', 'EUR'];


// =========================================================================
// SİLME İŞLEMİ
// =========================================================================
if ($action === 'delete' && $id) {
    try {
        $stmt = $db->prepare("DELETE FROM urunler WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $success = "Ürün silindi.";
    } catch (PDOException $e) { $error = $e->getMessage(); }
    $action = 'list';
}

// =========================================================================
// EKLEME/GÜNCELLEME İŞLEMİ
// =========================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($action === 'add' || $action === 'update')) {
    $ad = $_POST['ad'];
    $alis = $_POST['alis_fiyati'];
    $satis = $_POST['satis_fiyati'];
    $birim = $_POST['birim'];
    $para = $_POST['para_birimi'];

    if (empty($ad) || !is_numeric($alis) || !is_numeric($satis) || empty($para)) {
        $error = "Lütfen tüm gerekli alanları doğru şekilde doldurun.";
    } else {
        $data = [
            'ad' => $ad,
            'alis_fiyat' => $alis,
            'satis_fiyati' => $satis,
            'birim' => $birim,
            'para_birimi' => $para
        ];

        if ($action === 'add') {
            $sql = "INSERT INTO urunler (ad, alis_fiyati, satis_fiyati, birim, para_birimi)
                    VALUES (:ad, :alis_fiyat, :satis_fiyati, :birim, :para_birimi)";
            $success = "Ürün başarıyla eklendi.";
        } else { // update
            $sql = "UPDATE urunler SET ad=:ad, alis_fiyati=:alis_fiyat, satis_fiyati=:satis_fiyati, birim=:birim, para_birimi=:para_birimi WHERE id=:id";
            $data['id'] = $id;
            $success = "Ürün başarıyla güncellendi.";
        }

        try {
            $stmt = $db->prepare($sql);
            $stmt->execute($data);
        } catch (PDOException $e) { $error = $e->getMessage(); }
        $action = 'list';
    }
}

// =========================================================================
// ÜRÜN ÇEKME İŞLEMİ (Liste veya Düzenleme için)
// =========================================================================
if ($action === 'list' || $action === 'edit') {
    $sql = "SELECT u.* FROM urunler u";

    $params = [];
    if (!empty($search)) {
        $sql .= " WHERE u.ad LIKE :search";
        $params['search'] = '%' . $search . '%';
    }
    // SIRALAMA DÜZELTME: Ekleme sırasına göre sırala
    $sql .= " ORDER BY u.id ASC";
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll();

    if ($action === 'edit' && $id) {
        $stmt_edit = $db->prepare("SELECT * FROM urunler WHERE id = :id");
        $stmt_edit->execute(['id' => $id]);
        $productToEdit = $stmt_edit->fetch();
        if (!$productToEdit) { $error = "Ürün bulunamadı."; $action = 'list'; }
    }
}
?>

<div class="admin-panel">
    <?php if ($error): ?><div class="message error-message"><?php echo $error; ?></div><?php endif; ?>
    <?php if ($success): ?><div class="message success-message"><?php echo $success; ?></div><?php endif; ?>

    <?php if ($action === 'edit' || $action === 'new_product'): ?>
        <h2><?php echo $action === 'edit' ? 'Ürün Düzenle' : 'Yeni Ürün Ekle'; ?></h2>
        <form method="POST" class="admin-form">
            <input type="hidden" name="action" value="<?php echo $action === 'edit' ? 'update' : 'add'; ?>">
            <?php if ($action === 'edit'): ?><input type="hidden" name="id" value="<?php echo $productToEdit->id; ?>"><?php endif; ?>

            <div class="form-row">
                <div class="form-group"><label>Ürün Adı</label><input type="text" name="ad" value="<?php echo htmlspecialchars($productToEdit->ad ?? ''); ?>" required></div>
                <div class="form-group"><label>Para Birimi</label>
                    <select name="para_birimi" required>
                        <?php foreach($currencies as $c): ?>
                            <option value="<?php echo $c; ?>" <?php echo (isset($productToEdit->para_birimi) && $productToEdit->para_birimi == $c) ? 'selected':''; ?>><?php echo $c; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group"><label>Alış Fiyatı (Birim Başına)</label><input type="number" step="0.01" name="alis_fiyati" value="<?php echo htmlspecialchars($productToEdit->alis_fiyati ?? '0.00'); ?>" required></div>
                <div class="form-group"><label>Satış Fiyatı (Birim Başına)</label><input type="number" step="0.01" name="satis_fiyati" value="<?php echo htmlspecialchars($productToEdit->satis_fiyati ?? '0.00'); ?>" required></div>
            </div>

            <div class="form-row">
                <div class="form-group full-width"><label>Birim Adı</label><input type="text" name="birim" value="<?php echo htmlspecialchars($productToEdit->birim ?? 'Adet'); ?>" required></div>
            </div>

            <div class="form-actions">
                <button type="submit" class="button"><?php echo $action === 'edit' ? 'Ürünü Güncelle' : 'Ürünü Ekle'; ?></button>
                <a href="admin.php" class="button button-secondary">İptal</a>
            </div>
        </form>

    <?php else: // Ürün Listeleme ?>

        <table>
            <thead>
                <tr>
                    <th>Adı</th>
                    <th class="text-right">Alış Fiyatı</th>
                    <th class="text-right">Satış Fiyatı</th>
                    <th class="text-right">Marj (%)</th>
                    <th class="actions">İşlemler</th>
                </tr>
            </thead>
            <tbody>
            <?php if (count($products) > 0): ?>
            <?php foreach ($products as $p): ?>
            <?php
                $realCostPrice = $p->alis_fiyati;
                $realSellPrice = $p->satis_fiyati;

                // Marj Hesapla
                $margin = ($realSellPrice > 0) ? (($realSellPrice - $realCostPrice) / $realSellPrice) * 100 : 0;
                // Marj rengi style.css'teki değişkenlerden alınır
                $marginColor = $margin >= 0 ? 'var(--success)' : 'var(--danger)';
                $currencySymbol = getCurrencySymbol($p->para_birimi);
                $unit = htmlspecialchars($p->birim);
            ?>
                <tr>
                    <td data-label="Adı"><?php echo htmlspecialchars($p->ad); ?></td>
                    <td data-label="Alış Fiyatı" class="text-right"><?php echo number_format($realCostPrice, 2) . ' ' . $currencySymbol . ' (' . $p->para_birimi . ') / ' . $unit; ?></td>
                    <td data-label="Satış Fiyatı" class="text-right"><strong><?php echo number_format($realSellPrice, 2) . ' ' . $currencySymbol . ' (' . $p->para_birimi . ') / ' . $unit; ?></strong></td>
                    <td data-label="Marj (%)" class="text-right" style="color: <?php echo $marginColor; ?>; font-weight: bold;">
                        <?php echo number_format($margin, 2) . " %"; ?>
                    </td>
                    <td data-label="İşlemler" class="actions">
                        <a href="?action=edit&id=<?php echo $p->id; ?>" class="button button-small">Düzenle</a>
                        <a href="?action=delete&id=<?php echo $p->id; ?>" class="button button-small button-danger" onclick="return confirm('Silmek istediğinizden emin misiniz?');">Sil</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5" style="text-align: center; padding: 1rem;">Kayıtlı ürün bulunmamaktadır.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
