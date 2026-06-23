<?php
session_start();

if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header('Location: admin_login.php');
    exit;
}

$error = '';
$submittedEmail = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submittedEmail = trim($_POST['adminEmail'] ?? '');
    $submittedPassword = trim($_POST['adminPassword'] ?? '');

    $adminEmail = 'hilzarvalentine347@gmail.com';
    $adminPassword = 'admin123';

    if ($submittedEmail === $adminEmail && $submittedPassword === $adminPassword) {
        $_SESSION['isAdmin'] = true;
        $_SESSION['adminEmail'] = $submittedEmail;
        header('Location: admin_dashboard.php');
        exit;
    }

    $error = 'Invalid email or password!';
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Login</title>
    <link rel="stylesheet" href="admin_login.css" />
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  </head>

  <body>

  
    <!-- ADMIN LOGIN PAGE -->

    <div
      class="container d-flex justify-content-center align-items-center vh-100"
    >
      <div class="glass p-5" style="width: 400px">
        <h3 class="text-center mb-4" style="color: white">Admin Login</h3>

        <?php if ($error): ?>
          <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form id="loginForm" method="POST" action="admin_login.php">
          <div class="mb-3">
            <input
              type="email"
              name="adminEmail"
              value="<?= htmlspecialchars($submittedEmail) ?>"
              class="form-control glass-input"
              placeholder="Email"
              required
            />
          </div>

          <div class="mb-3">
            <input
              type="password"
              name="adminPassword"
              class="form-control glass-input"
              placeholder="Password"
              required
            />
          </div>

          <div class="d-flex justify-content-center">
            <button type="submit" class="button-28 login-button" role="button">Login</button>
          </div>

          <a
            href="forgot-password.php"
            class="text-decoration-none d-block text-center mt-3"
            style="color: gray"
            >Forgot password?</a
          >
        </form>
      </div>
      <a
        href="index.php"
        type="button"
        class="btn btn-outline-info arrow-left">
        <i class="fa-solid fa-arrow-left"></i>
      </a>
    </div>

    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-ENjdO4Dr2bkBIFxQpeo6l4Tn5KPhVY5KqjHtvG1F/LE9oE2BXj7raiF5cw3X9F9K"
      crossorigin="anonymous"
    ></script>
  </body>
</html>