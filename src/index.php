<?php
session_start();
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
</head>
<body>
    <h2>İskenderun Teknik Üniversitesi Su Altı Dalış Teknolojisi Programı</h2>
    <h1>DivingLog</h1>
    <div class="content">
        <h3>Hoş geldin, <?= htmlspecialchars(buyukHarfTR($user['ad'] ?? 'MİSAFİR ÜYE')) ?>! 👋</h3>
        <p>Web uygulamanızda dalış geçmişinizi kaydedebilir ve yönetebilirsiniz.</p>

        <?php if (!$logged_in): ?>
            <a href="users/login.php" class="btn">Giriş Yap</a>
            <a href="users/signup.php" class="btn">Kaydol</a>
        <?php else: ?>
            <a href="users/exit.php" class="btn">Çıkış Yap</a>
        <?php endif; ?>
    </div>
    <footer>
        <p>&copy; 2025 DivingLog Uygulaması</p>
    </footer>
</body>
</html>