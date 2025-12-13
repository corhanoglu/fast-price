<?php
/* =============================================================================
   DOSYA: ajax_get_products.php (DÜZELTİLMİŞ)
============================================================================= */
header('Content-Type: application/json; charset=utf-8');
include 'config.php'; // Bu dosya session_start()'ı içerir.

// KRİTİK DÜZELTME: $isLoggedIn değişkenini tanımla
$isLoggedIn = isset($_SESSION['user_id']);

// Oturum kontrolü
if (!$isLoggedIn) {
    // Bu, artık giriş yapılmadığı anlamına gelir.
    echo json_encode(['error' => 'Yetkisiz erişim.']);
    exit;
}

$searchTerm = $_GET['term'] ?? '';

// Arama terimi boşsa boş döndür
if (empty($searchTerm)) {
    echo json_encode([]);
    exit;
}

try {
    // Ürünleri adına göre ara (Büyük/küçük harf duyarsız)
    $sql = "SELECT * FROM urunler WHERE ad LIKE :search ORDER BY ad ASC LIMIT 20";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':search', '%' . $searchTerm . '%');
    $stmt->execute();

    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $json_response = [];

    foreach ($products as $p) {
        // JS'in beklediği anahtar isimlerini kontrol edin: ad, fiyat, agirlik, carpan
        $json_response[] = [
            'id' => $p['id'],
            'ad' => $p['ad'],
            'birim' => $p['birim'],
            'fiyat' => floatval($p['satis_fiyati']), // JS kodu bunu bekler
            'agirlik' => floatval($p['kg_agirlik']),
            'carpan' => floatval($p['birim_carpan'])
        ];
    }

    echo json_encode($json_response);

} catch (PDOException $e) {
    // Hata durumunda ekranda ne olduğunu görebilmek için
    error_log("AJAX Ürün Arama Hatası: " . $e->getMessage());
    echo json_encode(['error' => 'Veritabanı bağlantı hatası. Detaylar loglandı.']);
}
?>
