<?php
    $hostname = "localhost";
    $user = "root";
    $password = "[priadon1.5]";
    $database = "divinglog";
    $mysqlB = mysqli_connect($hostname, $user, $password, $database);
    if(mysqli_connect_error())
    {
        die("Veritabanı bağlantısı başarısız! HATA: " . mysqli_connect_error());
    }
?>