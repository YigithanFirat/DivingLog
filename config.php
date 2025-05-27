<?php
    $hostname = "localhost";
    $user = "root";
    $password = "[priadon1.5]";
    $database = "divinglog";
    $mysqlB = mysqli_connect($hostname, $user, $password, $database);
    if(!$mysqlB)
    {
        die("Veritabanı bağlantısı başarısız! Lütfen daha sonra tekrar deneyin.");
    }
    mysqli_set_charset($mysqlB, "utf8mb4");
?>