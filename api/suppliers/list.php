<?php
require_once __DIR__ . '/../../config.php';
session_start();
header('Content-Type: application/json');

function json_exit($ok, $data = null, $message = '') {
    echo json_encode(['ok' => $ok, 'data' => $data, 'message' => $message]);
    exit;
}

$q = trim($_GET['q'] ?? '');
$is_active = $_GET['is_active'] ?? '';
$page = max(1, (int)($_GET['page'] ?? 1));
$per_page = max(1, min(100, (int)($_GET['per_page'] ?? 10)));
$offset = ($page - 1) * $per_page;

$where = ' WHERE 1=1 ';
$params = [];
$types = '';
if ($q !== '') {
    $where .= ' AND (name LIKE ? OR email LIKE ? OR tax_no LIKE ?)';
    $like = '%' . $q . '%';
    $params[] = $like; $params[] = $like; $params[] = $like;
    $types .= 'sss';
}
if ($is_active !== '' && ($is_active === '0' || $is_active === '1')) {
    $where .= ' AND is_active = ?';
    $params[] = $is_active; $types .= 'i';
}

$total = 0;
$sqlTotal = 'SELECT COUNT(*) FROM suppliers' . $where;
$stmt = $conn->prepare($sqlTotal);
if ($types !== '') {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$stmt->bind_result($total);
$stmt->fetch();
$stmt->close();

$sql = 'SELECT s.*, (
            SELECT COUNT(*) FROM supplier_contacts c 
            WHERE c.supplier_id = s.id AND c.is_active = 1
        ) AS contact_count
        FROM suppliers s' . $where . ' ORDER BY s.name ASC LIMIT ?, ?';
$params2 = $params;
$types2 = $types . 'ii';
$params2[] = $offset; $params2[] = $per_page;
$stmt = $conn->prepare($sql);
$stmt->bind_param($types2, ...$params2);
$stmt->execute();
$result = $stmt->get_result();
$rows = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

json_exit(true, ['rows' => $rows, 'total' => $total]);
