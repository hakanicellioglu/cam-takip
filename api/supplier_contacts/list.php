<?php
require_once '../../includes/bootstrap.php';
require_login();

$supplier_id = (int)($_GET['supplier_id'] ?? 0);
if ($supplier_id <= 0) json_exit(false, 'supplier_id gerekli');

$q         = trim($_GET['q'] ?? '');
$is_active = $_GET['is_active'] ?? '';
$page      = max(1, (int)($_GET['page'] ?? 1));
$per_page  = min(100, max(5, (int)($_GET['per_page'] ?? 10)));
$offset    = ($page - 1) * $per_page;

$where = ['supplier_id = :sid'];
$params = ['sid' => $supplier_id];

if ($q !== '') {
  $where[] = '(full_name LIKE :q OR email LIKE :q OR phone LIKE :q)';
  $params['q'] = "%{$q}%";
}
if ($is_active !== '' && in_array($is_active, ['0','1'], true)) {
  $where[] = 'is_active = :act';
  $params['act'] = (int)$is_active;
}

$whereSql = $where ? ('WHERE '.implode(' AND ', $where)) : '';

$countSql = "SELECT COUNT(*) FROM supplier_contacts {$whereSql}";
$stmt = $pdo->prepare($countSql);
$stmt->execute($params);
$total = (int)$stmt->fetchColumn();

$sql = "SELECT id, supplier_id, full_name, role, phone, email, notes, is_primary, is_active, created_at
        FROM supplier_contacts
        {$whereSql}
        ORDER BY is_primary DESC, full_name ASC
        LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($sql);
foreach ($params as $k => $v) {
  $stmt->bindValue(":{$k}", $v);
}
$stmt->bindValue(':limit',  $per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset,   PDO::PARAM_INT);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

json_exit(true, 'OK', ['rows' => $rows, 'total' => $total, 'page' => $page, 'per_page' => $per_page]);
