<?php
    include('../../config.php'); // Veritabanı bağlantısı burada tanımlı olmalı
    $success_message = '';
    $error_message = '';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $user_id = $_POST['user_id'];
        $certificate_name = $_POST['certificate_name'];
        $issuing_organization = $_POST['issuing_organization'];
        $issue_date = $_POST['issue_date'];
        $expiration_date = $_POST['expiration_date'];
        $certificate_level = $_POST['certificate_level'];
        $certificate_number = $_POST['certificate_number'];
        $notes = $_POST['notes'];

        // Güvenlik için veri doğrulama ve boş alan kontrolü
        if (!empty($user_id) && !empty($certificate_name)) {
            $stmt = $mysqlB->prepare("INSERT INTO certificate 
                (user_id, certificate_name, issuing_organization, issue_date, expiration_date, certificate_level, certificate_number, notes)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

            $stmt->bind_param("isssssss", $user_id, $certificate_name, $issuing_organization, $issue_date, $expiration_date, $certificate_level, $certificate_number, $notes);

            if ($stmt->execute()) {
                $success_message = "Sertifika başarıyla eklendi.";
            } else {
                $error_message = "Hata oluştu: " . $stmt->error;
            }

            $stmt->close();
        } else {
            $error_message = "Kullanıcı ve Sertifika Adı alanları zorunludur.";
        }
    }
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DivingLog | Sertifika Ekle</title>
    <link rel="stylesheet" href="../CSS/certificate.css">
    <link rel="web icon" href="../images/divinglog.png">
</head>
<body>

    <?php if ($success_message): ?>
        <div class="success"><?php echo $success_message; ?></div>
    <?php endif; ?>

    <?php if ($error_message): ?>
        <div class="error"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <h2>Sertifika Bilgisi Ekle</h2>

        <label for="user_id">Kullanıcı ID:</label>
        <input type="number" name="user_id" id="user_id" required>

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
</body>
</html>
