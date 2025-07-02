<?php
include('../../config.php');
$success_message = '';
$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Girdi temizleme
    $full_name = trim($_POST['full_name']);
    $national_id = trim($_POST['national_id']);
    $certificate_name = trim($_POST['certificate_name']);
    $issuing_organization = trim($_POST['issuing_organization']);
    $issue_date = $_POST['issue_date'];
    $expiration_date = $_POST['expiration_date'];
    $certificate_level = trim($_POST['certificate_level']);
    $certificate_number = trim($_POST['certificate_number']);
    $notes = trim($_POST['notes']);

    // Gerekli alan kontrolü
    if (!empty($full_name) && !empty($national_id) && !empty($certificate_name)) {
        // Sertifika numarası kontrolü (varsa)
        if (!empty($certificate_number)) {
            $check_stmt = $mysqlB->prepare("SELECT id FROM certificate WHERE certificate_number = ?");
            if ($check_stmt) {
                $check_stmt->bind_param("s", $certificate_number);
                $check_stmt->execute();
                $check_stmt->store_result();

                if ($check_stmt->num_rows > 0) {
                    $error_message = "Bu sertifika numarası zaten kayıtlıdır.";
                    $check_stmt->close();
                } else {
                    $check_stmt->close();
                    // Sertifika ekleme
                    $stmt = $mysqlB->prepare("INSERT INTO certificate 
                        (full_name, tc, certificate_name, issuing_organization, issue_date, expiration_date, certificate_level, certificate_number, notes)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

                    if ($stmt) {
                        $stmt->bind_param("sssssssss", $full_name, $national_id, $certificate_name, $issuing_organization, $issue_date, $expiration_date, $certificate_level, $certificate_number, $notes);
                        if ($stmt->execute()) {
                            $success_message = "Sertifika başarıyla eklendi.";
                        } else {
                            $error_message = "Veritabanına eklenirken hata: " . $stmt->error;
                        }
                        $stmt->close();
                    } else {
                        $error_message = "Sorgu hazırlanamadı: " . $mysqlB->error;
                    }
                }
            }
        } else {
            // Sertifika numarası yoksa doğrudan ekle
            $stmt = $mysqlB->prepare("INSERT INTO certificate 
                (full_name, tc, certificate_name, issuing_organization, issue_date, expiration_date, certificate_level, certificate_number, notes)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

            if ($stmt) {
                $stmt->bind_param("sssssssss", $full_name, $national_id, $certificate_name, $issuing_organization, $issue_date, $expiration_date, $certificate_level, $certificate_number, $notes);
                if ($stmt->execute()) {
                    $success_message = "Sertifika başarıyla eklendi.";
                } else {
                    $error_message = "Veritabanına eklenirken hata: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $error_message = "Sorgu hazırlanamadı: " . $mysqlB->error;
            }
        }
    } else {
        $error_message = "İsim Soyisim, TC ve Sertifika Adı alanları zorunludur.";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>DivingLog | Sertifika Ekle</title>
    <link rel="stylesheet" href="../CSS/certificate.css">
    <link rel="icon" href="../images/divinglog.png">
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
<form class="certificate" method="POST" action="">
    <?php if ($success_message): ?>
        <div class="success"><?= htmlspecialchars($success_message) ?></div>
    <?php endif; ?>

    <?php if ($error_message): ?>
        <div class="error"><?= htmlspecialchars($error_message) ?></div>
    <?php endif; ?>

    <h2>Sertifika Bilgisi Ekle</h2>

    <label for="full_name">İsim Soyisim:</label>
    <input type="text" name="full_name" id="full_name" required>

    <label for="national_id">TC Kimlik No:</label>
    <input type="text" name="national_id" id="national_id" required pattern="[0-9]{11}" maxlength="11">

    <label for="certificate_name">Sertifika Adı:</label>
    <input type="text" name="certificate_name" id="certificate_name" required>

    <label for="issuing_organization">Veren Kuruluş:</label>
    <input type="text" name="issuing_organization" id="issuing_organization">

    <label for="issue_date">Veriliş Tarihi:</label>
    <input type="date" name="issue_date" id="issue_date">

    <label for="expiration_date">Son Geçerlilik Tarihi:</label>
    <input type="date" name="expiration_date" id="expiration_date">

    <label for="certificate_level">Sertifika Seviyesi:</label>
    <input type="text" name="certificate_level" id="certificate_level">

    <label for="certificate_number">Sertifika Numarası:</label>
    <input type="text" name="certificate_number" id="certificate_number">

    <label for="notes">Notlar:</label>
    <textarea name="notes" id="notes" rows="4"></textarea>

    <input type="submit" value="Sertifikayı Kaydet">
</form>

<footer>
    <p>&copy; 2025 DivingLog Uygulaması</p>
</footer>
<script src="../JS/certificate.js"></script>
</body>
</html>