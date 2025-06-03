<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit;
}

$rol = $_SESSION['role'];
$user_id = $_SESSION['user_id'];

// AGREGAR PEDIDO: solo si es cliente y se envía el formulario
if ($rol === 'Client' && isset($_POST['dish_id'], $_POST['quantity'])) {
    $dish_id = intval($_POST['dish_id']);
    $quantity = intval($_POST['quantity']);

    if ($dish_id > 0 && $quantity > 0) {
        $sql = "INSERT INTO orders (user_id, dish_id, quantity, status) VALUES (?, ?, ?, 'Pending')";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("iii", $user_id, $dish_id, $quantity);
            $stmt->execute();
            $stmt->close();
        }
    }
    // Redirige para evitar repost
    header("Location: pedidos.php");
    exit;
}

// Consulta pedidos según el rol
if ($rol === 'Administrator' || $rol === 'Chef' || $rol === 'Waiter') {
    $sql = "SELECT o.id, u.username, d.name as dish, o.quantity, o.status
            FROM orders o
            JOIN users u ON o.user_id = u.id
            JOIN dishes d ON o.dish_id = d.id
            ORDER BY o.id DESC";
    $stmt = $conn->prepare($sql);
} else { // Client
    $sql = "SELECT o.id, u.username, d.name as dish, o.quantity, o.status
            FROM orders o
            JOIN users u ON o.user_id = u.id
            JOIN dishes d ON o.dish_id = d.id
            WHERE o.user_id = ?
            ORDER BY o.id DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
}
if (!$stmt) {
    die("Error al preparar la consulta: " . $conn->error);
}
$stmt->execute();
$result = $stmt->get_result();

// Traer platos para el formulario si es cliente
$dishes = [];
if ($rol === 'Client') {
    $dish_sql = "SELECT id, name FROM dishes";
    $dish_result = $conn->query($dish_sql);
    while ($row = $dish_result->fetch_assoc()) {
        $dishes[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Pedidos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <div style='text-align:right;color:#1a7b4f;'><b>Rol: <?= htmlspecialchars($rol) ?></b></div>
    <h2>Gestión de Pedidos</h2>

    <?php if ($rol === 'Client'): ?>
    <!-- Formulario para agregar pedido -->
    <form method="POST" class="mb-4" style="max-width:400px;">
        <div class="mb-3">
            <label for="dish_id" class="form-label">Plato</label>
            <select name="dish_id" id="dish_id" class="form-select" required>
                <option value="">Seleccione un plato</option>
                <?php foreach ($dishes as $dish): ?>
                    <option value="<?= $dish['id'] ?>"><?= htmlspecialchars($dish['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="quantity" class="form-label">Cantidad</label>
            <input type="number" name="quantity" id="quantity" class="form-control" min="1" value="1" required>
        </div>
        <button type="submit" class="btn btn-primary">Agregar pedido</button>
    </form>
    <?php endif; ?>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Usuario</th>
                <th>Plato</th>
                <th>Cantidad</th>
                <th>Estado</th>
                <?php if ($rol !== 'Client'): ?>
                    <th>Acción</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['username']) ?></td>
                <td><?= htmlspecialchars($row['dish']) ?></td>
                <td><?= $row['quantity'] ?></td>
                <td><?= $row['status'] ?></td>
                <?php if ($rol !== 'Client'): ?>
                    <td>
                        <?php if ($rol === 'Administrator'): ?>
                            <a href="eliminar_pedido.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar pedido?')">Eliminar</a>
                        <?php endif; ?>
                        <?php if ($rol === 'Chef' && $row['status'] === 'Pending'): ?>
                            <a href="marcar_listo.php?id=<?= $row['id'] ?>" class="btn btn-success btn-sm">Marcar como Listo</a>
                        <?php endif; ?>
                        <?php if ($rol === 'Waiter' && $row['status'] === 'Ready to serve'): ?>
                            <a href="marcar_entregado.php?id=<?= $row['id'] ?>" class="btn btn-primary btn-sm">Marcar como Servido</a>
                        <?php endif; ?>
                    </td>
                <?php endif; ?>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <a href="menu.php" class="btn btn-secondary">← Volver al menú principal</a>
</div>
</body>
</html>