<?php
require_once('../../config.php');
require_once('../TCPDF-main/tcpdf.php');  // TCPDF yolu kendi yapına göre kontrol et

if (!isset($_GET['tcno']) || empty($_GET['tcno'])) {
    die('Geçersiz TC numarası.');
}

$tcno = $_GET['tcno'];

// Kullanıcının dalışlarını çek
$stmt = $mysqlB->prepare("
    SELECT diving_plans.*, users.ad, users.soyad 
    FROM diving_plans 
    LEFT JOIN users ON diving_plans.tcno = users.tcno 
    WHERE diving_plans.tcno = ?
    ORDER BY diving_plans.created_at DESC
");
$stmt->bind_param('s', $tcno);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die('Belirtilen TC numarasına ait dalış bulunamadı.');
}

// TCPDF ayarları
$pdf = new TCPDF();
$pdf->SetCreator('DivingLog');
$pdf->SetAuthor('DivingLog Sistemi');
$pdf->SetTitle('Dalış Planları - ' . $tcno);
$pdf->SetMargins(15, 20, 15);
$pdf->SetAutoPageBreak(true, 20);
$pdf->SetFont('dejavusans', '', 12);

// Başlık için renkleri belirle
$pdf->SetFillColor(13, 44, 67);  // koyu mavi arka plan
$pdf->SetTextColor(255, 255, 255); // beyaz yazı

$pageNum = 0;

while ($row = $result->fetch_assoc()) {
    $pdf->AddPage();
    $pageNum++;

    if ($pageNum === 1) {
        // Sadece ilk sayfada başlık göster
        $pdf->Cell(0, 10, "Dalış Planı - TC No: $tcno", 0, 1, 'C', 1);
        $pdf->Ln(5);
    } else {
        // Diğer sayfalarda başlık yok, ama istersen boşluk bırakabilirsin
        $pdf->Ln(15);
    }

    $pdf->SetTextColor(0, 0, 0); // Siyah metin

    // Dalış bilgileri tablosu
    $html = '
    <table border="1" cellpadding="6" cellspacing="0" style="font-size:12pt; border-collapse: collapse;">
        <tr bgcolor="#0d2c43" style="color:#fff;">
            <th style="width:25%;">Alan</th>
            <th style="width:75%;">Detay</th>
        </tr>
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

$pdf->Output("diving_plans_{$tcno}.pdf", 'I');
exit();
?>