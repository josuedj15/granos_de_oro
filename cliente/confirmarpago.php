<?php
session_start();
include_once "./diseño/encabezado.php";
include_once "../base/conexion.php"; // Asegúrate de que esta ruta sea correcta

// Verificar si se recibió el ID de la venta
if (!isset($_GET["id"])) {  // Cambiado de $_POST a $_GET
    header("Location: ./listar.php"); // Redirigir a la página de compras si no hay ID
    exit;
}

$idVenta = $_GET["id"];  // Obtener el ID de la venta desde la URL

// Función para obtener los detalles de la venta desde la base de datos
function obtenerDetallesVenta($conexion, $idVenta) {
    $sentencia = $conexion->prepare("
        SELECT 
            ventas.id AS id_venta,
            ventas.fecha_venta,
            ventas.total AS total_venta,
            ventas.cliente_id,
            GROUP_CONCAT(
                productos.id, '..',
                productos.nombre, '..',
                productos.descripcion, '..',
                productos.precio_venta, '..',
                productos.unidad_medida, '..',
                productos_vendidos.cantidad
                SEPARATOR '__'
            ) AS productos
        FROM ventas
        INNER JOIN productos_vendidos ON productos_vendidos.id_venta = ventas.id
        INNER JOIN productos ON productos.id = productos_vendidos.id_producto
        WHERE ventas.id = :idVenta
        GROUP BY ventas.id;
    ");
    $sentencia->bindParam(':idVenta', $idVenta, PDO::PARAM_INT);
    $sentencia->execute();
    $resultado = $sentencia->fetch(PDO::FETCH_OBJ);

    // Procesar los productos (desconcatenar)
    $detallesVenta = [
        "id_venta" => $resultado->id_venta,
        "fecha_venta" => $resultado->fecha_venta,
        "total_venta" => $resultado->total_venta,
        "cliente_id" => $resultado->cliente_id,
        "productos" => []
    ];

    if ($resultado->productos) {
        foreach (explode("__", $resultado->productos) as $productosConcatenados) {
            $productoArray = explode("..", $productosConcatenados);
            $detallesVenta["productos"][] = [
                "id" => $productoArray[0],
                "nombre" => $productoArray[1],
                "descripcion" => $productoArray[2],
                "precio_venta" => $productoArray[3],
                "cantidad" => $productoArray[5],
                "unidad_medida" => $productoArray[4],
                "total" => $productoArray[3] * $productoArray[5] // Calcular el total
            ];
        }
    }

    return $detallesVenta;
}

// Obtener los detalles de la venta
$detallesVenta = obtenerDetallesVenta($conexion, $idVenta);

if (!$detallesVenta) {
    echo "<div class='alert alert-danger'>No se encontraron detalles para la venta con ID: $idVenta</div>";
    exit;
}

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