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
$name = trim($_POST['name'] ?? '');
$address = trim($_POST['address'] ?? '');
$email = trim($_POST['email'] ?? '');
$tax_no = trim($_POST['tax_no'] ?? '');
$notes = trim($_POST['notes'] ?? '');
$is_active = isset($_POST['is_active']) ? 1 : 0;

$errors = [];
if ($name === '' || mb_strlen($name) < 3 || mb_strlen($name) > 150) {
    $errors['name'] = '3-150 karakter olmalı';
}
if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Geçersiz e-posta';
}
if ($tax_no !== '' && !preg_match('/^[A-Z0-9 \/-]+$/i', $tax_no)) {
    $errors['tax_no'] = 'Geçersiz format';
}
if ($is_active !== 0 && $is_active !== 1) {
    $errors['is_active'] = '0 veya 1 olmalı';
}
if ($errors) {
    json_exit(false, 'Doğrulama hatası', ['errors' => $errors]);
}

try {
    if ($id > 0) {
        $stmt = $conn->prepare('SELECT COUNT(*) FROM suppliers WHERE name=? AND id<>?');
        $stmt->bind_param('si', $name, $id);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
        if ($count > 0) {
            json_exit(false, 'Aynı isimde firma mevcut', ['errors' => ['name' => 'Benzersiz olmalı']]);
        }
        $stmt = $conn->prepare('UPDATE suppliers SET name=?, address=?, email=?, tax_no=?, notes=?, is_active=? WHERE id=?');
        $stmt->bind_param('ssssssi', $name, $address, $email, $tax_no, $notes, $is_active, $id);
        $stmt->execute();
        $stmt->close();
    } else {
        $stmt = $conn->prepare('SELECT COUNT(*) FROM suppliers WHERE name=?');
        $stmt->bind_param('s', $name);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
        if ($count > 0) {
            json_exit(false, 'Aynı isimde firma mevcut', ['errors' => ['name' => 'Benzersiz olmalı']]);
        }
        $stmt = $conn->prepare('INSERT INTO suppliers (name, address, email, tax_no, notes, is_active) VALUES (?,?,?,?,?,?)');
        $stmt->bind_param('sssssi', $name, $address, $email, $tax_no, $notes, $is_active);
        $stmt->execute();
        $id = $stmt->insert_id;
        $stmt->close();
    }
    json_exit(true, 'Kaydedildi', ['id' => $id]);
} catch (Throwable $e) {
    json_exit(false, 'Beklenmeyen hata');
}
