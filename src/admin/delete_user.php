<?php
require_once('../../config.php');

// Yalnızca POST isteklerine izin ver
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: manage_users.php?status=error&msg=Geçersiz+istek+yöntemi");
    exit();
}

// Kullanıcı ID'sini filtrele
$userId = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$userId || $userId <= 0) {
    header("Location: manage_users.php?status=error&msg=Geçersiz+veya+eksik+ID");
    exit();
}

// Kullanıcının gerçekten var olup olmadığını kontrol et
$checkStmt = $mysqlB->prepare("SELECT id FROM users WHERE id = ?");
if (!$checkStmt) {
    error_log("Kullanıcı kontrol sorgusu hazırlanamadı: " . $mysqlB->error);
    header("Location: manage_users.php?status=error&msg=Sistem+hatasi");
    exit();
}
$checkStmt->bind_param("i", $userId);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

if ($checkResult->num_rows === 0) {
    $checkStmt->close();
    header("Location: manage_users.php?status=error&msg=Kullanıcı+bulunamadı");
    exit();
}
$checkStmt->close();

// Silme işlemi
$deleteStmt = $mysqlB->prepare("DELETE FROM users WHERE id = ?");
if (!$deleteStmt) {
    error_log("Silme sorgusu hazırlanamadı: " . $mysqlB->error);
    header("Location: manage_users.php?status=error&msg=Silme+hazırlığı+başarısız");
    exit();
}
$deleteStmt->bind_param("i", $userId);

if ($deleteStmt->execute()) {
    header("Location: manage_users.php?status=success&msg=Kullanıcı+başarıyla+silindi");
} else {
    error_log("Silme işlemi başarısız: " . $deleteStmt->error);
    header("Location: manage_users.php?status=error&msg=Kullanıcı+silinemedi");
}
$deleteStmt->close();
exit();
?>