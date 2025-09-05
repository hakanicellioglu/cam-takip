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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #1E88E5;
            --secondary-color: #FFC107;
            --background-color: #F5F7FA;
            --text-color: #212121;
            --success-color: #4CAF50;
            --danger-color: #E53935;
        }
        body {
            background-color: var(--background-color);
            color: var(--text-color);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        .auth-card {
            background-color: #FFFFFF;
            border-radius: .5rem;
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.1);
            max-width: 400px;
            width: 100%;
        }
        .auth-card .card-body {
            padding: 2rem;
        }
        .auth-card h3 {
            color: var(--primary-color);
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .form-control {
            border-radius: 0.375rem;
        }
        .btn-primary-custom {
            background-color: var(--primary-color);
            color: #FFFFFF;
            border: none;
            border-radius: 0.375rem;
        }
        .btn-primary-custom:hover {
            background-color: #1565C0;
            color: #FFFFFF;
        }
        .alert-success {
            background-color: rgba(76,175,80,0.1);
            color: var(--success-color);
            border: none;
        }
        .alert-danger {
            background-color: rgba(229,57,53,0.1);
            color: var(--danger-color);
            border: none;
        }
        a {
            color: var(--primary-color);
            text-decoration: none;
        }
        a:hover {
            color: #1565C0;
        }
    </style>
</head>
<body>
    <div class="auth-card card">
        <div class="card-body">
            <h3>Oturum Aç</h3>
            <?php if ($error): ?>
                <div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($error); ?></div>
            <?php elseif ($success): ?>
                <div class="alert alert-success" role="alert"><?php echo htmlspecialchars($success); ?></div>
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
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary-custom">Oturum Aç</button>
                </div>
            </form>
            <p class="text-center mt-3 mb-0 text-muted">Hesabınız yok mu? <a href="register.php">Kayıt Ol</a></p>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
