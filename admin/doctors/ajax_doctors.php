<?php
// C:\xampp\htdocs\hpce\admin\doctors\ajax_doctors.php
require_once './../../include/config.php';
require_once __DIR__ . '/../include/auth.php';
header('Content-Type: application/json');
if (!canAccess('doctors')) { echo json_encode(['error'=>'Forbidden']); exit; }

$limit  = 10;
$page   = isset($_GET['page']) && is_numeric($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$searchLike = '%' . $conn->real_escape_string($search) . '%';

// Count Total
$countRes = $conn->query("SELECT COUNT(*) AS total FROM doctors WHERE name LIKE '$searchLike' OR designation LIKE '$searchLike'");
$totalRecords = $countRes->fetch_assoc()['total'];
$totalPages   = ceil($totalRecords / $limit);

// Fetch Records
$sql = "SELECT * FROM doctors WHERE name LIKE '$searchLike' OR designation LIKE '$searchLike' ORDER BY id DESC LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

$rows = [];
while ($row = $result->fetch_assoc()) {
    ob_start(); ?>
    <tr class="border-bottom border-light">
        <td class="ps-4 py-3">
            <div class="d-flex align-items-center gap-3">
                <img src="<?= SITE_URL ?>/assets/img/doctors/<?= $row['photo'] ?>" class="rounded-circle border" width="50" height="50" style="object-fit:cover;">
                <div>
                    <h6 class="mb-0 fw-bold text-dark"><?= htmlspecialchars($row['name']) ?></h6>
                    <small class="text-muted"><?= htmlspecialchars($row['designation']) ?></small>
                </div>
            </div>
        </td>
        <td><code>/doctor/<?= $row['slug'] ?></code></td>
        <td class="text-end pe-4">
            <div class="btn-group shadow-sm border rounded-pill overflow-hidden bg-white">
                <a href="<?= SITE_URL ?>/doctor/<?= $row['slug'] ?>" target="_blank" class="btn btn-sm btn-light border-0 py-2 px-3 text-secondary"><i class="fa fa-eye"></i></a>
                <a href="edit?id=<?= $row['id'] ?>" class="btn btn-sm btn-light border-0 py-2 px-3 text-primary"><i class="fa fa-pencil-alt"></i></a>
                <a href="./?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this doctor?')" class="btn btn-sm btn-light border-0 py-2 px-3 text-danger"><i class="fa fa-trash-alt"></i></a>
            </div>
        </td>
    </tr>
    <?php
    $rows[] = ob_get_clean();
}

echo json_encode([
    'rows' => !empty($rows) ? implode('', $rows) : '<tr><td colspan="3" class="text-center py-5">No doctors found.</td></tr>',
    'totalRecords' => $totalRecords,
    'totalPages'   => $totalPages,
    'currentPage'  => $page
]);