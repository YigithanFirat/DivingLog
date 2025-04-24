<?php
    include('../../config.php');
    $sql = "SELECT * FROM diving_plans";
    $result = $mysqlB->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DivingLog | Dalışları Yönet</title>
    <link rel="web icon" href="../images/divinglog.png">
    <link rel="stylesheet" href="../CSS/manage_diving.css">
    <style>
        /* Buradaki CSS, yukarıdaki CSS kodlarını içeriyor */
    </style>
</head>
<body>
    <div class="container">
        <h2>Diving Planları Yönetimi</h2>
        
        <?php
        if($result->num_rows > 0) {
            echo "<table>
                    <tr>
                        <th>ID</th>
                        <th>T.C</th>
                        <th>Dakika</th>
                        <th>Lokasyon</th>
                        <th>Dalış Ortamı</th>
                        <th>Derinlik (Feet)</th>
                        <th>Derinlik (Metre)</th>
                        <th>Solunum</th>
                        <th>Elbise</th>
                        <th>Amaç</th>
                        <th>Alet ve Cihaz</th>
                        <th>Takım</th>
                        <th>Amir</th>
                        <th>Tarih</th>
                        <th>İşlem</th>
                    </tr>";
            while($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>".$row['id']."</td>
                        <td>".$row['tcno']."</td>
                        <td>".$row['minutes']."</td>
                        <td>".$row['diving_location']."</td>
                        <td>".$row['water_type']."</td>
                        <td>".$row['depth_feet']."</td>
                        <td>".$row['depth_meter']."</td>
                        <td>".$row['respiration']."</td>
                        <td>".$row['clothing']."</td>
                        <td>".$row['diving_purpose']."</td>
                        <td>".$row['tools']."</td>
                        <td>".$row['tools_devices']."</td>
                        <td>".$row['supervisor']."</td>
                        <td>".$row['created_at']."</td>
                        <td>
                            <div class='action-buttons'>
                                <a href='edit_diving_plan.php?id=".$row['id']."' class='edit-btn'>Düzenle</a>
                                <a href='edit_diving_plan_export_pdf.php?id=".$row['id']."' class='export-btn'>Dışa Aktar (PDF)</a>
                                <a href='delete_diving_plan.php?id=".$row['id']."' class='delete-btn' onclick='return confirm(\"Bu planı silmek istediğinizden emin misiniz?\");'>Sil</a>
                            </div>
                        </td>
                    </tr>";
            }
            echo "</table>";
        } else {
            echo "<div class='error_message'>Hiçbir diving planı bulunamadı.</div>";
        }
        ?>
    </div>
    <footer>
        <p>&copy; 2025 DivingLog Uygulaması</p>
    </footer>
</body>
</html>