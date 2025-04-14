<?php
include('../../config.php');
$success_message = '';
$error_message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ad = mysqli_real_escape_string($mysqlB, $_POST['ad']);
    $soyad = mysqli_real_escape_string($mysqlB, $_POST['soyad']);
    $tcno = mysqli_real_escape_string($mysqlB, $_POST['tcno']);
    $dogum_tarihi = mysqli_real_escape_string($mysqlB, $_POST['dogum_tarihi']);
    $milliyet = mysqli_real_escape_string($mysqlB, $_POST['milliyet']);
    $adres = mysqli_real_escape_string($mysqlB, $_POST['adres']);
    $kaza_haber_kişi_ad_soyad = mysqli_real_escape_string($mysqlB, $_POST['kaza_haber_kişi_ad_soyad']);
    $telefon = mysqli_real_escape_string($mysqlB, $_POST['telefon']);
    $email = mysqli_real_escape_string($mysqlB, $_POST['email']);  // Yeni eklenen email
    $sifre = mysqli_real_escape_string($mysqlB, $_POST['sifre']);
    $hashed_sifre = password_hash($sifre, PASSWORD_DEFAULT);
    
    // Veritabanına kullanıcıyı ekleyen SQL sorgusu
    $sql = "INSERT INTO users (ad, soyad, tcno, dogum_tarihi, milliyet, adres, kaza_haber_kişi_ad_soyad, telefon, email, sifre) 
            VALUES ('$ad', '$soyad', '$tcno', '$dogum_tarihi', '$milliyet', '$adres', '$kaza_haber_kişi_ad_soyad', '$telefon', '$email', '$hashed_sifre')";
    
    if (mysqli_query($mysqlB, $sql)) {
        $success_message = "Kayıt başarılı! <a href='login.php' class='login-link'>Giriş Yap</a>";
    } else {
        $error_message = "Kayıt sırasında hata oluştu: " . mysqli_error($mysqlB);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kayıt Ol | DivingLog</title>
    <link rel="stylesheet" href="../CSS/signup.css">
    <link rel="web icon" href="../images/divinglog.png">
</head>
<body>
    <h1>DivingLog | Kayıt Ol</h1>
    <h2>Yeni Hesap Oluştur</h2>
    <div class="content">
        <?php if ($success_message): ?>
            <div class="success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <?php if ($error_message): ?>
            <div class="error"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <form action="signup.php" method="POST">
            <div class="form-row">
                <label for="ad">Ad:</label>
                <input type="text" id="ad" name="ad" required>
                
                <label for="soyad">Soyad:</label>
                <input type="text" id="soyad" name="soyad" required>
            </div>

            <div class="form-row">
                <label for="dogum_tarihi">Doğum Tarihi:</label>
                <input type="date" id="dogum_tarihi" name="dogum_tarihi" required>
                
                <label for="milliyet">Milliyet:</label>
                <input type="text" id="milliyet" name="milliyet" required>
            </div>

            <label for="tcno">TC Kimlik Numarası:</label><br>
            <input type="text" id="tcno" name="tcno" required pattern="\d{11}" title="Lütfen 11 haneli bir TC Kimlik Numarası girin."><br><br>

            <label for="adres">Adres:</label><br>
            <textarea id="adres" name="adres" required></textarea><br><br>

            <label for="kaza_haber_kişi_ad_soyad">Kaza Halinde Haber Verilecek Kişi:</label>
            <input type="text" id="kaza_haber_kişi_ad_soyad" name="kaza_haber_kişi_ad_soyad" required><br><br>

            <label for="telefon">Telefon Numarası:</label><br>
            <input type="text" id="telefon" name="telefon" required pattern="^\+?\d{10,15}$" title="Telefon numarasını +90xxxxxxxxxx formatında girin"><br><br>

            <label for="email">E-posta:</label><br>
            <input type="email" id="email" name="email" required><br><br>

            <label for="sifre">Şifre:</label><br>
            <input type="password" id="sifre" name="sifre" required><br><br>

            <button type="submit" class="btn">Kayıt Ol</button>
        </form>
    </div>
    <footer>
        <p>&copy; 2025 DivingLog Uygulaması</p>
    </footer>
</body>
</html>