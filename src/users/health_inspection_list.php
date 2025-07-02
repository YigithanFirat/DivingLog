<?php
session_start();
include('../../config.php');

$query = "SELECT id, muayene_tarihi, onaylayan, onaylanan, created_at FROM health_inspections ORDER BY muayene_tarihi DESC";
$result = mysqli_query($mysqlB, $query);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8" />
    <title>DivingLog | Sağlık Raporları Listesi</title>
    <link rel="stylesheet" href="../CSS/health_inspection_list.css" />
    <link rel="icon" href="../images/divinglog.png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="menu-toggle" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </div>
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <ul>
            <li><a href="../index.php">Ana Sayfa</a></li>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="manage_users.php">Kullanıcıları Yönet</a></li>
            <li><a href="diving.php">Dalış Oluştur</a></li>
            <li><a href="manage_diving.php">Dalışları Yönet</a></li>
            <li><a href="certificate.php">Sertifika Oluştur</a></li>
            <li><a href="certificate_list.php">Sertifikaları Listele</a></li>
            <li><a href="health_inspection.php">Sağlık Raporu Oluştur</a></li>
            <li><a href="health_inspection_list.php">Sağlık Raporlarını Listele</a></li>
            <li><a href="../users/exit.php">Çıkış Yap</a></li>
        </ul>
    </div>
<div class="container">
    <h1>Sağlık Raporları Listesi</h1>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?= $_SESSION['message']['type'] ?> alert-dismissible fade show mt-3" role="alert">
            <?= htmlspecialchars($_SESSION['message']['text']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Kapat"></button>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Muayene Tarihi</th>
                    <th>Onaylayan Doktor</th>
                    <th>Onaylanan Kişi</th>
                    <th>Kayıt Tarihi</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['muayene_tarihi']) ?></td>
                        <td><?= htmlspecialchars($row['onaylayan']) ?></td>
                        <td><?= htmlspecialchars($row['onaylanan']) ?></td>
                        <td><?= date('d.m.Y H:i', strtotime($row['created_at'])) ?></td>
                        <td>
                            <a href="health_inspection_edit.php?id=<?= $row['id'] ?>" class="btn-edit">Düzenle</a>
                            <button class="btn-delete" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" data-id="<?= $row['id'] ?>">
                                ⚠️ Sil
                            </button>
                            <a href="health_inspection_export_pdf.php?id=<?= $row['id'] ?>" target="_blank" class="btn-pdf">PDF Dışa Aktar</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="text-align:center; margin-top:40px; font-weight:bold; font-size:18px;">Henüz kayıtlı sağlık raporu yok.</p>
    <?php endif; ?>
</div>

<!-- Silme onay modali -->
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

<footer>
    <p>&copy; 2025 DivingLog Uygulaması</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../JS/health_inspection_list.js"></script>
</body>
</html>