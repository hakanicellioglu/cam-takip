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
  <meta name='viewport' content='width=device-width, initial-scale=1.0'>
  <title>Cam Takip Sistemi</title>
  
  <!-- Bootstrap CSS -->
  <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
  
  <!-- Google Fonts -->
  <link href='https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap' rel='stylesheet'>
  
  <!-- Font Awesome for icons -->
  <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css'>
  
  <style>
    :root {
      --primary-color: #A65D70;
      --secondary-color: #D9849B;
      --success-color: #D9A3B1;
      --warning-color: #D9849B;
      --danger-color: #A65D70;
      --light-bg: #E7E7E7;
      --dark-bg: #A65D70;
    }

    * {
      font-family: 'Inter', sans-serif;
    }

    body {
      background-color: var(--light-bg);
      min-height: 100vh;
    }

    .navbar {
      background: linear-gradient(135deg, #A65D70 0%, #D9849B 100%) !important;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
      padding: 1rem 0;
    }

    .navbar-brand {
      font-weight: 700;
      font-size: 1.5rem;
      color: white !important;
      transition: all 0.3s ease;
    }

    .navbar-brand:hover {
      color: rgba(255, 255, 255, 0.9) !important;
      transform: translateY(-1px);
    }

    .navbar-brand img {
      filter: brightness(0) invert(1);
      transition: all 0.3s ease;
    }

    .navbar-nav .nav-link {
      color: rgba(255, 255, 255, 0.9) !important;
      font-weight: 500;
      padding: 0.5rem 1rem !important;
      border-radius: 8px;
      transition: all 0.3s ease;
      margin: 0 0.2rem;
    }

    .navbar-nav .nav-link:hover {
      color: white !important;
      background: rgba(255, 255, 255, 0.1);
      transform: translateY(-1px);
    }

    .navbar-nav .nav-link.active {
      color: white !important;
      background: rgba(255, 255, 255, 0.2);
    }

    .navbar-toggler {
      border: 2px solid rgba(255, 255, 255, 0.3);
      padding: 0.5rem;
      border-radius: 8px;
    }

    .navbar-toggler:focus {
      box-shadow: 0 0 0 0.2rem rgba(255, 255, 255, 0.25);
    }

    .navbar-toggler-icon {
      background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 0.8%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
    }

    .dropdown-menu {
      border: none;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      border-radius: 12px;
      padding: 0.5rem 0;
      margin-top: 0.5rem;
    }

    .dropdown-item {
      padding: 0.75rem 1.5rem;
      font-weight: 500;
      transition: all 0.3s ease;
      color: #A65D70;
    }

    .dropdown-item:hover {
      background: var(--primary-color);
      color: white;
    }

    .dropdown-divider {
      margin: 0.5rem 0;
      border-color: #D9A3B1;
    }

    .dropdown-toggle::after {
      margin-left: 0.5rem;
    }

    /* User dropdown styling */
    #userDropdown {
      background: rgba(255, 255, 255, 0.1);
      border-radius: 8px;
      padding: 0.5rem 1rem !important;
      font-weight: 600;
    }

    #userDropdown:hover {
      background: rgba(255, 255, 255, 0.2);
    }

    /* Mobile responsive */
    @media (max-width: 991.98px) {
      .navbar-collapse {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        padding: 1rem;
        margin-top: 1rem;
        backdrop-filter: blur(10px);
      }

      .navbar-nav .nav-link {
        margin: 0.2rem 0;
      }

      #userDropdown {
        background: rgba(255, 255, 255, 0.2);
      }
    }

    /* Animation for navbar */
    .navbar {
      animation: slideDown 0.6s ease-out;
    }

    @keyframes slideDown {
      from {
        opacity: 0;
        transform: translateY(-20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* Content area styling */
    .main-content {
      padding-top: 2rem;
      min-height: calc(100vh - 80px);
    }
  </style>
</head>

<body>
  <nav class='navbar navbar-expand-lg'>
    <div class='container'>
      <?php if ($hasLogo): ?>
        <a class='navbar-brand' href='dashboard.php'>
          <img src='/assets/logo.svg' alt='<?php echo htmlspecialchars('Cam Takip'); ?>' height='35' class='me-2'>
          Cam Takip
        </a>
      <?php else: ?>
        <a class='navbar-brand' href='dashboard.php'>
          <i class='fas fa-wine-glass me-2'></i>
          Cam Takip
        </a>
      <?php endif; ?>
      
      <button class='navbar-toggler' type='button' data-bs-toggle='collapse' data-bs-target='#mainNavbar' 
              aria-controls='mainNavbar' aria-expanded='false' aria-label='Menüyü Aç'>
        <span class='navbar-toggler-icon'></span>
      </button>
      
      <div class='collapse navbar-collapse' id='mainNavbar'>
        <ul class='navbar-nav me-auto mb-2 mb-lg-0'>
          <li class='nav-item'>
            <a class='nav-link' href='dashboard.php'>
              <i class='fas fa-tachometer-alt me-1'></i>
              Panel
            </a>
          </li>
          <li class='nav-item'>
            <a class='nav-link' href='tedarikciler.php'>
              <i class='fas fa-truck me-1'></i>
              Tedarikçiler
            </a>
          </li>
          <li class='nav-item'>
            <a class='nav-link' href='musteriler.php'>
              <i class='fas fa-users me-1'></i>
              Müşteriler
            </a>
          </li>
          <li class='nav-item'>
            <a class='nav-link' href='urunler.php'>
              <i class='fas fa-wine-glass me-1'></i>
              Ürünler
            </a>
          </li>
          <li class='nav-item'>
            <a class='nav-link' href='fiyat-listesi.php'>
              <i class='fas fa-list-alt me-1'></i>
              Fiyat Listesi
            </a>
          </li>
          <li class='nav-item'>
            <a class='nav-link' href='siparisler.php'>
              <i class='fas fa-shopping-cart me-1'></i>
              Siparişler
            </a>
          </li>
        </ul>
        
        <ul class='navbar-nav mb-2 mb-lg-0'>
          <li class='nav-item dropdown'>
            <a class='nav-link dropdown-toggle' href='#' id='userDropdown' role='button' 
               data-bs-toggle='dropdown' aria-expanded='false'>
              <i class='fas fa-user-circle me-2'></i>
              <?php echo htmlspecialchars($userName); ?>
            </a>
            <ul class='dropdown-menu dropdown-menu-end' aria-labelledby='userDropdown'>
              <li>
                <a class='dropdown-item' href='profile.php'>
                  <i class='fas fa-user me-2'></i>
                  Profil
                </a>
              </li>
              <li>
                <a class='dropdown-item' href='settings.php'>
                  <i class='fas fa-cog me-2'></i>
                  Ayarlar
                </a>
              </li>
              <li><hr class='dropdown-divider'></li>
              <li>
                <form method='post' action='logout.php' class='d-inline'>
                  <?php if ($csrfToken !== ''): ?>
                    <input type='hidden' name='csrf_token' value='<?php echo htmlspecialchars($csrfToken); ?>'>
                  <?php endif; ?>
                  <button type='submit' class='dropdown-item text-danger'>
                    <i class='fas fa-sign-out-alt me-2'></i>
                    Çıkış Yap
                  </button>
                </form>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="main-content">