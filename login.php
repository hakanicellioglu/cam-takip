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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #1E88E5;
            --secondary: #FFC107;
            --accent: #4CAF50;
            --text-color: #212121;
            --background: #F5F7FA;
            --danger: #E53935;
            --radius: 0.375rem;
        }
        body {
            background-color: var(--background);
            color: var(--text-color);
            font-family: 'Segoe UI', Arial, sans-serif;
        }
        .card {
            border: none;
            border-radius: var(--radius);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.05);
        }
        .card-title {
            color: var(--primary);
        }
        .form-control {
            border-radius: var(--radius);
        }
        .btn-primary-custom {
            background-color: var(--primary);
            border-color: var(--primary);
            color: #fff;
            border-radius: var(--radius);
        }
        .btn-primary-custom:hover {
            background-color: #1669C1;
            border-color: #1669C1;
        }
        .alert-danger {
            background-color: rgba(229, 57, 53, 0.1);
            color: var(--danger);
            border-left: 4px solid var(--danger);
            border-radius: var(--radius);
        }
        .alert-success {
            background-color: rgba(76, 175, 80, 0.1);
            color: var(--accent);
            border-left: 4px solid var(--accent);
            border-radius: var(--radius);
        }
        a {
            color: var(--primary);
            text-decoration: none;
        }
        a:hover {
            color: #1669C1;
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card p-4">
                <h3 class="card-title text-center mb-1">Oturum Aç</h3>
                <p class="text-center text-muted mb-4">Hesabınıza erişin</p>
                <?php if ($error): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php elseif ($success): ?>
                    <div class="alert alert-success" role="alert">
                        <?php echo htmlspecialchars($success); ?>
                    </div>
                <?php endif; ?>
                <form method="post" action="">
                    <div class="mb-3">
                        <label for="username" class="form-label">Kullanıcı adı veya eposta</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Parola</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label" for="remember">Beni hatırla</label>
                    </div>
                    <button type="submit" class="btn btn-primary-custom w-100">Giriş Yap</button>
                </form>
                <div class="text-center mt-3">
                    <small class="text-muted">Hesabınız yok mu? <a href="register.php">Kayıt olun</a></small>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>