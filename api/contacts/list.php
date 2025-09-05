<?php
require_once __DIR__ . '/../../config.php';
session_start();
header('Content-Type: application/json');

function json_exit($ok, $data = null, $message = '') {
    echo json_encode(['ok' => $ok, 'data' => $data, 'message' => $message]);
    exit;
}

$supplier_id = (int)($_GET['supplier_id'] ?? 0);
if ($supplier_id <= 0) {
    json_exit(false, null, 'Geçersiz supplier_id');
}

$page = max(1, (int)($_GET['page'] ?? 1));
$per_page = max(1, min(100, (int)($_GET['per_page'] ?? 10)));
$offset = ($page - 1) * $per_page;

$total = 0;
$stmt = $conn->prepare('SELECT COUNT(*) FROM supplier_contacts WHERE supplier_id=? AND is_active=1');
$stmt->bind_param('i', $supplier_id);
$stmt->execute();
$stmt->bind_result($total);
$stmt->fetch();
$stmt->close();

$stmt = $conn->prepare('SELECT * FROM supplier_contacts WHERE supplier_id=? AND is_active=1 ORDER BY full_name ASC LIMIT ?, ?');
$stmt->bind_param('iii', $supplier_id, $offset, $per_page);
$stmt->execute();
$result = $stmt->get_result();
$rows = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

json_exit(true, ['rows' => $rows, 'total' => $total]);
