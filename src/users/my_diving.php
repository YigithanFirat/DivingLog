<?php
include('../session_guard.php');
include('../../config.php');

// Giriş kontrolü
if (!isset($_SESSION['tcno'])) {
    header('Location: ../users/login.php');
    exit;
}

$tcno = $_SESSION['tcno'];

// Kullanıcının dalış planlarını çek
$stmt = mysqli_prepare($mysqlB, "
    SELECT dp.id, dp.created_at, dp.id, dp.depth_meter, dp.minutes, p.name AS diving_location
    FROM diving_plans dp
    LEFT JOIN diving_places p ON dp.id = p.id
    WHERE dp.tcno = ?
    ORDER BY dp.created_at DESC
");

if (!$stmt) {
    die("Sorgu hatası: " . mysqli_error($mysqlB));
}

mysqli_stmt_bind_param($stmt, "s", $tcno);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$dives = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $dives[] = $row;
    }
}
mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dalışlarım | DivingLog</title>
    <link rel="stylesheet" href="../CSS/my_diving.css"/>
    <link rel="icon" href="../images/divinglog.png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>
<body>
    <div class="menu-toggle" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </div>

    <div class="sidebar">
        <h2>Menü</h2>
        <ul>
            <li><a href="../index.php">Ana Sayfa</a></li>
            <li><a href="my_certificate.php">Sertifikalarım</a></li>
            <li><a href="my_diving.php" class="active">Dalışlarım</a></li>
            <li><a href="exit.php">Çıkış Yap</a></li>
        </ul>
    </div>

    <div class="container">
        <h1>Benim Dalışlarım</h1>

        <?php if (count($dives) === 0): ?>
            <p>Henüz kayıtlı bir dalışınız yok.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Tarih</th>
                        <th>Bölge</th>
                        <th>Derinlik (m)</th>
                        <th>Süre (dk)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dives as $dive): ?>
                        <tr>
                            <td><?= htmlspecialchars(date('d.m.Y', strtotime($dive['created_at']))) ?></td>
                            <td><?= htmlspecialchars($dive['diving_location'] ?? 'Bilinmeyen Bölge') ?></td>
                            <td><?= htmlspecialchars($dive['depth_meter']) ?></td>
                            <td><?= htmlspecialchars($dive['minutes']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <footer>
        <p>&copy; 2025 DivingLog Uygulaması</p>
    </footer>

    <script src="../JS/my_diving.js"></script>
</body>
</html>