<?php
include('../session_guard.php');
include('../../config.php');
include('../sidebarmenu.php');

$equipment_options = ['Scuba', 'Nargile', 'MK-17', 'MK-18', 'Tam Yüz Maskesi', 'Basınç OD', 'Diğer'];
$water_type_options = ['Tatlı Su', 'Tuzlu Su', 'Sahil', 'Bot-Tekne', 'Diğer', 'Dalga', 'Rüzgar', 'Akıntı'];
$gas_options = ['Hava', 'Nitrox', 'Helioks', 'Trimks', 'Oksijen'];
$clothing_options = ['Kuru', 'İslak', 'Diğer'];

$success_message = '';
$error_message = '';
$date = date('d/m/Y');

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$search_name = $_POST['search_name'] ?? '';
$selected_user = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search_user'])) {
    $search_name = trim($_POST['search_name']);
    $stmt = mysqli_prepare($mysqlB, "SELECT tcno, ad, soyad FROM users WHERE CONCAT(ad, ' ', soyad) LIKE CONCAT('%', ?, '%')");
    mysqli_stmt_bind_param($stmt, "s", $search_name);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $users = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
} elseif (isset($_GET['tcno'])) {
    $tcno = $_GET['tcno'];
    $stmt = mysqli_prepare($mysqlB, "SELECT ad, soyad FROM users WHERE tcno = ?");
    mysqli_stmt_bind_param($stmt, "s", $tcno);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $ad, $soyad);
    mysqli_stmt_fetch($stmt);
    $selected_user = ["tcno" => $tcno, "ad_soyad" => "$ad $soyad"];
    mysqli_stmt_close($stmt);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_plan'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error_message = "Geçersiz form gönderimi.";
    } else {
        $tcno = trim($_POST['tcno']);
        $ad_soyad = trim($_POST['ad_soyad']);
        $minutes = trim($_POST['minutes'] ?? '');
        $diving_location = trim($_POST['diving_location'] ?? '');
        $water_type = $_POST['water_type'] ?? '';
        $depth_feet = trim($_POST['depth_feet'] ?? '');
        $depth_meter = trim($_POST['depth_meter'] ?? '');
        $respiration = $_POST['respiration'] ?? '';
        $clothing = $_POST['clothing'] ?? '';
        $diving_purpose = trim($_POST['diving_purpose'] ?? '');
        $tools = trim($_POST['tools'] ?? '');
        $tools_devices = $_POST['tools_devices'] ?? '';
        $supervisor = trim($_POST['supervisor'] ?? '');

        // Yeni alanlar
        $start_time = $_POST['start_time'] ?? '';
        $end_time = $_POST['end_time'] ?? '';
        $bottom_time = $_POST['bottom_time'] ?? '';
        $avg_depth = $_POST['avg_depth'] ?? '';
        $max_depth = $_POST['max_depth'] ?? '';
        $temperature = $_POST['temperature'] ?? '';

        if (!$tcno) {
            $error_message = "Lütfen önce bir kişi seçin.";
        } elseif (!is_numeric($minutes) || $minutes < 0) {
            $error_message = "Dalış süresi geçerli bir sayı olmalıdır.";
        } elseif (!in_array($water_type, $water_type_options)) {
            $error_message = "Geçersiz dalış ortamı.";
        } elseif (!in_array($respiration, $gas_options)) {
            $error_message = "Geçersiz solunum gazı.";
        } elseif (!in_array($clothing, $clothing_options)) {
            $error_message = "Geçersiz dalış elbisesi.";
        } elseif (!in_array($tools_devices, $equipment_options)) {
            $error_message = "Geçersiz dalış takımı.";
        } else {
            $stmt = mysqli_prepare($mysqlB, "INSERT INTO diving_plans (
                tcno, minutes, diving_location, water_type, depth_feet, depth_meter,
                respiration, clothing, diving_purpose, tools, tools_devices, supervisor,
                start_time, end_time, bottom_time, avg_depth, max_depth, temperature
            ) VALUES (?, ?, ?, ?, NULLIF(?, ''), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            mysqli_stmt_bind_param($stmt, "ssssssssssssssssss",
                $tcno, $minutes, $diving_location, $water_type, $depth_feet, $depth_meter,
                $respiration, $clothing, $diving_purpose, $tools, $tools_devices, $supervisor,
                $start_time, $end_time, $bottom_time, $avg_depth, $max_depth, $temperature
            );

            if (mysqli_stmt_execute($stmt)) {
                $success_message = "Dalış planı başarıyla kaydedildi.";
            } else {
                $error_message = "Veritabanı hatası.";
            }
            mysqli_stmt_close($stmt);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dalış Oluştur</title>
    <link rel="stylesheet" href="../CSS/diving.css">
    <link rel="web icon" href="../images/divinglog.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>
<body>
<div class="diving-plan-container">
    <h1>Dalış Oluştur</h1>

    <?php if ($error_message): ?><div class="error"><?= $error_message ?></div><?php endif; ?>
    <?php if ($success_message): ?><div class="success"><?= $success_message ?></div><?php endif; ?>

    <form method="POST" class="search-form">
        <label>Kullanıcı Ara (Ad Soyad):</label>
        <div class="search-row">
            <input type="text" name="search_name" placeholder="İsim Soyisim" value="<?= htmlspecialchars($search_name) ?>" required>
            <button class="search-button" type="submit" name="search_user">Ara</button>
        </div>
    </form>

    <?php if (isset($users) && count($users) > 0): ?>
        <table class="user-list">
            <thead>
                <tr><th>Ad Soyad</th><th>TC</th><th>İşlem</th></tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['ad'] . ' ' . $user['soyad']) ?></td>
                        <td><?= htmlspecialchars($user['tcno']) ?></td>
                        <td><a class="diving_plan_create" href="?tcno=<?= urlencode($user['tcno']) ?>">Dalış Oluştur</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <?php if ($selected_user): ?>
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        <input type="hidden" name="tcno" value="<?= $selected_user['tcno'] ?>">
        <table>
            <tr><td>Ad Soyad:</td><td><input type="text" name="ad_soyad" value="<?= htmlspecialchars($selected_user['ad_soyad']) ?>" readonly></td></tr>
            <tr><td>Dalış Mevki:</td><td>
                <input type="text" id="diving_location" name="diving_location" placeholder="Körfez veya bölge adı" style="width: 100%; margin-bottom: 8px;" autocomplete="off" required>
                <div id="map" style="width: 100%; height: 300px; border-radius: 8px; border: 1px solid #ccc;"></div>
            </td></tr>
            <tr><td>Dalış Başlangıç:</td><td><input type="time" name="start_time" required></td></tr>
            <tr><td>Dalış Bitiş:</td><td><input type="time" name="end_time" required></td></tr>
            <tr><td>Dip Süresi:</td><td><input type="text" name="bottom_time" placeholder="dk" required></td></tr>
            <tr><td>Ortalama Derinlik:</td><td><input type="text" name="avg_depth" required></td></tr>
            <tr><td>Maksimum Derinlik:</td><td><input type="text" name="max_depth" required></td></tr>
            <tr><td>Sıcaklık:</td><td><input type="text" name="temperature" placeholder="°C" required></td></tr>
            <tr><td>Derinlik (Feet):</td><td><input type="text" name="depth_feet"></td></tr>
            <tr><td>Derinlik (Metre):</td><td><input type="text" name="depth_meter" required></td></tr>
            <tr><td>Süre (dk):</td><td><input type="text" name="minutes" required></td></tr>
            <tr><td>Dalış Ortamı:</td><td><select name="water_type" required><option value="">Seçiniz</option><?php foreach ($water_type_options as $opt) echo "<option value='$opt'>$opt</option>"; ?></select></td></tr>
            <tr><td>Gaz:</td><td><select name="respiration" required><option value="">Seçiniz</option><?php foreach ($gas_options as $opt) echo "<option value='$opt'>$opt</option>"; ?></select></td></tr>
            <tr><td>Elbise:</td><td><select name="clothing" required><option value="">Seçiniz</option><?php foreach ($clothing_options as $opt) echo "<option value='$opt'>$opt</option>"; ?></select></td></tr>
            <tr><td>Amacı:</td><td><input type="text" name="diving_purpose"></td></tr>
            <tr><td>Aletler:</td><td><input type="text" name="tools"></td></tr>
            <tr><td>Takım:</td><td><select name="tools_devices" required><option value="">Seçiniz</option><?php foreach ($equipment_options as $opt) echo "<option value='$opt'>$opt</option>"; ?></select></td></tr>
            <tr><td>Gözetmen:</td><td><input type="text" name="supervisor"></td></tr>
        </table>
        <button class="saveButton" type="submit" name="submit_plan">Kaydet</button>
    </form>
    <?php endif; ?>
</div>
<footer>
    <p>&copy; 2025 DivingLog Uygulaması</p>
</footer>
<script src="../JS/diving.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAQG41CCNkw229rccG5xTlkBk3OKv1kVyY&callback=initMap" async defer></script>
</body>
</html>