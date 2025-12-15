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
        // data-label eklendi (Mobil uyum için)
        echo "<td data-label=\"Ürün Adı\">" . htmlspecialchars($product->ad) . "</td>";

        // Sağa Hizalama Sınıfı Eklendi
        echo "<td data-label=\"Alış Fiyatı\" class=\"text-right\">" . number_format($product->alis_fiyati, 2) . ' ' . $currencySymbol . ' (' . $product->para_birimi . ') / ' . $unit . "</td>";
        // Sağa Hizalama Sınıfı Eklendi
        echo "<td data-label=\"Satış Fiyatı\" class=\"text-right\"><strong>" . number_format($finalPrice, 2) . ' ' . $currencySymbol . ' (' . $product->para_birimi . ') / ' . $unit . "</strong></td>";

        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='" . $colspan . "' style='text-align: center; padding: 1rem;'>Aradığınız kriterlere uygun ürün bulunamadı.</td></tr>";
}
?>
