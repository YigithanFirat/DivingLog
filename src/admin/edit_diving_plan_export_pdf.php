<?php
    include('../../config.php');
    require_once('../TCPDF-main/tcpdf.php');
    if(!isset($_GET['id'])) 
    {
        die('ID belirtilmedi.');
    }
    $id = $_GET['id'];
    $stmt = $mysqlB->prepare("SELECT * FROM diving_plans WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows !== 1)
    {
        die('Dalış planı bulunamadı.');
    }
    $row = $result->fetch_assoc();
    $stmt->close();
    $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
    $pdf->SetCreator('DivingLog');
    $pdf->SetAuthor('DivingLog Uygulaması');
    $pdf->SetTitle('Dalış Planı');
    $pdf->SetHeaderData('', 0, 'Dalış Planı Raporu', 'Tarih: ' . date('d.m.Y'));
    $pdf->setHeaderFont(Array('dejavusans', '', 12));
    $pdf->setFooterFont(Array('dejavusans', '', 10));
    $pdf->SetMargins(15, 27, 15);
    $pdf->SetHeaderMargin(10);
    $pdf->SetFooterMargin(10);
    $pdf->SetAutoPageBreak(TRUE, 20);
    $pdf->AddPage();
    $pdf->SetFont('dejavusans', '', 10);
    $html = '<h2 style="text-align:center;">Dalış Planı Detayları</h2>';
    $html .= '<table border="1" cellpadding="6" cellspacing="0" style="width:100%;">';
    foreach($row as $key => $value) 
    {
        $label = ucwords(str_replace("_", " ", $key));
        $html .= "<tr>
                    <td style='font-weight:bold; width:30%; background-color:#f0f0f0;'>$label</td>
                    <td>$value</td>
                </tr>";
    }
    $html .= '</table>';
    $pdf->writeHTML($html, true, false, true, false, '');
    $pdf->Output('DivingPlan_'.$row['id'].'.pdf', 'I');
?>