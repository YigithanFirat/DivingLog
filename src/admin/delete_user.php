<?php
include('../session_guard.php');
include('../sidebarmenu.php');
require_once('../../config.php');

// Yalnızca POST isteklerine izin ver
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: manage_users.php?status=error&msg=Geçersiz+istek+yöntemi");
    exit();
}

$userId = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$userId || $userId <= 0) {
    header("Location: manage_users.php?status=error&msg=Geçersiz+veya+eksik+ID");
    exit();
}

// Kullanıcının tcno'sunu al
$getUserStmt = $mysqlB->prepare("SELECT tcno FROM users WHERE id = ?");
if (!$getUserStmt) {
    error_log("TCNO sorgusu hazırlanamadı: " . $mysqlB->error);
    header("Location: manage_users.php?status=error&msg=TCNO+çekilemedi");
    exit();
}
$getUserStmt->bind_param("i", $userId);
$getUserStmt->execute();
$result = $getUserStmt->get_result();

if ($result->num_rows === 0) {
    $getUserStmt->close();
    header("Location: manage_users.php?status=error&msg=Kullanıcı+bulunamadı");
    exit();
}
$user = $result->fetch_assoc();
$tcno = $user['tcno'];
$getUserStmt->close();

// 1. Bu kullanıcıya ait tüm diving_plans kayıtlarını sil
$deleteDivesStmt = $mysqlB->prepare("DELETE FROM diving_plans WHERE tcno = ?");
if (!$deleteDivesStmt) {
    error_log("Dalış kayıtları silme sorgusu hazırlanamadı: " . $mysqlB->error);
    header("Location: manage_users.php?status=error&msg=Dalış+silme+hazırlığı+başarısız");
    exit();
}
$deleteDivesStmt->bind_param("s", $tcno);
$deleteDivesStmt->execute();
$deleteDivesStmt->close();

// 2. Kullanıcıyı sil
$deleteUserStmt = $mysqlB->prepare("DELETE FROM users WHERE id = ?");
if (!$deleteUserStmt) {
    error_log("Kullanıcı silme sorgusu hazırlanamadı: " . $mysqlB->error);
    header("Location: manage_users.php?status=error&msg=Silme+hazırlığı+başarısız");
    exit();
}
$deleteUserStmt->bind_param("i", $userId);

if ($deleteUserStmt->execute()) {
    header("Location: manage_users.php?status=success&msg=Kullanıcı+ve+Dalışlar+silindi");
} else {
    error_log("Kullanıcı silinemedi: " . $deleteUserStmt->error);
    header("Location: manage_users.php?status=error&msg=Kullanıcı+silinemedi");
}
$deleteUserStmt->close();
exit();
?>