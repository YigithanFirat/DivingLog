<?php
    include('../../config.php');
    $success_message = '';
    $error_message = '';

    // Kullanıcı listesini çek (görüntülenmiyorsa bu sorguya gerek olmayabilir)
    $stmt_users = $mysqlB->prepare("SELECT * FROM users");
    $stmt_users->execute();
    $result = $stmt_users->get_result();

    // Toplam kullanıcı sayısı
    $stmt_total_users = $mysqlB->prepare("SELECT COUNT(*) as total_users FROM users");
    $stmt_total_users->execute();
    $stmt_total_users->bind_result($total_users);
    $stmt_total_users->fetch();
    $stmt_total_users->close();

    // Toplam dalış sayısı
    $stmt_total_diving = $mysqlB->prepare("SELECT COUNT(*) as total_diving FROM diving_plans");
    $stmt_total_diving->execute();
    $stmt_total_diving->bind_result($total_diving);
    $stmt_total_diving->fetch();
    $stmt_total_diving->close();

    // Toplam sertifika sayısı
    $stmt_total_certificate = $mysqlB->prepare("SELECT COUNT(*) as total_certificate FROM certificate");
    $stmt_total_certificate->execute();
    $stmt_total_certificate->bind_result($total_certificate);
    $stmt_total_certificate->fetch();
    $stmt_total_certificate->close();
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
            <div class="success"><?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>
        <?php if ($error_message): ?>
            <div class="error"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        <ul>
            <li><a href="../index.php">Ana Sayfa</a></li>
            <li><a href="manage_users.php">Kullanıcıları Yönet</a></li>
            <li><a href="manage_diving.php">Dalışları Yönet</a></li>
            <li><a href="../users/exit.php">Çıkış Yap</a></li>
        </ul>
        <p>Toplam Kullanıcı Sayısı: <?php echo htmlspecialchars($total_users); ?></p>
        <p>Toplam Dalış Sayısı: <?php echo htmlspecialchars($total_diving); ?></p>
        <p>Toplam Verilen Sertifika Sayısı: <?php echo htmlspecialchars($total_certificate); ?></p>
    </div>
    <footer>
        <p>&copy; 2025 DivingLog Uygulaması</p>
    </footer>
</body>
</html>