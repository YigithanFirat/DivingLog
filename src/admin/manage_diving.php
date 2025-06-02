<?php
include('../../config.php');

// Silme işlemi (POST ile)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $delete_id = intval($_POST['delete_id']);
    $stmt = $mysqlB->prepare("DELETE FROM diving_plans WHERE id = ?");
    $stmt->bind_param('i', $delete_id);
    if ($stmt->execute()) {
        $message = "Dalış planı başarıyla silindi.";
        $message_type = "success";
    } else {
        $message = "Silme işlemi başarısız oldu.";
        $message_type = "error";
    }
    $stmt->close();
}

// Verileri çek
$sql = "SELECT * FROM diving_plans ORDER BY created_at DESC";
$result = $mysqlB->query($sql);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>DivingLog | Dalışları Yönet</title>
    <link rel="icon" href="../images/divinglog.png" />
    <link rel="stylesheet" href="../CSS/manage_diving.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>
<body>
<div class="container my-4">
    <h2 class="text-center mb-4">Diving Planları Yönetimi</h2>

    <?php if (!empty($message)): ?>
        <div class="alert alert-<?= $message_type === 'success' ? 'success' : 'danger' ?> text-center" role="alert">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <?php if ($result && $result->num_rows > 0): ?>
        <div class="table-responsive">
            <table class="table table-striped table-bordered align-middle text-nowrap">
                <thead class="table-primary">
                    <tr>
                        <th>ID</th>
                        <th>T.C</th>
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
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-warning text-center">
            Hiçbir diving planı bulunamadı.
        </div>
    <?php endif; ?>
</div>

<!-- Silme Onay Modal -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form method="POST" class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="confirmDeleteModalLabel">Silme Onayı</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Kapat"></button>
      </div>
      <div class="modal-body">
        Bu dalış planını silmek istediğinize emin misiniz?
        <input type="hidden" name="delete_id" id="delete_id" value="">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
        <button type="submit" class="btn btn-danger">Sil</button>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Modal açılırken id bilgisini forma aktar
var confirmDeleteModal = document.getElementById('confirmDeleteModal');
confirmDeleteModal.addEventListener('show.bs.modal', function (event) {
  var button = event.relatedTarget;
  var id = button.getAttribute('data-id');
  var inputDeleteId = confirmDeleteModal.querySelector('#delete_id');
  inputDeleteId.value = id;
});
</script>
</body>
</html>