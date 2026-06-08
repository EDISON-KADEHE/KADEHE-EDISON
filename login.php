<?php
require_once __DIR__ . '/config.php';

if (is_logged_in()) {
    redirect('dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = db()->prepare('SELECT * FROM admins WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {
        session_regenerate_id(true);
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_name'] = $admin['name'];
        redirect('dashboard.php');
    }

    $error = 'Invalid email or password.';
}

$pageTitle = 'Admin Login';
require __DIR__ . '/partials/header.php';
?>

<main class="auth-page">
    <section class="auth-panel">
        <div class="auth-brand">
            <span class="brand-mark">SM</span>
            <div>
                <h1>Student Manager</h1>
                <p>Secure administration portal</p>
            </div>
        </div>

        <?php if ($error): ?>
            <div class="alert danger"><?= e($error) ?></div>
        <?php endif; ?>

        <form method="post" class="form-stack">
            <label>
                Email
                <input type="email" name="email" value="<?= e($_POST['email'] ?? 'admin@example.com') ?>" required>
            </label>
            <label>
                Password
                <input type="password" name="password" placeholder="admin123" required>
            </label>
            <button class="primary-button" type="submit">Log in</button>
        </form>
    </section>
</main>

<?php require __DIR__ . '/partials/footer.php'; ?>

