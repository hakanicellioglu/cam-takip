<?php
session_start();
require_once 'config.php';

$cookieExists = isset($_COOKIE['rememberme']);

// Otomatik giriş
if (!isset($_SESSION['user_id']) && $cookieExists) {
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
            header('Location: dashboard.php');
            exit();
        }
        $stmt->close();
    }
}


$success = '';
$error = '';
$cookieMessage = $cookieExists ? 'Remember me çerezi mevcut.' : 'Remember me çerezi bulunamadı.';
$cookieClass = $cookieExists ? 'success' : 'warning';

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
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Giriş Yap</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-center">Giriş Yap</div>
                    <div class="card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($error); ?></div>
                        <?php elseif ($success): ?>
                            <div class="alert alert-success" role="alert"><?php echo htmlspecialchars($success); ?></div>
                        <?php endif; ?>
                        <div class="alert alert-<?php echo $cookieClass; ?>" role="alert">
                            <?php echo htmlspecialchars($cookieMessage); ?>
                        </div>
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
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="#" class="small">Şifremi unuttum</a>
                                <button type="submit" class="btn btn-primary">Giriş Yap</button>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer text-center">
                        <a href="register.php">Kayıt ol</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>