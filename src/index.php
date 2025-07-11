<?php
include('../config.php');

$logged_in = false;
$ag = false;
$user = [];

function buyukHarfTR($metin)
{
    $harfler = ['i', 'ı', 'ğ', 'ü', 'ş', 'ö', 'ç'];
    $buyukler = ['İ', 'I', 'Ğ', 'Ü', 'Ş', 'Ö', 'Ç'];
    $metin = str_replace($harfler, $buyukler, $metin);
    return mb_strtoupper($metin, 'UTF-8');
}

if (isset($_SESSION['tcno'])) {
    $tcno = $_SESSION['tcno'];

    // Prepared statement kullanımı
    $stmt = mysqli_prepare($mysqlB, "SELECT login, admin, ad FROM users WHERE tcno = ?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $tcno);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result && mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);
            if ($user['login'] == 1) {
                $logged_in = true;
            }
            if ($user['admin'] == 1) {
                $ag = true;
            }
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>DivingLog | Dalış Planlama Uygulaması</title>
    <link rel="stylesheet" href="CSS/index.css" />
    <link rel="icon" href="images/divinglog.png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<?php if ($logged_in && $ag): ?>
    <div class="menu-toggle" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </div>
    <h2 class="h2title">İskenderun Teknik Üniversitesi Su Altı Dalış Teknolojisi Programı</h2>
    <h1 class="h1title">DivingLog</h1>
    <div class="content">
        <h3>Hoş geldin, <?= htmlspecialchars(buyukHarfTR($user['ad'] ?? 'MİSAFİR ÜYE')) ?>! 👋</h3>
        <p>Web uygulamanızda dalış geçmişinizi kaydedebilir ve yönetebilirsiniz.</p>

        <?php if (!$logged_in): ?>
            <a href="users/login.php" class="btn">Giriş Yap</a>
            <a href="users/signup.php" class="btn">Kaydol</a>
        <?php elseif ($ag == true): ?>
            <div class="sidebar">
                <h2>Admin Panel</h2>
                <ul>
                    <li><a href="index.php">Ana Sayfa</a></li>
                    <li><a href="admin/dashboard.php">Dashboard</a></li>
                    <li><a href="admin/manage_users.php">Kullanıcıları Yönet</a></li>
                    <li><a href="admin/diving.php">Dalış Oluştur</a></li>
                    <li><a href="admin/manage_diving.php">Dalışları Yönet</a></li>
                    <li><a href="diving_place.php">Dalış Bölgeleri</a></li>
                    <li><a href="admin/certificate.php">Sertifika Oluştur</a></li>
                    <li><a href="admin/certificate_list.php">Sertifikaları Listele</a></li>
                    <li><a href="admin/health_inspection.php">Sağlık Raporu Oluştur</a></li>
                    <li><a href="admin/health_inspection_list.php">Sağlık Raporlarını Listele</a></li>
                    <li><a href="users/exit.php">Çıkış Yap</a></li>
                </ul>
            </div>
        <?php else: ?>
            <a href="users/exit.php" class="btn">Çıkış Yap</a>
        <?php endif; ?>
<?php else: ?>
    <h2 class="titleh2">İskenderun Teknik Üniversitesi Su Altı Dalış Teknolojisi Programı</h2>
    <h1 class="titleh1">DivingLog</h1>
    <div class="content1">
        <h3>Hoş geldin, <?= htmlspecialchars(buyukHarfTR($user['ad'] ?? 'MİSAFİR ÜYE')) ?>! 👋</h3>
        <p>Web uygulamanızda dalış geçmişinizi kaydedebilir ve yönetebilirsiniz.</p>

        <?php if (!$logged_in): ?>
            <a href="users/login.php" class="btn">Giriş Yap</a>
            <a href="users/signup.php" class="btn">Kaydol</a>
        <?php else: ?>
            <a href="users/exit.php" class="btn">Çıkış Yap</a>
            <a href="users/my_certificate.php?tc=<?= urlencode($_SESSION['tcno'] ?? '') ?>" class="btn">Sertifikalarım</a>
            <a href="users/my_diving.php?tc=<?= urlencode($_SESSION['tcno'] ?? '') ?>" class="btn">Dalışlarım</a>
        <?php endif; ?>
<?php endif; ?>
    </div>
    <footer>
        <p>&copy; 2025 DivingLog Uygulaması</p>
    </footer>
    <script src="JS/index.js"></script>
</body>
</html>