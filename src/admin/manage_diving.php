<?php
include('../../config.php');

$limit = 1;
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
<div class="container my-4">
    <h2 class="text-center mb-4">TC Numarasına Göre Dalış Planlarını Listele</h2>

    <form method="GET" class="row g-3 justify-content-center mb-4">
        <div class="col-auto">
            <input 
                type="text" name="tcno" 
                class="form-control" 
                style="width: 400px; height: 45px; text-align: center;" 
                placeholder="TC Kimlik No" 
                value="<?= htmlspecialchars($tcFilter) ?>" 
                required />
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary">Listele</button>
        </div>
    </form>

    <?php if ($result && $result->num_rows > 0): ?>
        <p class="text-end"><strong>Toplam Kayıt:</strong> <?= $totalRecords ?></p>

        <div class="table-responsive">
            <table class="table table-striped table-bordered align-middle text-nowrap">
                <thead class="table-primary">
                    <tr>
                        <th>ID</th>
                        <th>T.C</th>
                        <th>Ad</th>
                        <th>Soyad</th>
                        <th>Dakika</th>
                        <th>Lokasyon</th>
                        <th>Dalış Ortamı</th>
                        <th>Derinlik (Feet)</th>
                        <th>Derinlik (Metre)</th>
                        <th>Solunum</th>
                        <th>Elbise</th>
                        <th>Amaç</th>
                        <th>Alet ve Cihaz</th>
                        <th>Takım</th>
                        <th>Amir</th>
                        <th>Tarih</th>
                        <th>İşlem</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['id']) ?></td>
                            <td><?= htmlspecialchars($row['tcno']) ?></td>
                            <td><?= htmlspecialchars($row['ad']) ?></td>
                            <td><?= htmlspecialchars($row['soyad']) ?></td>
                            <td><?= htmlspecialchars($row['minutes']) ?></td>
                            <td><?= htmlspecialchars($row['diving_location']) ?></td>
                            <td><?= htmlspecialchars($row['water_type']) ?></td>
                            <td><?= htmlspecialchars($row['depth_feet']) ?></td>
                            <td><?= htmlspecialchars($row['depth_meter']) ?></td>
                            <td><?= htmlspecialchars($row['respiration']) ?></td>
                            <td><?= htmlspecialchars($row['clothing']) ?></td>
                            <td><?= htmlspecialchars($row['diving_purpose']) ?></td>
                            <td><?= htmlspecialchars($row['tools']) ?></td>
                            <td><?= htmlspecialchars($row['tools_devices']) ?></td>
                            <td><?= htmlspecialchars($row['supervisor']) ?></td>
                            <td><?= htmlspecialchars($row['created_at']) ?></td>
                            <td>
                                <div class="d-flex gap-2 flex-wrap">
                                    <a href="edit_diving_plan.php?id=<?= urlencode($row['id']) ?>" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i> Düzenle
                                    </a>
                                    <a href="edit_diving_plan_export_pdf.php?id=<?= urlencode($row['id']) ?>" class="btn btn-info btn-sm">
                                        <i class="fas fa-file-pdf"></i> PDF Dışa Aktar
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
            <nav aria-label="Sayfa numaraları">
                <ul class="pagination justify-content-center">
                    <!-- Önceki sayfa -->
                    <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                        <a class="page-link" href="?tcno=<?= urlencode($tcFilter) ?>&page=<?= $page - 1 ?>" aria-label="Önceki">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>

                    <?php
                    $startPage = max(1, $page - 3);
                    $endPage = min($totalPages, $page + 3);

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
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Vazgeç</button>
        <button type="submit" class="btn btn-danger">Sil</button>
      </div>
    </form>
  </div>
</div>

<script>
  const deleteModal = document.getElementById('confirmDeleteModal');
  deleteModal.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    const id = button.getAttribute('data-id');
    const input = deleteModal.querySelector('#delete-id');
    input.value = id;
  });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>