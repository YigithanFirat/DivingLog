<?php
require_once('../../config.php');

// Yalnızca POST yöntemiyle çalışmasına izin ver
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: manage_diving.php?error=gecersiz_istek");
    exit();
}

// ID ve TC kontrolü
if (
    !isset($_POST['id']) || !filter_var($_POST['id'], FILTER_VALIDATE_INT) ||
    !isset($_POST['tcno']) || !preg_match('/^\d{11}$/', $_POST['tcno']) // TC 11 haneli olmalı
) {
    header("Location: manage_diving.php?error=gecersiz_veri");
    exit();
}

$id = (int) $_POST['id'];
$tcno = $_POST['tcno'];

// Silme sorgusu
$stmt = $mysqlB->prepare("DELETE FROM diving_plans WHERE id = ?");
if (!$stmt) {
    header("Location: manage_diving.php?tcno=$tcno&error=sorgu_hazirlanamadi");
    exit();
}

$stmt->bind_param("i", $id);
if ($stmt->execute()) {
    header("Location: manage_diving.php?tcno=$tcno&success=silindi");
} else {
    header("Location: manage_diving.php?tcno=$tcno&error=silinemedi");
}
$stmt->close();
exit();
?>