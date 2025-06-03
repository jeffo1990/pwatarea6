<?php
session_start();
require 'config.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Agregar comentario o respuesta
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    $comment = trim($_POST['comment']);
    $reply_to = isset($_POST['reply_to']) && $_POST['reply_to'] !== '' ? intval($_POST['reply_to']) : null;
    if ($comment !== '') {
        $stmt = $conn->prepare("INSERT INTO comments (user_id, comment, reply_to) VALUES (?, ?, ?)");
        $stmt->bind_param("isi", $user_id, $comment, $reply_to);
        $stmt->execute();
    }
    header("Location: comentarios.php");
    exit;
}

// Eliminar comentario (solo admin)
if (isset($_GET['eliminar']) && $role === 'Administrator') {
    $id = intval($_GET['eliminar']);
    $conn->query("DELETE FROM comments WHERE id = $id OR reply_to = $id");
    header("Location: comentarios.php");
    exit;
}

$comentarios = $conn->query("SELECT c.*, u.username FROM comments c JOIN users u ON c.user_id = u.id WHERE c.reply_to IS NULL ORDER BY c.created_at DESC");
function obtener_respuestas($conn, $id) {
    return $conn->query("SELECT c.*, u.username FROM comments c JOIN users u ON c.user_id = u.id WHERE c.reply_to = $id ORDER BY c.created_at ASC");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comentarios</title>
    <link rel="stylesheet" href="assets/style.css">
    <!-- Bootstrap para diseño moderno -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    .comentarios-container {
        max-width: 720px;
        margin: 40px auto;
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 18px rgba(26,123,79,0.11);
        padding: 2.2rem 2.5rem 2rem 2.5rem;
    }
    .comentarios-title {
        color: #1a7b4f;
        font-weight: bold;
        margin-bottom: 1.5em;
        text-align: center;
        letter-spacing: 1px;
    }
    .form-label {
        color: #237953;
        font-weight: 500;
    }
    .btn-comentario {
        background: #1a7b4f;
        color: #fff;
        font-weight: 600;
        border-radius: 5px;
        margin: 0 0.2em;
        padding: 0.5em 1.2em;
        transition: background 0.2s;
        border: none;
        letter-spacing: 1px;
    }
    .btn-comentario:hover {
        background: #145e3a;
        color: #fff;
    }
    .comentario-box {
        border: 1.6px solid #e9f5f0;
        border-radius: 10px;
        background: #fafdff;
        margin-bottom: 1.3em;
        padding: 1.1em 1.2em;
        box-shadow: 0 2px 7px rgba(26,123,79,0.04);
    }
    .comentario-autor {
        color: #237953;
        font-weight: 600;
        font-size: 1.04em;
    }
    .comentario-fecha {
        font-size: 0.89em;
        color: #aaa;
        margin-left: 1em;
    }
    .comentario-texto {
        margin: 0.7em 0 0.7em 0;
        color: #222;
        font-size: 1em;
        white-space: pre-line;
    }
    .comentario-acciones {
        margin-top: .5em;
    }
    .respuesta-box {
        margin-left: 2.3em;
        margin-top: 0.6em;
        border-left: 3.5px solid #d0f0e1;
        background: #f3fbf7;
        padding: 0.7em 1.2em;
        border-radius: 0 8px 8px 0;
        color: #444;
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
    @media (max-width: 700px) {
        .comentarios-container {
            padding: 1em 0.4em;
        }
        .comentario-box, .respuesta-box {
            padding: 0.7em 0.7em;
        }
    }
    </style>
</head>
<body>
<div class="comentarios-container">
    <h2 class="comentarios-title">Comentarios y Respuestas</h2>
    <form action="" method="post" class="mb-4">
        <label for="comment" class="form-label">Agregar un comentario:</label>
        <textarea name="comment" id="comment" class="form-control mb-2" required placeholder="Escribe tu comentario" maxlength="800"></textarea>
        <input type="hidden" name="reply_to" value="">
        <div class="d-grid">
            <button type="submit" class="btn btn-comentario">Comentar</button>
        </div>
    </form>
    <?php while ($c = $comentarios->fetch_assoc()): ?>
        <div class="comentario-box">
            <span class="comentario-autor"><?= htmlspecialchars($c['username']) ?></span>
            <span class="comentario-fecha"><?= date('d/m/Y H:i', strtotime($c['created_at'])) ?></span>
            <div class="comentario-texto"><?= nl2br(htmlspecialchars($c['comment'])) ?></div>
            <div class="comentario-acciones">
                <?php if (in_array($role, ['Administrator', 'Chef', 'Waiter'])): ?>
                    <button class="btn btn-sm btn-outline-success"
                        onclick="mostrarRespuesta(<?= $c['id'] ?>); return false;">Responder</button>
                <?php endif; ?>
                <?php if ($role === 'Administrator'): ?>
                    <a href="?eliminar=<?= $c['id'] ?>" class="btn btn-sm btn-outline-danger ms-2"
                        onclick="return confirm('¿Eliminar este comentario y sus respuestas?')">Eliminar</a>
                <?php endif; ?>
            </div>
            <!-- Formulario respuesta inline -->
            <form action="" method="post" class="mt-2 d-none" id="form-responder-<?= $c['id'] ?>">
                <div class="input-group">
                    <input type="text" name="comment" class="form-control" placeholder="Responder..." maxlength="300" required>
                    <input type="hidden" name="reply_to" value="<?= $c['id'] ?>">
                    <button type="submit" class="btn btn-comentario">Enviar</button>
                </div>
            </form>
            <!-- Respuestas -->
            <?php $respuestas = obtener_respuestas($conn, $c['id']); ?>
            <?php while ($r = $respuestas->fetch_assoc()): ?>
                <div class="respuesta-box">
                    <span class="comentario-autor"><?= htmlspecialchars($r['username']) ?></span>
                    <span class="comentario-fecha"><?= date('d/m/Y H:i', strtotime($r['created_at'])) ?></span><br>
                    <span class="comentario-texto"><?= nl2br(htmlspecialchars($r['comment'])) ?></span>
                    <?php if ($role === 'Administrator'): ?>
                        <a href="?eliminar=<?= $r['id'] ?>" class="btn btn-sm btn-outline-danger ms-2"
                            onclick="return confirm('¿Eliminar respuesta?')">Eliminar</a>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>
    <?php endwhile; ?>
    <a href="menu.php" class="volver-link">← Volver al menú principal</a>
</div>
<script>
function mostrarRespuesta(id) {
    // Oculta cualquier otro formulario visible
    document.querySelectorAll('form[id^="form-responder-"]').forEach(f => f.classList.add('d-none'));
    // Muestra el formulario de respuesta correspondiente
    document.getElementById('form-responder-' + id).classList.remove('d-none');
    // Da foco al input
    document.getElementById('form-responder-' + id).querySelector('input[name="comment"]').focus();
}
</script>
</body>
</html>