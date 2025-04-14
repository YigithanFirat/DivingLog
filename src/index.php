<?php
    $hostname = "localhost";
    $user = "root";
    $password = "[priadon1.5]";
    $database = "divinglog";

    $mysqlB = mysqli_connect($hostname, $user, $password, $database);
    if(mysqli_connect_error() == 0)
    {
        //echo "Veritabanı bağlantısı başarılı!";
    }
    else
    {
        //echo "Veritabanı bağlantısı başarısız! HATA" .mysqli_connect_error();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DivingLog | WebApp</title>
    <link rel="stylesheet" href="CSS/index.css">
    <link rel="web icon" href="images/divinglog.png">
</head>
<body>
    <h1>DivingLog</h1>
    <h2>Web Uygulamasına Hoşgeldiniz</h2>

    <div class="content">
        <p>Web uygulamanızda dalış geçmişinizi kaydedebilir ve yönetebilirsiniz.</p>
        <a href="users/login.php" class="btn">Giriş Yap</a>
        <a href="users/signup.php" class="btn">Kayıt Ol</a>
    </div>

    <footer>
        <p>&copy; 2025 DivingLog Uygulaması</p>
    </footer>
</body>
</html>