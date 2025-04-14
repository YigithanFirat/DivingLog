<?php
    session_start();
    include('../../config.php');
    if(isset($_SESSION['tcno']))
    {
        $tcno = $_SESSION['tcno'];
        $update_sql = "UPDATE users SET login = 0 WHERE tcno = '$tcno'";
        if(mysqli_query($mysqlB, $update_sql))
        {
            session_unset();
            session_destroy();
            header("Location: ../index.php");
            exit();
        }
        else
        {
            echo "Bir hata oluştu. Lütfen tekrar deneyin.";
        }
    }
    else
    {
        header("Location: ../index.php");
        exit();
    }
?>