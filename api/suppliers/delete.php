<?php
require_once __DIR__ . '/../../config.php';
session_start();
header('Content-Type: application/json');

function json_exit($ok, $message = '') {
    echo json_encode(['ok' => $ok, 'message' => $message]);
    exit;
}

$csrf = $_POST['csrf'] ?? '';
if ($csrf === '' || !isset($_SESSION['csrf_token']) || $csrf !== $_SESSION['csrf_token']) {
    json_exit(false, 'Geçersiz oturum');
}

$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) {
    json_exit(false, 'Geçersiz ID');
}

$stmt = $conn->prepare('UPDATE suppliers SET is_active=0 WHERE id=?');
$stmt->bind_param('i', $id);
$ok = $stmt->execute();
$stmt->close();

json_exit($ok, $ok ? 'Silindi' : 'Silinemedi');
