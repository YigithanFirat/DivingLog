<?php
require_once('../../config.php');

// Yalnızca GET yerine POST kullanmak daha güvenlidir
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: manage_users.php?status=error&msg=Geçersiz+istek+yöntemi");
    exit();
}

// ID kontrolü
if (!isset($_POST['id']) || !filter_var($_POST['id'], FILTER_VALIDATE_INT)) {
    header("Location: manage_users.php?status=error&msg=Geçersiz+ID");
    exit();
}

$userId = (int) $_POST['id'];

// Önce böyle bir kullanıcı gerçekten var mı diye kontrol etmek isteyebilirsin
$checkStmt = $mysqlB->prepare("SELECT id FROM users WHERE id = ?");
$checkStmt->bind_param("i", $userId);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

if ($checkResult->num_rows === 0) {
    $checkStmt->close();
    header("Location: manage_users.php?status=error&msg=Kullanıcı+bulunamadı");
    exit();
}
$checkStmt->close();

// Kullanıcıyı sil
$stmt = $mysqlB->prepare("DELETE FROM users WHERE id = ?");
if (!$stmt) {
    header("Location: manage_users.php?status=error&msg=Sorgu+hazırlanamadı");
    exit();
}

$stmt->bind_param("i", $userId);
if ($stmt->execute()) {
    header("Location: manage_users.php?status=success&msg=Kullanıcı+başarıyla+silindi");
} else {
    header("Location: manage_users.php?status=error&msg=Silme+i̇şlemi+başarısız");
}
$stmt->close();
exit();
?>