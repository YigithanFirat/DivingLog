<?php
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    <div class="menu-toggle" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </div>
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <ul>
            <li><a href="../index.php"><i class="fas fa-home"></i> Ana Sayfa</a></li>
            <li><a href="dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a></li>
            <li><a href="manage_users.php"><i class="fas fa-users"></i> Öğrencileri Listesi</a></li>
            <li><a href="diving.php"><i class="fas fa-water"></i> Dalış Oluştur</a></li>
            <li><a href="manage_diving.php"><i class="fas fa-database"></i> Dalışları Yönet</a></li>
            <li><a href="diving_place.php"><i class="fas fa-map-marker-alt"></i> Dalış Bölgeleri</a></li>
            <li><a href="certificate.php"><i class="fas fa-certificate"></i> Sertifika Oluştur</a></li>
            <li><a href="certificate_list.php"><i class="fas fa-list"></i> Sertifikaları Listele</a></li>
            <li><a href="health_inspection.php"><i class="fas fa-notes-medical"></i> Sağlık Raporu Oluştur</a></li>
            <li><a href="health_inspection_list.php"><i class="fas fa-clipboard-list"></i> Sağlık Raporlarını Listele</a></li>
            <li><a href="../users/exit.php"><i class="fas fa-sign-out-alt"></i> Çıkış Yap</a></li>
        </ul>
    </div>
</body>
</html>