<?php
include('../session_guard.php');
include('../../config.php');

function e($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

$search_name = $_GET['search_name'] ?? '';
$search_name = trim($search_name);
$users = [];

if ($search_name !== '') {
    $stmt = mysqli_prepare($mysqlB, "SELECT * FROM users WHERE CONCAT(ad, ' ', soyad) LIKE CONCAT('%', ?, '%')");
    mysqli_stmt_bind_param($stmt, "s", $search_name);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($result) {
        $users = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
    mysqli_stmt_close($stmt);
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Ad Soyada Göre Kullanıcı Ara | DivingLog</title>
    <link rel="stylesheet" href="../CSS/search_name.css" />
    <link rel="icon" href="../images/divinglog.png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
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

    <div class="container">
        <h2>Ad Soyada Göre Kullanıcı Ara</h2>
        <form method="GET" action="search_name.php" class="search-form">
            <input
                type="text"
                name="search_name"
                placeholder="Ad Soyad giriniz"
                value="<?= e($search_name) ?>"
                required
                autocomplete="off"
            />
            <button type="submit" class="btn"><i class="fa-solid fa-magnifying-glass"></i> Ara</button>
        </form>

        <?php if ($search_name !== ''): ?>
            <h3>Arama Sonuçları (<?= count($users) ?>)</h3>

            <?php if (count($users) > 0): ?>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Adı</th>
                                <th>Soyadı</th>
                                <th>E-posta</th>
                                <th>Telefon</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?= e($user['ad']) ?></td>
                                    <td><?= e($user['soyad']) ?></td>
                                    <td><?= e($user['email']) ?></td>
                                    <td><?= e($user['telefon']) ?></td>
                                    <td>
                                        <a href="edit_user.php?id=<?= urlencode($user['id']) ?>" class="btn">Düzenle</a>
                                        <a href="admin_reset_password.php?id=<?= urlencode($user['id']) ?>" class="btn">Şifre Sıfırla</a>
                                        <a href="export_pdf.php?id=<?= urlencode($user['id']) ?>" class="btn">PDF İndir</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p>Aramanıza uygun kullanıcı bulunamadı.</p>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <script src="../JS/search_name.js"></script>
    <footer>
        <p>&copy; 2025 DivingLog Uygulaması</p>
    </footer>
</body>
</html>