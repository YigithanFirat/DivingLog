<?php
session_start();

require_once '../PHPMailer-master/src/Exception.php';
require_once '../PHPMailer-master/src/PHPMailer.php';
require_once '../PHPMailer-master/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include('../../config.php');

$success_message = '';
$error_message = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email'] ?? '');

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Hazır ifade (prepared statement) ile sorgu:
        $stmt = $mysqlB->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();

            try {
                $reset_token = bin2hex(random_bytes(32));
            } catch (Exception $e) {
                $error_message = "Güvenlik hatası oluştu, lütfen tekrar deneyin.";
            }

            if (!$error_message) {
                date_default_timezone_set('Europe/Istanbul');
                $token_expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

                // Token güncelleme sorgusu - yine prepared statement ile
                $update_stmt = $mysqlB->prepare("UPDATE users SET reset_token = ?, token_expiry = ? WHERE email = ?");
                $update_stmt->bind_param('sss', $reset_token, $token_expiry, $email);

                if ($update_stmt->execute()) {
                    // NOT: Burada kesinlikle HTTPS kullanılmalı. Örnek localhost demo amaçlıdır.
                    $reset_link = "https://yourdomain.com/DivingLog/src/users/reset_password.php?token=" . urlencode($reset_token);

                    $mail = new PHPMailer(true);

                    try {
                        $mail->isSMTP();
                        $mail->Host = 'smtp.gmail.com';
                        $mail->SMTPAuth = true;
                        // KULLANICI ADI VE ŞİFREYİ config.php veya environment değişkenlerinden alın
                        $mail->Username = 'your-email@gmail.com';  
                        $mail->Password = 'your-email-app-password'; 
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                        $mail->Port = 587;

                        $mail->setFrom('divinglog@example.com', 'DivingLog');
                        $mail->addAddress($email);

                        $mail->CharSet = 'UTF-8';
                        $mail->Subject = 'Şifre Sıfırlama Talebi';
                        $mail->Body = "Şifrenizi sıfırlamak için aşağıdaki bağlantıya tıklayın:\n$reset_link";
                        $mail->isHTML(false);

                        $mail->send();
                        $success_message = "Şifre sıfırlama bağlantınız e-posta adresinize gönderildi.";
                    } catch (Exception $e) {
                        $error_message = "E-posta gönderilemedi, lütfen daha sonra tekrar deneyin.";
                    }
                } else {
                    $error_message = "Veritabanı güncellenirken bir hata oluştu.";
                }
            }
        } else {
            // Kullanıcı yok mesajı genel bırakıldı (bilgi sızdırmamak için)
            $success_message = "Şifre sıfırlama bağlantınız e-posta adresinize gönderildi.";
            // Gerçek hayatta burada saldırı tespiti yapılabilir.
        }
        $stmt->close();
    } else {
        $error_message = "Lütfen geçerli bir e-posta adresi giriniz.";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>DivingLog | Şifremi Unuttum</title>
    <link rel="stylesheet" href="../CSS/authentication.css" />
    <link rel="icon" href="../images/divinglog.png" />
</head>
<body>
    <h1>DivingLog | Şifremi Unuttum</h1>
    <div class="content">
        <?php if ($success_message): ?>
            <div class="success"><?= htmlspecialchars($success_message, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>
        <?php if ($error_message): ?>
            <div class="error"><?= htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>
        <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST" novalidate>
            <label for="email">E-posta Adresiniz:</label><br />
            <input type="email" id="email" name="email" required /><br /><br />
            <button type="submit" class="btn">Şifre Sıfırlama Bağlantısı Gönder</button>
        </form>
    </div>
    <footer>
        <p>&copy; 2025 DivingLog Uygulaması</p>
    </footer>
</body>
</html>