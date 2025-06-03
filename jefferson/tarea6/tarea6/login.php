<?php
session_start();
require 'config.php';
$msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['username'];
    $pass = $_POST['password'];
    $stmt = $conn->prepare("SELECT u.id, u.username, u.password, r.name as role FROM users u JOIN roles r ON u.role_id=r.id WHERE username=?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($info = $result->fetch_assoc()) {
        if (password_verify($pass, $info['password'])) {
            $_SESSION['user_id'] = $info['id'];
            $_SESSION['role'] = $info['role'];
            $_SESSION['username'] = $info['username'];
            header('Location: menu.php');
            exit;
        } else {
            $msg = "Contraseña incorrecta";
        }
    } else {
        $msg = "Usuario no registrado";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="assets/style.css">
    <!-- Bootstrap CDN opcional para mayor elegancia -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        background: linear-gradient(120deg, #e6f4ec 0%, #fafcfd 100%);
        min-height: 100vh;
    }
    .login-container {
        max-width: 430px;
        margin: 80px auto;
        background: #fff;
        border-radius: 18px;
        box-shadow: 0 4px 18px rgba(26,123,79,0.10);
        padding: 2.5rem 2.5rem 2rem 2.5rem;
    }
    .login-title {
        color: #1a7b4f;
        letter-spacing: 1px;
        font-weight: bold;
        margin-bottom: 1.5rem;
        text-align: center;
    }
    .form-label {
        color: #237953;
        font-weight: 500;
    }
    .login-btn {
        width: 100%;
        background: #1a7b4f;
        border: none;
        font-weight: bold;
        letter-spacing: 1px;
        transition: background 0.2s;
    }
    .login-btn:hover {
        background: #145e3a;
    }
    .login-link {
        display: block;
        text-align: center;
        margin-top: 1.3em;
        color: #1a7b4f;
        text-decoration: none;
        font-size: 1em;
    }
    .login-link:hover {
        color: #145e3a;
        text-decoration: underline;
    }
    </style>
</head>
<body>
<div class="login-container">
    <h2 class="login-title">Bienvenido al Restaurante</h2>
    <?php if ($msg): ?>
        <div class="alert alert-error mb-3"><?= $msg ?></div>
    <?php endif; ?>
    <form method="post" autocomplete="off">
        <div class="mb-3">
            <label for="user" class="form-label">Usuario</label>
            <input type="text" name="username" id="user" class="form-control" required autofocus>
        </div>
        <div class="mb-3">
            <label for="pass" class="form-label">Contraseña</label>
            <input type="password" name="password" id="pass" class="form-control" required>
        </div>
        <button type="submit" class="btn login-btn mt-2">Iniciar sesión</button>
    </form>
    <a href="register.php" class="login-link">¿No tienes cuenta? Regístrate aquí</a>
</div>
</body>
</html>