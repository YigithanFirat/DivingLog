<?php
session_start();
include('../../config.php');

$success = false;
$error = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $muayene_tarihi = $_POST['muayene_tarihi'] ?? '';
    $onaylayan = trim($_POST['onaylayan'] ?? '');
    $onaylanan = trim($_POST['onaylanan'] ?? '');
    $created_at = date('Y-m-d H:i:s');

    if (!empty($muayene_tarihi) && !empty($onaylayan) && !empty($onaylanan)) {
        $stmt = mysqli_prepare($mysqlB, "INSERT INTO health_inspections (muayene_tarihi, onaylayan, onaylanan, created_at) VALUES (?, ?, ?, ?)");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ssss", $muayene_tarihi, $onaylayan, $onaylanan, $created_at);
            $success = mysqli_stmt_execute($stmt);
            if (!$success) $error = true;
            mysqli_stmt_close($stmt);
        } else {
            $error = true;
        }
    } else {
        $error = true;
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8" />
    <title>DivingLog | Sağlık Raporu Ekleme</title>
    <link rel="stylesheet" href="../CSS/health_inspection.css" />
    <link rel="icon" href="../images/divinglog.png" />
</head>
<body>
    <div class="content">
        <h1>Sağlık Raporu Kaydı</h1>

        <?php if ($success): ?>
            <p class="success">✔️ Sağlık Raporu başarıyla kaydedildi.</p>
        <?php elseif ($error): ?>
            <p class="error">❌ Lütfen tüm alanları doğru doldurduğunuzdan emin olun.</p>
        <?php endif; ?>

        <form method="POST" class="form" novalidate>
            <label for="muayene_tarihi">Muayene Tarihi:</label>
            <input type="date" id="muayene_tarihi" name="muayene_tarihi" required>

            <label for="onaylayan">Onaylayan Doktor:</label>
            <input type="text" id="onaylayan" name="onaylayan" placeholder="Dr. Adı Soyadı" required>

            <label for="onaylanan">Onaylanan Kişi:</label>
            <input type="text" id="onaylanan" name="onaylanan" placeholder="Onaylanan Kişi Adı Soyadı" required>

            <div class="btn-container">
                <button type="submit" class="btn">Kaydet</button>
                <a href="../index.php" class="btn">⬅️ Geri Dön</a>
            </div>
        </form>
    </div>

    <footer>
        <p>&copy; 2025 DivingLog Uygulaması</p>
    </footer>
</body>
</html>