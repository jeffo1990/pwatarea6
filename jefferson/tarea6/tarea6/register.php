<?php
require 'config.php';
$msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $pass = $_POST['password'];
    $role_id = intval($_POST['role_id']);
    if (empty($username) || empty($email) || empty($pass) || empty($role_id)) {
        $msg = "Todos los campos son obligatorios.";
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE username=? OR email=?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $msg = "El usuario o email ya existen.";
        } else {
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, email, password, role_id) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("sssi", $username, $email, $hash, $role_id);
            if ($stmt->execute()) {
                $msg = "Usuario registrado correctamente. <a href='login.php'>Iniciar sesión</a>";
            } else {
                $msg = "Error al registrar: " . $stmt->error;
            }
        }
    }
}
$roles = $conn->query("SELECT * FROM roles");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Usuario</title>
    <link rel="stylesheet" href="assets/style.css">
    <!-- Bootstrap CDN para diseño elegante -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        background: linear-gradient(120deg, #e6f4ec 0%, #fafcfd 100%);
        min-height: 100vh;
    }
    .register-container {
        max-width: 480px;
        margin: 60px auto;
        background: #fff;
        border-radius: 18px;
        box-shadow: 0 6px 25px rgba(26,123,79,0.12);
        padding: 2.5rem 2.5rem 2rem 2.5rem;
    }
    .register-title {
        color: #1a7b4f;
        font-weight: bold;
        letter-spacing: 1px;
        margin-bottom: 1.2rem;
        text-align: center;
    }
    .form-label {
        color: #237953;
        font-weight: 500;
    }
    .register-btn {
        width: 100%;
        background: #1a7b4f;
        border: none;
        font-weight: bold;
        letter-spacing: 1px;
        transition: background 0.2s;
    }
    .register-btn:hover {
        background: #145e3a;
    }
    .register-link {
        display: block;
        text-align: center;
        margin-top: 1.3em;
        color: #1a7b4f;
        text-decoration: none;
        font-size: 1em;
    }
    .register-link:hover {
        color: #145e3a;
        text-decoration: underline;
    }
    </style>
</head>
<body>
<div class="register-container">
    <h2 class="register-title">Crear una Cuenta</h2>
    <?php if ($msg): ?>
        <div class="alert <?= strpos($msg, 'correctamente') !== false ? 'alert-success' : 'alert-error' ?> mb-3"><?= $msg ?></div>
    <?php endif; ?>
    <form method="post" autocomplete="off">
        <div class="mb-3">
            <label for="username" class="form-label">Usuario</label>
            <input type="text" name="username" id="username" class="form-control" maxlength="50" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Correo electrónico</label>
            <input type="email" name="email" id="email" class="form-control" maxlength="100" required>
        </div>
        <div class="mb-3">
            <label for="pass" class="form-label">Contraseña</label>
            <input type="password" name="password" id="pass" class="form-control" minlength="4" maxlength="50" required>
        </div>
        <div class="mb-3">
            <label for="role_id" class="form-label">Rol</label>
            <select name="role_id" id="role_id" class="form-select" required>
                <option value="">Selecciona un rol</option>
                <?php while ($r = $roles->fetch_assoc()): ?>
                    <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['name']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <button type="submit" class="btn register-btn mt-2">Registrarme</button>
    </form>
    <a href="login.php" class="register-link">¿Ya tienes cuenta? Inicia sesión aquí</a>
</div>
</body>
</html>