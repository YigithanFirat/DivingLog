<?php
include('../../config.php'); // Veritabanı bağlantısı

// Kullanıcı adını 'ad' ve 'soyad' sütunlarını birleştirerek alıyoruz
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
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-info text-center">Henüz kayıtlı sertifika bulunmamaktadır.</div>
    <?php endif; ?>
</div>

<footer>
    <p>&copy; 2025 DivingLog Uygulaması</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>