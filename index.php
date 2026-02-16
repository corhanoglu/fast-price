<?php
$page_title = 'Ürün Listesi';
$page_search_handler = 'index';
include 'header.php';

if (!$isLoggedIn) {
    header('Location: login.php');
    exit;
}
?>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Ürün Adı</th>
                    <th class="text-right">
                        Alış Fiyatı
                        <button id="toggle-blur" class="button button-secondary" style="padding: 2px 8px; font-size: 0.7rem; margin-left: 8px;">
                            <i class="fas fa-eye-slash"></i>
                        </button>
                    </th>
                    <th class="text-right">Satış Fiyatı</th>
                </tr>
            </thead>
            <tbody id="product-table-body">
                </tbody>
        </table>
        <div id="scroll-loader" style="text-align:center; padding:15px; display:none; color:var(--text-muted);">
            <i class="fas fa-spinner fa-spin"></i> Yükleniyor...
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const tableBody = document.getElementById('product-table-body');
        const toggleBtn = document.getElementById('toggle-blur');
        const loader = document.getElementById('scroll-loader');

        let offset = 0;
        const limit = 20;
        let isLoading = false;
        let isBlurred = true;
        let hasMore = true; // Daha fazla ürün var mı kontrolü

        // Ürünleri getiren ana fonksiyon
        const fetchProducts = async (append = false) => {
            if (isLoading || (!append && !hasMore && offset > 0)) return;

            // Eğer yeni bir arama/yükleme yapılıyorsa hasMore'u sıfırla
            if (!append) {
                hasMore = true;
                offset = 0;
            }

            if (!hasMore && append) return;

            isLoading = true;
            loader.style.display = 'block';

            try {
                // Input'u her seferinde güncel yakala
                const searchInput = document.getElementById('search-input');
                const query = searchInput ? searchInput.value : '';

                const response = await fetch(`ajax_search.php?search=${encodeURIComponent(query)}&offset=${offset}`);
                const data = await response.text();
                const trimmedData = data.trim();

                if (!append) tableBody.innerHTML = '';

                if (trimmedData === '') {
                    hasMore = false;
                    if (!append) {
                        tableBody.innerHTML = '<tr><td colspan="3" style="text-align:center; padding: 2rem;">Ürün bulunamadı.</td></tr>';
                    }
                } else {
                    tableBody.insertAdjacentHTML('beforeend', data);
                    offset += limit;

                    // Gelen veri limitin altındaysa daha fazla ürün yoktur
                    if (trimmedData.split('<tr>').length - 1 < limit) {
                        hasMore = false;
                    }

                    if (typeof applyBlur === "function") applyBlur();
                }
            } catch (error) {
                console.error("Yükleme Hatası:", error);
            } finally {
                isLoading = false;
                loader.style.display = 'none';
            }
        };

        // Sonsuz Kaydırma Dinleyicisi
        window.addEventListener('scroll', () => {
            if (!hasMore || isLoading) return;

            if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 500) {
                fetchProducts(true);
            }
        });

        // Blur (Fiyat Gizleme) Fonksiyonu
        window.applyBlur = () => {
            const cells = document.querySelectorAll('.purchase-price-col');
            cells.forEach(cell => {
                if (isBlurred) cell.classList.add('blurred');
                else cell.classList.remove('blurred');
            });
            toggleBtn.innerHTML = isBlurred ? '<i class="fas fa-eye-slash"></i>' : '<i class="fas fa-eye"></i>';
        };

        toggleBtn.addEventListener('click', () => {
            isBlurred = !isBlurred;
            window.applyBlur();
        });

        // Sayfa ilk açıldığında verileri çek
        fetchProducts();
    });
    </script>
<?php include 'footer.php'; ?>
