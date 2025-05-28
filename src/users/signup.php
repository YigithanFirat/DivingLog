<?php
include('../../config.php');

$success_message = '';
$error_messages = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $ad = trim($_POST['ad'] ?? '');
    $soyad = trim($_POST['soyad'] ?? '');
    $tcno = trim($_POST['tcno'] ?? '');
    $dogum_tarihi = trim($_POST['dogum_tarihi'] ?? '');
    $milliyet = trim($_POST['milliyet'] ?? '');
    $adres = trim($_POST['adres'] ?? '');
    $kaza_haber_kişi_ad_soyad = trim($_POST['kaza_haber_kişi_ad_soyad'] ?? '');
    $telefon = trim($_POST['telefon'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $sifre = $_POST['sifre'] ?? '';
    $fotograf = trim($_POST['fotograf'] ?? '');

    // Validasyonlar
    if (!preg_match('/^\d{11}$/', $tcno)) {
        $error_messages[] = "TC Kimlik Numarası 11 haneli olmalıdır.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_messages[] = "Geçerli bir e-posta adresi girin.";
    }
    if (strlen($sifre) < 6) {
        $error_messages[] = "Şifre en az 6 karakter olmalıdır.";
    }

    // TCNO kontrolü
    $tcno_check_query = mysqli_prepare($mysqlB, "SELECT id FROM users WHERE tcno = ?");
    if ($tcno_check_query) {
        mysqli_stmt_bind_param($tcno_check_query, "s", $tcno);
        mysqli_stmt_execute($tcno_check_query);
        mysqli_stmt_store_result($tcno_check_query);

        if (mysqli_stmt_num_rows($tcno_check_query) > 0) {
            $error_messages[] = "Bu TC Kimlik Numarası ile daha önce kayıt olunmuş.";
        }

        mysqli_stmt_close($tcno_check_query);
    } else {
        $error_messages[] = "Veritabanı kontrolü sırasında hata oluştu.";
    }

    if (empty($error_messages)) {
        $hashed_sifre = password_hash($sifre, PASSWORD_DEFAULT);

        $stmt = mysqli_prepare($mysqlB, "INSERT INTO users (ad, soyad, tcno, dogum_tarihi, milliyet, adres, kaza_haber_kişi_ad_soyad, telefon, email, sifre, fotograf) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "sssssssssss", $ad, $soyad, $tcno, $dogum_tarihi, $milliyet, $adres, $kaza_haber_kişi_ad_soyad, $telefon, $email, $hashed_sifre, $fotograf);

            if (mysqli_stmt_execute($stmt)) {
                $success_message = "Kayıt başarılı! Giriş yapmak için <a href='login.php' class='login-link'>buraya tıklayın</a>.";
            } else {
                $error_messages[] = "Kayıt sırasında hata oluştu: " . mysqli_stmt_error($stmt);
            }
            mysqli_stmt_close($stmt);
        } else {
            $error_messages[] = "Veritabanı sorgusu hazırlanamadı.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Kayıt Ol | DivingLog</title>
    <link rel="stylesheet" href="../CSS/signup.css" />
    <link rel="icon" href="../images/divinglog.png" />
</head>
<body>
    <h1>DivingLog | Kayıt Ol</h1>
    <h2>Yeni Hesap Oluştur</h2>
    <div class="content">

        <?php if (!empty($error_messages) || $success_message): ?>
            <div class="<?= $success_message ? 'success' : 'error' ?>">
                <?php if ($success_message): ?>
                    <?= $success_message ?>
                <?php else: ?>
                    <ul>
                        <?php foreach ($error_messages as $msg): ?>
                            <li><?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if (!$success_message): ?>
        <form action="signup.php" method="POST">
            <div class="form-row">
                <label for="ad">Ad:</label>
                <input type="text" id="ad" name="ad" required />
                <label for="soyad">Soyad:</label>
                <input type="text" id="soyad" name="soyad" required />
            </div>
            <div class="form-row">
                <label for="dogum_tarihi">Doğum Tarihi:</label>
                <input type="date" id="dogum_tarihi" name="dogum_tarihi" required />
                <label for="milliyet">Milliyet:</label>
                <input type="text" id="milliyet" name="milliyet" required />
            </div>
            <label for="tcno">TC Kimlik Numarası:</label><br />
            <input type="text" id="tcno" name="tcno" required pattern="\d{11}" title="Lütfen 11 haneli bir TC Kimlik Numarası girin." /><br /><br />

            <label for="adres">Adres:</label><br />
            <textarea id="adres" name="adres" required></textarea><br /><br />

            <label for="kaza_haber_kişi_ad_soyad">Kaza Halinde Haber Verilecek Kişi:</label>
            <input type="text" id="kaza_haber_kişi_ad_soyad" name="kaza_haber_kişi_ad_soyad" required /><br /><br />

            <label for="telefon">Telefon Numarası:</label><br />
            <input type="text" id="telefon" name="telefon" required pattern="^\+?\d{10,15}$" title="Telefon numarasını +90xxxxxxxxxx formatında girin" /><br /><br />

            <label for="fotograf">Fotoğraf:</label>
            <input type="text" id="fotograf" name="fotograf" required /><br /><br />

            <label for="email">E-posta:</label><br />
            <input type="email" id="email" name="email" required /><br /><br />

            <label for="sifre">Şifre:</label><br />
            <input type="password" id="sifre" name="sifre" required /><br /><br />

            <button type="submit" class="btn">Kayıt Ol</button>
        </form>
        <?php endif; ?>
    </div>

    <footer>
        <p>&copy; 2025 DivingLog Uygulaması</p>
    </footer>
</body>
</html>