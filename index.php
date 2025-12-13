<?php
$page_title = 'Ürün Kataloğu';
include 'header.php'; // header.php dosyasını dahil et

// KRİTİK DÜZELTME: Giriş yapılmadıysa login sayfasına yönlendir.
if (!$isLoggedIn) {
    header('Location: login.php');
    exit;
}

// Sayfa ilk yüklendiğinde tüm ürünleri getir.
// SIRALAMA DÜZELTME: Ekleme sırasına göre sırala
$stmt = $db->query("SELECT u.* FROM urunler u ORDER BY u.id ASC");
$initialProducts = $stmt->fetchAll();

// Yeni colspan: Ad, Alış Fiyatı, Satış Fiyatı -> 3 sütun
$colspan = 3;
?>
    <div class="filters">
        <form id="filter-form" onsubmit="return false;">
            <input type="text" id="search-input" name="search" placeholder="Ürün ara...">
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>Ürün Adı</th>
                <th>Alış Fiyatı</th>
                <th>Satış Fiyatı</th>
            </tr>
        </thead>
        <tbody id="product-table-body">
            <?php
            if (count($initialProducts) > 0) {
                foreach ($initialProducts as $product) {
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
                echo "<tr><td colspan='" . $colspan . "'>Sistemde ürün bulunmuyor.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('search-input');
    const tableBody = document.getElementById('product-table-body');

    // 3 kolon: Ad, Alış Fiyatı, Satış Fiyatı
    const tableColspan = 3;

    const fetchProducts = async () => {
        const searchTerm = searchInput.value;
        const colspan = tableColspan;
        tableBody.innerHTML = `<tr><td colspan="${colspan}" style="text-align:center;">Yükleniyor...</td></tr>`;
        try {
            // AJAX isteğini gönder
            const response = await fetch(`ajax_search.php?search=${encodeURIComponent(searchTerm)}`);
            tableBody.innerHTML = await response.text();
        } catch (error) {
            tableBody.innerHTML = `<tr><td colspan="${colspan}" style="text-align:center; color: red;">Bir hata oluştu.</td></tr>`;
        }
    };

    // 300ms bekleme süresi ile arama işlemini debounce et
    let debounceTimer;
    searchInput.addEventListener('input', () => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(fetchProducts, 300);
    });
});
</script>

<?php include 'footer.php'; ?>
