<?php
    include('../../config.php');
    if(isset($_GET['id']) && is_numeric($_GET['id']))
    {
        $userId = intval($_GET['id']);
        $sql = "DELETE FROM users WHERE id = ?";
        $stmt = $mysqlB->prepare($sql);
        $stmt->bind_param("i", $userId);
        if($stmt->execute())
        {
            header("Location: manage_users.php?status=success&msg=Kullanıcı silindi.");
        }
        else
        {
            header("Location: manage_users.php?status=error&msg=Kullanıcı silinemedi.");
        }
        $stmt->close();
    }
    else
    {
        header("Location: manage_users.php?status=error&msg=Geçersiz kullanıcı ID.");
    }
    exit();
?>