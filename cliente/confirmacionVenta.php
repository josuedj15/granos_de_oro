<?php
session_start();
include_once "./diseño/encabezado.php"; // Ajusta la ruta si es necesario

// *** Verificación de permisos para el cliente ***
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] !== 'cliente') {
    header("Location: ../login/login.php"); // Redirigir si no es cliente o no ha iniciado sesión
    exit();
}

if (!isset($_SESSION["detalles_venta"])) {
    header("Location: ./vender.php");
    exit;
}

$detallesVenta = $_SESSION["detalles_venta"];
unset($_SESSION["detalles_venta"]); // Limpiar los detalles de la sesión
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
                <th>Precio</th>
                <th>Cantidad Total</th>
                <th>Origen(es)</th> <th>Total Producto</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($detallesVenta["productos"] as $producto) { ?>
                <tr>
                    <td><?php echo $producto["id"]; ?></td>
                    <td><?php echo $producto["nombre"]; ?></td>
                    <td><?php echo $producto["precio_venta"]; ?></td>
                    <td><?php echo $producto["cantidad"] . " " . $producto["unidad_medida"]; ?></td>
                    <td>
                        <?php
                        // Mostrar el nombre principal (ej. "Múltiples")
                        echo htmlspecialchars($producto["almacen_origen_display"] ?? 'N/A');

                        // Opcional: Mostrar el desglose detallado si hay múltiples fuentes
                        if (isset($producto["fulfillment_breakdown"]) && count($producto["fulfillment_breakdown"]) > 1) {
                            echo " (";
                            $breakdowns = [];
                            foreach ($producto["fulfillment_breakdown"] as $source) {
                                $breakdowns[] = htmlspecialchars($source['cantidad']) . " de " . htmlspecialchars($source['almacen_nombre']);
                            }
                            echo implode(', ', $breakdowns) . ")";
                        }
                        ?>
                    </td>
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
    <a href="./vender.php" class="btn btn-primary">Realizar otra compra</a>

</div>

<?php include_once "./diseño/pie.php" ?>