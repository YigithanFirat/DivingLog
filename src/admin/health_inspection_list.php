<?php
include('../session_guard.php');
require_once('../../config.php');
include('../sidebarmenu.php');

// Sayfa başına gösterilecek sağlık raporu sayısı
$perPage = 10;

// Aktif sayfa numarası
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
if ($page < 1) $page = 1;

// Toplam sağlık raporu sayısını al - prepared statement ile
$stmtCount = $mysqlB->prepare("SELECT COUNT(*) AS total FROM health_inspections");
$stmtCount->execute();
$stmtCount->bind_result($totalRecords);
$stmtCount->fetch();
$stmtCount->close();

$totalPages = ceil($totalRecords / $perPage);

// OFFSET hesapla
$offset = ($page - 1) * $perPage;

// Sağlık raporlarını tarih sırasına göre çek - prepared statement
$query = "
    SELECT id, muayene_tarihi, created_at, onaylayan, onaylanan
    FROM health_inspections
    ORDER BY muayene_tarihi DESC
    LIMIT ? OFFSET ?
";
$stmt = $mysqlB->prepare($query);
$stmt->bind_param('ii', $perPage, $offset);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8" />
    <title>DivingLog | Sağlık Raporları Listesi</title>
    <link rel="stylesheet" href="../CSS/health_inspection_list.css" />
    <link rel="icon" href="../images/divinglog.png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="content">
        <div class="container mt-4">
            <h1 class="text-center">Sağlık Raporları Listesi</h1>
            <div class="all-pdf">
                <div class="all_pdf d-flex justify-content-end mb-3">
                    <a href="export_all_health_inspection_pdf.php" target="_blank" class="btn btn-dark">
                        <i class="fas fa-file-pdf"></i> Tüm Sağlık Raporlarını PDF Olarak İndir
                    </a>
                    <a href="health_inspection_name_search.php" class="btn">
                        <i class="fa-solid fa-magnifying-glass"></i> Ad Soyada Göre Arama
                    </a>
                </div>
            </div>
            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-<?= htmlspecialchars($_SESSION['message']['type'], ENT_QUOTES) ?> alert-dismissible fade show mt-3" role="alert">
                    <?= htmlspecialchars($_SESSION['message']['text'], ENT_QUOTES) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Kapat"></button>
                </div>
                <?php unset($_SESSION['message']); ?>
            <?php endif; ?>

            <?php if ($result && $result->num_rows > 0): ?>
                <table class="table table-striped table-bordered mt-4">
                    <thead class="table-dark">
                        <tr>
                            <th>Muayene Tarihi</th>
                            <th>Onaylayan Doktor</th>
                            <th>Onaylanan Kişi</th>
                            <th>Kayıt Tarihi</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars(date('d.m.Y', strtotime($row['muayene_tarihi'])), ENT_QUOTES) ?></td>
                                <td><?= htmlspecialchars($row['onaylayan'], ENT_QUOTES) ?></td>
                                <td><?= htmlspecialchars($row['onaylanan'], ENT_QUOTES) ?></td>
                                <td><?= htmlspecialchars(date('d.m.Y H:i', strtotime($row['created_at'])), ENT_QUOTES) ?></td>
                                <td>
                                    <a href="../admin/health_inspection_edit.php?id=<?= urlencode($row['id']) ?>" class="btn-edit">Düzenle</a>
                                    <button class="btn-delete" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" data-id="<?= htmlspecialchars($row['id'], ENT_QUOTES) ?>">⚠️ Sil</button>
                                    <a href="../admin/health_inspection_export_pdf.php?id=<?= urlencode($row['id']) ?>" target="_blank" class="btn-pdf">PDF</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>

                <!-- Sayfalama -->
                <?php if ($totalPages > 1): ?>
                    <div class="pagination">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="?page=<?= $i ?>" class="<?= ($i === $page) ? 'active' : '' ?>"><?= $i ?></a>
                        <?php endfor; ?>
                    </div>
                <?php endif; ?>

            <?php else: ?>
                <p class="text-center mt-5 fw-bold fs-5">Henüz kayıtlı sağlık raporu yok.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Silme onay modalı -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark text-white">
          <div class="modal-header">
            <h5 class="modal-title" id="confirmDeleteModalLabel">Silme Onayı</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Kapat"></button>
          </div>
          <div class="modal-body">
            Bu sağlık raporunu silmek istediğinize emin misiniz?
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Vazgeç</button>
            <a href="#" id="confirmDeleteBtn" class="btn btn-danger">Evet, Sil</a>
          </div>
        </div>
      </div>
    </div>

    <footer class="text-center mt-5">
        <p>&copy; 2025 DivingLog Uygulaması</p>
    </footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../JS/health_inspection_list.js"></script>
</body>
</html>