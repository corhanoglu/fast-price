<?php
include 'config.php';

$searchQuery = $_GET['search'] ?? '';
$isLoggedIn = isset($_SESSION['user_id']);

// KRİTİK DÜZELTME: Giriş yapılmadıysa hata döndür.
if (!$isLoggedIn) {
    // 3 kolonu (Ad, Alış, Satış) dikkate alarak colspan 3 yapıldı.
    echo "<tr><td colspan='3' style='text-align: center; color: red;'>Bu içeriğe erişim için giriş yapmanız gerekmektedir.</td></tr>";
    exit;
}

// Yeni colspan: Ad, Alış Fiyatı, Satış Fiyatı -> 3 sütun
$colspan = 3;

// SIRALAMA DÜZELTME: Ekleme sırasına göre sırala
$sql = "SELECT u.* FROM urunler u WHERE u.ad LIKE :search OR u.birim LIKE :search OR u.para_birimi LIKE :search ORDER BY u.id ASC";
$stmt = $db->prepare($sql);
$stmt->bindValue(':search', '%' . $searchQuery . '%');
$stmt->execute();
$products = $stmt->fetchAll();

if (count($products) > 0) {
    foreach ($products as $product) {
        $finalPrice = $product->satis_fiyati;
        $currencySymbol = getCurrencySymbol($product->para_birimi);
        $unit = htmlspecialchars($product->birim);

        echo "<tr>";
        echo "<td>" . htmlspecialchars($product->ad) . "</td>";

        // Fiyata para birimi kodu eklendi
        echo "<td>" . number_format($product->alis_fiyati, 2) . ' ' . $currencySymbol . ' (' . $product->para_birimi . ') / ' . $unit . "</td>";
        // Fiyata para birimi kodu eklendi
        echo "<td><strong>" . number_format($finalPrice, 2) . ' ' . $currencySymbol . ' (' . $product->para_birimi . ') / ' . $unit . "</strong></td>";

        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='" . $colspan . "' style='text-align: center;'>Aradığınız kriterlere uygun ürün bulunamadı.</td></tr>";
}
?>
