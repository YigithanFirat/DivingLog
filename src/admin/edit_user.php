<?php
    include('../../config.php');
    session_start();
    if(isset($_GET['id']))
    {
        $user_id = $_GET['id'];
        $sql = "SELECT * FROM users WHERE id = '$user_id'";
        $result = mysqli_query($mysqlB, $sql);
        if(mysqli_num_rows($result) > 0)
        {
            $user = mysqli_fetch_assoc($result);
        }
        else
        {
            echo "Kullanıcı bulunamadı.";
            exit;
        }
    }
    if($_SERVER["REQUEST_METHOD"] == "POST")
    {
        $name = mysqli_real_escape_string($mysqlB, $_POST['name']);
        $email = mysqli_real_escape_string($mysqlB, $_POST['email']);
        $status = $_POST['login'];
        $update_sql = "UPDATE users SET ad='$name', email='$email', login='$status' WHERE id='$user_id'";
        if(mysqli_query($mysqlB, $update_sql))
        {
            $success_message = "Kullanıcı başarıyla güncellendi.";
        }
        else
        {
            $error_message = "Kullanıcı güncellenirken bir hata oluştu.";
        }
    }
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DivingLog | Kullanıcı Düzenle</title>
    <link rel="stylesheet" href="../CSS/edit_user.css">
    <link rel="web icon" href="../images/divinglog.png">
</head>
<body>
    <header>
        <h1>DivingLog | Kullanıcı Düzenle</h1>
        <nav>
            <ul>
                <li><a href="../index.php">Ana Sayfa</a></li>
                <li><a href="manage_users.php">Kullanıcıları Yönet</a></li>
                <li><a href="../users/exit.php">Çıkış Yap</a></li>
            </ul>
        </nav>
    </header>
    <div class="container">
        <?php if (isset($success_message)): ?>
            <div class="success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <?php if (isset($error_message)): ?>
            <div class="error"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <h2>Kullanıcı Bilgilerini Düzenle</h2>
        <form action="edit_user.php?id=<?php echo $user_id; ?>" method="POST">
            <label for="name">Ad:</label>
            <input type="text" id="name" name="name" value="<?php echo $user['ad']; ?>" required>
            <label for="surname">Soyad:</label>
            <input type="text" id="surname" name="surname" value="<?php echo $user['soyad']; ?>" required>
            <label for="dogumatarihi">Doğum Tarihi:</label>
            <input type="text" id="dogumtarihi" name="dogumtarihi" value="<?php echo $user['dogum_tarihi']; ?>" required>
            <label for="milliyet">Milliyet:</label>
            <input type="text" id="milliyet" name="milliyet" value="<?php echo $user['milliyet']; ?>" required>
            <label for="adres">Adres:</label>
            <input type="text" id="adres" name="adres" value="<?php echo $user['adres']; ?>" required>
            <label for="kaza_haber_kişi_ad_soyad">Kaza Halinde Haber Verilecek Kişinin Ad Soyadı:</label>
            <input type="text" id="kaza_haber_kişi_ad_soyad" name="kaza_haber_kişi_ad_soyad" value="<?php echo $user['kaza_haber_kişi_ad_soyad']; ?>" required>
            <label for="telefon">Telefon Numarası:</label><br>
            <input type="text" id="telefon" name="telefon" value="<?php echo $user['telefon']; ?>" required>
            <label for="tcno">T.C Kimlik Numarası:</label>
            <input type="text" id="tcno" name="tcno" value="<?php echo $user['tcno']; ?>" required>
            <label for="fotograf">Fotoğraf:</label>
            <input type="text" id="fotograf" name="fotograf" value="<?php echo $user['fotograf']; ?>" required>
            <label for="login">Durum:</label>
            <select id="login" name="login" required>
                <option value="1" <?php if ($user['login'] == 1) echo 'selected'; ?>>Aktif</option>
                <option value="0" <?php if ($user['login'] == 0) echo 'selected'; ?>>Pasif</option>
            </select>
            <label for="email">E-posta:</label>
            <input type="email" id="email" name="email" value="<?php echo $user['email']; ?>" required>
            <select name="administrator" id="administrator" required>
                <option value="0" <?php if($user['admin'] == 0) echo 'selected'; ?>>Üye</option>
                <option value="1" <?php if($user['admin'] == 1) echo 'selected'; ?>>Administrator</option>
            </select>
            <button type="submit" class="btn">Güncelle</button>
        </form>
    </div>
    <footer>
        <p>&copy; 2025 DivingLog Uygulaması</p>
    </footer>
</body>
</html>