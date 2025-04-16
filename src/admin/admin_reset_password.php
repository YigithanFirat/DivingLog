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
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        if($new_password !== $confirm_password)
        {
            $error_message = "Şifreler eşleşmiyor.";
        }
        else
        {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_sql = "UPDATE users SET password='$hashed_password' WHERE id='$user_id'";
            if(mysqli_query($mysqlB, $update_sql))
            {
                $success_message = "Şifre başarıyla güncellendi.";
            }
            else
            {
                $error_message = "Şifre güncellenirken bir hata oluştu.";
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DivingLog | Admin Şifre Sıfırlama</title>
    <link rel="stylesheet" href="../CSS/admin_reset_password.css">
    <link rel="web icon" href="../images/divinglog.png">
</head>
<body>
    <header>
        <h1>DivingLog | Admin Şifre Sıfırlama</h1>
        <nav>
            <ul>
                <li><a href="dashboard.php">Ana Sayfa</a></li>
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
        <h2><?php echo $user['ad']; ?> için Şifreyi Sıfırlama</h2>
        <form action="admin_reset_password.php?id=<?php echo $user_id; ?>" method="POST">
            <label for="new_password">Yeni Şifre:</label>
            <input type="password" id="new_password" name="new_password" required>

            <label for="confirm_password">Yeni Şifreyi Onayla:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>

            <button type="submit" class="btn">Şifreyi Güncelle</button>
        </form>
    </div>
    <footer>
        <p>&copy; 2025 DivingLog Uygulaması</p>
    </footer>
</body>
</html>