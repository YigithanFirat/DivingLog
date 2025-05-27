<?php
// Basit env dosyası okuma fonksiyonu
function env($key, $default = null) {
    $value = getenv($key);
    if ($value === false) {
        return $default;
    }
    return $value;
}

$hostname = env('DB_HOST', 'localhost');
$user = env('DB_USER', 'root');
$password = env('DB_PASS', '');
$database = env('DB_NAME', 'divinglog');

$mysqlB = mysqli_connect($hostname, $user, $password, $database);

if (!$mysqlB) {
    // Genel hata mesajı, detay veritabanı bilgileri gizli kalır
    die("Veritabanı bağlantısı başarısız! Lütfen daha sonra tekrar deneyin.");
}

// UTF-8 karakter seti ayarla
mysqli_set_charset($mysqlB, "utf8mb4");
?>