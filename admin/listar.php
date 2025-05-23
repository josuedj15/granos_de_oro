<?php include_once "./diseño/encabezado.php" ?>

<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once "../base/conexion.php";

// Consulta de stock bajo (MOVIDA AQUÍ)
$sentencia_stock_bajo = $conexion->query("SELECT
    p.nombre,
    SUM(sa.stock) AS total_stock
FROM
    productos p
LEFT JOIN
    stock_almacen sa ON p.id = sa.producto_id
GROUP BY
    p.id, p.nombre
HAVING
    SUM(sa.stock) < 100;");

$productos_bajo_stock = $sentencia_stock_bajo->fetchAll(PDO::FETCH_ASSOC);

if (!empty($productos_bajo_stock)) {
    $_SESSION['productos_bajo_stock'] = $productos_bajo_stock;
    // Para depuración, puedes agregar esta línea temporalmente:
    // var_dump($_SESSION['productos_bajo_stock']);
}

// Consulta de productos (EXISTENTE)
$sentencia = $conexion->query("SELECT
    p.id,
    p.nombre,
    p.descripcion,
    p.precio_compra,
    p.precio_venta,
    p.unidad_medida,
    SUM(sa.stock) AS total_stock
FROM
    productos p
LEFT JOIN
    stock_almacen sa ON p.id = sa.producto_id
GROUP BY
    p.id, p.nombre, p.descripcion, p.precio_compra, p.precio_venta, p.unidad_medida;");
$productos = $sentencia->fetchAll(PDO::FETCH_OBJ);
?>

<div class="col-xs-12">
    <h1>Productos</h1>
    <?php
    if (isset($_SESSION['productos_bajo_stock']) && !empty($_SESSION['productos_bajo_stock'])) {
        echo '<div class="alert alert-warning">';
        echo '<strong>¡Atención!</strong> Los siguientes productos tienen un stock bajo:<br>';
        echo '<ul>';
        foreach ($_SESSION['productos_bajo_stock'] as $producto) {
            echo '<li>' . htmlspecialchars($producto['nombre']) . ' (Stock Total: ' . htmlspecialchars($producto['total_stock']) . ')</li>';
        }
        echo '</ul>';
        echo '</div>';
        unset($_SESSION['productos_bajo_stock']);
    }
    ?>
    <div>
        <a class="btn btn-success" id= "nuevo" href="./formulario.php">Nuevo <i class="fa fa-plus"></i></a>
    </div>
    <br>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Precio de compra</th>
                <th>Precio de venta</th>
                <th>Existencia Total</th>
                <th>Editar</th>
                <th>Eliminar</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($productos as $producto) { ?>
                <tr <?php if ($producto->total_stock < 100) {
                        echo 'class="table-danger"';
                    } ?>>
                    <td><?php echo $producto->id ?></td>
                    <td><?php echo $producto->nombre ?></td>
                    <td><?php echo $producto->descripcion ?></td>
                    <td><?php echo $producto->precio_compra ?></td>
                    <td><?php echo $producto->precio_venta ?></td>
                    <td><?php echo $producto->total_stock . " " . $producto->unidad_medida ?></td>
                    <td><a class="btn btn-warning" href="<?php echo "./editar.php?id=" . $producto->id ?>"><i class="fa fa-edit"></i></a></td>
                    <td><a class="btn btn-danger" href="<?php echo "./eliminar.php?id=" . $producto->id ?>"><i class="fa fa-trash"></i></a></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<?php include_once "./diseño/pie.php" ?>