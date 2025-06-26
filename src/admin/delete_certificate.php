<?php
require_once('../../config.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: certificate_list.php?error=method_not_allowed");
    exit;
}

if (!isset($_POST['id']) || !filter_var($_POST['id'], FILTER_VALIDATE_INT)) {
    header("Location: certificate_list.php?error=invalid_id");
    exit;
}

$certificateId = (int) $_POST['id'];

$stmt = $mysqlB->prepare("DELETE FROM certificate WHERE id = ?");
if (!$stmt) {
    header("Location: certificate_list.php?error=stmt_prepare_failed");
    exit;
}

$stmt->bind_param("i", $certificateId);

if ($stmt->execute()) {
    $stmt->close();
    header("Location: certificate_list.php?success=deleted");
    exit;
} else {
    $stmt->close();
    header("Location: certificate_list.php?error=delete_failed");
    exit;
}
?>