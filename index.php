<?php
/* =============================================================================
   DOSYA: index.php
   AÇIKLAMA: Ana ürün kataloğu sayfası.
   GÜNCELLEMELER: Arama bölümü header.php'ye taşındı. Tablo başlıklarına hizalama sınıfı eklendi.
============================================================================= */
$page_title = 'Hızlı Fiyat';
// KRİTİK: header.php'ye arama formunun index.php için olduğunu bildir.
$page_search_handler = 'index';
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
    <table>
        <thead>
            <tr>
                <th>Ürün Adı</th>
                <th class="text-right">Alış Fiyatı</th>
                <th class="text-right">Satış Fiyatı</th>
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
                    echo "<td data-label=\"Ürün Adı\">" . htmlspecialchars($product->ad) . "</td>";
                    // Hizalama sınıfı burada da mevcut
                    echo "<td data-label=\"Alış Fiyatı\" class=\"text-right\">" . number_format($product->alis_fiyati, 2) . ' ' . $currencySymbol . ' (' . $product->para_birimi . ') / ' . $unit . "</td>";
                    // Hizalama sınıfı burada da mevcut
                    echo "<td data-label=\"Satış Fiyatı\" class=\"text-right\"><strong>" . number_format($finalPrice, 2) . ' ' . $currencySymbol . ' (' . $product->para_birimi . ') / ' . $unit . "</strong></td>";

                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='" . $colspan . "' style='text-align: center; padding: 1rem;'>Sistemde ürün bulunmuyor.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // KRİTİK: search-input artık header.php içinde olduğu için ID ile yakalanır.
    const searchInput = document.getElementById('search-input');
    const tableBody = document.getElementById('product-table-body');

    // 3 kolon: Ad, Alış Fiyatı, Satış Fiyatı
    const tableColspan = 3;

    // Eğer bu sayfa index.php ise (searchInput var ise) ve form gönderimi yapılmıyorsa
    if (searchInput && document.getElementById('filter-form').onsubmit) {
        const fetchProducts = async () => {
            const searchTerm = searchInput.value;
            const colspan = tableColspan;
            tableBody.innerHTML = `<tr><td colspan="${colspan}" style="text-align:center; padding: 1rem;">Yükleniyor...</td></tr>`;
            try {
                // AJAX isteğini gönder
                const response = await fetch(`ajax_search.php?search=${encodeURIComponent(searchTerm)}`);
                tableBody.innerHTML = await response.text();
            } catch (error) {
                tableBody.innerHTML = `<tr><td colspan="${colspan}" style="text-align:center; color: var(--danger); padding: 1rem;">Bir hata oluştu.</td></tr>`;
            }
        };

        // 300ms bekleme süresi ile arama işlemini debounce et
        let debounceTimer;
        searchInput.addEventListener('input', () => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(fetchProducts, 300);
        });

        // Sayfa yüklendiğinde bir kere çalıştır
        fetchProducts();
    }
});
</script>

<?php include 'footer.php'; ?>
