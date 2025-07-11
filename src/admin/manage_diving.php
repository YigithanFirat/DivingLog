<?php
include('../session_guard.php');
include('../../config.php');
include('../sidebarmenu.php');

$limit = 10; // Sayfa başına gösterilecek kayıt sayısı
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

$tcFilter = '';
$result = false;
$totalRecords = 0;
$totalPages = 0;
$totalMinutes = 0;

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['tcno'])) {
    $tcFilter = trim($_GET['tcno']);

    // Toplam kayıt sayısını al
    $countStmt = $mysqlB->prepare("SELECT COUNT(*) FROM diving_plans WHERE tcno = ?");
    $countStmt->bind_param('s', $tcFilter);
    $countStmt->execute();
    $countStmt->bind_result($totalRecords);
    $countStmt->fetch();
    $countStmt->close();

    $totalPages = ceil($totalRecords / $limit);

    // Tüm dalışların toplam süresini al (sayfalama dışı)
    $sumStmt = $mysqlB->prepare("SELECT SUM(minutes) FROM diving_plans WHERE tcno = ?");
    $sumStmt->bind_param('s', $tcFilter);
    $sumStmt->execute();
    $sumStmt->bind_result($totalMinutes);
    $sumStmt->fetch();
    $sumStmt->close();

    $totalMinutes = $totalMinutes ?? 0;

    // Sayfaya ait kayıtları çek
    $stmt = $mysqlB->prepare("
        SELECT diving_plans.*, users.ad, users.soyad 
        FROM diving_plans 
        LEFT JOIN users ON diving_plans.tcno = users.tcno 
        WHERE diving_plans.tcno = ? 
        ORDER BY diving_plans.created_at DESC
        LIMIT ? OFFSET ?
    ");
    $stmt->bind_param('sii', $tcFilter, $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>DivingLog | TC'ye Göre Dalışları Listele</title>
    <link rel="icon" href="../images/divinglog.png" />
    <link rel="stylesheet" href="../CSS/manage_diving.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>
<body>
<div class="main-content">
    <h2>TC Numarasına Göre Dalış Planlarını Listele</h2>
    <form method="GET" class="d-flex flex-column align-items-center gap-3 mb-4">
        <input 
            type="text" name="tcno" 
            class="form-control" 
            style="max-width: 400px; height: 45px; text-align: center;" 
            placeholder="TC Kimlik No" 
            value="<?= htmlspecialchars($tcFilter) ?>" 
            required />
        <button type="submit" class="btn btn-primary">Listele</button>
    </form>
    <?php if ($result && $result->num_rows > 0): ?>
        <div class="d-flex justify-content-end mb-3 gap-2">
            <a href="export_user_all_diving_plan_pdf.php?tcno=<?= urlencode($tcFilter) ?>" target="_blank" class="btn btn-success">
                <i class="fa-solid fa-file-pdf"></i> Kullanıcıya Ait Tüm Dalışları PDF Olarak İndir
            </a>
            <a href="export_all_diving_plans_pdf.php" target="_blank" class="btn btn-dark">
                <i class="fa-solid fa-file-pdf"></i> Tüm Dalışları PDF Olarak İndir
            </a>
        </div>
    <?php endif; ?>
    <?php if ($result && $result->num_rows > 0): ?>
        <p class="text-end"><strong>Toplam Kayıt:</strong> <?= $totalRecords ?></p>

        <div class="table-responsive">
            <table class="table table-striped table-bordered align-middle text-nowrap">
                <thead class="table-primary">
                    <tr>
                        <th>Ad</th>
                        <th>Soyad</th>
                        <th>Dakika</th>
                        <th>Lokasyon</th>
                        <th>Dalış Ortamı</th>
                        <th>Derinlik (Metre)</th>
                        <th>Solunum</th>
                        <th>Elbise</th>
                        <th>Amaç</th>
                        <th>Takım</th>
                        <th>İşlem</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['ad']) ?></td>
                            <td><?= htmlspecialchars($row['soyad']) ?></td>
                            <td><?= htmlspecialchars($row['minutes']) ?></td>
                            <td><?= htmlspecialchars($row['diving_location']) ?></td>
                            <td><?= htmlspecialchars($row['water_type']) ?></td>
                            <td><?= htmlspecialchars($row['depth_meter']) ?></td>
                            <td><?= htmlspecialchars($row['respiration']) ?></td>
                            <td><?= htmlspecialchars($row['clothing']) ?></td>
                            <td><?= htmlspecialchars($row['diving_purpose']) ?></td>
                            <td><?= htmlspecialchars($row['tools']) ?></td>
                            <td>
                                <div class="d-flex gap-2 flex-wrap">
                                    <a href="edit_diving_plan.php?id=<?= urlencode($row['id']) ?>" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i> Düzenle
                                    </a>
                                    <a href="edit_diving_plan_export_pdf.php?id=<?= urlencode($row['id']) ?>" class="btn btn-info btn-sm">
                                        <i class="fas fa-file-pdf"></i> PDF
                                    </a>
                                    <button type="button" 
                                        class="btn btn-danger btn-sm" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#confirmDeleteModal" 
                                        data-id="<?= (int)$row['id'] ?>">
                                        <i class="fas fa-trash-alt"></i> Sil
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="17" class="text-end fw-bold">
                            Toplam Süre: <?= ($totalMinutes > 0) ? htmlspecialchars($totalMinutes) . ' dakika' : 'Kayıt bulunamadı' ?>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Sayfalama -->
        <?php if ($totalPages > 1): ?>
            <nav aria-label="Sayfa numaraları" class="mt-4">
                <ul class="pagination justify-content-center">
                    <!-- Önceki sayfa -->
                    <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                        <a class="page-link" href="?tcno=<?= urlencode($tcFilter) ?>&page=<?= $page - 1 ?>" aria-label="Önceki">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>

                    <?php
                    $startPage = max(1, $page - 2);
                    $endPage = min($totalPages, $page + 2);

                    if ($startPage > 1) {
                        echo '<li class="page-item"><a class="page-link" href="?tcno=' . urlencode($tcFilter) . '&page=1">1</a></li>';
                        if ($startPage > 2) {
                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        }
                    }

                    for ($i = $startPage; $i <= $endPage; $i++):
                    ?>
                        <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                            <a class="page-link" href="?tcno=<?= urlencode($tcFilter) ?>&page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor;

                    if ($endPage < $totalPages) {
                        if ($endPage < $totalPages - 1) {
                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        }
                        echo '<li class="page-item"><a class="page-link" href="?tcno=' . urlencode($tcFilter) . '&page=' . $totalPages . '">' . $totalPages . '</a></li>';
                    }
                    ?>

                    <!-- Sonraki sayfa -->
                    <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                        <a class="page-link" href="?tcno=<?= urlencode($tcFilter) ?>&page=<?= $page + 1 ?>" aria-label="Sonraki">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>

    <?php elseif (isset($_GET['tcno'])): ?>
        <div class="alert alert-danger text-center">
            Belirtilen TC numarasına ait dalış planı bulunamadı.
        </div>
    <?php endif; ?>
</div>

<!-- Silme Onay Modal -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form method="POST" action="delete_diving_plan.php" class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="confirmDeleteModalLabel">Silme Onayı</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Kapat"></button>
      </div>
      <div class="modal-body">
        Bu dalış kaydını silmek istediğinize emin misiniz?
        <input type="hidden" name="id" id="delete-id" />
        <input type="hidden" name="tcno" value="<?= htmlspecialchars($tcFilter) ?>" />
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Vazgeç</button>
        <button type="submit" class="btn btn-danger">Sil</button>
      </div>
    </form>
  </div>
</div>
<script src="../JS/manage_diving.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<footer>
    <p>&copy; 2025 DivingLog Uygulaması</p>
</footer>
</body>
</html>