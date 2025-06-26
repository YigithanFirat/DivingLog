<?php
include('../../config.php');

// Sayfa başına gösterilecek sertifika sayısı
$perPage = 25;

// Aktif sayfa numarası
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
if ($page < 1) $page = 1;

// Toplam sertifika sayısını al
$countQuery = "SELECT COUNT(*) AS total FROM certificate";
$countResult = mysqli_query($mysqlB, $countQuery);
$totalCertificates = mysqli_fetch_assoc($countResult)['total'];
$totalPages = ceil($totalCertificates / $perPage);

// Başlangıç verisi
$offset = ($page - 1) * $perPage;

// Sertifikaları ve kullanıcı adlarını çek
$query = "SELECT *
        FROM certificate
        ORDER BY issue_date DESC
        LIMIT $perPage OFFSET $offset";

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
    <div class="page-wrapper">
        <div class="sidebar">
            <h2>Admin Panel</h2>
            <ul>
                <li><a href="../index.php">Ana Sayfa</a></li>
                <li><a href="manage_users.php">Kullanıcıları Yönet</a></li>
                <li><a href="manage_diving.php">Dalışları Yönet</a></li>
                <li><a href="certificate_list.php">Sertifikaları Listele</a></li>
                <li><a href="health_inspection_list.php">Sağlık Raporlarını Listele</a></li>
                <li><a href="../users/exit.php">Çıkış Yap</a></li>
            </ul>
        </div>

        <main class="content-container">
            <div class="container mt-4">
                <h1 class="mb-4">Sertifika Listesi</h1>

                <?php if (mysqli_num_rows($result) > 0): ?>
                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>Ad - Soyad</th>
                                <th>TC</th>
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
                                    <td><?= htmlspecialchars($row['full_name'] ?? 'Bilinmiyor') ?></td>
                                    <td><?= htmlspecialchars($row['tc']) ?></td>
                                    <td><?= htmlspecialchars($row['certificate_name']) ?></td>
                                    <td><?= htmlspecialchars($row['issuing_organization']) ?></td>
                                    <td><?= htmlspecialchars($row['issue_date']) ?></td>
                                    <td><?= htmlspecialchars($row['expiration_date']) ?></td>
                                    <td><?= htmlspecialchars($row['certificate_level']) ?></td>
                                    <td><?= htmlspecialchars($row['certificate_number']) ?></td>
                                    <td><?= nl2br(htmlspecialchars($row['notes'])) ?></td>
                                    <td class="action-buttons">
                                        <a href="edit_certificate.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Düzenle</a>
                                        <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal" onclick="setDeleteId(<?= $row['id'] ?>)">
                                            <i class="fas fa-exclamation-triangle"></i> Sil
                                        </button>
                                        <a href="export_certificate_pdf.php?id=<?= $row['id'] ?>" class="btn btn-primary btn-sm">PDF</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>

                    <!-- Sayfalama -->
                    <?php if ($totalPages > 1): ?>
                        <div class="pagination">
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <a href="?page=<?= $i ?>" class="<?= ($i == $page) ? 'active' : '' ?>"><?= $i ?></a>
                            <?php endfor; ?>
                        </div>
                    <?php endif; ?>

                <?php else: ?>
                    <div class="alert alert-info text-center">Henüz kayıtlı sertifika bulunmamaktadır.</div>
                <?php endif; ?>
            </div>
        </main>
    </div>

<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form method="POST" action="delete_certificate.php" class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="deleteModalLabel">Silme Onayı</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Kapat"></button>
      </div>
      <div class="modal-body">
        Bu sertifikayı silmek istediğinize emin misiniz?
        <input type="hidden" name="id" id="deleteCertificateId" value="">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
        <button type="submit" class="btn btn-danger">Evet, Sil</button>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../JS/certificate_list.js"></script>
</body>
</html>