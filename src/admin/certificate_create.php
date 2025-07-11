<?php
include('../../config.php');
include('../session_guard.php');
include('../sidebarmenu.php');

// Giriş kontrolü
if (!isset($_SESSION['tcno'])) {
    header("Location: ../users/login.php");
    exit();
}

$error = '';
$success = '';
$full_name = '';
$tc = '';

// Eğer URL'de user_id varsa, veritabanından bilgileri al
if (isset($_GET['user_id']) && is_numeric($_GET['user_id'])) {
    $user_id = intval($_GET['user_id']);
    $stmt = $mysqlB->prepare("SELECT ad, soyad, tcno FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($user = $result->fetch_assoc()) {
        $full_name = $user['ad'] . ' ' . $user['soyad'];
        $tc = $user['tcno'];
    }
    $stmt->close();
}

// Sertifika oluşturma işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Form verilerini al
    $full_name = trim($_POST['full_name'] ?? '');
    $tc = trim($_POST['tc'] ?? '');
    $certificate_name = trim($_POST['certificate_name'] ?? '');
    $issuing_organization = trim($_POST['issuing_organization'] ?? '');
    $issue_date = $_POST['issue_date'] ?? null;
    $expiration_date = $_POST['expiration_date'] ?? null;
    $certificate_level = trim($_POST['certificate_level'] ?? '');
    $certificate_number = trim($_POST['certificate_number'] ?? '');
    $notes = trim($_POST['notes'] ?? '');

    // Zorunlu alan kontrolü
    if ($full_name === '' || $tc === '' || $certificate_name === '') {
        $error = "Ad Soyad, TC ve Sertifika Adı zorunludur.";
    } else {
        // Hazırlıklı sorgu
        $stmt = $mysqlB->prepare("INSERT INTO certificate 
            (full_name, tc, certificate_name, issuing_organization, issue_date, expiration_date, certificate_level, certificate_number, notes)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->bind_param("sssssssss", 
            $full_name, $tc, $certificate_name, $issuing_organization, 
            $issue_date, $expiration_date, $certificate_level, 
            $certificate_number, $notes
        );

        if ($stmt->execute()) {
            $success = "Sertifika başarıyla oluşturuldu.";
            // Form temizle
            $certificate_name = $issuing_organization = $issue_date = $expiration_date = $certificate_level = $certificate_number = $notes = '';
        } else {
            if ($stmt->errno === 1062) {
                $error = "Bu sertifika numarası zaten kayıtlı.";
            } else {
                $error = "Hata oluştu: " . $stmt->error;
            }
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Sertifika Oluştur</title>
    <link rel="stylesheet" href="../CSS/certificate_create.css">
    <link rel="web icon" href="../images/divinglog.png">
</head>
<body>
    <div class="container">
        <h2>Yeni Sertifika Oluştur</h2>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php elseif ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <label>Ad Soyad *</label>
            <input type="text" name="full_name" value="<?= htmlspecialchars($full_name) ?>" required>

            <label>TC Kimlik No *</label>
            <input type="text" name="tc" maxlength="11" pattern="\d{11}" value="<?= htmlspecialchars($tc) ?>" required>

            <label>Sertifika Adı *</label>
            <input type="text" name="certificate_name" value="<?= htmlspecialchars($certificate_name ?? '') ?>" required>

            <label>Veren Kurum</label>
            <input type="text" name="issuing_organization" value="<?= htmlspecialchars($issuing_organization ?? '') ?>">

            <label>Veriliş Tarihi</label>
            <input type="date" name="issue_date" value="<?= htmlspecialchars($issue_date ?? '') ?>">

            <label>Geçerlilik Tarihi</label>
            <input type="date" name="expiration_date" value="<?= htmlspecialchars($expiration_date ?? '') ?>">

            <label>Sertifika Seviyesi</label>
            <input type="text" name="certificate_level" value="<?= htmlspecialchars($certificate_level ?? '') ?>">

            <label>Sertifika Numarası</label>
            <input type="text" name="certificate_number" value="<?= htmlspecialchars($certificate_number ?? '') ?>">

            <label>Notlar</label>
            <textarea name="notes" rows="3"><?= htmlspecialchars($notes ?? '') ?></textarea>

            <button class="btn" type="submit">Kaydet</button>
        </form>
    </div>
    <footer>
        <p>&copy; 2025 DivingLog Uygulaması</p>
    </footer>
</body>
</html>