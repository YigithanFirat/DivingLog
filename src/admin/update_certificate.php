<?php
include('../../config.php');

// POST isteğiyle geldiyse devam et
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Verileri güvenli şekilde al ve filtrele
    $id = intval($_POST['id'] ?? 0);
    $certificate_name = trim($_POST['certificate_name'] ?? '');
    $issuing_organization = trim($_POST['issuing_organization'] ?? '');
    $issue_date = $_POST['issue_date'] ?? null;
    $expiration_date = $_POST['expiration_date'] ?? null;
    $certificate_level = trim($_POST['certificate_level'] ?? '');
    $certificate_number = trim($_POST['certificate_number'] ?? '');
    $notes = trim($_POST['notes'] ?? '');

    // Temel doğrulamalar
    if ($id <= 0 || empty($certificate_name) || empty($issuing_organization) || empty($issue_date)) {
        echo "Zorunlu alanlar eksik.";
        exit;
    }

    // Güncelleme sorgusu
    $query = "UPDATE certificate SET 
                certificate_name = ?, 
                issuing_organization = ?, 
                issue_date = ?, 
                expiration_date = ?, 
                certificate_level = ?, 
                certificate_number = ?, 
                notes = ?
              WHERE id = ?";

    $stmt = mysqli_prepare($mysqlB, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sssssssi", 
            $certificate_name,
            $issuing_organization,
            $issue_date,
            $expiration_date,
            $certificate_level,
            $certificate_number,
            $notes,
            $id
        );

        $result = mysqli_stmt_execute($stmt);

        if ($result) {
            header("Location: certificate_list.php?success=1");
            exit;
        } else {
            echo "Güncelleme başarısız: " . mysqli_stmt_error($stmt);
        }

        mysqli_stmt_close($stmt);
    } else {
        echo "Sorgu hazırlanamadı: " . mysqli_error($mysqlB);
    }
} else {
    echo "Geçersiz istek.";
}
?>