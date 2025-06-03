<?php
session_start();
require 'config.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$menu = $conn->query("SELECT * FROM dishes");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Menú del Restaurante</title>
    <link rel="stylesheet" href="assets/style.css">
    <!-- Bootstrap CDN para estilos modernos y responsive -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    .main-menu-container {
        max-width: 1100px;
        margin: 40px auto 0 auto;
        background: #fff;
        box-shadow: 0 2px 12px rgba(26,123,79,0.11);
        padding: 2.3rem 2.7rem 2.7rem 2.7rem;
        border-radius: 20px;
    }
    .menu-title {
        color: #1a7b4f;
        margin-bottom: .5em;
        font-weight: bold;
        letter-spacing: 1px;
        text-align: center;
    }
    .user-bar {
        text-align: center;
        margin-bottom: 2em;
        font-size: 1.1em;
        color: #237953;
    }
    .card-restaurant {
        border-radius: 18px;
        box-shadow: 0 2px 10px rgba(26,123,79,0.07);
        transition: transform 0.14s, box-shadow 0.14s;
        border: none;
        background: #fafdff;
    }
    .card-restaurant:hover {
        transform: translateY(-6px) scale(1.025);
        box-shadow: 0 8px 22px rgba(26,123,79,0.16);
    }
    .card-title {
        color: #1a7b4f;
        font-size: 1.3em;
        font-weight: bold;
    }
    .card-text {
        color: #444;
        font-size: 1em;
    }
    .card-price {
        color: #237953;
        font-size: 1.1em;
        font-weight: bold;
    }
    .menu-btns {
        margin: 2.3em 0 1.8em 0;
        text-align: center;
    }
    .btn-custom {
        background: #1a7b4f;
        color: #fff;
        font-weight: 600;
        border-radius: 5px;
        margin: 0 0.3em;
        padding: 0.55em 1.3em;
        transition: background 0.2s;
        border: none;
        letter-spacing: 1px;
    }
    .btn-custom:hover {
        background: #145e3a;
        color: #fff;
    }
    .no-dishes {
        color: #777;
        text-align: center;
        margin: 2em 0 1.5em 0;
        font-size: 1.15em;
        letter-spacing: 1px;
    }
    @media (max-width: 800px) {
        .main-menu-container {
            padding: 1em 0.4em;
        }
    }
    </style>
</head>
<body>
<div class="main-menu-container">
    <h2 class="menu-title">Menú del Restaurante</h2>
    <div class="user-bar">
        Bienvenido, <b><?= htmlspecialchars($_SESSION['username']) ?></b> (<?= htmlspecialchars($_SESSION['role']) ?>)
        | <a href="logout.php" class="btn btn-sm btn-outline-danger ms-2">Salir</a>
    </div>
    <div class="menu-btns">
        <?php if ($_SESSION['role'] === 'Administrator'): ?>
            <a href="gestionar_menu.php" class="btn btn-custom">Gestionar Menú</a>
        <?php endif; ?>
        <a href="pedidos.php" class="btn btn-custom">Pedidos</a>
        <a href="comentarios.php" class="btn btn-custom">Comentarios</a>
    </div>
    <div class="row g-4">
        <?php if ($menu->num_rows === 0): ?>
            <div class="no-dishes">No hay platos en el menú actualmente.</div>
        <?php endif; ?>
        <?php while($dish = $menu->fetch_assoc()): ?>
            <div class="col-md-4 col-sm-6">
                <div class="card card-restaurant h-100">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <h5 class="card-title"><?= htmlspecialchars($dish['name']); ?></h5>
                        <p class="card-text mb-2"><?= htmlspecialchars($dish['description']); ?></p>
                        <div class="card-price mb-2">$<?= number_format($dish['price'], 2); ?></div>
                        <!-- Botón de pedir (puedes activar según rol) -->
                        <?php if (in_array($_SESSION['role'], ['Client', 'Waiter'])): ?>
                            <a href="pedidos.php" class="btn btn-outline-success btn-sm mt-auto">Pedir este plato</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>
</body>
</html>