<?php
include('../../config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;

    if (!$id) {
        echo "Geçersiz istek.";
        exit;
    }

    // Sertifikayı sil
    $query = "DELETE FROM certificate WHERE id = ?";
    $stmt = mysqli_prepare($mysqlB, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        header("Location: certificate_list.php?success=deleted");
        exit;
    } else {
        echo "Silme işlemi başarısız.";
    }

} else {
    echo "Geçersiz istek yöntemi.";
}
?>