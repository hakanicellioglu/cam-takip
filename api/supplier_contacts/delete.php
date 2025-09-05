<?php
require_once '../../includes/bootstrap.php';
require_login();

if (!csrf_verify($_POST['csrf'] ?? '')) {
  json_exit(false, 'Geçersiz oturum (CSRF)');
}

$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) json_exit(false, 'id gerekli');

try {
  // SOFT DELETE tercih ediyorsan:
  // $stmt = $pdo->prepare('UPDATE supplier_contacts SET is_active=0 WHERE id=?');
  // $stmt->execute([$id]);

  // HARD DELETE:
  $stmt = $pdo->prepare('DELETE FROM supplier_contacts WHERE id=?');
  $stmt->execute([$id]);

  if ($stmt->rowCount() === 0) json_exit(false, 'Kayıt bulunamadı');

  json_exit(true, 'Silindi');
} catch (Throwable $e) {
  json_exit(false, 'Beklenmeyen hata');
}
