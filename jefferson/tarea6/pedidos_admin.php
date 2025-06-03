<?php
// Simulación de pedidos
$pedidos = [
    ['id' => 1, 'cliente' => 'Juan Pérez', 'estado' => 'En preparación', 'fecha' => '2025-05-30 09:20'],
    ['id' => 2, 'cliente' => 'Ana Torres', 'estado' => 'Listo', 'fecha' => '2025-05-30 10:05'],
    ['id' => 3, 'cliente' => 'Luis Gómez', 'estado' => 'Entregado', 'fecha' => '2025-05-29 18:55'],
];

// Estados posibles
$estados = ['En preparación', 'Listo', 'Entregado', 'Cancelado'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Pedidos (Administrador)</title>
    <style>
        body { background:#f6faf9; font-family:sans-serif; }
        .container { max-width:900px; margin:40px auto; background:#fff; border-radius:13px; box-shadow:0 4px 20px rgba(26,123,79,0.13); padding:2.3em 2em;}
        h2 { color:#1a7b4f; text-align:center; margin-bottom:1.4em; }
        table { width:100%; border-collapse:collapse; margin-bottom:1em;}
        th, td { border:1px solid #e3e9e7; padding:0.7em 0.5em; text-align:center; }
        th { background:#e6f4ec; }
        .btn { padding:6px 16px; border-radius:5px; border:none; cursor:pointer; }
        .btn-ver { background:#369cf7; color:#fff; }
        .btn-editar { background:#f7be3b; color:#fff; }
        .btn-eliminar { background:#e15a5e; color:#fff; }
        .btn-exportar { background:#1a7b4f; color:#fff; margin-right:0.7em;}
        .filtros { margin-bottom:1.4em; display:flex; gap:1em; }
        .filtros input, .filtros select { padding:6px; border-radius:4px; border:1px solid #b0cdbe; }
        @media (max-width:700px) {
            .container { padding:0.8em 0.2em;}
            .filtros { flex-direction:column; }
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Gestión de Pedidos <span style="font-size:1em; color:#7ad8a3;">(Administrador)</span></h2>
    
    <div style="margin-bottom:1.2em; display:flex; justify-content:space-between; align-items:center;">
        <form class="filtros" method="get">
            <input type="text" name="buscar" placeholder="Buscar por cliente o ID">
            <select name="estado">
                <option value="">Todos los estados</option>
                <?php foreach ($estados as $estado): ?>
                    <option value="<?= $estado ?>"><?= $estado ?></option>
                <?php endforeach; ?>
            </select>
            <button class="btn btn-ver" type="submit">Buscar</button>
        </form>
        <div>
            <button class="btn btn-exportar" onclick="alert('Función de exportar a PDF')">Exportar PDF</button>
            <button class="btn btn-exportar" onclick="alert('Función de exportar a Excel')">Exportar Excel</button>
        </div>
    </div>
    
    <table>
        <tr>
            <th>ID Pedido</th>
            <th>Cliente</th>
            <th>Fecha</th>
            <th>Estado</th>
            <th>Opciones</th>
        </tr>
        <?php foreach ($pedidos as $pedido): ?>
        <tr>
            <td><?= $pedido['id'] ?></td>
            <td><?= $pedido['cliente'] ?></td>
            <td><?= $pedido['fecha'] ?></td>
            <td>
                <form style="margin:0;">
                    <select name="estado_<?= $pedido['id'] ?>">
                        <?php foreach ($estados as $estado): ?>
                            <option value="<?= $estado ?>" <?= $pedido['estado'] === $estado ? 'selected' : '' ?>><?= $estado ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button class="btn btn-editar" type="submit" title="Actualizar Estado">✔</button>
                </form>
            </td>
            <td>
                <button class="btn btn-ver" onclick="alert('Ver detalles del pedido #<?= $pedido['id'] ?>')">Ver</button>
                <button class="btn btn-editar" onclick="alert('Editar pedido #<?= $pedido['id'] ?>')">Editar</button>
                <button class="btn btn-eliminar" onclick="if(confirm('¿Eliminar pedido #<?= $pedido['id'] ?>?')){alert('Pedido eliminado');}">Eliminar</button>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    
    <div style="text-align:center; color:#888; font-size:0.97em;">
        <em>Opciones exclusivas para el Administrador: ver, editar, eliminar, cambiar estado, buscar y exportar pedidos.</em>
    </div>
</div>
</body>
</html>