<?php
session_start();
include('../../config.php');

function e($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

$usersPerPage = 25;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
if ($page < 1) $page = 1;

$totalUsersResult = mysqli_query($mysqlB, "SELECT COUNT(*) as total FROM users");
$totalUsersRow = mysqli_fetch_assoc($totalUsersResult);
$totalUsers = $totalUsersRow['total'];

$totalPages = ceil($totalUsers / $usersPerPage);
$offset = ($page - 1) * $usersPerPage;

$sql = "SELECT * FROM users LIMIT $usersPerPage OFFSET $offset";
$result = mysqli_query($mysqlB, $sql);

$loginStatus = null;
if (isset($_GET['login'])) {
    $loginParam = $_GET['login'];
    if ($loginParam === 'success') {
        $loginStatus = 'success';
    } elseif ($loginParam === 'error') {
        $loginStatus = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>DivingLog | Kullanıcıları Yönet</title>
    <link rel="stylesheet" href="../CSS/manage_users.css" />
    <link rel="icon" href="../images/divinglog.png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>
<body>
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
    <?php if ($loginStatus === 'success'): ?>
        <div class="success_message">İşlem başarıyla gerçekleşti.</div>
    <?php elseif ($loginStatus === 'error'): ?>
        <div class="error_message">Bir hata oluştu, lütfen tekrar deneyin.</div>
    <?php endif; ?>

    <h2>Kullanıcılar Listesi</h2>

    <?php if ($result && mysqli_num_rows($result) > 0): ?>
        <div class="all_pdf">
            <a href="export_all_users_pdf.php" class="btn">Tüm Kullanıcıları PDF Olarak İndir</a>
        </div>
        <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Adı</th>
                    <th>Soyadı</th>
                    <th>E-posta</th>
                    <th>Telefon</th>
                    <th>Haber Verilecek Kişi</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= e($user['ad']) ?></td>
                        <td><?= e($user['soyad']) ?></td>
                        <td><?= e($user['email']) ?></td>
                        <td><?= e($user['telefon']) ?></td>
                        <td><?= e($user['kaza_haber_kişi_ad_soyad']) ?></td>
                        <td>
                            <div class="d-flex gap-2 flex-wrap">
                                <a href="edit_user.php?id=<?= urlencode($user['id']) ?>" class="btn">Düzenle</a>
                                <a href="admin_reset_password.php?id=<?= urlencode($user['id']) ?>" class="btn">Şifre Sıfırla</a>
                                <a href="export_pdf.php?id=<?= urlencode($user['id']) ?>" class="btn">Dışa Aktar (PDF)</a>
                                <a href="#" class="btn delete-btn" onclick="openConfirmModal(<?= (int)$user['id'] ?>); return false;">
                                    <i class="fas fa-exclamation-triangle"></i> Sil
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        </div>
        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?= $i ?>" class="<?= ($i == $page) ? 'active' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <p>Henüz kullanıcı bulunmamaktadır.</p>
    <?php endif; ?>
</div>

<!-- Silme Modal -->
<div id="confirmModal" class="modal-overlay">
    <div class="modal-box">
        <h3><i class="fas fa-triangle-exclamation"></i> Dikkat!</h3>
        <p>Bu kullanıcıyı silmek istediğinize emin misiniz?</p>
        <div class="modal-actions">
            <form id="deleteForm" method="POST" action="delete_user.php">
                <input type="hidden" name="id" id="deleteUserId" value="">
                <button type="submit" class="modal-confirm">Evet, Sil</button>
                <button type="button" class="modal-cancel" onclick="closeConfirmModal()">İptal</button>
            </form>
        </div>
    </div>
</div>
<script src="../JS/manage_users.js"></script>
<footer>
    <p>&copy; 2025 DivingLog Uygulaması</p>
</footer>
</body>
</html>