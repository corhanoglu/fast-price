<?php
include_once 'config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'admin') { exit; }

$search = $_GET['search'] ?? '';

try {
    if (!empty($search)) {
        $stmt = $db->prepare("SELECT * FROM urunler WHERE ad LIKE ? OR birim LIKE ? ORDER BY id DESC");
        $stmt->execute(["%$search%", "%$search%"]);
        $products = $stmt->fetchAll(PDO::FETCH_OBJ);
    } else {
        $products = $db->query("SELECT * FROM urunler ORDER BY id DESC")->fetchAll(PDO::FETCH_OBJ);
    }

    foreach ($products as $p) {
        $symbol = ($p->para_birimi == 'USD' ? '$' : ($p->para_birimi == 'EUR' ? '€' : '₺'));
        echo "<tr>
                <td><strong>".htmlspecialchars($p->ad)."</strong> <small style='color:#888;'>(".htmlspecialchars($p->birim).")</small></td>
                <td class='text-right'>".number_format($p->alis_fiyati, 2, ',', '.')." {$symbol}</td>
                <td class='text-right'><strong>".number_format($p->satis_fiyati, 2, ',', '.')." {$symbol}</strong></td>
                <td style='text-align:right;'>
                    <a href='?action=edit&id={$p->id}' style='color:var(--accent); margin-right:15px;'><i class='fas fa-pen'></i></a>
                    <a href='?action=delete&id={$p->id}' style='color:red;' onclick='return confirm(\"Silinsin mi?\")'><i class='fas fa-trash'></i></a>
                </td>
              </tr>";
    }
} catch (PDOException $e) { exit; }
