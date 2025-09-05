<?php
require_once __DIR__ . '/../../config.php';
session_start();
header('Content-Type: application/json');

function json_exit($ok, $message = '', $extra = []) {
    echo json_encode(array_merge(['ok' => $ok, 'message' => $message], $extra));
    exit;
}

$csrf = $_POST['csrf'] ?? '';
if ($csrf === '' || !isset($_SESSION['csrf_token']) || $csrf !== $_SESSION['csrf_token']) {
    json_exit(false, 'Geçersiz oturum');
}

$id = (int)($_POST['id'] ?? 0);
$supplier_id = (int)($_POST['supplier_id'] ?? 0);
$full_name = trim($_POST['full_name'] ?? '');
$role = trim($_POST['role'] ?? 'Satın Alma Görevlisi');
$phone = trim($_POST['phone'] ?? '');
$email = trim($_POST['email'] ?? '');
$notes = trim($_POST['notes'] ?? '');
$is_primary = isset($_POST['is_primary']) ? 1 : 0;
$is_active = isset($_POST['is_active']) ? 1 : 0;

$errors = [];
if ($supplier_id <= 0) {
    $errors['supplier_id'] = 'Zorunlu';
}
if ($full_name === '' || mb_strlen($full_name) < 3 || mb_strlen($full_name) > 120) {
    $errors['full_name'] = '3-120 karakter olmalı';
}
if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Geçersiz e-posta';
}
if ($phone !== '' && mb_strlen($phone) > 25) {
    $errors['phone'] = 'En fazla 25 karakter';
}
if ($is_primary !== 0 && $is_primary !== 1) {
    $errors['is_primary'] = '0 veya 1 olmalı';
}
if ($is_active !== 0 && $is_active !== 1) {
    $errors['is_active'] = '0 veya 1 olmalı';
}
if ($errors) {
    json_exit(false, 'Doğrulama hatası', ['errors' => $errors]);
}

try {
    if ($is_primary) {
        $stmt = $conn->prepare('UPDATE supplier_contacts SET is_primary=0 WHERE supplier_id=?');
        $stmt->bind_param('i', $supplier_id);
        $stmt->execute();
        $stmt->close();
    }
    if ($id > 0) {
        $stmt = $conn->prepare('UPDATE supplier_contacts SET supplier_id=?, full_name=?, role=?, phone=?, email=?, notes=?, is_primary=?, is_active=? WHERE id=?');
        $stmt->bind_param('isssssiii', $supplier_id, $full_name, $role, $phone, $email, $notes, $is_primary, $is_active, $id);
        $stmt->execute();
        $stmt->close();
    } else {
        $stmt = $conn->prepare('INSERT INTO supplier_contacts (supplier_id, full_name, role, phone, email, notes, is_primary, is_active) VALUES (?,?,?,?,?,?,?,?)');
        $stmt->bind_param('isssssii', $supplier_id, $full_name, $role, $phone, $email, $notes, $is_primary, $is_active);
        $stmt->execute();
        $id = $stmt->insert_id;
        $stmt->close();
    }
    json_exit(true, 'Kaydedildi', ['id' => $id]);
} catch (Throwable $e) {
    json_exit(false, 'Beklenmeyen hata');
}
