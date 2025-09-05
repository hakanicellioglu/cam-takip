<?php
require_once 'config.php';

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
                header('Location: login.php');
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
            max-width: 500px;
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
        .btn-secondary-custom {
            background-color: var(--secondary-color);
            color: var(--text-color);
            border: none;
            border-radius: 0.375rem;
        }
        .btn-secondary-custom:hover {
            background-color: #FFA000;
            color: var(--text-color);
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
            <h3>Kayıt Ol</h3>
            <?php if ($error): ?>
                <div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($error); ?></div>
            <?php elseif ($success): ?>
                <div class="alert alert-success" role="alert"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            <form method="post" action="">
                <div class="row g-3 mb-3">
                    <div class="col">
                        <label for="firstname" class="form-label">İsim</label>
                        <input type="text" class="form-control" id="firstname" name="firstname" required>
                    </div>
                    <div class="col">
                        <label for="lastname" class="form-label">Soyisim</label>
                        <input type="text" class="form-control" id="lastname" name="lastname" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="username" class="form-label">Kullanıcı adı</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">E-posta</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-4">
                    <label for="password" class="form-label">Parola</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-secondary-custom">Kayıt Ol</button>
                </div>
            </form>
            <p class="text-center mt-3 mb-0 text-muted">Zaten hesabınız var mı? <a href="login.php">Oturum Aç</a></p>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
