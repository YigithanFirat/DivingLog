<?php
session_start();
include('../../config.php');

$sql = "SELECT * FROM users";
$result = mysqli_query($mysqlB, $sql);

function e($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

// login parametresi sadece belirlenen değerler için mesaj gösterimi:
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
<header>
    <h1>DivingLog | Kullanıcıları Yönet</h1>
    <nav>
        <ul>
            <li><a href="../index.php">Ana Sayfa</a></li>
            <li><a href="../users/exit.php">Çıkış Yap</a></li>
        </ul>
    </nav>
</header>

<div class="container">
    <?php if ($loginStatus === 'success'): ?>
        <div class="success_message">İşlem başarıyla gerçekleşti.</div>
    <?php elseif ($loginStatus === 'error'): ?>
        <div class="error_message">Bir hata oluştu, lütfen tekrar deneyin.</div>
    <?php endif; ?>

    <h2>Kullanıcılar Listesi</h2>

    <?php if ($result && mysqli_num_rows($result) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Adı</th>
                    <th>Soyadı</th>
                    <th>E-posta</th>
                    <th>Durum</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= e($user['id']) ?></td>
                        <td><?= e($user['ad']) ?></td>
                        <td><?= e($user['soyad']) ?></td>
                        <td><?= e($user['email']) ?></td>
                        <td><?= ($user['login'] == 1) ? 'Çevrim İçi' : 'Çevrim Dışı' ?></td>
                        <td>
                            <a href="edit_user.php?id=<?= urlencode($user['id']) ?>" class="btn">Düzenle</a>
                            <a href="admin_reset_password.php?id=<?= urlencode($user['id']) ?>" class="btn">Şifre Sıfırla</a>
                            <a href="export_pdf.php?id=<?= urlencode($user['id']) ?>" class="btn">Dışa Aktar (PDF)</a>
                            <a href="#" class="btn delete-btn" onclick="openConfirmModal(<?= (int)$user['id'] ?>); return false;">
                                <i class="fas fa-exclamation-triangle"></i> Sil
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
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

<footer>
    <p>&copy; 2025 DivingLog Uygulaması</p>
</footer>

<script>
    function openConfirmModal(userId) {
        document.getElementById('deleteUserId').value = userId;
        document.getElementById('confirmModal').classList.add('active');
    }

    function closeConfirmModal() {
        document.getElementById('confirmModal').classList.remove('active');
    }
</script>
</body>
</html>