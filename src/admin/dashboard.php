<?php
    include('../../config.php');
    $success_message = '';
    $error_message = '';
    $sql = "SELECT * FROM users";
    $result = mysqli_query($mysqlB, $sql);

    $total_user_sql = "SELECT COUNT(*) as total_users FROM users";
    $sonuc = mysqli_query($mysqlB, $total_user_sql);
    $row = mysqli_fetch_assoc($sonuc);
    $total_users = $row['total_users'];

    $total_diving_sql = "SELECT COUNT(*) as total_diving FROM diving_plans";
    $total_diving_result = mysqli_query($mysqlB, $total_diving_sql);
    $satir = mysqli_fetch_assoc($total_diving_result);
    $total_diving = $satir['total_diving'];

    $total_certificate_sql = "SELECT COUNT(*) as total_certificate FROM certificate";
    $total_certificate_result = mysqli_query($mysqlB, $total_certificate_sql);
    $csatir = mysqli_fetch_assoc($total_certificate_result);
    $total_certificate = $csatir['total_certificate'];
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DivingLog | Admin Paneli</title>
    <link rel="stylesheet" href="../CSS/dashboard.css">
    <link rel="web icon" href="../images/divinglog.png">
</head>
<body>
    <header>
        <h1>DivingLog | Admin Paneli</h1>
        <h2>Admin İşlemlerine Aşağıdan Ulaşabilirsiniz!</h2>
    </header>
    <div class="container">
        <h3>İşlemleri bu kısımdan yapabilirsiniz.</h3>
        <?php if ($success_message): ?>
            <div class="success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <?php if ($error_message): ?>
            <div class="error"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <ul>
            <li><a href="../index.php">Ana Sayfa</a></li>
            <li><a href="manage_users.php">Kullanıcıları Yönet</a></li>
            <li><a href="manage_diving.php">Dalışları Yönet</a></li>
            <li><a href="../users/exit.php">Çıkış Yap</a></li>
        </ul>
        <p>Toplam Kullanıcı Sayısı: <?php echo $total_users; ?></p>
        <p>Toplam Dalış Sayısı: <?php echo $total_diving; ?></p>
        <p>Toplam Verilen Sertifika Sayısı: <?php echo $total_certificate ?></p>
    </div>
    <footer>
        <p>&copy; 2025 DivingLog Uygulaması</p>
    </footer>
</body>
</html>