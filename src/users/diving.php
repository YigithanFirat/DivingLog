<?php
    include('../../config.php');
    $equipment = 'Scuba, Nargile, MK-17, MK-18, Tam Yüz Maskesi, Basınç OD, Diğer';
    $water_type = 'Tatlı Su, Tuzlu Su, Sahil, Bot-Tekne, Diğer, Dalga, Rüzgar, Akıntı';
    $gas = 'Hava, Nitrox, Helioks, Trimks, Oksijen';
    $clothing = 'Kuru, Islak, Diğer';
    $success_message = '';
    $error_message = '';
    if($_SERVER['REQUEST_METHOD'] == 'POST')
    {
        $date = date('d/m/Y');
        $minutes = $_POST['diving_time'] ?? '';
        $diving_location = $_POST['diving_location'] ?? '';
        $water_type = $_POST['water_type'] ?? '';
        $depth_feet = $_POST['depth_feet'] ?? '';
        $depth_meter = $_POST['depth_meter'] ?? '';
        $respiration = $_POST['respiration'] ?? '';
        $clothing = $_POST['clothing'] ?? '';
        $diving_purpose = $_POST['diving_purpose'] ?? '';
        $tools = $_POST['tools'] ?? '';
        $tools_devices = $_POST['tools_devices'] ?? '';
        $supervisor = $_POST['supervisor'] ?? '';
        session_start();
        $tcno = $_SESSION['tcno'] ?? null;
        if($tcno)
        {
            $stmt = mysqli_prepare($mysqlB, "INSERT INTO diving_plans
            (tcno, minutes, diving_location, water_type, depth_feet, depth_meter, respiration, clothing, diving_purpose, tools, tools_devices, supervisor) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "ssssssssssss", $tcno, $minutes, $diving_location, $water_type, $depth_feet, $depth_meter, $respiration, $clothing, $diving_purpose, $tools, $tools_devices, $supervisor);
            if(mysqli_stmt_execute($stmt))
            {
                $success_message = "Dalış planı başarıyla kaydedildi!";
            }
            else
            {
                $error_message = "Kayıt sırasında hata oluştu. Lütfen tekrar deneyin.";
            }
            mysqli_stmt_close($stmt);
        }
        else
        {
            $error_message = "Lütfen giriş yapın.";
        }
    }
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DivingLog | Dalış Planı</title>
    <link rel="stylesheet" href="../CSS/diving.css">
    <link rel="web icon" href="../images/divinglog.png">
</head>
<body>
    <div class="diving-plan-container">
        <header>
            <h1>Dalış Planı</h1>
            <p>İSTE | Tarih: <?php echo $date; ?></p>
        </header>
        <div class="plan-details">
            <form action="#" method="POST">
                <table>
                <?php if ($success_message): ?>
                    <div class="success"><?php echo $success_message; ?></div>
                <?php endif; ?>
                <?php if ($error_message): ?>
                    <div class="error"><?php echo $error_message; ?></div>
                <?php endif; ?>
                    <tr>
                        <td>Toplam Dalış Zamanı (dakika):</td>
                        <td><input type="text" name="minutes" placeholder="Dalış Süresini Giriniz ( Dakika )"></td>
                    </tr>
                    <tr>
                        <td>Dalış Mevki:</td>
                        <td><input type="text" name="diving_location" placeholder="Dalış Mevkinizi Giriniz" required></td>
                    </tr>
                    <tr>
                        <td>Dalış Ortamı:</td>
                        <td>
                            <select name="water_type" required>
                                <option value="Tatlı Su" <?php if($water_type == 'Tatlı Su') echo 'selected'; ?>>Tatlı Su</option>
                                <option value="Tuzlu Su" <?php if($water_type == 'Tuzlu Su') echo 'selected'; ?>>Tuzlu Su</option>
                                <option value="Sahil" <?php if($water_type == 'Sahil') echo 'selected'; ?>>Sahil</option>
                                <option value="Bot-Tekne" <?php if($water_type == 'Bot-Tekne') echo 'selected'; ?>>Bot-Tekne</option>
                                <option value="Diğer" <?php if($water_type == 'Diğer') echo 'selected'; ?>>Diğer</option>
                                <option value="Dalga" <?php if($water_type == 'Dalga') echo 'selected'; ?>>Dalga</option>
                                <option value="Rüzgar" <?php if($water_type == 'Rüzgar') echo 'selected'; ?>>Rüzgar</option>
                                <option value="Akıntı" <?php if($water_type == 'Akıntı') echo 'selected'; ?>>Akıntı</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Planlanan Derinlik (Feet):</td>
                        <td><input type="text" name="depth_feet" placeholder="Derinliği Feet Cinsinden Giriniz"></td>
                    </tr>
                    <tr>
                        <td>Planlanan Derinlik (Metre):</td>
                        <td><input type="text" name="depth_meter" placeholder="Derinliği Metre Cinsinden Giriniz"></td>
                    </tr>
                    <tr>
                        <td>Solunum Gazı:</td>
                        <td>
                            <select name="respiration" required>
                                <option value="Hava" <?php if($gas == 'Hava') echo 'selected'; ?>>Hava</option>
                                <option value="Nitrox" <?php if($gas == 'Nitrox') echo 'selected'; ?>>Nitrox</option>
                                <option value="Helioks" <?php if($gas == 'Helioks') echo 'selected'; ?>>Helioks</option>
                                <option value="Trimks" <?php if($gas == 'Trimks') echo 'selected'; ?>>Trimks</option>
                                <option value="Oksijen" <?php if($gas == 'Oksijen') echo 'selected'; ?>>Oksijen</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Dalış Elbisesi:</td>
                        <td>
                            <select name="clothing" required>
                                <option value="Kuru" <?php if($clothing == 'Kuru') echo 'selected'; ?>>Kuru</option>
                                <option value="Islak" <?php if($clothing == 'Islak') echo 'selected'; ?>>Islak</option>
                                <option value="Diğer" <?php if($clothing == 'Diğer') echo 'selected'; ?>>Diğer</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Dalışın Amacı:</td>
                        <td><input type="text" name="diving_purpose" placeholder="Dalışın Amacını Giriniz" required></td>
                    </tr>
                    <tr>
                        <td>Kullanılan Alet ve Cihazlar:</td>
                        <td><input type="text" name="tools" placeholder="Kullanılan Alet ve Cihazları Yazınız" required></td>
                    </tr>
                    <tr>
                        <td>Dalış Takımı:</td>
                        <td>
                            <select select name="tools_devices" required>
                                <option value="Scuba" <?php if($equipment == 'Scuba') echo 'selected'; ?>>Scuba</option>
                                <option value="Nargile" <?php if($equipment == 'Nargile') echo 'selected'; ?>>Nargile</option>
                                <option value="MK-18" <?php if($equipment == 'MK-18') echo 'selected'; ?>>MK-18</option>
                                <option value="MK-17" <?php if($equipment == 'MK-17') echo 'selected'; ?>>MK-17</option>
                                <option value="Tam Yüz Maskesi" <?php if($equipment == 'Tam Yüz Maskesi') echo 'selected'; ?>>Tam Yüz Maskesi</option>
                                <option value="Basınç OD" <?php if($equipment == 'Basınç OD') echo 'selected'; ?>>Basınç OD</option>
                                <option value="Diğer" <?php if($equipment == 'Diğer') echo 'selected'; ?>>Diğer</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Dalış Amiri:</td>
                        <td><input type="text" name="supervisor" placeholder="Dalış Amirini Giriniz"></td>
                    </tr>
                </table>
                <button class="diving_plan_create">Dalış Planı Oluştur</button>
            </form>
        </div>
    </div>
    <footer>
        <p>&copy; 2025 DivingLog Uygulaması</p>
    </footer>
</body>
</html>