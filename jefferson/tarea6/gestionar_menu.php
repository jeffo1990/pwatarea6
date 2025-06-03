<?php
session_start();
require 'config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Administrator') {
    header('Location: login.php');
    exit;
}

$msg = "";

// Agregar plato
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'], $_POST['price'])) {
    $name = trim($_POST['name']);
    $desc = trim($_POST['description']);
    $price = floatval($_POST['price']);
    if ($name && $price > 0) {
        $stmt = $conn->prepare("INSERT INTO dishes (name, description, price) VALUES (?, ?, ?)");
        $stmt->bind_param("ssd", $name, $desc, $price);
        if ($stmt->execute()) {
            $msg = "Plato agregado correctamente.";
        } else {
            $msg = "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $msg = "Datos inválidos.";
    }
}

// Eliminar plato (usar POST para mayor seguridad)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_id'])) {
    $id = intval($_POST['eliminar_id']);
    $stmt = $conn->prepare("DELETE FROM dishes WHERE id=?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $msg = "Plato eliminado.";
    } else {
        $msg = "Error al eliminar: " . $stmt->error;
    }
    $stmt->close();
}

// Obtener platos
$dishes = $conn->query("SELECT * FROM dishes ORDER BY name ASC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestionar Menú</title>
    <link rel="stylesheet" href="assets/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    .menu-admin-container {
        max-width: 900px;
        margin: 40px auto;
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 4px 18px rgba(26,123,79,0.10);
        padding: 2.5rem 2.5rem 2rem 2.5rem;
    }
    .menu-admin-title {
        color: #1a7b4f;
        font-weight: bold;
        letter-spacing: 1px;
        margin-bottom: 1.7rem;
        text-align: center;
    }
    .table thead {
        background: #e6f4ec;
    }
    .table td, .table th {
        vertical-align: middle;
    }
    .form-label {
        color: #237953;
        font-weight: 500;
    }
    .btn-admin {
        background: #1a7b4f;
        color: #fff;
        border: none;
        font-weight: bold;
        letter-spacing: 1px;
        transition: background 0.2s;
    }
    .btn-admin:hover {
        background: #145e3a;
        color: #fff;
    }
    .volver-link {
        display: block;
        text-align: center;
        margin-top: 1.7em;
        color: #1a7b4f;
        text-decoration: none;
        font-size: 1em;
    }
    .volver-link:hover {
        color: #145e3a;
        text-decoration: underline;
    }
    </style>
</head>
<body>
<div class="menu-admin-container">
    <h2 class="menu-admin-title">Gestión de Menú <span style="font-size:0.8em; color:#555;">(Administrador)</span></h2>
    <div class="mb-4">
        <?php if ($msg): ?>
            <div class="alert <?= strpos($msg, 'correctamente') !== false ? 'alert-success' : (strpos($msg, 'Error') !== false ? 'alert-danger' : 'alert-warning') ?>">
                <?= htmlspecialchars($msg) ?>
            </div>
        <?php endif; ?>
        <form method="post" class="row g-3 align-items-end border rounded p-3 mb-4 shadow-sm" autocomplete="off">
            <div class="col-md-4">
                <label for="name" class="form-label">Nombre del Plato</label>
                <input name="name" id="name" class="form-control" maxlength="50" required>
            </div>
            <div class="col-md-5">
                <label for="description" class="form-label">Descripción</label>
                <input name="description" id="description" class="form-control" maxlength="255">
            </div>
            <div class="col-md-2">
                <label for="price" class="form-label">Precio ($)</label>
                <input name="price" id="price" type="number" min="0" step="0.01" class="form-control" required>
            </div>
            <div class="col-md-1 d-grid">
                <button type="submit" class="btn btn-admin">Agregar</button>
            </div>
        </form>
    </div>
    <h3 class="mb-3" style="font-size:1.25em; color:#237953;">Platos existentes</h3>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Precio</th>
                    <th style="width:120px;">Acción</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($dishes->num_rows === 0): ?>
                <tr>
                    <td colspan="4" class="text-center text-muted">No hay platos registrados.</td>
                </tr>
            <?php endif; ?>
            <?php while($dish = $dishes->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($dish['name']) ?></td>
                    <td><?= htmlspecialchars($dish['description']) ?></td>
                    <td>$<?= number_format($dish['price'],2) ?></td>
                    <td>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="eliminar_id" value="<?= $dish['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar este plato?')">Eliminar</button>
                        </form>
                        <!-- Botón para editar (preparado, pero no implementado) -->
                        <!--
                        <a href="editar_plato.php?id=<?= $dish['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                        -->
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <a href="menu.php" class="volver-link">← Volver al menú principal</a>
</div>
</body>
</html>