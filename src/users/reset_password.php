<?php
include('../../config.php');
session_start();

$success_message = '';
$error_message = '';

// Token parametresi URL'den alınıyor
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Token'ın veritabanında var olup olmadığını ve süresinin geçip geçmediğini kontrol et
    $sql = "SELECT * FROM users WHERE reset_token='$token' AND token_expiry > NOW()";
    $result = mysqli_query($mysqlB, $sql);

    if (mysqli_num_rows($result) > 0) {
        // Token geçerli, kullanıcıyı yönlendir
        $user = mysqli_fetch_assoc($result);

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Şifre sıfırlama işlemi
            $new_password = mysqli_real_escape_string($mysqlB, $_POST['new_password']);
            $confirm_password = mysqli_real_escape_string($mysqlB, $_POST['confirm_password']);

            if ($new_password === $confirm_password) {
                // Yeni şifreyi hash'leyerek veritabanına kaydediyoruz
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $update_sql = "UPDATE users SET sifre='$hashed_password', reset_token=NULL, token_expiry=NULL WHERE reset_token='$token'";
                
                if (mysqli_query($mysqlB, $update_sql)) {
                    $success_message = "Şifreniz başarıyla sıfırlandı.";
                } else {
                    $error_message = "Şifreniz sıfırlanırken bir hata oluştu.";
                }
            } else {
                $error_message = "Yeni şifre ve onay şifresi uyuşmuyor.";
            }
        }
    } else {
        // Geçersiz veya süresi geçmiş token
        $error_message = "Geçersiz veya süresi geçmiş şifre sıfırlama bağlantısı.";
    }
} else {
    $error_message = "Geçersiz bağlantı.";
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Şifre Sıfırlama</title>
    <link rel="stylesheet" href="../CSS/authentication.css">
</head>
<body>
    <h1>DivingLog | Şifre Sıfırlama</h1>

    <div class="content">
        <?php if ($success_message): ?>
            <div class="success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <?php if ($error_message): ?>
            <div class="error"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <!-- Şifre sıfırlama formu -->
        <form action="reset_password.php?token=<?php echo htmlspecialchars($token); ?>" method="POST">
            <label for="new_password">Yeni Şifre:</label><br>
            <input type="password" id="new_password" name="new_password" required><br><br>

            <label for="confirm_password">Şifreyi Onayla:</label><br>
            <input type="password" id="confirm_password" name="confirm_password" required><br><br>

            <button type="submit" class="btn">Şifremi Sıfırla</button>
        </form>
    </div>

    <footer>
        <p>&copy; 2025 DivingLog Uygulaması</p>
    </footer>
</body>
</html>