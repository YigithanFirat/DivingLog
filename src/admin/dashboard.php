<?php
    include('../../config.php');
    $success_message = '';
    $error_message = '';
    $sql = "SELECT * FROM users";
    $result = mysqli_query($mysqlB, $sql);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DivingLog | Admin Paneli</title>
    <link rel="stylesheet" href="../CSS/dashboard.css">
    <link rel="web icon" href="../images/divinglog.png">
</head>
<body>
    <header>
        <h1>DivingLog | Admin Paneli</h1>
        <nav>
            <ul>
                <li><a href="../index.php">Ana Sayfa</a></li>
                <li><a href="manage_users.php">Kullanıcıları Yönet</a></li>
                <li><a href="../users/exit.php">Çıkış Yap</a></li>
            </ul>
        </nav>
    </header>
    <div class="container">
        <?php if ($success_message): ?>
            <div class="success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <?php if ($error_message): ?>
            <div class="error"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <h2>Kullanıcılar</h2>
        <table class="user-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Ad</th>
                    <th>Soyad</th>
                    <th>E-Posta</th>
                    <th>Durum</th>
                    <th>Yetki Düzeyi</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php while($user = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo $user['ad']; ?></td>
                        <td><?php echo $user['soyad']; ?></td>
                        <td><?php echo $user['email']; ?></td>
                        <td><?php echo $user['login'] == 1 ? 'Aktif' : 'Pasif'; ?></td>
                        <td><?php echo $user['admin'] == 1 ? 'Administrator' : 'Üye'; ?></td>
                        <td>
                            <a href="admin_reset_password.php?id=<?php echo $user['id']; ?>" class="btn">Şifre Sıfırla</a>
                            <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="btn">Düzenle</a>
                            <a href="delete_user.php?id=<?php echo $user['id']; ?>" class="btn delete" onclick="return confirm('Bu kullanıcıyı silmek istediğinize emin misiniz?');">Sil</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <footer>
        <p>&copy; 2025 DivingLog Uygulaması</p>
    </footer>
</body>
</html>