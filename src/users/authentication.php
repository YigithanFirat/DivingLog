<?php
    require_once '../PHPMailer-master/src/Exception.php';
    require_once '../PHPMailer-master/src/PHPMailer.php';
    require_once '../PHPMailer-master/src/SMTP.php';
    include('../../config.php');
    session_start();
    $success_message = '';
    $error_message = '';
    if($_SERVER["REQUEST_METHOD"] == "POST")
    {
        $email = mysqli_real_escape_string($mysqlB, $_POST['email']);
        if(filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            $sql = "SELECT * FROM users WHERE email='$email'";
            $result = mysqli_query($mysqlB, $sql);
            if(mysqli_num_rows($result) > 0)
            {
                $user = mysqli_fetch_assoc($result);
                $reset_token = bin2hex(random_bytes(32));
                date_default_timezone_set('Europe/Istanbul');
                $token_expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
                $update_sql = "UPDATE users SET reset_token='$reset_token', token_expiry='$token_expiry' WHERE email='$email'";
                if(mysqli_query($mysqlB, $update_sql))
                {
                    $reset_link = "http://localhost/DivingLog/src/users/reset_password.php?token=$reset_token";
                    $mail = new PHPMailer\PHPMailer\PHPMailer();
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = ($email);
                    $mail->Password = 'yaef dgtn euzd dtkj';
                    $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;
                    $mail->setFrom('divinglog@example.com', 'DivingLog');
                    $mail->addAddress($email);
                    $mail->CharSet = 'UTF-8';
                    $mail->Subject = "=?UTF-8?B?" . base64_encode('Şifre Sıfırlama Talebi') . "?=";
                    $mail->Body = "Şifrenizi sıfırlamak için aşağıdaki bağlantıya tıklayın:\n$reset_link";
                    $mail->isHTML(false);
                    $mail->SMTPDebug = 0;
                    if($mail->send())
                    {
                        $success_message = "Şifre sıfırlama bağlantınız e-posta adresinize gönderildi.";
                    }
                    else
                    {
                        $error_message = "E-posta gönderilemedi. Lütfen tekrar deneyin. Hata: " . $mail->ErrorInfo;
                    }
                }
                else
                {
                    $error_message = "Veritabanı güncellenirken bir hata oluştu.";
                }
            }
            else
            {
                $error_message = "Bu e-posta adresiyle kayıtlı bir kullanıcı bulunamadı.";
            }
        }
        else
        {
            $error_message = "Geçersiz e-posta adresi.";
        }
    }
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DivingLog | Şifremi Unuttum</title>
    <link rel="stylesheet" href="../CSS/authentication.css">
    <link rel="web icon" href="../images/divinglog.png">
</head>
<body>
    <h1>DivingLog | Şifremi Unuttum</h1>
    <div class="content">
        <?php if ($success_message): ?>
            <div class="success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <?php if ($error_message): ?>
            <div class="error"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <form action="authentication.php" method="POST">
            <label for="email">E-posta Adresiniz:</label><br>
            <input type="email" id="email" name="email" required><br><br>

            <button type="submit" class="btn">Şifre Sıfırlama Bağlantısı Gönder</button>
        </form>
    </div>
    <footer>
        <p>&copy; 2025 DivingLog Uygulaması</p>
    </footer>
</body>
</html>