<?php
include('../../config.php');
session_start();

$success_message = '';
$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tcno = $_POST['tcno'] ?? '';
    $sifre = $_POST['sifre'] ?? '';

    if ($tcno && $sifre) {
        // Kullanıcıyı hazırlıklı ifade ile sorgula
        $stmt = mysqli_prepare($mysqlB, "SELECT sifre FROM users WHERE tcno = ?");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $tcno);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);

            if (mysqli_stmt_num_rows($stmt) > 0) {
                mysqli_stmt_bind_result($stmt, $hashed_password);
                mysqli_stmt_fetch($stmt);

                if (password_verify($sifre, $hashed_password)) {
                    mysqli_stmt_close($stmt);

                    // Kullanıcı giriş durumunu güncelle
                    $update_stmt = mysqli_prepare($mysqlB, "UPDATE users SET login = 1 WHERE tcno = ?");
                    if ($update_stmt) {
                        mysqli_stmt_bind_param($update_stmt, "s", $tcno);
                        if (mysqli_stmt_execute($update_stmt)) {
                            $_SESSION['tcno'] = $tcno;
                            mysqli_stmt_close($update_stmt);
                            header("Location: ../index.php");
                            exit();
                        } else {
                            $error_message = "Veritabanı güncellenirken bir hata oluştu.";
                        }
                    } else {
                        $error_message = "Veritabanı güncelleme için sorgu hazırlanamadı.";
                    }
                } else {
                    $error_message = "Geçersiz şifre.";
                }
            } else {
                $error_message = "Kullanıcı bulunamadı.";
            }
            mysqli_stmt_close($stmt);
        } else {
            $error_message = "Veritabanı sorgusu hazırlanamadı.";
        }
    } else {
        $error_message = "Lütfen tüm alanları doldurun.";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>DivingLog | Giriş Yap</title>
    <link rel="stylesheet" href="../CSS/login.css" />
    <link rel="icon" href="../images/divinglog.png" />
</head>
<body>
    <h1>DivingLog | Giriş Yap</h1>
    <div class="content">
        <?php if ($success_message): ?>
            <div class="success"><?= htmlspecialchars($success_message) ?></div>
        <?php endif; ?>
        <?php if ($error_message): ?>
            <div class="error"><?= htmlspecialchars($error_message) ?></div>
        <?php endif; ?>
        <form action="login.php" method="POST">
            <label for="tcno">TC Kimlik Numarası:</label><br />
            <input type="text" id="tcno" name="tcno" required /><br /><br />
            <label for="sifre">Şifre:</label><br />
            <input type="password" id="sifre" name="sifre" required /><br /><br />
            <button type="submit" class="btn">Giriş Yap</button>
        </form>
        <p>Hesabınız yok mu? <a href="signup.php" class="signup-link">Kayıt Ol</a></p>
        <p>Şifrenizi mi unuttunuz? <a href="authentication.php" class="forget-password">Şifremi Unuttum</a></p>
    </div>
    <footer>
        <p>&copy; 2025 DivingLog Uygulaması</p>
    </footer>
</body>
</html>