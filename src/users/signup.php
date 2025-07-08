<?php
include('../session_guard.php');
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
    $tekrarsifre = $_POST['tekrarsifre'] ?? '';

    // Validasyonlar
    if (!preg_match('/^\d{11}$/', $tcno)) {
        $error_messages[] = "TC Kimlik Numarası 11 haneli olmalıdır.";
    }
    if ($sifre !== $tekrarsifre) {
        $error_messages[] = "Şifreler uyuşmuyor. Lütfen aynı şifreyi iki kez girin.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_messages[] = "Geçerli bir e-posta adresi girin.";
    }
    if (strlen($sifre) < 6) {
        $error_messages[] = "Şifre en az 6 karakter olmalıdır.";
    }

    // TCNO tekrar kontrolü
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

    // Fotoğraf yükleme
    $foto_yolu = '';
    if (isset($_FILES['fotograf']) && $_FILES['fotograf']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $tmpName = $_FILES['fotograf']['tmp_name'];
        $fileName = basename($_FILES['fotograf']['name']);
        $targetPath = $uploadDir . time() . '_' . $fileName;

        $fileType = mime_content_type($tmpName);
        if (strpos($fileType, 'image') !== 0) {
            $error_messages[] = "Yüklenen dosya bir resim olmalıdır.";
        } elseif (move_uploaded_file($tmpName, $targetPath)) {
            $foto_yolu = $targetPath;
        } else {
            $error_messages[] = "Fotoğraf yüklenemedi.";
        }
    } else {
        $error_messages[] = "Fotoğraf seçilmedi ya da yükleme hatası oluştu.";
    }

    // Captcha kontrolü
    $recaptcha_secret = '6LcpwGwrAAAAAHRXcDsC1bLEbk_RBFGihKTm7NI6';
    $recaptcha_response = $_POST['g-recaptcha-response'] ?? '';
    $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=" . $recaptcha_secret . "&response=" . $recaptcha_response);
    $response_data = json_decode($verify);
    if (!$response_data->success) {
        $error_messages[] = "Lütfen reCAPTCHA doğrulamasını geçin.";
    }

    // Kayıt işlemi
    if (empty($error_messages)) {
        $hashed_sifre = password_hash($sifre, PASSWORD_DEFAULT);

        $stmt = mysqli_prepare($mysqlB, "INSERT INTO users (ad, soyad, tcno, dogum_tarihi, milliyet, adres, kaza_haber_kişi_ad_soyad, telefon, email, sifre, fotograf) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "sssssssssss", $ad, $soyad, $tcno, $dogum_tarihi, $milliyet, $adres, $kaza_haber_kişi_ad_soyad, $telefon, $email, $hashed_sifre, $foto_yolu);

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
        <form action="signup.php" method="POST" enctype="multipart/form-data">
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

            <label for="fotograf">Fotoğraf Yükle:</label><br />
            <input type="file" id="fotograf" name="fotograf" accept="image/*" required /><br /><br />

            <label for="email">E-posta:</label><br />
            <input type="email" id="email" name="email" required /><br /><br />

            <label for="sifre">Şifre:</label><br />
            <input type="password" id="sifre" name="sifre" required /><br /><br />

            <label for="tekrarsifre">Şifre Onayla:</label><br />
            <input type="password" id="tekrarsifre" name="tekrarsifre" required /><br /><br /> 

            <div class="g-recaptcha" data-sitekey="6LcpwGwrAAAAAA2kUVfXGEpbnE0WmdFXu0DDdfF7"></div><br /><br /> 

            <button type="submit" class="btn">Kayıt Ol</button>
        </form>
        <?php endif; ?>
    </div>

    <footer>
        <p>&copy; 2025 DivingLog Uygulaması</p>
    </footer>
    <script src="../JS/signup.js"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</body>
</html>