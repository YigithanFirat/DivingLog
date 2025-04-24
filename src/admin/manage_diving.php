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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <h2>Diving Planları Yönetimi</h2>
        <?php
            if($result->num_rows > 0)
            {
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
                while($row = $result->fetch_assoc())
                {
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
                                    <a href='edit_diving_plan_export_pdf.php?id=".$row['id']."' class='export-btn'>Dışa Aktar (PDF)</a>";

                    echo '<a href="#" class="btn delete-btn" onclick="openConfirmModal(' . $row['id'] . '); return false;">
                            <i class="fas fa-exclamation-triangle"></i> Sil
                          </a>';
                    echo "    </div>
                            </td>
                        </tr>";
                }
                echo "</table>";
            }
            else
            {
                echo "<div class='error_message'>Hiçbir diving planı bulunamadı.</div>";
            }
        ?>
    </div>
    <div id="confirmModal" class="modal">
        <div class="modal-content">
            <p>Bu planı silmek istediğinizden emin misiniz?</p>
            <div class="modal-actions">
                <button class="confirm-btn" onclick="confirmDelete()">Evet, Sil</button>
                <button class="cancel-btn" onclick="closeConfirmModal()">Vazgeç</button>
            </div>
        </div>
    </div>
    <footer>
        <p>&copy; 2025 DivingLog Uygulaması</p>
    </footer>
    <script src="../JS/manage_diving.js"></script>
</body>
</html>