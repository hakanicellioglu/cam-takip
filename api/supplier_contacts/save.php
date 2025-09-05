<?php
require_once '../../includes/bootstrap.php';
require_login();

if (!csrf_verify($_POST['csrf'] ?? '')) {
  json_exit(false, 'Geçersiz oturum (CSRF)');
}

$id          = (int)($_POST['id'] ?? 0);
$supplier_id = (int)($_POST['supplier_id'] ?? 0);
$full_name   = trim($_POST['full_name'] ?? '');
$role        = trim($_POST['role'] ?? 'Satın Alma Görevlisi');
$phone       = trim($_POST['phone'] ?? '');
$email       = trim($_POST['email'] ?? '');
$notes       = trim($_POST['notes'] ?? '');
$is_primary  = isset($_POST['is_primary']) && (int)$_POST['is_primary'] === 1 ? 1 : 0;
$is_active   = isset($_POST['is_active'])  && (int)$_POST['is_active']  === 0 ? 0 : 1;

$errors = [];
if ($supplier_id <= 0) $errors['supplier_id'] = 'Geçersiz supplier_id';
if ($full_name === '' || mb_strlen($full_name) < 3) $errors['full_name'] = 'En az 3 karakter';
if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Geçersiz e-posta';
if ($errors) json_exit(false, 'Doğrulama hatası', ['errors' => $errors]);

try {
  $pdo->beginTransaction();

  // supplier var mı?
  $chk = $pdo->prepare('SELECT id FROM suppliers WHERE id = ?');
  $chk->execute([$supplier_id]);
  if (!$chk->fetchColumn()) {
    $pdo->rollBack();
    json_exit(false, 'Tedarikçi bulunamadı', ['errors' => ['supplier_id' => 'Mevcut değil']]);
  }

  // is_primary = 1 ise aynı firmadaki diğer birincilleri sıfırla
  if ($is_primary === 1) {
    $sqlReset = 'UPDATE supplier_contacts SET is_primary = 0 WHERE supplier_id = ?';
    if ($id > 0) $sqlReset .= ' AND id <> ?';
    $stmt = $pdo->prepare($sqlReset);
    $id > 0 ? $stmt->execute([$supplier_id, $id]) : $stmt->execute([$supplier_id]);
  }

  if ($id > 0) {
    // güncelle
    $stmt = $pdo->prepare('UPDATE supplier_contacts
                           SET full_name=?, role=?, phone=?, email=?, notes=?, is_primary=?, is_active=?
                           WHERE id=? AND supplier_id=?');
    $ok = $stmt->execute([$full_name, $role, $phone, $email, $notes, $is_primary, $is_active, $id, $supplier_id]);
    if (!$ok || $stmt->rowCount() === 0) {
      $pdo->rollBack();
      json_exit(false, 'Güncellenecek kayıt bulunamadı');
    }
  } else {
    // ekle
    $stmt = $pdo->prepare('INSERT INTO supplier_contacts
      (supplier_id, full_name, role, phone, email, notes, is_primary, is_active)
      VALUES (?,?,?,?,?,?,?,?)');
    $stmt->execute([$supplier_id, $full_name, $role, $phone, $email, $notes, $is_primary, $is_active]);
    $id = (int)$pdo->lastInsertId();
  }

  $pdo->commit();
  json_exit(true, 'Kaydedildi', ['id' => $id]);
} catch (Throwable $e) {
  if ($pdo->inTransaction()) $pdo->rollBack();
  json_exit(false, 'Beklenmeyen hata');
}
