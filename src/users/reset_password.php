<?php
include('../../config.php');
session_start();

$success_message = '';
$error_message = '';
$token = $_GET['token'] ?? '';

if ($token) {
    // Token doğrulama için prepared statement
    $stmt = mysqli_prepare($mysqlB, "SELECT tcno FROM users WHERE reset_token = ? AND token_expiry > NOW()");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $token);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            mysqli_stmt_bind_result($stmt, $tcno);
            mysqli_stmt_fetch($stmt);

            if ($_SERVER["REQUEST_METHOD"] === "POST") {
                $new_password = $_POST['new_password'] ?? '';
                $confirm_password = $_POST['confirm_password'] ?? '';

                if ($new_password === $confirm_password) {
                    if (strlen($new_password) < 6) {
                        // İstersen şifre minimum uzunluk kontrolü ekleyebilirsin
                        $error_message = "Şifre en az 6 karakter olmalıdır.";
                    } else {
                        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                        $update_stmt = mysqli_prepare($mysqlB, "UPDATE users SET sifre = ?, reset_token = NULL, token_expiry = NULL WHERE tcno = ?");
                        if ($update_stmt) {
                            mysqli_stmt_bind_param($update_stmt, "ss", $hashed_password, $tcno);
                            if (mysqli_stmt_execute($update_stmt)) {
                                $success_message = "Şifreniz başarıyla sıfırlandı.";
                                // Başarılı işlem sonrası index.php'ye yönlendir
                                header("Location: ../index.php");
                                exit();
                            } else {
                                $error_message = "Şifreniz sıfırlanırken bir hata oluştu.";
                            }
                            mysqli_stmt_close($update_stmt);
                        } else {
                            $error_message = "Veritabanı sorgusu hazırlanamadı.";
                        }
                    }
                } else {
                    $error_message = "Yeni şifre ve onay şifresi uyuşmuyor.";
                }
            }
        } else {
            $error_message = "Geçersiz veya süresi geçmiş şifre sıfırlama bağlantısı.";
        }
        mysqli_stmt_close($stmt);
    } else {
        $error_message = "Veritabanı sorgusu hazırlanamadı.";
    }
} else {
    $error_message = "Geçersiz bağlantı.";
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Şifre Sıfırlama</title>
    <link rel="stylesheet" href="../CSS/reset_password.css" />
    <link rel="icon" href="../images/divinglog.png" />
</head>
<body>
    <h1>DivingLog | Şifre Sıfırlama</h1>
    <div class="content">
        <?php if ($success_message): ?>
            <div class="success"><?= htmlspecialchars($success_message) ?></div>
        <?php endif; ?>
        <?php if ($error_message): ?>
            <div class="error"><?= htmlspecialchars($error_message) ?></div>
        <?php endif; ?>

        <?php if (!$success_message): ?>
        <form action="reset_password.php?token=<?= htmlspecialchars($token) ?>" method="POST">
            <label for="new_password">Yeni Şifre:</label><br />
            <input type="password" id="new_password" name="new_password" required /><br /><br />
            <label for="confirm_password">Şifreyi Onayla:</label><br />
            <input type="password" id="confirm_password" name="confirm_password" required /><br /><br />
            <button type="submit" class="btn">Şifremi Sıfırla</button>
        </form>
        <?php endif; ?>
    </div>
    <footer>
        <p>&copy; 2025 DivingLog Uygulaması</p>
    </footer>
</body>
</html>