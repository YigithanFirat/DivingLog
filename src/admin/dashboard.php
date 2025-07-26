<?php
include('../session_guard.php');
include('../../config.php');
include('../sidebarmenu.php');
$success_message = '';
$error_message = '';

// Sayılar
$stmt_total_users = $mysqlB->prepare("SELECT COUNT(*) as total_users FROM users");
$stmt_total_users->execute();
$stmt_total_users->bind_result($total_users);
$stmt_total_users->fetch();
$stmt_total_users->close();

$stmt_total_diving = $mysqlB->prepare("SELECT COUNT(*) as total_diving FROM diving_plans");
$stmt_total_diving->execute();
$stmt_total_diving->bind_result($total_diving);
$stmt_total_diving->fetch();
$stmt_total_diving->close();

$stmt_total_certificate = $mysqlB->prepare("SELECT COUNT(*) as total_certificate FROM certificate");
$stmt_total_certificate->execute();
$stmt_total_certificate->bind_result($total_certificate);
$stmt_total_certificate->fetch();
$stmt_total_certificate->close();

$stmt_total_health_inspection = $mysqlB->prepare("SELECT COUNT(*) as total_health_inspections FROM health_inspections");
$stmt_total_health_inspection->execute();
$stmt_total_health_inspection->bind_result($total_health_inspections);
$stmt_total_health_inspection->fetch();
$stmt_total_health_inspection->close();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>DivingLog | Admin Paneli</title>
    <link rel="stylesheet" href="../CSS/dashboard.css">
    <link rel="icon" href="../images/divinglog.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    <div class="wrapper">
        <div class="main-content">
            <div class="container">
                <?php if ($success_message): ?>
                    <div class="success"><?php echo htmlspecialchars($success_message); ?></div>
                <?php endif; ?>
                <?php if ($error_message): ?>
                    <div class="error"><?php echo htmlspecialchars($error_message); ?></div>
                <?php endif; ?>

                <div class="dashboard-cards">
                    <!-- Kullanıcı Sayısı -->
                    <div class="total-user-count">
                        <div class="count-text">
                            <p class="count"><?php echo htmlspecialchars($total_users); ?></p>
                            <p class="label">Toplam Öğrenci Sayısı</p>
                        </div>
                        <div class="icon">
                            <i class="fa-solid fa-users"></i>
                        </div>
                        <a href="../admin/manage_users.php" class="more-info">Daha Fazla <i class="fa fa-arrow-circle-right"></i></a>
                    </div>

                    <!-- Dalış Sayısı -->
                    <div class="total-diving-count">
                        <div class="count-text">
                            <p class="count"><?php echo htmlspecialchars($total_diving); ?></p>
                            <p class="label">Toplam Dalış Sayısı</p>
                        </div>
                        <div class="icon">
                            <i class="fa-solid fa-water"></i>
                        </div>
                        <a href="../admin/manage_diving.php" class="more-info">Daha Fazla <i class="fa fa-arrow-circle-right"></i></a>
                    </div>

                    <!-- Sertifika Sayısı -->
                    <div class="total-certificate-count">
                        <div class="count-text">
                            <p class="count"><?php echo htmlspecialchars($total_certificate); ?></p>
                            <p class="label">Toplam Sertifika Sayısı</p>
                        </div>
                        <div class="icon">
                            <i class="fa-solid fa-certificate"></i>
                        </div>
                        <a href="../admin/certificate_list.php" class="more-info">Daha Fazla <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                    <div class="total-health-inspections-count">
                        <div class="count-text">
                            <p class="count"><?php echo htmlspecialchars($total_health_inspections); ?></p>
                            <p class="label">Toplam Rapor Sayısı</p>
                        </div>
                        <div class="icon">
                            <i class="fa-solid fa-flag"></i>
                        </div>
                        <a href="../admin/health_inspection_list.php" class="more-info">Daha Fazla <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>

            <footer>
                <p>&copy; 2025 DivingLog Uygulaması</p>
            </footer>
        </div>
    </div>
    <script src="../JS/dashboard.js"></script>
</body>
</html>