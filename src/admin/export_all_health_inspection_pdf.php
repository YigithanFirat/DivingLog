<?php
require_once('../../config.php');
require_once('../TCPDF-main/tcpdf.php'); // TCPDF dizin yolunu kendi sistemine göre ayarla

// Veriyi çek
$stmt = $mysqlB->prepare("
    SELECT muayene_tarihi, created_at, onaylayan, onaylanan 
    FROM health_inspections 
    ORDER BY muayene_tarihi DESC
");
$stmt->execute();
$result = $stmt->get_result();

// Veri kontrolü
if (!$result || $result->num_rows === 0) {
    die('Hiç sağlık raporu bulunamadı.');
}

// PDF nesnesi
$pdf = new TCPDF();
$pdf->SetCreator('DivingLog');
$pdf->SetAuthor('DivingLog Sistemi');
$pdf->SetTitle('Tüm Sağlık Raporları');
$pdf->SetMargins(20, 20, 20);
$pdf->SetAutoPageBreak(true, 20);
$pdf->SetFont('dejavusans', '', 12);

// Kapak sayfasını atla (logo ve yazılar kaldırıldı)

// 📄 Her kayıt için yeni sayfa
while ($row = $result->fetch_assoc()) {
    $pdf->AddPage();

    $html = '
    <h2 style="text-align:center;">Sağlık Raporu</h2>
    <table border="1" cellpadding="6" cellspacing="0" style="font-size:12pt;">
        <tr><td><b>Muayene Tarihi</b></td><td>' . date('d.m.Y', strtotime($row['muayene_tarihi'])) . '</td></tr>
        <tr><td><b>Onaylayan Doktor</b></td><td>' . htmlspecialchars($row['onaylayan']) . '</td></tr>
        <tr><td><b>Onaylanan Kişi</b></td><td>' . htmlspecialchars($row['onaylanan']) . '</td></tr>
        <tr><td><b>Kayıt Tarihi</b></td><td>' . date('d.m.Y H:i', strtotime($row['created_at'])) . '</td></tr>
    </table>';
    
    $pdf->writeHTML($html, true, false, true, false, '');
}

// PDF çıktısı
$pdf->Output('saglik_raporlari.pdf', 'I');
exit();
?>