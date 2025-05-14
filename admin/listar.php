<?php include_once "./diseño/encabezado.php" ?>

<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<?php
include_once "../base/conexion.php";
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
        <div>
            <a class="btn btn-success" href="./nuevo.php">Nuevo <i class="fa fa-plus"></i></a>
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
                <?php foreach($productos as $producto){ ?>
                <tr>
                    <td><?php echo $producto->id ?></td>
                    <td><?php echo $producto->nombre ?></td>
                    <td><?php echo $producto->descripcion ?></td>
                    <td><?php echo $producto->precio_compra ?></td>
                    <td><?php echo $producto->precio_venta ?></td>
                    <td><?php echo $producto->total_stock  .' '. $producto ->unidad_medida ?></td>
                    <td><a class="btn btn-warning" href="<?php echo "./editar.php?id=" . $producto->id?>"><i class="fa fa-edit"></i></a></td>
                    <td><a class="btn btn-danger" href="<?php echo "./eliminar.php?id=" . $producto->id?>"><i class="fa fa-trash"></i></a></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
<?php include_once "./diseño/pie.php" ?>