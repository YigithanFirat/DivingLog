<?php
include('../../config.php');

$id = $_GET['id'] ?? null;
if (!$id) {
    echo "Geçersiz istek.";
    exit;
}

$query = "SELECT * FROM certificate WHERE id = ?";
$stmt = mysqli_prepare($mysqlB, $query);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$certificate = mysqli_fetch_assoc($result);

if (!$certificate) {
    echo "Sertifika bulunamadı.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>DivingLog | Sertifika Düzenle</title>
    <link rel="stylesheet" href="../CSS/edit_certificate.css">
    <link rel="icon" href="../images/divinglog.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container">
    <h2>Sertifika Düzenle</h2>

    <form action="update_certificate.php" method="POST">
        <input type="hidden" name="id" value="<?= $certificate['id'] ?>">

        <label for="certificate_name">Sertifika Adı:</label>
        <input type="text" name="certificate_name" id="certificate_name" value="<?= htmlspecialchars($certificate['certificate_name']) ?>" required>

        <label for="issuing_organization">Veren Kuruluş:</label>
        <input type="text" name="issuing_organization" id="issuing_organization" value="<?= htmlspecialchars($certificate['issuing_organization']) ?>" required>

        <label for="issue_date">Veriliş Tarihi:</label>
        <input type="date" name="issue_date" id="issue_date" value="<?= $certificate['issue_date'] ?>" required>

        <label for="expiration_date">Geçerlilik Tarihi:</label>
        <input type="date" name="expiration_date" id="expiration_date" value="<?= $certificate['expiration_date'] ?>">

        <label for="certificate_level">Seviye:</label>
        <input type="text" name="certificate_level" id="certificate_level" value="<?= htmlspecialchars($certificate['certificate_level']) ?>">

        <label for="certificate_number">Sertifika No:</label>
        <input type="text" name="certificate_number" id="certificate_number" value="<?= htmlspecialchars($certificate['certificate_number']) ?>">

        <label for="notes">Notlar:</label>
        <textarea name="notes" id="notes" rows="4"><?= htmlspecialchars($certificate['notes']) ?></textarea>

        <button type="submit">Kaydet</button>
        <a href="certificate_list.php" class="btn-secondary">İptal</a>
        <button type="button" class="btn btn-danger float-end" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal">Sil</button>
    </form>
</div>

<!-- Bootstrap Modal -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="confirmDeleteModalLabel">Silme Onayı</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Kapat"></button>
      </div>
      <div class="modal-body">
        Bu sertifikayı silmek istediğinize emin misiniz? Bu işlem geri alınamaz.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Vazgeç</button>
        <form action="delete_certificate.php" method="POST" style="display:inline;">
            <input type="hidden" name="id" value="<?= $certificate['id'] ?>">
            <button type="submit" class="btn btn-danger">Evet, Sil</button>
        </form>
      </div>
    </div>
  </div>
</div>

<footer>
    <p>&copy; 2025 DivingLog Uygulaması</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>