<?php
    include('../../config.php');
    session_start();
    $sql = "SELECT * FROM users";
    $result = mysqli_query($mysqlB, $sql);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DivingLog | Kullanıcıları Yönet</title>
    <link rel="stylesheet" href="../CSS/manage_users.css">
    <link rel="web icon" href="../images/divinglog.png">
</head>
<body>
    <header>
        <h1>DivingLog | Kullanıcıları Yönet</h1>
        <nav>
            <ul>
                <li><a href="../index.php">Ana Sayfa</a></li>
                <li><a href="../users/exit.php">Çıkış Yap</a></li>
            </ul>
        </nav>
    </header>
    <div class="container">
        <h2>Kullanıcılar Listesi</h2>
        <?php if (mysqli_num_rows($result) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Adı</th>
                        <th>Soyadı</th>
                        <th>E-posta</th>
                        <th>Durum</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($user = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><?php echo $user['ad']; ?></td>
                            <td><?php echo $user['soyad']; ?></td>
                            <td><?php echo $user['email']; ?></td>
                            <td><?php echo $user['login'] == 1 ? 'Aktif' : 'Pasif'; ?></td>
                            <td>
                                <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="btn">Düzenle</a>
                                <a href="admin_reset_password.php?id=<?php echo $user['id']; ?>" class="btn">Şifre Sıfırla</a>
                                <a href="delete_user.php?id=<?php echo $user['id']; ?>" class="btn delete" onclick="return confirm('Bu kullanıcıyı silmek istediğinize emin misiniz?');">Sil</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Henüz kullanıcı bulunmamaktadır.</p>
        <?php endif; ?>
    </div>
    <footer>
        <p>&copy; 2025 DivingLog Uygulaması</p>
    </footer>
</body>
</html>
