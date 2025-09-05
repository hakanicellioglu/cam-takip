<?php require_once __DIR__ . '/helpers.php'; ?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cam Takip Sistemi - Hoşgeldiniz</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/typography.css">
    
    <style>
        :root {
            --primary-color: #003840;
            --secondary-color: #005A5B;
            --success-color: #007369;
            --warning-color: #008C72;
            --danger-color: #02A676;
            --light-bg: #E0F2F1;
        }

        * {
            font-family: 'Inter', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #003840 0%, #005A5B 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .main-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }

        .logo-area {
            margin-bottom: 2rem;
            padding: 1rem;
        }

        .logo-placeholder {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            border: 2px dashed rgba(255, 255, 255, 0.3);
            transition: all 0.3s ease;
        }

        .logo-placeholder:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.5);
        }

        .welcome-title {
            color: white;
            font-weight: 700;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .welcome-description {
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 2rem;
            font-weight: 300;
        }

        .welcome-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            max-width: 500px;
            width: 100%;
            padding: 3rem;
            transition: transform 0.3s ease;
        }

        .welcome-card:hover {
            transform: translateY(-5px);
        }

        .btn-custom-primary {
            background: var(--primary-color);
            border: none;
            color: #fff;
            padding: 12px 30px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 56, 64, 0.3);
        }

        .btn-custom-primary:hover {
            background: #005A5B;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 90, 91, 0.4);
        }

        .btn-custom-secondary {
            background: var(--secondary-color);
            border: none;
            color: #fff;
            padding: 12px 30px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 90, 91, 0.3);
        }

        .btn-custom-secondary:hover {
            background: #003840;
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 56, 64, 0.4);
        }

        .buttons-container {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .footer {
            background: rgba(0, 56, 64, 0.2);
            color: rgba(255, 255, 255, 0.8);
            text-align: center;
            padding: 1.5rem 0;
        }

        .footer a {
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer a:hover {
            color: white;
            text-decoration: underline;
        }

        .glass-icon {
            color: rgba(255, 255, 255, 0.7);
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .welcome-title {
            }
            
            .welcome-card {
                padding: 2rem;
                margin: 1rem;
            }
            
            .buttons-container {
                flex-direction: column;
                align-items: stretch;
            }
            
            .btn-custom-primary,
            .btn-custom-secondary {
                width: 100%;
                margin-bottom: 0.5rem;
            }
        }

        @media (max-width: 480px) {
            .welcome-title {
            }
            
            .welcome-description {
            }
            
            .welcome-card {
                padding: 1.5rem;
            }
        }

        /* Animation for page load */
        .welcome-card {
            animation: slideInUp 0.6s ease-out;
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .logo-area {
            animation: fadeIn 0.8s ease-out 0.2s both;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
    </style>
</head>

<body>
    <div class="content main-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12">
                    <!-- Logo ve Başlık Alanı -->
                    <div class="logo-area text-center">
                        <div class="logo-placeholder">
                            <i class="fas fa-wine-glass glass-icon ic-lg"></i>
                        </div>
                        <h1 class="welcome-title fs-2xl">Cam Takip Sistemine Hoşgeldiniz</h1>
                        <p class="welcome-description fs-md">
                            Cam fiyat listesi ve siparişlerinizi kolayca yönetin.
                        </p>
                    </div>
                    
                    <!-- Ana Kart -->
                    <div class="d-flex justify-content-center">
                        <div class="welcome-card">
                            <div class="card-body text-center">
                                <div class="mb-4">
                                    <i class="fas fa-chart-line text-primary mb-3 ic-3xl"></i>
                                    <h4 class="card-title text-dark mb-3">Sisteme Giriş Yapın</h4>
                                    <p class="card-text text-muted">
                                        Hesabınıza giriş yaparak cam ürünlerinizi takip etmeye başlayın.
                                    </p>
                                </div>
                                
                                <div class="buttons-container">
                                    <a href="<?= url('login') ?>" class="btn btn-custom-primary">
                                        <i class="fas fa-sign-in-alt me-2"></i>
                                        Oturum Aç
                                    </a>
                                    <a href="<?= url('register') ?>" class="btn btn-custom-secondary">
                                        <i class="fas fa-user-plus me-2"></i>
                                        Kayıt Ol
                                    </a>
                                </div>
                                
                                <div class="mt-4 pt-3 border-top">
                                    <small class="text-muted">
                                        <i class="fas fa-shield-alt me-1"></i>
                                        Güvenli ve kullanıcı dostu arayüz
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer fs-xs">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <p class="mb-0">
                        © 2025 Cam Takip Sistemi | 
                        <a href="mailto:destek@camtakip.com">
                            <i class="fas fa-envelope me-1"></i>
                            İletişim
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Optional: Add smooth scrolling and enhanced interactions -->
    <script>
        // Add some interactive enhancements
        document.addEventListener('DOMContentLoaded', function() {
            // Add subtle hover effects to the main card
            const card = document.querySelector('.welcome-card');
            
            card.addEventListener('mouseenter', function() {
                this.style.boxShadow = '0 25px 50px rgba(0, 0, 0, 0.15)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.boxShadow = '0 20px 40px rgba(0, 0, 0, 0.1)';
            });
        });
    </script>
</body>

</html>