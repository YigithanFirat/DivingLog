<?php
function env($key, $default = null) {
    $value = getenv($key);

    // Eğer false değil ama string değilse (ör: nesne dönerse)
    if ($value === false || !is_string($value)) {
        return $default;
    }
    return $value;
}

// Ortam değişkenlerinden oku, yoksa default değerleri kullan
$hostname = env('DB_HOST', 'localhost');
$user = env('DB_USER', 'root');
$password = env('DB_PASS', '');
$database = env('DB_NAME', 'divinglog');

$mysqlB = mysqli_connect($hostname, $user, $password, $database);

if (!$mysqlB) {
    die("Veritabanı bağlantısı başarısız! Lütfen daha sonra tekrar deneyin.");
}

mysqli_set_charset($mysqlB, "utf8mb4");
?>