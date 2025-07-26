<?php
include('../session_guard.php');
include('../../config.php');
include('../sidebarmenu.php');

$limit = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

$searchTerm = '';
$result = false;
$totalRecords = 0;
$totalPages = 0;
$totalMinutes = 0;
$matchedUsers = [];
$isTcNo = false;

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search'])) {
    $searchTerm = trim($_GET['search']);
    $isTcNo = preg_match('/^\d{11}$/', $searchTerm);

    if ($isTcNo) {
        // TC No araması - direkt dalış planları ve kullanıcı bilgisi
        $countStmt = $mysqlB->prepare("SELECT COUNT(*) FROM diving_plans WHERE tcno = ?");
        $countStmt->bind_param('s', $searchTerm);
        $countStmt->execute();
        $countStmt->bind_result($totalRecords);
        $countStmt->fetch();
        $countStmt->close();

        $totalPages = ceil($totalRecords / $limit);

        $sumStmt = $mysqlB->prepare("SELECT COALESCE(SUM(minutes),0) FROM diving_plans WHERE tcno = ?");
        $sumStmt->bind_param('s', $searchTerm);
        $sumStmt->execute();
        $sumStmt->bind_result($totalMinutes);
        $sumStmt->fetch();
        $sumStmt->close();

        $stmt = $mysqlB->prepare("SELECT diving_plans.*, users.ad, users.soyad 
                                 FROM diving_plans 
                                 LEFT JOIN users ON diving_plans.tcno = users.tcno 
                                 WHERE diving_plans.tcno = ? 
                                 ORDER BY diving_plans.created_at DESC 
                                 LIMIT ? OFFSET ?");
        $stmt->bind_param('sii', $searchTerm, $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();

    } else {
        // İsim soyisim araması

        $searchWords = preg_split('/\s+/', $searchTerm);
        $whereParts = [];
        $params = [];
        $types = '';

        foreach ($searchWords as $word) {
            $whereParts[] = "(users.ad LIKE ? OR users.soyad LIKE ?)";
            $params[] = "%$word%";
            $params[] = "%$word%";
            $types .= 'ss';
        }

        $whereSql = implode(' AND ', $whereParts);

        // Kullanıcıları getir (arama sonucu)
        $stmt = $mysqlB->prepare("SELECT tcno, ad, soyad FROM users WHERE $whereSql ORDER BY ad, soyad");
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $userResult = $stmt->get_result();
        $matchedUsers = $userResult->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        // Dalış planları için kayıt sayısı
        $countSql = "SELECT COUNT(*) FROM diving_plans LEFT JOIN users ON diving_plans.tcno = users.tcno WHERE $whereSql";
        $countStmt = $mysqlB->prepare($countSql);
        $countStmt->bind_param($types, ...$params);
        $countStmt->execute();
        $countStmt->bind_result($totalRecords);
        $countStmt->fetch();
        $countStmt->close();

        $totalPages = ceil($totalRecords / $limit);

        // Toplam dalış süresi
        $sumSql = "SELECT COALESCE(SUM(diving_plans.minutes),0) FROM diving_plans LEFT JOIN users ON diving_plans.tcno = users.tcno WHERE $whereSql";
        $sumStmt = $mysqlB->prepare($sumSql);
        $sumStmt->bind_param($types, ...$params);
        $sumStmt->execute();
        $sumStmt->bind_result($totalMinutes);
        $sumStmt->fetch();
        $sumStmt->close();

        // Dalış planlarını getir
        $selectSql = "SELECT diving_plans.*, users.ad, users.soyad FROM diving_plans LEFT JOIN users ON diving_plans.tcno = users.tcno WHERE $whereSql ORDER BY diving_plans.created_at DESC LIMIT ? OFFSET ?";
        $stmt = $mysqlB->prepare($selectSql);

        $typesWithLimit = $types . 'ii';
        $paramsWithLimit = array_merge($params, [$limit, $offset]);

        $stmt->bind_param($typesWithLimit, ...$paramsWithLimit);
        $stmt->execute();
        $result = $stmt->get_result();
    }
}

// Eğer istersen arama boşsa tüm kullanıcılar:
$allUsers = [];
$userStmt = $mysqlB->prepare("SELECT tcno, ad, soyad FROM users ORDER BY ad, soyad");
$userStmt->execute();
$userResult = $userStmt->get_result();
if ($userResult) {
    $allUsers = $userResult->fetch_all(MYSQLI_ASSOC);
}
$userStmt->close();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>DivingLog | Kullanıcı Arama</title>
    <link rel="icon" href="../images/divinglog.png" />
    <link rel="stylesheet" href="../CSS/manage_diving.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>
<body>
<div class="main-content">
    <h2>Ad/Soyad veya TC No ile Kullanıcı Ara</h2>
    <form method="GET" class="d-flex flex-column align-items-center gap-3 mb-4">
        <input 
            type="text" name="search" 
            class="form-control" 
            style="max-width: 400px; height: 45px; text-align: center;" 
            placeholder="Ad Soyad veya TC No" 
            value="<?= htmlspecialchars($searchTerm) ?>" 
            required />
        <button type="submit" class="btn btn-primary">Ara</button>
    </form>

    <?php if (!empty($searchTerm) && !$isTcNo): ?>
        <h3>Arama Sonuçları</h3>
        <?php if (count($matchedUsers) > 0): ?>
        <div class="table-responsive" style="max-height: 300px; overflow-y: auto; margin-bottom: 2rem;">
            <table class="table table-bordered table-hover table-sm">
                <thead class="table-light">
                    <tr>
                        <th>Ad</th>
                        <th>Soyad</th>
                        <th>TC No</th>
                        <th>İşlem</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($matchedUsers as $user): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['ad']) ?></td>
                            <td><?= htmlspecialchars($user['soyad']) ?></td>
                            <td><?= htmlspecialchars($user['tcno']) ?></td>
                            <td>
                                <a href="diving.php?tcno=<?= urlencode($user['tcno']) ?>" class="btn btn-sm btn-success">
                                    Yeni Dalış Oluştur
                                </a>
                                <a href="manage_diving.php?search=<?= urlencode($user['tcno']) ?>" class="btn btn-sm btn-primary">
                                    Tüm Dalışları Göster
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
            <div class="alert alert-danger text-center">
                Aradığınız kriterlere uygun kullanıcı bulunamadı.
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <?php if (!empty($searchTerm) && $isTcNo && $result && $result->num_rows > 0): ?>
        <div class="diving-cards d-flex gap-4 mb-3">
            <div class="total-diving-count border p-3 rounded bg-primary text-white d-flex align-items-center gap-3">
                <div>
                    <p class="count fs-3 fw-bold"><?= htmlspecialchars($totalRecords) ?></p>
                    <p class="label mb-0">Toplam Dalış Sayısı</p>
                </div>
                <div class="icon fs-1">
                    <i class="fa-solid fa-water"></i>
                </div>
            </div>
            <div class="total-minutes-count border p-3 rounded bg-success text-white d-flex align-items-center gap-3">
                <div>
                    <p class="count fs-3 fw-bold"><?= htmlspecialchars($totalMinutes) ?></p>
                    <p class="label mb-0">Toplam Dalış Süresi (Dakika)</p>
                </div>
                <div class="icon fs-1">
                    <i class="fa-solid fa-clock"></i>
                </div>
            </div>
            <div class="d-flex justify-content-end mb-3 gap-2 flex-wrap">
                <a href="export_user_all_diving_plan_pdf.php?tcno=<?= urlencode($searchTerm) ?>" target="_blank" class="btn btn-success">
                    <i class="fa-solid fa-file-pdf"></i> Kullanıcıya Ait Tüm Dalışları PDF Olarak İndir
                </a>
                <a href="export_all_diving_plans_pdf.php" target="_blank" class="btn btn-dark">
                    <i class="fa-solid fa-file-pdf"></i> Tüm Dalışları PDF Olarak İndir
                </a>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-bordered align-middle text-nowrap">
                <thead class="table-primary">
                    <tr>
                            <th>Ad</th>
                            <th>Soyad</th>
                            <th>Dakika</th>
                            <th>Lokasyon</th>
                            <th>Dalış Ortamı</th>
                            <th>Derinlik</th>
                            <th>Solunum</th>
                            <th>Elbise</th>
                            <th>Amaç</th>
                            <th>Takım</th>
                            <th>İşlem</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                                <td><?= htmlspecialchars($row['ad']) ?></td>
                                <td><?= htmlspecialchars($row['soyad']) ?></td>
                                <td><?= htmlspecialchars($row['minutes']) ?></td>
                                <td><?= htmlspecialchars($row['diving_location']) ?></td>
                                <td><?= htmlspecialchars($row['water_type']) ?></td>
                                <td><?= htmlspecialchars($row['depth_meter']) ?></td>
                                <td><?= htmlspecialchars($row['respiration']) ?></td>
                                <td><?= htmlspecialchars($row['clothing']) ?></td>
                                <td><?= htmlspecialchars($row['diving_purpose']) ?></td>
                                <td><?= htmlspecialchars($row['tools']) ?></td>
                                <td>
                                    <div class="d-flex gap-2 flex-wrap">
                                        <a href="edit_diving_plan.php?id=<?= urlencode($row['id']) ?>" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i> Düzenle
                                        </a>
                                        <a href="edit_diving_plan_export_pdf.php?id=<?= urlencode($row['id']) ?>" class="btn btn-info btn-sm">
                                            <i class="fas fa-file-pdf"></i> PDF
                                        </a>
                                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" data-id="<?= (int)$row['id'] ?>">
                                            <i class="fas fa-trash-alt"></i> Sil
                                        </button>
                                    </div>
                                </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
<script src="../JS/manage_diving.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<footer>
    <p>&copy; 2025 DivingLog Uygulaması</p>
</footer>
</body>
</html>