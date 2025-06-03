<?php
function administradorDescripcion() {
    return <<<HTML
    <div class="rol-box">
        <span style="font-size:1.5em;">👑</span>
        <b style="color:#1a7b4f;">Administrador</b>
        <div>
            Acceso completo al sistema. Gestiona menús, usuarios, pedidos y la configuración general.
        </div>
    </div>
    HTML;
}

function chefDescripcion() {
    return <<<HTML
    <div class="rol-box">
        <span style="font-size:1.5em;">👨‍🍳</span>
        <b style="color:#237953;">Chef</b>
        <div>
            Encargado de la cocina. Visualiza y actualiza el estado de los pedidos.
        </div>
    </div>
    HTML;
}

function camareroDescripcion() {
    return <<<HTML
    <div class="rol-box">
        <span style="font-size:1.5em;">🧑‍💼</span>
        <b style="color:#369cf7;">Camarero</b>
        <div>
            Atiende a los clientes, toma pedidos y los marca como entregados.
        </div>
    </div>
    HTML;
}

function clienteDescripcion() {
    return <<<HTML
    <div class="rol-box">
        <span style="font-size:1.5em;">🧑‍🍽️</span>
        <b style="color:#b97d14;">Cliente</b>
        <div>
            Consulta el menú, realiza pedidos y hace seguimiento de sus órdenes.
        </div>
    </div>
    HTML;
}
?>