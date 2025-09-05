<?php
session_start();
require_once 'config.php';

// Remember me çerezi varsa kullanıcıyı panele yönlendir
if (isset($_COOKIE['rememberme'])) {
    if (!isset($_SESSION['user_id'])) {
        $rememberId = (int)$_COOKIE['rememberme'];
        $stmt = $conn->prepare('SELECT id, username, firstname, lastname FROM users WHERE id = ? LIMIT 1');
        if ($stmt) {
            $stmt->bind_param('i', $rememberId);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['user'] = [
                    'username'  => $row['username'],
                    'firstname' => $row['firstname'],
                    'lastname'  => $row['lastname'],
                    'full_name' => $row['firstname'] . ' ' . $row['lastname']
                ];
            }
            $stmt->close();
        }
    }
    header('Location: dashboard.php');
    exit();
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
        $stmt = $conn->prepare('SELECT id, username, firstname, lastname, password_hash FROM users WHERE username = ? OR email = ? LIMIT 1');
        if ($stmt) {
            $stmt->bind_param('ss', $username, $username);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                if (password_verify($password, $row['password_hash'])) {
                    $_SESSION['user_id'] = $row['id'];
                    $_SESSION['user'] = [
                        'username'  => $row['username'],
                        'firstname' => $row['firstname'],
                        'lastname'  => $row['lastname'],
                        'full_name' => $row['firstname'] . ' ' . $row['lastname']
                    ];

                    $rememberFlag = isset($_POST['remember']) ? 1 : 0;
                    if ($rememberFlag) {
                        setcookie('rememberme', $row['id'], time() + (86400 * 30), '/');
                    } else {
                        setcookie('rememberme', '', time() - 3600, '/');
                    }

                    header('Location: dashboard.php');
                    exit();
                } else {
                    $error = 'Kullanıcı adı veya parola yanlış.';
                }
            } else {
                $error = 'Kullanıcı adı veya parola yanlış.';
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
    <title>Giriş Yap - Cam Takip Sistemi</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary-color: #0066cc;
            --secondary-color: #6c757d;
            --success-color: #198754;
            --warning-color: #fd7e14;
            --danger-color: #dc3545;
            --light-bg: #f8f9fa;
        }

        * {
            font-family: 'Inter', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            max-width: 450px;
            width: 100%;
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .login-card:hover {
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
            font-size: 1.8rem;
            position: relative;
            z-index: 1;
        }

        .card-header .icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.9;
        }

        .card-body {
            padding: 2.5rem;
        }

        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 16px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(0, 102, 204, 0.25);
        }

        .btn-custom-primary {
            background: var(--primary-color);
            border: none;
            padding: 12px 30px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 102, 204, 0.3);
            width: 100%;
        }

        .btn-custom-primary:hover {
            background: #0056b3;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 102, 204, 0.4);
        }

        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
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
            color: #0056b3;
            text-decoration: underline;
        }

        .forgot-password {
            color: var(--secondary-color);
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s ease;
        }

        .forgot-password:hover {
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
        }

        /* Animation for page load */
        .login-card {
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
    <a href="index.php" class="back-to-home">
        <i class="fas fa-arrow-left me-2"></i>
        Ana Sayfaya Dön
    </a>

    <div class="container">
        <div class="row min-vh-100 align-items-center justify-content-center">
            <div class="col-12 col-sm-10 col-md-6 col-lg-4 mx-auto">
                <div class="login-card">
                    <div class="card-header">
                        <i class="fas fa-sign-in-alt icon"></i>
                        <h3>Oturum Aç</h3>
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

                        <form method="post" action="">
                            <div class="mb-3">
                                <label for="username" class="form-label">
                                    <i class="fas fa-user me-2"></i>
                                    Kullanıcı adı veya eposta
                                </label>
                                <input type="text" class="form-control" id="username" name="username" required
                                    placeholder="Kullanıcı adınızı veya e-posta adresinizi girin">
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock me-2"></i>
                                    Parola
                                </label>
                                <input type="password" class="form-control" id="password" name="password" required
                                    placeholder="Parolanızı girin">
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                <label class="form-check-label" for="remember">
                                    Beni hatırla
                                </label>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <a href="#" class="forgot-password">
                                    <i class="fas fa-question-circle me-1"></i>
                                    Şifremi unuttum
                                </a>
                            </div>
                            <button type="submit" class="btn btn-custom-primary">
                                <i class="fas fa-sign-in-alt me-2"></i>
                                Giriş Yap
                            </button>
                        </form>
                    </div>
                    <div class="card-footer">
                        <p class="mb-0">
                            Hesabınız yok mu?
                            <a href="register.php">
                                <i class="fas fa-user-plus me-1"></i>
                                Kayıt olun
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
        });
    </script>
</body>

</html>