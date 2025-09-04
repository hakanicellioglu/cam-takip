<?php
session_start();
if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$userName = 'Kullanıcı';
if (isset($_SESSION['user']['username']) && $_SESSION['user']['username'] !== '') {
  $userName = $_SESSION['user']['username'];
} else {
  $first = $_SESSION['user']['firstname'] ?? '';
  $last = $_SESSION['user']['lastname'] ?? '';
  $full = trim($first . ' ' . $last);
  if ($full !== '') {
    $userName = $full;
  }
}
$csrfToken = $_SESSION['csrf_token'];
$logoFile = __DIR__ . '/assets/logo.svg';
$hasLogo = file_exists($logoFile);
?>
<!DOCTYPE html>
<html lang='tr'>

<head>
  <meta charset='UTF-8'>
  <meta name='viewport' content='width=device-width, initial-scale=1'>
  <title>Cam Takip</title>
  <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
  <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css'>
</head>

<body>
  <nav class='navbar navbar-expand-lg navbar-light bg-light'>
    <div class='container'>
      <?php if ($hasLogo): ?>
        <a class='navbar-brand' href='#'><img src='/assets/logo.svg' alt='<?php echo htmlspecialchars('Cam Takip'); ?>' height='30'></a>
      <?php else: ?>
        <a class='navbar-brand' href='#'>Cam Takip</a>
      <?php endif; ?>
      <button class='navbar-toggler' type='button' data-bs-toggle='collapse' data-bs-target='#mainNavbar' aria-controls='mainNavbar' aria-expanded='false' aria-label='Menüyü Aç'>
        <span class='navbar-toggler-icon'></span>
      </button>
      <div class='collapse navbar-collapse' id='mainNavbar'>
        <ul class='navbar-nav me-auto mb-2 mb-lg-0'>
          <li class='nav-item'><a class='nav-link' href='tedarikciler.php'>Tedarikçiler</a></li>
          <li class='nav-item'><a class='nav-link' href='musteriler.php'>Müşteriler</a></li>
          <li class='nav-item'><a class='nav-link' href='urunler.php'>Ürünler</a></li>
          <li class='nav-item'><a class='nav-link' href='fiyat-listesi.php'>Fiyat Listesi</a></li>
          <li class='nav-item'><a class='nav-link' href='siparisler.php'>Siparişler</a></li>
        </ul>
        <ul class='navbar-nav mb-2 mb-lg-0'>
          <li class='nav-item dropdown'>
            <a class='nav-link dropdown-toggle' href='#' id='userDropdown' role='button' data-bs-toggle='dropdown' aria-expanded='false'>
              <?php echo htmlspecialchars($userName); ?>
            </a>
            <ul class='dropdown-menu dropdown-menu-end' aria-labelledby='userDropdown'>
              <li><a class='dropdown-item' href='settings.php'>Ayarlar</a></li>
              <li>
                <hr class='dropdown-divider'>
              </li>
              <li>
                <form method='post' action='logout.php'>
                  <?php if ($csrfToken !== ''): ?>
                    <input type='hidden' name='csrf_token' value='<?php echo htmlspecialchars($csrfToken); ?>'>
                  <?php endif; ?>
                  <button type='submit' class='dropdown-item'>Çıkış Yap</button>
                </form>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </nav>