<?php
    include('../../config.php');

    // ID doğrulama ve güvenli alma
    if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
        die('Geçerli bir ID belirtilmedi.');
    }

    $id = (int)$_GET['id'];

    // Sorguyu hazırla
    $stmt = $mysqlB->prepare("DELETE FROM diving_plans WHERE id = ?");
    if (!$stmt) {
        die("Sorgu hazırlanamadı: " . $mysqlB->error);
    }

    // Parametreyi bağla ve çalıştır
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        // Başarılıysa yönlendir
        header("Location: manage_diving.php?deleted=1");
        exit();
    } else {
        // Hata varsa göster
        echo "Silme işlemi başarısız: " . $stmt->error;
    }

    $stmt->close();
?>