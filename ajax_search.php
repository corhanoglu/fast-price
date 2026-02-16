<?php
include_once 'config.php';

if (!function_exists('getCurrencySymbol')) {
    function getCurrencySymbol($currency) {
        switch ($currency) {
            case 'USD': return '$'; case 'EUR': return '€'; default: return '₺';
        }
    }
}

if (!isset($_SESSION['user_id'])) { exit; }

$searchQuery = $_GET['search'] ?? '';
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
$limit = 20; // 20 ürün sınırlaması

try {
    $sql = "SELECT * FROM urunler WHERE ad LIKE :search OR birim LIKE :search OR para_birimi LIKE :search ORDER BY id ASC LIMIT :offset, :limit";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':search', '%' . $searchQuery . '%');
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    $products = $stmt->fetchAll();

    if (count($products) > 0) {
        foreach ($products as $product) {
            $symbol = getCurrencySymbol($product->para_birimi);
            $unit = htmlspecialchars($product->birim);

            echo "<tr>";
            echo "<td data-label='Ürün Adı'><strong>" . htmlspecialchars($product->ad) . "</strong></td>";
            echo "<td data-label='Alış Fiyatı' class='text-right purchase-price-col'>" .
                 number_format($product->alis_fiyati, 2, ',', '.') . " " . $symbol . " (" . $product->para_birimi . ") / " . $unit . "</td>";
            // Satış fiyatının yanına / birim eklendi
            echo "<td data-label='Satış Fiyatı' class='text-right'><strong>" .
                 number_format($product->satis_fiyati, 2, ',', '.') . " " . $symbol . "</strong> <small style='color:var(--text-muted); font-weight:normal;'>/ " . $unit . "</small></td>";
            echo "</tr>";
        }
    }
} catch (PDOException $e) {
    echo "Hata!";
}
