<?php
session_start();
require 'config.php';

// Solo CHEF puede marcar como listo
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'Chef') {
    header("Location: pedidos.php");
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: pedidos.php");
    exit;
}

$order_id = intval($_GET['id']);

// Cambiar el estado del pedido a 'Ready to serve'
$sql = "UPDATE orders SET status = 'Ready to serve' WHERE id = ?";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $stmt->close();
}

header("Location: pedidos.php");
exit;
?>