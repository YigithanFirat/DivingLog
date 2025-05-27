<?php
    include('../../config.php');

    // ID kontrolü: GET parametresi var mı ve geçerli bir tam sayı mı?
    if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
        header("Location: manage_users.php?status=error&msg=Geçersiz+ID");
        exit();
    }

    $userId = (int) $_GET['id'];

    // Kullanıcıyı silme işlemi
    $stmt = $mysqlB->prepare("DELETE FROM users WHERE id = ?");
    if (!$stmt) {
        header("Location: manage_users.php?status=error&msg=Sorgu+hazırlanamadı");
        exit();
    }

    $stmt->bind_param("i", $userId);
    if ($stmt->execute()) {
        header("Location: manage_users.php?status=success&msg=Kullanıcı+silindi");
    } else {
        header("Location: manage_users.php?status=error&msg=Kullanıcı+silinemedi");
    }

    $stmt->close();
    exit();
?>