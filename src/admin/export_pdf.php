<?php
    ob_start();
    include('../../config.php');
    require_once('../TCPDF-main/tcpdf.php');
    $user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $sql = "SELECT * FROM users WHERE id = $user_id";
    $result = $mysqlB->query($sql);
    if($result->num_rows == 0)
    {
        echo "<div class='error_message'>Kullanıcı bulunamadı.</div>";
        die();
    }
    $user = $result->fetch_assoc();
    class MYPDF extends TCPDF
    {
        public function Footer()
        {
            $this->SetY(-15);
            $this->SetFont('dejavusans', 'I', 8);
            $this->Cell(0, 10, 'Sayfa '.$this->getAliasNumPage().' / '.$this->getAliasNbPages().' - '.date('d.m.Y'), 0, false, 'C');
        }
    }
    $pdf = new MYPDF();
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Uygulama Adı');
    $pdf->SetTitle($user['ad'] . ' ' . $user['soyad'] . ' Kullanıcı Bilgileri');
    $pdf->SetSubject('Kullanıcı PDF Export');
    $pdf->SetMargins(20, 20, 20);
    $pdf->AddPage();
    if(file_exists('../images/divinglog.png'))
    {
        $pdf->Image('../images/divinglog.png', 150, 10, 40);
    }
    $pdf->SetFont('dejavusans', 'B', 18);
    $pdf->Cell(0, 15, $user['ad'] . ' ' . $user['soyad'] . ' Kullanıcı Bilgileri', 0, 1, 'L');
    $pdf->Ln(2);
    $pdf->Line(20, $pdf->GetY(), 190, $pdf->GetY());
    $pdf->Ln(10);
    $profile_image = "../uploads/profile_$user_id.jpg";
    if(file_exists($profile_image))
    {
        $pdf->Image($profile_image, 150, 40, 40, 0, '', '', '', false, 300, '', false, false, 0);
    }
    $pdf->SetFont('dejavusans', '', 12);
    $pdf->SetFillColor(245, 245, 245);
    $pdf->SetTextColor(33, 33, 33);
    foreach($user as $key => $value)
    {
        $label = ucfirst(str_replace('_', ' ', $key));
        $pdf->SetFont('dejavusans', 'B', 11);
        $pdf->MultiCell(50, 10, $label . ':', 1, 'L', 1, 0, '', '', true);
        $pdf->SetFont('dejavusans', '', 11);
        $pdf->MultiCell(130, 10, $value, 1, 'L', 0, 1, '', '', true);
    }
    $pdf->Ln(10);
    $profile_url = "https://seninsiten.com/profil.php?id=" . $user_id;
    $pdf->write2DBarcode($profile_url, 'QRCODE,H', 20, $pdf->GetY(), 30, 30, [], 'N');
    $pdf->SetXY(55, $pdf->GetY() + 10);
    $pdf->SetFont('dejavusans', '', 10);
    $pdf->Write(0, 'Kullanıcı profiline gitmek için QR kodu tarayın.');
    $pdf->Output("kullanici_{$user_id}_bilgileri.pdf", 'D');
    echo "<div class='success_message'>PDF başarıyla indirildi.</div>";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DivingLog | Export PDF</title>
    <link rel="stylesheet" href="../CSS/export_pdf.css">
</head>
<body>
    
</body>
</html>