<?php
session_start();
include('../../config.php');

$equipment_options = ['Scuba', 'Nargile', 'MK-17', 'MK-18', 'Tam Yüz Maskesi', 'Basınç OD', 'Diğer'];
$water_type_options = ['Tatlı Su', 'Tuzlu Su', 'Sahil', 'Bot-Tekne', 'Diğer', 'Dalga', 'Rüzgar', 'Akıntı'];
$gas_options = ['Hava', 'Nitrox', 'Helioks', 'Trimks', 'Oksijen'];
$clothing_options = ['Kuru', 'Islak', 'Diğer'];

$success_message = '';
$error_message = '';
$date = date('d/m/Y');

// CSRF token oluşturma
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF token kontrolü
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error_message = "Geçersiz form gönderimi.";
    } else {
        // Temizleme ve doğrulama
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

        $tcno = $_SESSION['tcno'] ?? null;

        // Basit validasyon
        if (!$tcno) {
            $error_message = "Lütfen giriş yapın.";
        } elseif (!is_numeric($minutes) || $minutes < 0) {
            $error_message = "Dalış süresi geçerli bir sayı olmalıdır.";
        } elseif (!in_array($water_type, $water_type_options)) {
            $error_message = "Geçersiz dalış ortamı seçimi.";
        } elseif (!in_array($respiration, $gas_options)) {
            $error_message = "Geçersiz solunum gazı seçimi.";
        } elseif (!in_array($clothing, $clothing_options)) {
            $error_message = "Geçersiz dalış elbisesi seçimi.";
        } elseif (!in_array($tools_devices, $equipment_options)) {
            $error_message = "Geçersiz dalış takımı seçimi.";
        } else {
            // Veritabanı insert işlemi
            $stmt = mysqli_prepare($mysqlB, "INSERT INTO diving_plans
            (tcno, minutes, diving_location, water_type, depth_feet, depth_meter, respiration, clothing, diving_purpose, tools, tools_devices, supervisor) 
            VALUES (?, ?, ?, ?, NULLIF(?, ''), ?, ?, ?, ?, ?, ?, ?)");

            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "ssssssssssss", $tcno, $minutes, $diving_location, $water_type, $depth_feet, $depth_meter, $respiration, $clothing, $diving_purpose, $tools, $tools_devices, $supervisor);

                if (mysqli_stmt_execute($stmt)) {
                    $success_message = "Dalış planı başarıyla kaydedildi!";
                    $minutes = $diving_location = $water_type = $depth_feet = $depth_meter = $respiration = $clothing = $diving_purpose = $tools = $tools_devices = $supervisor = '';
                } else {
                    $error_message = "Kayıt sırasında hata oluştu. Lütfen tekrar deneyin.";
                }
                mysqli_stmt_close($stmt);
            } else {
                $error_message = "Veritabanı sorgusu hazırlanamadı.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>DivingLog | Dalış Planı</title>
    <link rel="stylesheet" href="../CSS/diving.css" />
    <link rel="icon" href="../images/divinglog.png" />
</head>
<body>
    <div class="diving-plan-container">
        <header>
            <h1>Dalış Planı</h1>
            <p>İSTE | Tarih: <?php echo htmlspecialchars($date); ?></p>
        </header>

        <div class="plan-details">
            <?php if ($success_message): ?>
                <div class="success"><?php echo htmlspecialchars($success_message); ?></div>
            <?php endif; ?>
            <?php if ($error_message): ?>
                <div class="error"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>

        <form action="" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>" />

            <table>
                <tr>
                    <td>Dalış Mevki:</td>
                    <td>
                        <input type="text" name="diving_location" id="diving_location" value="<?php echo htmlspecialchars($diving_location ?? ''); ?>" required>
                        <div id="map"></div>
                    </td>
                </tr>
                <tr>
                    <td>Dalış Derinliği (Feet):</td>
                    <td><input type="text" name="depth_feet" value="<?php echo htmlspecialchars($depth_feet ?? ''); ?>"></td>
                </tr>
                <tr>
                    <td>Dalış Derinliği (Metre):</td>
                    <td><input type="text" name="depth_meter" value="<?php echo htmlspecialchars($depth_meter ?? ''); ?>" required></td>
                </tr>
                <tr>
                    <td>Dalış Süresi (Dakika):</td>
                    <td><input type="text" name="minutes" value="<?php echo htmlspecialchars($minutes ?? ''); ?>" required></td>
                </tr>
                <tr>
                    <td>Dalış Ortamı:</td>
                    <td>
                        <select name="water_type" required>
                            <option value="">Seçiniz</option>
                            <?php foreach ($water_type_options as $option): ?>
                                <option value="<?php echo $option; ?>" <?php if (($water_type ?? '') === $option) echo 'selected'; ?>>
                                    <?php echo $option; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Solunum Gazı:</td>
                    <td>
                        <select name="respiration" required>
                            <option value="">Seçiniz</option>
                            <?php foreach ($gas_options as $option): ?>
                                <option value="<?php echo $option; ?>" <?php if (($respiration ?? '') === $option) echo 'selected'; ?>>
                                    <?php echo $option; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Dalış Elbisesi:</td>
                    <td>
                        <select name="clothing" required>
                            <option value="">Seçiniz</option>
                            <?php foreach ($clothing_options as $option): ?>
                                <option value="<?php echo $option; ?>" <?php if (($clothing ?? '') === $option) echo 'selected'; ?>>
                                    <?php echo $option; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Dalış Amacı:</td>
                    <td><input type="text" name="diving_purpose" value="<?php echo htmlspecialchars($diving_purpose ?? ''); ?>"></td>
                </tr>
                <tr>
                    <td>Aletler:</td>
                    <td><input type="text" name="tools" value="<?php echo htmlspecialchars($tools ?? ''); ?>"></td>
                </tr>
                <tr>
                    <td>Dalış Takımı:</td>
                    <td>
                        <select name="tools_devices" required>
                            <option value="">Seçiniz</option>
                            <?php foreach ($equipment_options as $option): ?>
                                <option value="<?php echo $option; ?>" <?php if (($tools_devices ?? '') === $option) echo 'selected'; ?>>
                                    <?php echo $option; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Gözetmen:</td>
                    <td><input type="text" name="supervisor" value="<?php echo htmlspecialchars($supervisor ?? ''); ?>"></td>
                </tr>
            </table>

            <button class="saveButton" type="submit">Kaydet</button>
        </form>
        </div>
    </div>

    <!-- Google Maps JS -->
    <script>
        let map;
        let markers = [];
        let selectedMarker = null;

        const coves = [
            { name: "İzmir Körfezi", lat: 38.4192, lng: 27.1287 },
            { name: "Gökova Körfezi", lat: 36.9500, lng: 28.0000 },
            { name: "Edremit Körfezi", lat: 39.5500, lng: 26.8000 },
            { name: "Antalya Körfezi", lat: 36.8500, lng: 30.7000 },
            { name: "Saros Körfezi", lat: 40.4000, lng: 26.8000 },
            { name: "Mersin Körfezi", lat: 36.8000, lng: 34.6000 },
            { name: "İskenderun Körfezi", lat: 36.6000, lng: 36.2000 }
        ];

        function initMap() {
            map = new google.maps.Map(document.getElementById("map"), {
                center: { lat: 38.5, lng: 32.0 },
                zoom: 6
            });

            coves.forEach(cove => {
                const marker = new google.maps.Marker({
                    position: { lat: cove.lat, lng: cove.lng },
                    map: map,
                    title: cove.name
                });

                marker.addListener("click", () => {
                    if (selectedMarker) {
                        selectedMarker.setAnimation(null);
                    }
                    marker.setAnimation(google.maps.Animation.BOUNCE);
                    selectedMarker = marker;
                    document.getElementById("diving_location").value = cove.name;
                });

                markers.push(marker);
            });

            map.addListener("click", function(event) {
                const lat = event.latLng.lat();
                const lng = event.latLng.lng();

                const geocoder = new google.maps.Geocoder();
                geocoder.geocode({ location: { lat, lng } }, (results, status) => {
                    if (status === "OK" && results[0]) {
                        document.getElementById("diving_location").value = results[0].formatted_address;
                    } else {
                        document.getElementById("diving_location").value = `${lat.toFixed(5)}, ${lng.toFixed(5)}`;
                    }
                });
            });
        }
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAQG41CCNkw229rccG5xTlkBk3OKv1kVyY&callback=initMap" async defer></script>
</body>
</html>