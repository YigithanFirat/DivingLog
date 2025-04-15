<?php
    session_start();
    include('../config.php');
    $logged_in = false;
    $ag = false;
    if(isset($_SESSION['tcno']))
    {
        $tcno = $_SESSION['tcno'];
        $sql = "SELECT login, admin FROM users WHERE tcno='$tcno'";
        $result = mysqli_query($mysqlB, $sql);
        if(mysqli_num_rows($result) > 0)
        {
            $user = mysqli_fetch_assoc($result);
            if($user['login'] == 1)
            {
                $logged_in = true;
            }
            if($user['admin'] == 1)
            {
                $ag = true;
            }
        }
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
        <?php if(!$logged_in): ?>
            <a href="users/login.php" class="btn">Giriş Yap</a>
            <a href="users/signup.php" class="btn">Kaydol</a>
        <?php endif; ?>
        <?php if($logged_in): ?>
            <a href="users/exit.php" class="btn">Çıkış Yap</a>
            <?php if($ag): ?>
                <a href="admin/dashboard.php" class="btn">Dashboard</a>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <footer>
        <p>&copy; 2025 DivingLog Uygulaması</p>
    </footer>
</body>
</html>