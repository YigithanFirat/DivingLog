<?php
require_once('../../config.php'); // Veritabanı bağlantısı
require_once('../TCPDF-main/tcpdf.php');

// Sertifika ID'sini al
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Geçersiz ID");
}

$certificate_id = intval($_GET['id']);

// Veritabanından veriyi al
$query = "SELECT c.*, CONCAT(u.ad, ' ', u.soyad) AS user_name
          FROM certificate c
          LEFT JOIN users u ON c.user_id = u.id
          WHERE c.id = $certificate_id";

$result = mysqli_query($mysqlB, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    die("Sertifika bulunamadı.");
}

$data = mysqli_fetch_assoc($result);

// TCPDF nesnesi oluştur
$pdf = new TCPDF();
$pdf->SetCreator('DivingLog');
$pdf->SetAuthor('DivingLog Sistemi');
$pdf->SetTitle('Sertifika PDF');
$pdf->SetSubject('Sertifika Bilgisi');
$pdf->SetMargins(20, 20, 20);
$pdf->AddPage();

// Türkçe karakterleri destekleyen yazı tipi ayarla
$pdf->SetFont('dejavusans', '', 10, '', true);

// Stil tanımları ve içerik
$html = '
<style>
    h1 { color: #005a87; text-align: center; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { padding: 10px; border: 1px solid #ddd; }
    th { background-color: #f0f8ff; color: #005a87; }
</style>

<h1>Sertifika Bilgisi</h1>
<table>
    <tr><th>Kullanıcı</th><td>' . htmlspecialchars($data['user_name']) . '</td></tr>
    <tr><th>Sertifika Adı</th><td>' . htmlspecialchars($data['certificate_name']) . '</td></tr>
    <tr><th>Veren Kuruluş</th><td>' . htmlspecialchars($data['issuing_organization']) . '</td></tr>
    <tr><th>Veriliş Tarihi</th><td>' . htmlspecialchars($data['issue_date']) . '</td></tr>
    <tr><th>Geçerlilik Tarihi</th><td>' . htmlspecialchars($data['expiration_date']) . '</td></tr>
    <tr><th>Seviye</th><td>' . htmlspecialchars($data['certificate_level']) . '</td></tr>
    <tr><th>Sertifika No</th><td>' . htmlspecialchars($data['certificate_number']) . '</td></tr>
    <tr><th>Notlar</th><td>' . nl2br(htmlspecialchars($data['notes'])) . '</td></tr>
</table>
';

// HTML içeriği PDF'e ekle
$pdf->writeHTML($html, true, false, true, false, '');

// PDF çıktısını indir
$pdf->Output('sertifika_' . $certificate_id . '.pdf', 'I');
?>