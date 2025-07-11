<?php
include('../session_guard.php');
include('../../config.php');
include('../sidebarmenu.php');

$success = false;
$error = false;
$users = [];

// Kullanıcı arama işlemi
if (isset($_POST['search_user'])) {
    $search_input = trim($_POST['search_input']);

    $stmt = mysqli_prepare($mysqlB, "SELECT id, ad, soyad, tcno FROM users WHERE tcno LIKE CONCAT('%', ?, '%') OR ad LIKE CONCAT('%', ?, '%') OR soyad LIKE CONCAT('%', ?, '%')");
    mysqli_stmt_bind_param($stmt, "sss", $search_input, $search_input, $search_input);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $users = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
}

// Sağlık raporu kaydetme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_report'])) {
    $muayene_tarihi = $_POST['muayene_tarihi'] ?? '';
    $onaylayan = trim($_POST['onaylayan'] ?? '');
    $user_id = $_POST['user_id'] ?? '';
    $created_at = date('Y-m-d H:i:s');

    if (!empty($muayene_tarihi) && !empty($onaylayan) && !empty($user_id)) {
        // Kullanıcı bilgilerini çek (ad, soyad, tcno)
        $query = "SELECT ad, soyad, tcno FROM users WHERE id = ?";
        $stmt = mysqli_prepare($mysqlB, $query);
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if ($user) {
            $onaylanan = $user['ad'] . ' ' . $user['soyad'];
            $tcno = $user['tcno'];

            // Sağlık raporu ekleme sorgusu
            $stmt = mysqli_prepare($mysqlB, "INSERT INTO health_inspections (muayene_tarihi, onaylayan, onaylanan, tcno, created_at) VALUES (?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "sssss", $muayene_tarihi, $onaylayan, $onaylanan, $tcno, $created_at);
            $success = mysqli_stmt_execute($stmt);
            if (!$success) $error = true;
            mysqli_stmt_close($stmt);
        } else {
            $error = true;
        }
    } else {
        $error = true;
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8" />
    <title>DivingLog | Sağlık Raporu Ekleme</title>
    <link rel="stylesheet" href="../CSS/health_inspection.css" />
    <link rel="icon" href="../images/divinglog.png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    <div class="content">
        <h1>Sağlık Raporu Oluştur</h1>

        <?php if ($success): ?>
            <p class="success">✔️ Sağlık Raporu başarıyla kaydedildi.</p>
        <?php elseif ($error): ?>
            <p class="error">❌ Lütfen tüm alanları eksiksiz doldurduğunuzdan emin olun.</p>
        <?php endif; ?>

        <!-- Kullanıcı Arama Formu -->
        <form method="POST" class="form" style="margin-bottom: 30px;">
            <label for="search_input">Kullanıcı Ara (Ad, Soyad, TC):</label>
            <input type="text" id="search_input" name="search_input" placeholder="Ad, Soyad ya da TC girin" required>
            <button type="submit" name="search_user" class="btn">🔍 Ara</button>
        </form>

        <?php if (!empty($users)): ?>
            <form method="POST" class="form" novalidate>
                <label for="user_id">Kullanıcı Seç:</label>
                <select name="user_id" id="user_id" required>
                    <?php foreach ($users as $user): ?>
                        <option value="<?= $user['id'] ?>">
                            <?= htmlspecialchars($user['ad']) ?> <?= htmlspecialchars($user['soyad']) ?> - <?= htmlspecialchars($user['tcno']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="muayene_tarihi">Muayene Tarihi:</label>
                <input type="date" id="muayene_tarihi" name="muayene_tarihi" required>

                <label for="onaylayan">Onaylayan Doktor:</label>
                <input type="text" id="onaylayan" name="onaylayan" placeholder="Dr. Adı Soyadı" required>

                <div class="btn-container">
                    <button type="submit" name="create_report" class="btn">Kaydet</button>
                    <a href="../index.php" class="btn">⬅️ Geri Dön</a>
                </div>
            </form>
        <?php elseif (isset($_POST['search_user'])): ?>
            <p class="error">🔍 Eşleşen kullanıcı bulunamadı.</p>
        <?php endif; ?>
    </div>

    <footer>
        <p>&copy; 2025 DivingLog Uygulaması</p>
    </footer>
    <script src="../JS/health_inspection.js"></script>
</body>
</html>