<?php
include('../session_guard.php');
require_once('../../config.php');
require_once('../TCPDF-main/tcpdf.php');

$query = "SELECT * FROM certificate ORDER BY issue_date DESC";
$result = mysqli_query($mysqlB, $query);
if (!$result || mysqli_num_rows($result) == 0) {
    die("Sertifika bulunamadı.");
}

$pdf = new TCPDF();
$pdf->SetCreator('DivingLog');
$pdf->SetAuthor('DivingLog Sistemi');
$pdf->SetTitle('Tüm Sertifikalar');
$pdf->SetMargins(20, 20, 20);
$pdf->SetAutoPageBreak(true, 20);
$pdf->SetFont('dejavusans', '', 12);

// Her kayıt için ayrı sayfa
while ($row = mysqli_fetch_assoc($result)) {
    $pdf->AddPage();

    $html = '
    <h2 style="text-align:center;">Sertifika Bilgileri</h2>
    <table border="1" cellpadding="6" cellspacing="0" style="font-size:12pt;">
        <tr><td><b>Ad Soyad</b></td><td>' . htmlspecialchars($row['full_name']) . '</td></tr>
        <tr><td><b>TC</b></td><td>' . htmlspecialchars($row['tc']) . '</td></tr>
        <tr><td><b>Sertifika Adı</b></td><td>' . htmlspecialchars($row['certificate_name']) . '</td></tr>
        <tr><td><b>Veren Kuruluş</b></td><td>' . htmlspecialchars($row['issuing_organization']) . '</td></tr>
        <tr><td><b>Veriliş Tarihi</b></td><td>' . htmlspecialchars($row['issue_date']) . '</td></tr>
        <tr><td><b>Geçerlilik Tarihi</b></td><td>' . htmlspecialchars($row['expiration_date']) . '</td></tr>
        <tr><td><b>Seviye</b></td><td>' . htmlspecialchars($row['certificate_level']) . '</td></tr>
        <tr><td><b>Sertifika No</b></td><td>' . htmlspecialchars($row['certificate_number']) . '</td></tr>
        <tr><td><b>Notlar</b></td><td>' . nl2br(htmlspecialchars($row['notes'])) . '</td></tr>
    </table>';
    
    $pdf->writeHTML($html, true, false, true, false, '');
}

$pdf->Output('tum_sertifikalar.pdf', 'I');
exit();
?>