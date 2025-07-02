<?php
    include('../../config.php');

    // POST isteği: Güncelleme işlemi
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        // Giriş doğrulama (örnek: sadece rakam ve 11 karakter kontrolü)
        $tcno = trim($_POST['tcno']);
        if (!preg_match('/^\d{11}$/', $tcno)) {
            die("Geçersiz T.C. Kimlik Numarası.");
        }

        $stmt = $mysqlB->prepare("UPDATE diving_plans SET 
            tcno = ?, minutes = ?, diving_location = ?, water_type = ?, 
            depth_feet = ?, depth_meter = ?, respiration = ?, clothing = ?, 
            diving_purpose = ?, tools = ?, tools_devices = ?, supervisor = ? 
            WHERE id = ?");
        if (!$stmt) {
            die("Sorgu hatası: " . $mysqlB->error);
        }

        $stmt->bind_param(
            "sissddssssssi",
            $tcno,
            $_POST['minutes'],
            $_POST['diving_location'],
            $_POST['water_type'],
            $_POST['depth_feet'],
            $_POST['depth_meter'],
            $_POST['respiration'],
            $_POST['clothing'],
            $_POST['diving_purpose'],
            $_POST['tools'],
            $_POST['tools_devices'],
            $_POST['supervisor'],
            $_POST['id']
        );

        if ($stmt->execute()) {
            header("Location: manage_diving.php?success=1");
            exit();
        } else {
            die("Güncelleme hatası: " . $stmt->error);
        }
        $stmt->close();
    }

    // GET isteği: Mevcut veriyi çekme
    if (isset($_GET['id']) && filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
        $id = (int)$_GET['id'];

        $stmt = $mysqlB->prepare("SELECT * FROM diving_plans WHERE id = ?");
        if (!$stmt) {
            die("Sorgu hatası: " . $mysqlB->error);
        }
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows !== 1) {
            $stmt->close();
            die("Geçersiz ID.");
        }

        $row = $result->fetch_assoc();
        $stmt->close();
    } else {
        die("ID belirtilmedi veya geçersiz.");
    }
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Dalış Planı Düzenle</title>
    <link rel="stylesheet" href="../CSS/edit_diving_plan.css">
    <link rel="web icon" href="../images/divinglog.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    <div class="menu-toggle" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </div>
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <ul>
            <li><a href="../index.php">Ana Sayfa</a></li>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="manage_users.php">Kullanıcıları Yönet</a></li>
            <li><a href="diving.php">Dalış Oluştur</a></li>
            <li><a href="manage_diving.php">Dalışları Yönet</a></li>
            <li><a href="certificate.php">Sertifika Oluştur</a></li>
            <li><a href="certificate_list.php">Sertifikaları Listele</a></li>
            <li><a href="health_inspection.php">Sağlık Raporu Oluştur</a></li>
            <li><a href="health_inspection_list.php">Sağlık Raporlarını Listele</a></li>
            <li><a href="../users/exit.php">Çıkış Yap</a></li>
        </ul>
    </div>
    <div class="container">
        <h2>Dalış Planı Düzenle</h2>
        <form action="" method="POST">
            <input type="hidden" name="id" value="<?= htmlspecialchars($row['id']) ?>">

            <label>T.C No:</label>
            <input type="text" name="tcno" value="<?= htmlspecialchars($row['tcno']) ?>" required>

            <label>Dakika:</label>
            <input type="number" name="minutes" value="<?= htmlspecialchars($row['minutes']) ?>" required>

            <label>Lokasyon:</label>
            <input type="text" name="diving_location" value="<?= htmlspecialchars($row['diving_location']) ?>" required>

            <label>Dalış Ortamı:</label>
            <input type="text" name="water_type" value="<?= htmlspecialchars($row['water_type']) ?>" required>

            <label>Derinlik (Feet):</label>
            <input type="number" step="0.1" name="depth_feet" value="<?= htmlspecialchars($row['depth_feet']) ?>">

            <label>Derinlik (Metre):</label>
            <input type="number" step="0.1" name="depth_meter" value="<?= htmlspecialchars($row['depth_meter']) ?>">

            <label>Solunum:</label>
            <input type="text" name="respiration" value="<?= htmlspecialchars($row['respiration']) ?>">

            <label>Elbise:</label>
            <input type="text" name="clothing" value="<?= htmlspecialchars($row['clothing']) ?>">

            <label>Amaç:</label>
            <input type="text" name="diving_purpose" value="<?= htmlspecialchars($row['diving_purpose']) ?>">

            <label>Aletler:</label>
            <input type="text" name="tools" value="<?= htmlspecialchars($row['tools']) ?>">

            <label>Takım:</label>
            <input type="text" name="tools_devices" value="<?= htmlspecialchars($row['tools_devices']) ?>">

            <label>Amir:</label>
            <input type="text" name="supervisor" value="<?= htmlspecialchars($row['supervisor']) ?>">

            <button type="submit">Güncelle</button>
            <a href="manage_diving.php" class="cancel-btn">İptal</a>
        </form>
    </div>
<footer>
    <p>&copy; 2025 DivingLog Uygulaması</p>
</footer>
    <script src="../JS/edit_diving_plan.js"></script>
</body>
</html>