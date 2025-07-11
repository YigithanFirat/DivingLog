<?php
include('../session_guard.php');
include('../../config.php');
include('../sidebarmenu.php');

// Giriş kontrolü
if (!isset($_SESSION['tcno'])) {
    header('Location: ../users/login.php');
    exit;
}

// Yeni bölge ekleme
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_region'])) {
    $name = trim($_POST['region_name'] ?? '');
    $lat = trim($_POST['latitude'] ?? '');
    $lng = trim($_POST['longitude'] ?? '');

    if ($name && is_numeric($lat) && is_numeric($lng)) {
        $stmt = $mysqlB->prepare("INSERT INTO diving_places (name, latitude, longitude) VALUES (?, ?, ?)");
        $stmt->bind_param("sdd", $name, $lat, $lng);
        $stmt->execute();
        $stmt->close();
    }
}

// Bölge silme
if (isset($_GET['delete_id'])) {
    $id = (int)$_GET['delete_id'];
    $stmt = $mysqlB->prepare("DELETE FROM diving_places WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

// Bölgeleri çek
$places = [];
$result = $mysqlB->query("SELECT * FROM diving_places ORDER BY id DESC");
while ($row = $result->fetch_assoc()) {
    $places[] = $row;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Dalış Bölgeleri</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../CSS/diving_place.css">
    <link rel="icon" href="../images/divinglog.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>
<body>
    <div class="container">
        <h1>Dalış Bölgeleri</h1>

        <!-- Yeni bölge ekleme -->
        <form method="POST" class="add-region-form">
            <h3>Yeni Bölge Ekle</h3>
            <input type="text" name="region_name" placeholder="Bölge Adı" required />
            <input type="text" name="latitude" placeholder="Enlem (ör. 36.5)" required />
            <input type="text" name="longitude" placeholder="Boylam (ör. 29.1)" required />
            <button type="submit" name="add_region">Ekle</button>
            <div id="map" style="width: 100%; height: 300px; border-radius: 8px; border: 1px solid #ccc;"></div>
        </form>

        <!-- Var olan bölgeler -->
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Ad</th>
                    <th>Enlem</th>
                    <th>Boylam</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($places as $place): ?>
                <tr>
                    <td><?= $place['id'] ?></td>
                    <td><?= htmlspecialchars($place['name']) ?></td>
                    <td><?= $place['latitude'] ?></td>
                    <td><?= $place['longitude'] ?></td>
                    <td>
                        <a href="edit_place.php?id=<?= $place['id'] ?>" class="btn btn-sm btn-warning">Düzenle</a>
                        <a href="?delete_id=<?= $place['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Silmek istediğine emin misin?')">Sil</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <footer>
        <p>&copy; 2025 DivingLog Uygulaması</p>
    </footer>
    <script src="../JS/diving_place.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAQG41CCNkw229rccG5xTlkBk3OKv1kVyY&callback=initMap" async defer></script>
</body>
</html>