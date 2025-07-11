<?php
include('../session_guard.php');
// Output başlamadan önce hiçbir şey gönderilmediğinden emin ol
ob_start();

require_once('../../config.php');
require_once('../TCPDF-main/tcpdf.php');

// Verileri çek
$query = "
    SELECT diving_plans.*, users.ad, users.soyad 
    FROM diving_plans 
    LEFT JOIN users ON diving_plans.tcno = users.tcno 
    ORDER BY diving_plans.created_at DESC
";

$result = $mysqlB->query($query);
if (!$result || $result->num_rows === 0) {
    // PDF çıktısı olmadığından önce header gönderebiliriz
    ob_end_clean();
    die('Veritabanında dalış kaydı bulunamadı.');
}

// TCPDF ayarları
$pdf = new TCPDF();
$pdf->SetCreator('DivingLog');
$pdf->SetAuthor('DivingLog Sistemi');
$pdf->SetTitle('Tüm Dalış Planları');
$pdf->SetMargins(15, 20, 15);
$pdf->SetAutoPageBreak(true, 20);
$pdf->SetFont('dejavusans', '', 12);

// Kapak sayfası
$pdf->AddPage();

// Logo (şeffaf)
$pdf->Image('../images/istelogo.png', 65, 40, 80, '', '', '', '', false, 300, '', false, false, 0.2);

// Başlıklar
$pdf->SetY(130);
$pdf->SetFont('dejavusans', 'B', 16);
$pdf->Cell(0, 10, 'T.C. İskenderun Teknik Üniversitesi', 0, 1, 'C');
$pdf->Cell(0, 10, 'Denizcilik Meslek Yüksekokulu', 0, 1, 'C');
$pdf->Cell(0, 10, "Sualtı Teknolojisi Programı", 0, 1, 'C');
$pdf->Cell(0, 10, 'Dalış Defteri', 0, 1, 'C');

$pdf->Ln(15);
$pdf->SetFont('dejavusans', '', 11);
$pdf->Cell(0, 10, 'Tüm kayıtlar aşağıda dalış başına bir sayfa olacak şekilde sunulmuştur.', 0, 1, 'C');

$pdf->AddPage();

$pdf->SetFillColor(13, 44, 67);
$pdf->SetTextColor(0, 0, 0);

$pageCount = 0;

while ($row = $result->fetch_assoc()) {
    if ($pageCount > 0) {
        $pdf->AddPage();
    }

    $pageCount++;

    $html = '
    <table border="1" cellpadding="6" cellspacing="0" style="font-size:12pt;">
        <tr bgcolor="#0d2c43" style="color:#fff;">
            <th style="width:30%;">Alan</th>
            <th style="width:70%;">Detay</th>
        </tr>
        <tr><td><b>TC No</b></td><td>' . htmlspecialchars($row['tcno']) . '</td></tr>
        <tr><td><b>Ad</b></td><td>' . htmlspecialchars($row['ad']) . '</td></tr>
        <tr><td><b>Soyad</b></td><td>' . htmlspecialchars($row['soyad']) . '</td></tr>
        <tr><td><b>Dakika</b></td><td>' . htmlspecialchars($row['minutes']) . '</td></tr>
        <tr><td><b>Lokasyon</b></td><td>' . htmlspecialchars($row['diving_location']) . '</td></tr>
        <tr><td><b>Dalış Ortamı</b></td><td>' . htmlspecialchars($row['water_type']) . '</td></tr>
        <tr><td><b>Derinlik (m)</b></td><td>' . htmlspecialchars($row['depth_meter']) . '</td></tr>
        <tr><td><b>Solunum</b></td><td>' . htmlspecialchars($row['respiration']) . '</td></tr>
        <tr><td><b>Elbise</b></td><td>' . htmlspecialchars($row['clothing']) . '</td></tr>
        <tr><td><b>Amaç</b></td><td>' . htmlspecialchars($row['diving_purpose']) . '</td></tr>
        <tr><td><b>Takım</b></td><td>' . htmlspecialchars($row['tools_devices']) . '</td></tr>
        <tr><td><b>Amir</b></td><td>' . htmlspecialchars($row['supervisor']) . '</td></tr>
        <tr><td><b>Tarih</b></td><td>' . htmlspecialchars($row['created_at']) . '</td></tr>
    </table>
    ';

    $pdf->writeHTML($html, true, false, true, false, '');
}

// Çıktı tamponunu temizle, PDF verisi dışında hiçbir şey gitmesin
ob_end_clean();
$pdf->Output('tum_dalislar.pdf', 'I');
exit();
?>