<?php
include('../../config.php');
$sql = "SELECT * FROM diving_plans";
$result = $mysqlB->query($sql);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DivingLog | Dalışları Yönet</title>
    <link rel="icon" href="../images/divinglog.png">
    <link rel="stylesheet" href="../CSS/manage_diving.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="container">
    <h2>Diving Planları Yönetimi</h2>

    <?php if ($result && $result->num_rows > 0): ?>
        <table>
            <thead>
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
            </tr>
            </thead>
            <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['id']) ?></td>
                    <td><?= htmlspecialchars($row['tcno']) ?></td>
                    <td><?= htmlspecialchars($row['minutes']) ?></td>
                    <td><?= htmlspecialchars($row['diving_location']) ?></td>
                    <td><?= htmlspecialchars($row['water_type']) ?></td>
                    <td><?= htmlspecialchars($row['depth_feet']) ?></td>
                    <td><?= htmlspecialchars($row['depth_meter']) ?></td>
                    <td><?= htmlspecialchars($row['respiration']) ?></td>
                    <td><?= htmlspecialchars($row['clothing']) ?></td>
                    <td><?= htmlspecialchars($row['diving_purpose']) ?></td>
                    <td><?= htmlspecialchars($row['tools']) ?></td>
                    <td><?= htmlspecialchars($row['tools_devices']) ?></td>
                    <td><?= htmlspecialchars($row['supervisor']) ?></td>
                    <td><?= htmlspecialchars($row['created_at']) ?></td>
                    <td>
                        <div class="action-buttons">
                            <a href="edit_diving_plan.php?id=<?= urlencode($row['id']) ?>" class="edit-btn">Düzenle</a>
                            <a href="edit_diving_plan_export_pdf.php?id=<?= urlencode($row['id']) ?>" class="export-btn">Dışa Aktar (PDF)</a>
                            <a href="#" class="btn delete-btn" onclick="openConfirmModal(<?= htmlspecialchars($row['id']) ?>); return false;">
                                <i class="fas fa-exclamation-triangle"></i> Sil
                            </a>
                        </div>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="error_message">Hiçbir diving planı bulunamadı.</div>
    <?php endif; ?>
</div>

<!-- Silme Modalı -->
<div id="confirmModal" class="modal" style="display:none;">
    <div class="modal-content">
        <p>Bu planı silmek istediğinizden emin misiniz?</p>
        <div class="modal-actions">
            <form id="deleteForm" method="POST" action="delete_diving_plan.php">
                <input type="hidden" name="id" id="deletePlanId">
                <button type="submit" class="confirm-btn">Evet, Sil</button>
                <button type="button" class="cancel-btn" onclick="closeConfirmModal()">Vazgeç</button>
            </form>
        </div>
    </div>
</div>

<footer>
    <p>&copy; 2025 DivingLog Uygulaması</p>
</footer>

<script>
    function openConfirmModal(planId) {
        document.getElementById("deletePlanId").value = planId;
        document.getElementById("confirmModal").style.display = "block";
    }

    function closeConfirmModal() {
        document.getElementById("confirmModal").style.display = "none";
    }
</script>
</body>
</html>