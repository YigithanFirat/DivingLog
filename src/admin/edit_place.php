<?php
include('../session_guard.php');
include('../../config.php');
include('../sidebarmenu.php');
// ID kontrolü
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    echo "Geçersiz ID.";
    exit;
}

// Mevcut veriyi al
$stmt = $mysqlB->prepare("SELECT * FROM diving_places WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$place = $result->fetch_assoc();

if (!$place) {
    echo "Böyle bir kayıt bulunamadı.";
    exit;
}

$success = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $lat = trim($_POST['latitude'] ?? '');
    $lng = trim($_POST['longitude'] ?? '');

    if ($name === '' || $lat === '' || $lng === '') {
        $errors[] = "Tüm alanlar doldurulmalıdır.";
    } elseif (!is_numeric($lat) || !is_numeric($lng)) {
        $errors[] = "Enlem ve boylam sayısal olmalıdır.";
    }

    if (empty($errors)) {
        $updateStmt = $mysqlB->prepare("UPDATE diving_places SET name = ?, latitude = ?, longitude = ? WHERE id = ?");
        $updateStmt->bind_param("ssdi", $name, $lat, $lng, $id);
        if ($updateStmt->execute()) {
            header("Location: diving_place.php?updated=1");
            exit;
        } else {
            $errors[] = "Güncelleme başarısız: " . $updateStmt->error;
        }
        $updateStmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Bölge Düzenle</title>
    <link rel="stylesheet" href="../CSS/edit_place.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>
<body class="p-4">
    <div class="container">
        <h1>Bölgeyi Düzenle</h1>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errors as $err): ?>
                        <li><?= htmlspecialchars($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST">
            <label for="name">Bölge Adı:</label>
            <input type="text" name="name" value="<?= htmlspecialchars($place['name']) ?>" required />

            <label for="latitude">Enlem:</label>
            <input type="text" name="latitude" value="<?= htmlspecialchars($place['latitude']) ?>" required />

            <label for="longitude">Boylam:</label>
            <input type="text" name="longitude" value="<?= htmlspecialchars($place['longitude']) ?>" required />

            <button type="submit" class="btn">Güncelle</button>
            <a href="diving_place.php" class="btn" style="background-color: #6c757d;">İptal</a>
        </form>
    </div>
    <footer>
        <p>&copy; 2025 DivingLog Uygulaması</p>
    </footer>
    <script src="../JS/edit_place.js"></script>
</body>
</html>