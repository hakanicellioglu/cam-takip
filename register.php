<?php
require_once 'config.php';
require_once __DIR__ . '/helpers.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = trim($_POST['firstname'] ?? '');
    $lastname  = trim($_POST['lastname'] ?? '');
    $username  = trim($_POST['username'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $password  = $_POST['password'] ?? '';

    if ($firstname && $lastname && $username && $email && $password) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare('INSERT INTO users (firstname, lastname, username, email, password_hash) VALUES (?, ?, ?, ?, ?)');
        if ($stmt) {
            $stmt->bind_param('sssss', $firstname, $lastname, $username, $email, $hash);
            try {
                $stmt->execute();
                header('Location: ' . url('login'));
                exit;
            } catch (mysqli_sql_exception $e) {
                if ($e->getCode() === 1062) {
                    $error = 'Kullanıcı adı zaten mevcut.';
                } else {
                    $error = $e->getMessage();
                }
            }
            $stmt->close();
        } else {
            $error = 'Sorgu hazırlanamıyor: ' . $conn->error;
        }
    } else {
        $error = 'Tüm alanları doldurunuz.';
    }
}
?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kayıt Ol - Cam Takip Sistemi</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

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
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            font-size: 1rem;
        }

        .register-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            max-width: 500px;
            width: 100%;
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .register-card:hover {
            transform: translateY(-5px);
        }

        .card-header {
            background: var(--primary-color);
            color: white;
            padding: 2rem;
            text-align: center;
            border: none;
            position: relative;
        }

        .card-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(255, 255, 255, 0.1) 0%, transparent 100%);
        }

        .card-header h3 {
            margin: 0;
            font-weight: 700;
            font-size: 1.5rem;
            position: relative;
            z-index: 1;
        }

        .card-header .icon {
            font-size: 2rem;
            margin-bottom: 1rem;
            opacity: 0.9;
        }

        .card-body {
            padding: 2.5rem;
        }

        .form-label {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }

        .form-control {
            border-radius: 10px;
            border: 2px solid #007369;
            padding: 12px 16px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(0, 56, 64, 0.25);
        }

        .btn-custom-success {
            background: var(--primary-color);
            border: none;
            padding: 12px 30px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 56, 64, 0.3);
            width: 100%;
            color: #fff;
        }

        .btn-custom-success:hover {
            background: #005A5B;
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 90, 91, 0.4);
        }

        .alert {
            border-radius: 10px;
            border: none;
            padding: 1rem 1.5rem;
        }

        .alert-danger {
            background: rgba(220, 53, 69, 0.1);
            color: var(--danger-color);
            border-left: 4px solid var(--danger-color);
        }

        .alert-success {
            background: rgba(25, 135, 84, 0.1);
            color: var(--success-color);
            border-left: 4px solid var(--success-color);
        }

        .card-footer {
            background: rgba(248, 249, 250, 0.8);
            border: none;
            padding: 1.5rem 2.5rem;
            text-align: center;
        }

        .card-footer a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .card-footer a:hover {
            color: var(--primary-color);
            text-decoration: underline;
        }

        .back-to-home {
            position: absolute;
            top: 2rem;
            left: 2rem;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.1);
            padding: 0.5rem 1rem;
            border-radius: 10px;
            backdrop-filter: blur(10px);
        }

        .back-to-home:hover {
            color: white;
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }

        .row-cols-custom {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .card-body {
                padding: 2rem;
            }

            .card-header {
                padding: 1.5rem;
            }

            .card-header h3 {
                font-size: 1.5rem;
            }

            .back-to-home {
                position: static;
                display: inline-block;
                margin-bottom: 2rem;
            }

            .row-cols-custom {
                grid-template-columns: 1fr;
            }
        }

        /* Animation for page load */
        .register-card {
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
    </style>
</head>

<body>
    <a href="<?= url('') ?>" class="back-to-home">
        <i class="fas fa-arrow-left me-2"></i>
        Ana Sayfaya Dön
    </a>

    <div class="container">
        <div class="row min-vh-100 align-items-center justify-content-center">
            <div class="col-12 col-sm-10 col-md-6 col-lg-4 mx-auto">
                <div class="register-card">
                    <div class="card-header">
                        <i class="fas fa-user-plus icon"></i>
                        <h3>Yeni Hesap Oluştur</h3>
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <?php echo htmlspecialchars($error); ?>
                            </div>
                        <?php elseif ($success): ?>
                            <div class="alert alert-success" role="alert">
                                <i class="fas fa-check-circle me-2"></i>
                                <?php echo htmlspecialchars($success); ?>
                            </div>
                        <?php endif; ?>

                        <form method="post" action="<?= url('register') ?>">
                            <div class="row-cols-custom mb-3">
                                <div>
                                    <label for="firstname" class="form-label">
                                        <i class="fas fa-user me-2"></i>
                                        İsim
                                    </label>
                                    <input type="text" class="form-control" id="firstname" name="firstname" required
                                        placeholder="Adınız">
                                </div>
                                <div>
                                    <label for="lastname" class="form-label">
                                        <i class="fas fa-user me-2"></i>
                                        Soyisim
                                    </label>
                                    <input type="text" class="form-control" id="lastname" name="lastname" required
                                        placeholder="Soyadınız">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="username" class="form-label">
                                    <i class="fas fa-at me-2"></i>
                                    Kullanıcı adı
                                </label>
                                <input type="text" class="form-control" id="username" name="username" required
                                    placeholder="Benzersiz kullanıcı adı seçin">
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope me-2"></i>
                                    E-posta
                                </label>
                                <input type="email" class="form-control" id="email" name="email" required
                                    placeholder="ornek@email.com">
                            </div>
                            <div class="mb-4">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock me-2"></i>
                                    Parola
                                </label>
                                <input type="password" class="form-control" id="password" name="password" required
                                    placeholder="Güçlü bir parola oluşturun">
                            </div>
                            <button type="submit" class="btn btn-custom-success">
                                <i class="fas fa-user-plus me-2"></i>
                                Hesap Oluştur
                            </button>
                        </form>
                    </div>
                    <div class="card-footer">
                        <p class="mb-0">
                            Zaten hesabınız var mı?
                            <a href="<?= url('login') ?>">
                                <i class="fas fa-sign-in-alt me-1"></i>
                                Giriş yapın
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Add interactive enhancements
        document.addEventListener('DOMContentLoaded', function() {
            // Add focus effects to form inputs
            const inputs = document.querySelectorAll('.form-control');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.style.transform = 'translateY(-2px)';
                });

                input.addEventListener('blur', function() {
                    this.parentElement.style.transform = 'translateY(0)';
                });
            });

            // Password strength indicator (optional enhancement)
            const passwordInput = document.getElementById('password');
            passwordInput.addEventListener('input', function() {
                const password = this.value;
                const strength = calculatePasswordStrength(password);
                // You can add visual feedback here if needed
            });
        });

        function calculatePasswordStrength(password) {
            let strength = 0;
            if (password.length >= 8) strength++;
            if (/[a-z]/.test(password)) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;
            return strength;
        }
    </script>
</body>

</html>