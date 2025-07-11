<?php
include('../session_guard.php');
require_once('../../config.php');

$search_tc = $_POST['search_tc'] ?? '';
$users = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search_user'])) {
    $search_tc = trim($_POST['search_tc']);

    $stmt = mysqli_prepare($mysqlB, "SELECT id, ad, soyad, tcno, email, telefon FROM users WHERE tcno LIKE CONCAT('%', ?, '%')");
    mysqli_stmt_bind_param($stmt, "s", $search_tc);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $users = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>TC Numarasına Göre Arama</title>
    <link rel="stylesheet" href="../CSS/search_tc.css" />
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
    <h2>TC Numarasına Göre Kullanıcı Arama</h2>

    <form method="POST" class="search-form">
        <label for="search_tc" class="search-label">TC Kimlik No:</label>
        <div class="search-row">
            <input type="text" name="search_tc" id="search_tc" class="search-input" placeholder="TC No giriniz" value="<?= htmlspecialchars($search_tc) ?>" required maxlength="11" pattern="\d{11}" title="11 haneli TC Kimlik No giriniz." />
            <button type="submit" name="search_user" class="btn"><i class="fa fa-search"></i> Ara</button>
        </div>
    </form>

    <?php if (!empty($users)): ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Adı</th>
                        <th>Soyadı</th>
                        <th>E-posta</th>
                        <th>Telefon</th>
                        <th>TC</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['ad']) ?></td>
                            <td><?= htmlspecialchars($user['soyad']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= htmlspecialchars($user['telefon']) ?></td>
                            <td><?= htmlspecialchars($user['tcno']) ?></td>
                            <td>
                                <div class="table-action-buttons">
                                    <a class="btn" href="edit_user.php?id=<?= urlencode($user['id']) ?>"><i class="fas fa-edit"></i> Düzenle</a>
                                    <a class="btn" href="export_pdf.php?id=<?= urlencode($user['id']) ?>"><i class="fas fa-file-pdf"></i> PDF</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <div class="error_message">Sonuç bulunamadı.</div>
    <?php endif; ?>
</div>
<footer>
    <p>&copy; 2025 DivingLog Uygulaması</p>
</footer>
<script>
function toggleSidebar() {
    document.querySelector('.sidebar').classList.toggle('hidden');
}
</script>
</body>
</html>