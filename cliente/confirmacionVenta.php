<?php
session_start();
include_once "./diseño/encabezado.php";

// Verificar si hay detalles de la venta en la sesión
if (!isset($_SESSION["detalles_venta"])) {
    header("Location: ./vender.php"); // Si no hay detalles, redirigir a la página de venta
    exit;
}

$detallesVenta = $_SESSION["detalles_venta"];
unset($_SESSION["detalles_venta"]); // Limpiar los detalles de la sesión para que no se muestren de nuevo accidentalmente
?>

<div class="col-xs-12">
    <h1>Confirmación de Venta</h1>
    <p>¡Gracias por tu compra!</p>

    <h2>Detalles de la Venta</h2>
    <ul>
        <li><strong>ID de Venta:</strong> <?php echo $detallesVenta["id_venta"]; ?></li>
        <li><strong>Fecha de Venta:</strong> <?php echo $detallesVenta["fecha_venta"]; ?></li>
        <li><strong>Total de la Venta:</strong> <?php echo $detallesVenta["total_venta"]; ?></li>
        <li><strong>ID de Cliente:</strong> <?php echo $detallesVenta["cliente_id"]; ?></li>
    </ul>

    <h2>Productos Comprados</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Precio</th>
                <th>Cantidad</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($detallesVenta["productos"] as $producto) { ?>
                <tr>
                    <td><?php echo $producto["id"]; ?></td>
                    <td><?php echo $producto["nombre"]; ?></td>
                    <td><?php echo $producto["descripcion"]; ?></td>
                    <td><?php echo $producto["precio_venta"]; ?></td>
                    <td><?php echo $producto["cantidad"] . " " . $producto["unidad_medida"]; ?></td>
                    <td><?php echo $producto["total"]; ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <h2>Datos para Pagar</h2>
    <p>Por favor, realiza el pago a la siguiente cuenta:</p>
    <p>
        <strong>Banco:</strong> Mi Banco<br>
        <strong>Número de Cuenta:</strong> 1234-5678-90<br>
        <strong>Titular de la Cuenta:</strong> Mi Empresa S.A.
    </p>

    <p>
        Una vez realizado el pago, envía el comprobante a:
        <a href="mailto:pagos@granosdeoro.com">pagos@granosdeoro.com</a>
    </p>
    <p>
        O a este numero de WhatsApp:
        <a href="https://wa.me/7751586346">7751586346</a>
    </p>


    <p>¡Gracias por tu preferencia!</p>
</div>

<?php include_once "./diseño/pie.php"; ?>