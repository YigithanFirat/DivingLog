<?php
    $date = date('d/m/Y');
    $diving_no = 1;
    $max_depth = 6;
    $max_depth_meter = 2;
    $gas = 'Hava, Nitrox, Helioks, Trimks, Oksijen';
    $gas_percentage = 21;
    $equipment = 'Scuba, Nargile, MK-18, MK-17, Tam Yüz Maskesi, Basınç OD, Diğer';
    $purpose = 'Eğitim Dalışı';
    $water_type = 'Tatlı Su';
    $clothing = 'Kuru, Islak, Diğer';
    $tools = 'Scuba, Nargile';
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
                        <td><input type="text" placeholder="Derinliği Feet Cinsinden Giriniz"></td>
                    </tr>
                    <tr>
                        <td>Planlanan Derinlik (Metre):</td>
                        <td><input type="text" placeholder="Derinliği Metre Cinsinden Giriniz"></td>
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
                            <input type="radio" name="clothing" value="Kuru" <?php if($clothing == 'Kuru') echo 'checked'; ?>> Kuru
                            <input type="radio" name="clothing" value="Islak" <?php if($clothing == 'Islak') echo 'checked'; ?>> Islak
                            <input type="radio" name="clothing" value="Islak" <?php if($clothing == 'Diğer') echo 'checked'; ?>> Diğer
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
                </table>
            </form>
        </div>
    </div>
    <footer>
        <p>&copy; 2025 DivingLog Uygulaması</p>
    </footer>
</body>
</html>