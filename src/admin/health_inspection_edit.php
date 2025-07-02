<?php
session_start();
include('../../config.php');

$success = false;
$error = false;
$errorMessage = '';

// ID ile kayıt getir
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);

    $stmt = mysqli_prepare($mysqlB, "SELECT muayene_tarihi, onaylayan, onaylanan FROM health_inspections WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $muayene_tarihi, $onaylayan, $onaylanan);
    $found = mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    if (!$found) {
        $error = true;
        $errorMessage = "❌ Kayıt bulunamadı.";
    }
} else {
    $error = true;
    $errorMessage = "❌ Geçersiz ID.";
}

// Güncelleme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$error) {
    $muayene_tarihi = $_POST['muayene_tarihi'] ?? '';
    $onaylayan = trim($_POST['onaylayan'] ?? '');
    $onaylanan = trim($_POST['onaylanan'] ?? '');

    if (!empty($muayene_tarihi) && !empty($onaylayan) && !empty($onaylanan)) {
        $stmt = mysqli_prepare($mysqlB, "UPDATE health_inspections SET muayene_tarihi = ?, onaylayan = ?, onaylanan = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "sssi", $muayene_tarihi, $onaylayan, $onaylanan, $id);
        $success = mysqli_stmt_execute($stmt);
        if (!$success) {
            $error = true;
            $errorMessage = "❌ Güncelleme sırasında bir hata oluştu.";
        }
        mysqli_stmt_close($stmt);
    } else {
        $error = true;
        $errorMessage = "❌ Tüm alanları doldurduğunuzdan emin olun.";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>DivingLog | Sağlık Raporu Düzenle</title>
    <link rel="stylesheet" href="../CSS/health_inspection_edit.css">
    <link rel="icon" href="../images/divinglog.png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    <div class="menu-toggle" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </div>
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <ul>
            <li><a href="../index.php">Ana Sayfa</a></li>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="manage_users.php">Kullanıcıları Yönet</a></li>
            <li><a href="diving.php">Dalış Oluştur</a></li>
            <li><a href="manage_diving.php">Dalışları Yönet</a></li>
            <li><a href="certificate.php">Sertifika Oluştur</a></li>
            <li><a href="certificate_list.php">Sertifikaları Listele</a></li>
            <li><a href="health_inspection.php">Sağlık Raporu Oluştur</a></li>
            <li><a href="health_inspection_list.php">Sağlık Raporlarını Listele</a></li>
            <li><a href="../users/exit.php">Çıkış Yap</a></li>
        </ul>
    </div>
<div class="content">
    <h1>Sağlık Raporu Düzenle</h1>

    <?php if ($success): ?>
        <p class="success">✔️ Rapor başarıyla güncellendi.</p>
    <?php elseif ($error): ?>
        <p class="error"><?= htmlspecialchars($errorMessage) ?></p>
    <?php endif; ?>

    <?php if (!$error || $_SERVER['REQUEST_METHOD'] === 'POST'): ?>
    <form method="POST" class="form">
        <label for="muayene_tarihi">Muayene Tarihi:</label>
        <input type="date" name="muayene_tarihi" id="muayene_tarihi" value="<?= htmlspecialchars($muayene_tarihi ?? '') ?>" required>

        <label for="onaylayan">Onaylayan Doktor:</label>
        <input type="text" name="onaylayan" id="onaylayan" value="<?= htmlspecialchars($onaylayan ?? '') ?>" required>

        <label for="onaylanan">Onaylanan Kişi:</label>
        <input type="text" name="onaylanan" id="onaylanan" value="<?= htmlspecialchars($onaylanan ?? '') ?>" required>

        <div class="btn-container">
            <button type="submit" class="btn">💾 Güncelle</button>
            <a href="health_inspection_list.php" class="btn">⬅️ Listeye Geri Dön</a>
        </div>
    </form>
    <?php endif; ?>
</div>

<footer>
    <p>&copy; 2025 DivingLog Uygulaması</p>
</footer>
<script src="../JS/health_inspection_edit.js"></script>
</body>
</html>