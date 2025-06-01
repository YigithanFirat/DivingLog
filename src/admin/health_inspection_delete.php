<?php
session_start();
include('../../config.php');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['message'] = ['type' => 'danger', 'text' => 'Geçersiz silme isteği.'];
    header('Location: health_inspection_list.php');
    exit;
}

$id = (int)$_GET['id'];

// Kayıt var mı kontrol et
$checkQuery = "SELECT id FROM health_inspections WHERE id = $id";
$checkResult = mysqli_query($mysqlB, $checkQuery);

if (mysqli_num_rows($checkResult) === 0) {
    $_SESSION['message'] = ['type' => 'warning', 'text' => 'Silinecek kayıt bulunamadı.'];
    header('Location: health_inspection_list.php');
    exit;
}

// Silme işlemi
$deleteQuery = "DELETE FROM health_inspections WHERE id = $id";
if (mysqli_query($mysqlB, $deleteQuery)) {
    $_SESSION['message'] = ['type' => 'success', 'text' => 'Sağlık raporu başarıyla silindi.'];
} else {
    $_SESSION['message'] = ['type' => 'danger', 'text' => 'Silme işlemi başarısız.'];
}

header('Location: health_inspection_list.php');
exit;