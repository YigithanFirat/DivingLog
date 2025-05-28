<?php
require_once('../../config.php'); // Veritabanı bağlantısı
require_once('../../tcpdf/tcpdf.php'); // TCPDF dahil et

// PDF ayarları
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('DivingLog Uygulaması');
$pdf->SetTitle('Sağlık Muayeneleri Raporu');
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(TRUE, 10);
$pdf->AddPage();
$pdf->SetFont('dejavusans', '', 10);

// Başlık
$html = '<h2 style="text-align:center;">Sağlık Muayeneleri Raporu</h2><br><table border="1" cellpadding="4">
<thead>
<tr style="background-color:#f2f2f2; font-weight:bold;">
    <th>T.C. No</th>
    <th>Boy (cm)</th>
    <th>Kilo (kg)</th>
    <th>Göz</th>
    <th>Kulak</th>
    <th>Burun</th>
    <th>Akciğer</th>
    <th>Karar</th>
</tr>
</thead><tbody>';

// Verileri al
$sql = "SELECT tcno, height, weight, eye, ear, nose, lung, decision FROM health_inspection ORDER BY tcno ASC";
$result = mysqli_query($mysqlB, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $html .= '<tr>
            <td>' . htmlspecialchars($row['tcno']) . '</td>
            <td>' . htmlspecialchars($row['height']) . '</td>
            <td>' . htmlspecialchars($row['weight']) . '</td>
            <td>' . htmlspecialchars($row['eye']) . '</td>
            <td>' . htmlspecialchars($row['ear']) . '</td>
            <td>' . htmlspecialchars($row['nose']) . '</td>
            <td>' . htmlspecialchars($row['lung']) . '</td>
            <td>' . htmlspecialchars($row['decision']) . '</td>
        </tr>';
    }
} else {
    $html .= '<tr><td colspan="8" style="text-align:center;">Kayıt bulunamadı.</td></tr>';
}

$html .= '</tbody></table>';

// PDF çıktısı oluştur
$pdf->writeHTML($html, true, false, true, false, '');

// PDF dosyasını tarayıcıya gönder
$pdf->Output('Saglik_Muayeneleri_Raporu.pdf', 'I'); // 'I' -> tarayıcıda göster, 'D' -> indir
?>