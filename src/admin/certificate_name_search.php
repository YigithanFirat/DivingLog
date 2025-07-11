<?php
include('../session_guard.php');
require_once('../../config.php');

function e($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

$search_name = '';
$results = [];

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search_name'])) {
    $search_name = trim($_GET['search_name']);

    if (!empty($search_name)) {
        $stmt = $mysqlB->prepare("
            SELECT * FROM certificate
            WHERE full_name LIKE CONCAT('%', ?, '%')
            ORDER BY issue_date DESC
        ");
        $stmt->bind_param("s", $search_name);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $results[] = $row;
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Sertifika Arama | Ad Soyada Göre</title>
    <link rel="stylesheet" href="../CSS/certificate_name_search.css">
    <link rel="icon" href="../images/divinglog.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
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
            <li><a href="diving_place.php">Dalış Bölgeleri</a></li>
            <li><a href="certificate.php">Sertifika Oluştur</a></li>
            <li><a href="certificate_list.php">Sertifikaları Listele</a></li>
            <li><a href="health_inspection.php">Sağlık Raporu Oluştur</a></li>
            <li><a href="health_inspection_list.php">Sağlık Raporlarını Listele</a></li>
            <li><a href="../users/exit.php">Çıkış Yap</a></li>
        </ul>
    </div>
    <div class="content">
        <div class="container mt-5">
            <h1 class="text-center">Ad Soyada Göre Sertifika Arama</h1>
            <form method="get" class="d-flex justify-content-center mb-4">
                <input 
                    type="text" 
                    name="search_name" 
                    value="<?= e($search_name) ?>" 
                    placeholder="Ad Soyad giriniz" 
                    class="form-control w-50 me-2" 
                    required 
                />
                <button type="submit" class="btn btn-search">
                    <i class="fa-solid fa-magnifying-glass"></i> Ara
                </button>
            </form>

            <?php if (!empty($search_name)): ?>
                <?php if (count($results) > 0): ?>
                    <table class="table table-striped table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>Ad Soyad</th>
                                <th>TC</th>
                                <th>Sertifika Adı</th>
                                <th>Veren Kurum</th>
                                <th>Veriliş</th>
                                <th>Geçerlilik</th>
                                <th>Seviye</th>
                                <th>Sertifika No</th>
                                <th>Notlar</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($results as $row): ?>
                                <tr>
                                    <td><?= e($row['full_name']) ?></td>
                                    <td><?= e($row['tc']) ?></td>
                                    <td><?= e($row['certificate_name']) ?></td>
                                    <td><?= e($row['issuing_organization']) ?></td>
                                    <td><?= e($row['issue_date']) ?></td>
                                    <td><?= e($row['expiration_date']) ?></td>
                                    <td><?= e($row['certificate_level']) ?></td>
                                    <td><?= e($row['certificate_number']) ?></td>
                                    <td><?= nl2br(e($row['notes'])) ?></td>
                                    <td>
                                        <a href="edit_certificate.php?id=<?= urlencode($row['id']) ?>" class="btn btn-warning btn-sm">Düzenle</a>
                                        <a href="export_certificate_pdf.php?id=<?= urlencode($row['id']) ?>" target="_blank" class="btn btn-primary btn-sm">PDF</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="text-center mt-4 fw-bold text-warning">"<?= e($search_name) ?>" adına ait sertifika bulunamadı.</p>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <footer class="text-center mt-5">
        <p>&copy; 2025 DivingLog Uygulaması</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>