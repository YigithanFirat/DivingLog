<?php
session_start();
include('../../config.php');

if (isset($_SESSION['tcno'])) {
    $tcno = $_SESSION['tcno'];

    // Hazırlıklı ifade ile güvenli sorgu
    $stmt = mysqli_prepare($mysqlB, "UPDATE users SET login = 0 WHERE tcno = ?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $tcno);
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            session_unset();
            session_destroy();
            header("Location: ../index.php");
            exit();
        } else {
            mysqli_stmt_close($stmt);
            echo "Bir hata oluştu. Lütfen tekrar deneyin.";
        }
    } else {
        echo "Veritabanı sorgusu hazırlanamadı.";
    }
} else {
    header("Location: ../index.php");
    exit();
}
?>