<?php
    include('../../config.php');
    if($_SERVER["REQUEST_METHOD"] == "POST")
    {
        $stmt = $mysqlB->prepare("UPDATE diving_plans SET 
            tcno = ?, minutes = ?, diving_location = ?, water_type = ?, 
            depth_feet = ?, depth_meter = ?, respiration = ?, clothing = ?, 
            diving_purpose = ?, tools = ?, tools_devices = ?, supervisor = ? 
            WHERE id = ?");
        $stmt->bind_param("sissddssssssi",
            $_POST['tcno'],
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
        if($stmt->execute())
        {
            header("Location: manage_diving.php?success=1");
            exit();
        }
        else
        {
            echo "Güncelleme hatası: " . $stmt->error;
        }
        $stmt->close();
    }
    if(isset($_GET['id']))
    {
        $id = $_GET['id'];
        $stmt = $mysqlB->prepare("SELECT * FROM diving_plans WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows == 1)
        {
            $row = $result->fetch_assoc();
        }
        else
        {
            echo "Geçersiz ID.";
            exit();
        }
        $stmt->close();
    }
    else
    {
        echo "ID belirtilmedi.";
        exit();
    }
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Dalış Planı Düzenle</title>
    <link rel="stylesheet" href="../CSS/edit_diving_plan.css">
    <link rel="web icon" href="../images/divinglog.png">
</head>
<body>
    <div class="container">
        <h2>Dalış Planı Düzenle</h2>
        <form action="" method="POST">
            <input type="hidden" name="id" value="<?= $row['id'] ?>">

            <label>T.C No:</label>
            <input type="text" name="tcno" value="<?= $row['tcno'] ?>" required>

            <label>Dakika:</label>
            <input type="number" name="minutes" value="<?= $row['minutes'] ?>" required>

            <label>Lokasyon:</label>
            <input type="text" name="diving_location" value="<?= $row['diving_location'] ?>" required>

            <label>Dalış Ortamı:</label>
            <input type="text" name="water_type" value="<?= $row['water_type'] ?>" required>

            <label>Derinlik (Feet):</label>
            <input type="number" name="depth_feet" value="<?= $row['depth_feet'] ?>">

            <label>Derinlik (Metre):</label>
            <input type="number" name="depth_meter" value="<?= $row['depth_meter'] ?>">

            <label>Solunum:</label>
            <input type="text" name="respiration" value="<?= $row['respiration'] ?>">

            <label>Elbise:</label>
            <input type="text" name="clothing" value="<?= $row['clothing'] ?>">

            <label>Amaç:</label>
            <input type="text" name="diving_purpose" value="<?= $row['diving_purpose'] ?>">

            <label>Aletler:</label>
            <input type="text" name="tools" value="<?= $row['tools'] ?>">

            <label>Takım:</label>
            <input type="text" name="tools_devices" value="<?= $row['tools_devices'] ?>">

            <label>Amir:</label>
            <input type="text" name="supervisor" value="<?= $row['supervisor'] ?>">

            <button type="submit">Güncelle</button>
            <a href="manage_diving.php" class="cancel-btn">İptal</a>
        </form>
    </div>
</body>
</html>