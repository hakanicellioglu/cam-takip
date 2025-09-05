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
        .btn-accent {
            background-color: var(--accent);
            border-color: var(--accent);
            color: #fff;
            border-radius: var(--radius);
        }
        .btn-accent:hover {
            background-color: #3E8E41;
            border-color: #3E8E41;
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
        <div class="col-md-6 col-lg-5">
            <div class="card p-4">
                <h3 class="card-title text-center mb-1">Kayıt Ol</h3>
                <p class="text-center text-muted mb-4">Yeni hesap oluşturun</p>
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
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="firstname" class="form-label">İsim</label>
                            <input type="text" class="form-control" id="firstname" name="firstname" required>
                        </div>
                        <div class="col-md-6 mb-3">
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
                    <button type="submit" class="btn btn-accent w-100">Hesap Oluştur</button>
                </form>
                <div class="text-center mt-3">
                    <small class="text-muted">Zaten hesabınız var mı? <a href="login.php">Giriş yapın</a></small>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>