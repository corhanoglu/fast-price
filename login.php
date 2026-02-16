<?php
$page_title = 'Giriş Yap';
include 'header.php';

// Zaten giriş yapmışsa yönlendir
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Kullanıcı adı ve şifre boş bırakılamaz.';
    } else {
        $stmt = $db->prepare("SELECT * FROM kullanicilar WHERE kullanici_adi = :username");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user->sifre)) {
            $_SESSION['user_id'] = $user->id;
            $_SESSION['username'] = $user->kullanici_adi;
            $_SESSION['rol'] = $user->rol;
            $_SESSION['role'] = $user->rol; // Header ve yetki kontrolleri ile tam uyum için

            // Tüm kullanıcıları (Admin dahil) index.php'ye yönlendir
            header("Location: index.php");
            exit;
        } else {
            $error = 'Geçersiz kullanıcı adı veya şifre.';
        }
    }
}
?>

<style>
    .login-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 70vh;
        padding: 20px;
    }
    .login-card {
        background: #fff;
        width: 100%;
        max-width: 400px;
        padding: 40px;
        border-radius: 20px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        text-align: center;
    }
    .login-card h2 { margin-top: 0; font-weight: 600; color: #1d1d1f; }
    .login-card .form-group { text-align: left; margin-bottom: 20px; }
    .login-card label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 8px; }
    .login-card input {
        width: 100%;
        padding: 12px;
        border: 1px solid #d2d2d7;
        border-radius: 12px;
        font-size: 16px;
        box-sizing: border-box;
    }
    .login-card input:focus { border-color: #0071e3; outline: none; }
    .login-card .button {
        width: 100%;
        padding: 14px;
        background: #0071e3;
        color: #fff;
        border: none;
        border-radius: 12px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
    }
    .error-box {
        background: #fff2f2;
        color: #d8000c;
        padding: 10px;
        border-radius: 10px;
        margin-bottom: 20px;
        font-size: 14px;
    }
</style>

<div class="container login-wrapper">
    <div class="login-card">
        <h2>Sistem Girişi</h2>

        <?php if ($error): ?>
            <div class="error-box"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="username">Kullanıcı Adı</label>
                <input type="text" id="username" name="username" required autofocus>
            </div>
            <div class="form-group">
                <label for="password">Şifre</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="button">Giriş Yap</button>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>
