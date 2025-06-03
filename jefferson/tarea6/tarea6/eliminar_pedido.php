<?php
session_start();
require 'config.php';

// Solo el administrador puede eliminar pedidos
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'Administrator') {
    header("Location: pedidos.php");
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: pedidos.php");
    exit;
}

$order_id = intval($_GET['id']);

// Eliminar el pedido
$sql = "DELETE FROM orders WHERE id = ?";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $stmt->close();
}

header("Location: pedidos.php");
exit;
?>