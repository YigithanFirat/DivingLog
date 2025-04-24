<?php
    include('../../config.php');
    if(!isset($_GET['id']))
    {
        die('Geçerli bir ID belirtilmedi.');
    }
    $id = $_GET['id'];
    $stmt = $mysqlB->prepare("DELETE FROM diving_plans WHERE id = ?");
    $stmt->bind_param("i", $id);
    if($stmt->execute())
    {
        header("Location: manage_diving.php?deleted=1");
        exit();
    }
    else
    {
        echo "Silme işlemi başarısız: " . $stmt->error;
    }
    $stmt->close();
?>