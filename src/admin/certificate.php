<?php
include('../session_guard.php');
include('../../config.php');
include('../sidebarmenu.php');
$success_message = '';
$error_message = '';
$users = [];

// Kullanıcı arama işlemi
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search_name'])) {
    $search_name = trim($_POST['search_name']);

    if (!empty($search_name)) {
        $stmt = $mysqlB->prepare("SELECT id, ad, soyad, tcno FROM users WHERE CONCAT(ad, ' ', soyad) LIKE CONCAT('%', ?, '%')");
        $stmt->bind_param("s", $search_name);
        $stmt->execute();
        $result = $stmt->get_result();
        $users = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>DivingLog | Sertifika Oluştur</title>
    <link rel="stylesheet" href="../CSS/certificate.css">
    <link rel="icon" href="../images/divinglog.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    <form class="certificate" method="POST">
        <h2>Ad Soyada Göre Kullanıcı Ara</h2>

        <?php if ($success_message): ?>
            <div class="success"> <?= htmlspecialchars($success_message) ?> </div>
        <?php endif; ?>

        <?php if ($error_message): ?>
            <div class="error"> <?= htmlspecialchars($error_message) ?> </div>
        <?php endif; ?>

        <label for="search_name">İsim Soyisim:</label>
        <input type="text" name="search_name" id="search_name" required>
        <input type="submit" value="Ara">
    </form>

    <?php if (!empty($users)): ?>
        <h3>Arama Sonuçları</h3>
        <div class="table-wrapper">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Ad Soyad</th>
                        <th>TC</th>
                        <th>İşlem</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['ad'] . ' ' . $user['soyad']) ?></td>
                            <td><?= htmlspecialchars($user['tcno']) ?></td>
                            <td>
                                <a href="certificate_create.php?user_id=<?= $user['id'] ?>" class="btn btn-primary">
                                    Sertifika Oluştur
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php elseif ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
        <div class="error">Aranan isim ile eşleşen kullanıcı bulunamadı.</div>
    <?php endif; ?>

    <footer>
        <p>&copy; 2025 DivingLog Uygulaması</p>
    </footer>
    <script src="../JS/certificate.js"></script>
</body>
</html>