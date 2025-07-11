<?php
include('../session_guard.php');
include('../../config.php');
require_once('../TCPDF-main/tcpdf.php'); // TCPDF kütüphane yolu (projene göre değiştir)

// TCPDF sınıfını genişletmeye gerek yok ama istersen özel header/footer ekleyebilirsin

// Yeni TCPDF nesnesi
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Belge bilgileri
$pdf->SetCreator('DivingLog');
$pdf->SetAuthor('DivingLog Admin');
$pdf->SetTitle('DivingLog - Kullanıcı Listesi');
$pdf->SetSubject('Kullanıcı Listesi');
$pdf->SetKeywords('DivingLog, Kullanıcı, Liste, PDF');

// Varsayılan başlık ve footer kapalı (istersen açabilirsin)
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Yazı tipi ayarla
$pdf->SetFont('dejavusans', '', 10); // UTF-8 destekli font

// Yeni sayfa ekle
$pdf->AddPage();

// Başlık yazısı
$pdf->SetFont('dejavusans', 'B', 14);
$pdf->Cell(0, 15, 'DivingLog - Kullanıcı Listesi', 0, 1, 'C');
$pdf->Ln(5);

// Tablo başlıkları
$pdf->SetFont('dejavusans', 'B', 10);
$header = ['Ad', 'Soyad', 'E-posta', 'Telefon', 'Haber Verilecek Kişi'];
$w = [30, 30, 60, 30, 50]; // sütun genişlikleri

for ($i = 0; $i < count($header); $i++) {
    $pdf->Cell($w[$i], 8, $header[$i], 1, 0, 'C', 1);
}
$pdf->Ln();

// Tablo verisi için arka plan rengi ayarı
// Tablo başlıkları
$pdf->SetFillColor(0, 51, 102); // koyu mavi gibi bir arka plan rengi
$pdf->SetTextColor(255, 255, 255); // Beyaz yazı rengi
$pdf->SetFont('dejavusans', 'B', 10);
$header = ['Ad', 'Soyad', 'E-posta', 'Telefon', 'Haber Verilecek Kişi'];
$w = [30, 30, 60, 32, 50]; // sütun genişlikleri

for ($i = 0; $i < count($header); $i++) {
    $pdf->Cell($w[$i], 8, $header[$i], 1, 0, 'C', 1); // 1: fill aktif
}
$pdf->Ln();

// Tablo veri kısmı için renk ve yazı ayarı
$pdf->SetFillColor(224, 235, 255); // Açık mavi arka plan
$pdf->SetTextColor(0, 0, 0);       // Siyah yazı
$pdf->SetFont('dejavusans', '', 10);

$fill = 0;

// Kullanıcıları çek
$sql = "SELECT ad, soyad, email, telefon, kaza_haber_kişi_ad_soyad FROM users ORDER BY ad ASC";
$result = mysqli_query($mysqlB, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $pdf->Cell($w[0], 8, $row['ad'], 'LR', 0, 'L', $fill);
        $pdf->Cell($w[1], 8, $row['soyad'], 'LR', 0, 'L', $fill);
        $pdf->Cell($w[2], 8, $row['email'], 'LR', 0, 'L', $fill);
        $pdf->Cell($w[3], 8, $row['telefon'], 'LR', 0, 'L', $fill);
        $pdf->Cell($w[4], 8, $row['kaza_haber_kişi_ad_soyad'], 'LR', 0, 'L', $fill);
        $pdf->Ln();
        $fill = !$fill;
    }
    // Tablo alt çizgisi
    $pdf->Cell(array_sum($w), 0, '', 'T');
} else {
    $pdf->Cell(array_sum($w), 10, 'Kayıtlı kullanıcı bulunamadı.', 1, 1, 'C');
}

// PDF çıktısı olarak indirme
$pdf->Output('DivingLog_Kullanici_Listesi.pdf', 'D');
exit;