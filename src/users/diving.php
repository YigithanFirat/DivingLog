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
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "ssssssssssss", $tcno, $minutes, $diving_location, $water_type, $depth_feet, $depth_meter, $respiration, $clothing, $diving_purpose, $tools, $tools_devices, $supervisor);

                if (mysqli_stmt_execute($stmt)) {
                    $success_message = "Dalış planı başarıyla kaydedildi!";
                    // Form verilerini sıfırla
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
                        <td>Toplam Dalış Zamanı (dakika):</td>
                        <td><input type="number" min="0" name="minutes" placeholder="Dalış Süresini Giriniz (Dakika)" value="<?php echo isset($minutes) ? htmlspecialchars($minutes) : ''; ?>" required></td>
                    </tr>
                    <tr>
                        <td>Dalış Mevki:</td>
                        <td><input type="text" name="diving_location" placeholder="Dalış Mevkinizi Giriniz" value="<?php echo isset($diving_location) ? htmlspecialchars($diving_location) : ''; ?>" required></td>
                    </tr>
                    <tr>
                        <td>Dalış Ortamı:</td>
                        <td>
                            <select name="water_type" required>
                                <option value="">Seçiniz</option>
                                <?php foreach($water_type_options as $option): ?>
                                    <option value="<?php echo htmlspecialchars($option); ?>" <?php echo (isset($water_type) && $water_type === $option) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($option); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Planlanan Derinlik (Feet):</td>
                        <td><input type="number" min="0" name="depth_feet" ... ></td>
                    </tr>
                    <tr>
                        <td>Planlanan Derinlik (Metre):</td>
                        <td><input type="number" min="0" name="depth_meter" ... ></td>
                    </tr>
                    <tr>
                        <td>Solunum Gazı:</td>
                        <td>
                            <select name="respiration" required>
                                <option value="">Seçiniz</option>
                                <?php foreach($gas_options as $option): ?>
                                    <option value="<?php echo htmlspecialchars($option); ?>" <?php echo (isset($respiration) && $respiration === $option) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($option); ?>
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
                                <?php foreach($clothing_options as $option): ?>
                                    <option value="<?php echo htmlspecialchars($option); ?>" <?php echo (isset($clothing) && $clothing === $option) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($option); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Dalışın Amacı:</td>
                        <td><input type="text" name="diving_purpose" placeholder="Dalışın Amacını Giriniz" value="<?php echo isset($diving_purpose) ? htmlspecialchars($diving_purpose) : ''; ?>" required></td>
                    </tr>
                    <tr>
                        <td>Kullanılan Alet ve Cihazlar:</td>
                        <td><input type="text" name="tools" placeholder="Kullanılan Alet ve Cihazları Yazınız" value="<?php echo isset($tools) ? htmlspecialchars($tools) : ''; ?>" required></td>
                    </tr>
                    <tr>
                        <td>Dalış Takımı:</td>
                        <td>
                            <select name="tools_devices" required>
                                <option value="">Seçiniz</option>
                                <?php foreach($equipment_options as $option): ?>
                                    <option value="<?php echo htmlspecialchars($option); ?>" <?php echo (isset($tools_devices) && $tools_devices === $option) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($option); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Dalış Amiri:</td>
                        <td><input type="text" name="supervisor" placeholder="Dalış Amirini Giriniz" value="<?php echo isset($supervisor) ? htmlspecialchars($supervisor) : ''; ?>"></td>
                    </tr>
                </table>
                <button type="submit" class="diving_plan_create">Dalış Planı Oluştur</button>
            </form>
        </div>
    </div>
    <footer>
        <p>&copy; 2025 DivingLog Uygulaması</p>
    </footer>
</body>
</html>