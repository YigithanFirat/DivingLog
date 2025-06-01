<?php
// .env dosyasını oku
$envPath = __DIR__ . '/.env';
if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0 || strpos($line, '=') === false) continue;
        list($name, $value) = explode('=', $line, 2);
        putenv(trim($name) . '=' . trim($value));
    }
}

// Ortam değişkenlerinden veritabanı bilgilerini al
$hostname = getenv('DB_HOST');
$user     = getenv('DB_USER');
$password = getenv('DB_PASS');
$database = getenv('DB_DATA');

// Bağlantıyı oluştur
$mysqlB = mysqli_connect($hostname, $user, $password, $database);

// Hata kontrolü
if (!$mysqlB) {
    die("Veritabanı bağlantısı başarısız! Lütfen daha sonra tekrar deneyin.");
}

// Karakter setini ayarla
mysqli_set_charset($mysqlB, "utf8mb4");
?>