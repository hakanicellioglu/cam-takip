<?php
require_once '../../includes/bootstrap.php';
require_login();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) json_exit(false, 'id gerekli');

$stmt = $pdo->prepare("SELECT id, supplier_id, full_name, role, phone, email, notes, is_primary, is_active, created_at
                       FROM supplier_contacts WHERE id = ?");
$stmt->execute([$id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$row) json_exit(false, 'Kayıt bulunamadı');

json_exit(true, 'OK', ['row' => $row]);
