<?php
require_once('../../config.php'); // Veritabanı bağlantısı
require_once('../TCPDF-main/tcpdf.php'); // TCPDF kütüphanesi

// ID kontrolü
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Geçersiz sağlık raporu ID'si.");
}

$id = intval($_GET['id']);

// Sağlık raporunu veritabanından çek
$query = "SELECT * FROM health_inspections WHERE id = $id LIMIT 1";
$result = mysqli_query($mysqlB, $query);

if (!$result || mysqli_num_rows($result) === 0) {
    die("Sağlık raporu bulunamadı.");
}

$row = mysqli_fetch_assoc($result);

// Onaylayan ve onaylanan kişi bilgileri (veritabanında doğrudan ad-soyad olarak tutuluyor)
$onaylayan = htmlspecialchars($row['onaylayan']);
$onaylanan = htmlspecialchars($row['onaylanan']);
$muayene_tarihi = date('d.m.Y', strtotime($row['muayene_tarihi']));
$created_at = date('d.m.Y H:i', strtotime($row['created_at']));

// PDF ayarları
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('DivingLog Uygulaması');
$pdf->SetTitle('Sağlık Raporu #' . $id);
$pdf->SetMargins(20, 20, 20);
$pdf->AddPage();
$pdf->SetFont('dejavusans', '', 11);

// PDF içeriği
$html = '
<h2 style="text-align:center;">Sağlık Muayenesi Raporu</h2>
<hr>
<table cellpadding="6">
    <tr>
        <td width="40%"><strong>Muayene Tarihi:</strong></td>
        <td>' . $muayene_tarihi . '</td>
    </tr>
    <tr>
        <td><strong>Onaylayan Doktor:</strong></td>
        <td>' . $onaylayan . '</td>
    </tr>
    <tr>
        <td><strong>Onaylanan Kişi:</strong></td>
        <td>' . $onaylanan . '</td>
    </tr>
    <tr>
        <td><strong>Kayıt Tarihi:</strong></td>
        <td>' . $created_at . '</td>
    </tr>
</table>

<br><br>
<p>Bu rapor DivingLog uygulaması üzerinden oluşturulmuştur. Geçerliliği ilgili sağlık kuruluşu tarafından onaylandığında resmileşir.</p>
';

$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output("saglik_raporu_$id.pdf", 'I'); // 'I' tarayıcıda açar, 'D' indirir
?>