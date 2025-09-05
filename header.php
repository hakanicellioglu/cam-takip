<?php
require_once __DIR__ . '/helpers.php';
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
  <meta name='viewport' content='width=device-width, initial-scale=1.0'>
  <title>Cam Takip Sistemi</title>
  
  <!-- Bootstrap CSS -->
  <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
  
  <!-- Google Fonts -->
  <link href='https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap' rel='stylesheet'>
  
  <!-- Font Awesome for icons -->
  <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css'>
  <link rel="stylesheet" href="assets/css/typography.css">

  <style>
    :root {
      --primary-color: #003840;
      --secondary-color: #005A5B;
      --light-bg: #E0F2F1;
    }

    * {
      font-family: 'Inter', sans-serif;
    }

    body {
      background-color: var(--light-bg);
      min-height: 100vh;
    }

    /* Sidebar */
    #sidebar {
      width: 200px;
      height: 100vh;
      position: fixed;
      top: 0;
      left: -200px;
      z-index: 1045;
      transition: left 0.3s ease;
    }

    #sidebar.active {
      left: 0;
    }

    #sidebar .nav-link {
      color: rgba(255, 255, 255, 0.9);
      border-radius: 0.375rem;
    }

    #sidebar .nav-link:hover {
      background: rgba(255, 255, 255, 0.1);
      color: #fff;
    }

    #menu-toggle {
      position: fixed;
      top: 10px;
      left: 10px;
      z-index: 1050;
      transition: left 0.3s ease;
    }

    #menu-toggle.active {
      left: 210px;
    }
  </style>
</head>

<body>
  <button id="menu-toggle" class="btn btn-dark">
    <i class="fas fa-bars"></i>
  </button>

  <div id="sidebar" class="bg-dark text-white d-flex flex-column p-3">
    <a href="<?= url('dashboard') ?>" class="d-flex align-items-center mb-3 mb-md-0 text-white text-decoration-none">
      <?php if ($hasLogo): ?>
        <img src='/assets/logo.svg' alt='<?php echo htmlspecialchars('Cam Takip'); ?>' height='35' class='me-2'>
      <?php else: ?>
        <i class='fas fa-wine-glass me-2'></i>
      <?php endif; ?>
      <span class="fs-lg">Cam Takip</span>
    </a>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto">
      <li class="nav-item"><a class="nav-link text-white" href="<?= url('dashboard') ?>"><i class='fas fa-tachometer-alt me-2'></i>Panel</a></li>
      <li><a class="nav-link text-white" href="<?= url('tedarikciler') ?>"><i class='fas fa-truck me-2'></i>Tedarikçiler</a></li>
      <li><a class="nav-link text-white" href="<?= url('musteriler') ?>"><i class='fas fa-users me-2'></i>Müşteriler</a></li>
      <li><a class="nav-link text-white" href="<?= url('urunler') ?>"><i class='fas fa-wine-glass me-2'></i>Ürünler</a></li>
      <li><a class="nav-link text-white" href="<?= url('fiyat-listesi') ?>"><i class='fas fa-list-alt me-2'></i>Fiyat Listesi</a></li>
      <li><a class="nav-link text-white" href="<?= url('siparisler') ?>"><i class='fas fa-shopping-cart me-2'></i>Siparişler</a></li>
    </ul>
    <hr>
    <div class="dropdown">
      <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fas fa-user-circle me-2"></i>
        <strong><?php echo htmlspecialchars($userName); ?></strong>
      </a>
      <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="userDropdown">
        <li><a class="dropdown-item" href="<?= url('profile') ?>"><i class="fas fa-user me-2"></i>Profil</a></li>
        <li><a class="dropdown-item" href="<?= url('settings') ?>"><i class="fas fa-cog me-2"></i>Ayarlar</a></li>
        <li><hr class="dropdown-divider"></li>
        <li>
          <form method="post" action="<?= url('logout') ?>" class="d-inline">
            <?php if ($csrfToken !== ''): ?>
              <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
            <?php endif; ?>
            <button type="submit" class="dropdown-item text-danger"><i class="fas fa-sign-out-alt me-2"></i>Çıkış Yap</button>
          </form>
        </li>
      </ul>
    </div>
  </div>

  <script>
    const menuToggle = document.getElementById('menu-toggle');
    const sidebar = document.getElementById('sidebar');
    if (menuToggle && sidebar) {
      menuToggle.addEventListener('click', () => {
        sidebar.classList.toggle('active');
        menuToggle.classList.toggle('active');
        const icon = menuToggle.querySelector('i');
        if (icon) {
          icon.classList.toggle('fa-bars');
          icon.classList.toggle('fa-times');
        }
      });
    }
  </script>
