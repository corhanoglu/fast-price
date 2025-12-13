<?php
$page_title = 'Giriş Yap';
include 'header.php'; // Ortak başlık

// Zaten giriş yapmışsa yönlendir
if (isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='index.php';</script>";
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

            $redirect = ($user->rol === 'admin') ? 'admin.php' : 'index.php';
            header("Location: $redirect");
            exit;
        } else {
            $error = 'Geçersiz kullanıcı adı veya şifre.';
        }
    }
}
?>

<div class="login-page" style="min-height: 60vh;">
    <div class="login-container">
        <form action="login.php" method="POST">
            <h2>Sistem Girişi</h2>
            <?php if ($error): ?><div class="message error-message"><?php echo $error; ?></div><?php endif; ?>
            <div class="form-group">
                <label for="username">Kullanıcı Adı</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Şifre</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="button">Giriş Yap</button>
        </form>
    </div>
</div>

</div><!-- container kapanış -->
</body>
</html>
