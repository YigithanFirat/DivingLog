<?php
    include('../../config.php');
    session_start();

    // Kullanıcı verisini güvenli şekilde al
    if(isset($_GET['id']))
    {
        $user_id = $_GET['id'];

        // Prepared statement kullanarak SQL Injection'a karşı koruma
        $stmt = $mysqlB->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id); // "i" -> integer
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows > 0)
        {
            $user = $result->fetch_assoc();
        }
        else
        {
            echo "Kullanıcı bulunamadı.";
            exit;
        }
        $stmt->close();
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

            // Prepared statement ile güncelleme işlemi
            $update_stmt = $mysqlB->prepare("UPDATE users SET sifre = ? WHERE id = ?");
            $update_stmt->bind_param("si", $hashed_password, $user_id);

            if($update_stmt->execute())
            {
                $success_message = "Şifre başarıyla güncellendi.";
            }
            else
            {
                $error_message = "Şifre güncellenirken bir hata oluştu.";
            }

            $update_stmt->close();
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
        <form action="admin_reset_password.php?id=<?php echo $user_id; ?>" method="POST">
            <h2><?php echo $user['ad']; ?> için Şifreyi Sıfırlama</h2>
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