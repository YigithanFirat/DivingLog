<?php
include('../../config.php');

// Sertifikalar ve kullanıcı adını çek
$query = "SELECT c.*, CONCAT(u.ad, ' ', u.soyad) AS user_name
          FROM certificate c
          LEFT JOIN users u ON c.user_id = u.id
          ORDER BY c.issue_date DESC";

$result = mysqli_query($mysqlB, $query);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>DivingLog | Sertifika Listesi</title>
    <link rel="stylesheet" href="../CSS/certificate_list.css">
    <link rel="icon" href="../images/divinglog.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h1 class="mb-4">Sertifika Listesi</h1>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Kullanıcı</th>
                    <th>Sertifika Adı</th>
                    <th>Veren Kuruluş</th>
                    <th>Veriliş Tarihi</th>
                    <th>Geçerlilik Tarihi</th>
                    <th>Seviye</th>
                    <th>Sertifika No</th>
                    <th>Notlar</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['user_name'] ?? 'Bilinmiyor') ?></td>
                        <td><?= htmlspecialchars($row['certificate_name']) ?></td>
                        <td><?= htmlspecialchars($row['issuing_organization']) ?></td>
                        <td><?= htmlspecialchars($row['issue_date']) ?></td>
                        <td><?= htmlspecialchars($row['expiration_date']) ?></td>
                        <td><?= htmlspecialchars($row['certificate_level']) ?></td>
                        <td><?= htmlspecialchars($row['certificate_number']) ?></td>
                        <td><?= nl2br(htmlspecialchars($row['notes'])) ?></td>
                        <td class="action-buttons">
                            <a href="edit_certificate.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Düzenle</a>
                            <button class="btn btn-danger btn-sm" onclick="setDeleteId(<?= $row['id'] ?>)" data-bs-toggle="modal" data-bs-target="#deleteModal"><i class="fas fa-exclamation-triangle"></i>Sil</button>
                            <a href="export_certificate_pdf.php?id=<?= $row['id'] ?>" class="btn btn-primary btn-sm">PDF</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-info text-center">Henüz kayıtlı sertifika bulunmamaktadır.</div>
    <?php endif; ?>
</div>

<!-- Silme Onay Modali -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="deleteModalLabel">Silme Onayı</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Kapat"></button>
      </div>
      <div class="modal-body">
        Bu sertifikayı silmek istediğinize emin misiniz?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
        <a href="#" class="btn btn-danger" id="confirmDeleteBtn">Evet, Sil</a>
      </div>
    </div>
  </div>
</div>

<footer>
    <p>&copy; 2025 DivingLog Uygulaması</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function setDeleteId(id) {
        const deleteBtn = document.getElementById("confirmDeleteBtn");
        deleteBtn.href = 'delete_certificate.php?id=' + id;
    }
</script>
</body>
</html>