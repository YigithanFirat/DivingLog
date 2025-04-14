<?php
include('../../config.php');
$success_message = '';
$error_message = '';

if($_SERVER["REQUEST_METHOD"] == "POST")
{
    $tcno = mysqli_real_escape_string($mysqlB, $_POST['tcno']);
    $sifre = mysqli_real_escape_string($mysqlB, $_POST['sifre']);
    $sql = "SELECT * FROM users WHERE tcno='$tcno'";
    $result = mysqli_query($mysqlB, $sql);
    if(mysqli_num_rows($result) > 0)
    {
        $user = mysqli_fetch_assoc($result);
        if(password_verify($sifre, $user['sifre']))
        {
            $success_message = "Giriş başarılı! Yönlendiriliyorsunuz...";
            header("Location: ../index.php");
            exit();
        }
        else
        {
            $error_message = "Geçersiz şifre.";
        }
    }
    else
    {
        $error_message = "Kullanıcı bulunamadı.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DivingLog | Giriş Yap</title>
    <link rel="stylesheet" href="../CSS/login.css">
    <link rel="web icon" href="../images/divinglog.png">
</head>
<body>
    <h1>DivingLog | Giriş Yap</h1>
    <div class="content">
        <?php if($success_message): ?>
            <div class="success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <?php if($error_message): ?>
            <div class="error"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <form action="login.php" method="POST">
            <label for="tcno">TC Kimlik Numarası:</label><br>
            <input type="text" id="tcno" name="tcno" required><br><br>

            <label for="sifre">Şifre:</label><br>
            <input type="password" id="sifre" name="sifre" required><br><br>

            <button type="submit" class="btn">Giriş Yap</button>
        </form>
        <p>Hesabınız yok mu? <a href="signup.php" class="signup-link">Kayıt Ol</a></p>
    </div>
    <footer>
        <p>&copy; 2025 DivingLog Uygulaması</p>
    </footer>
</body>
</html>