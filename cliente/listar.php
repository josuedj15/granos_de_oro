<?php include_once "./diseño/encabezado.php" ?>

<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<?php
include_once "../base/conexion.php";


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

<div class="container">
	<div class="row">
		<div class="col-md-12">
			<h1>Granos de Oro</h1>
			<p>Bienvenido a la tienda de Granos de Oro</p>
		</div>
	</div>
</div>
<div class="col-xs-12">
	<h1>Productos</h1>
	
	<br>
	<table class="table table-bordered">
		<thead>
			<tr>
				<th>ID</th>
				<th>nombre</th>
				<th>Descripción</th>
				<th>Precio </th>
				<th>Existencia</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($productos as $producto) { ?>
				<tr>
					<td><?php echo $producto->id ?></td>
					<td><?php echo $producto->nombre ?></td>
					<td><?php echo $producto->descripcion ?></td>
					<td><?php echo $producto->precio_venta ?></td>
					<td><?php echo $producto->total_stock . ' ' . $producto->unidad_medida ?></td>				
				</tr>
			<?php } ?>
		</tbody>
	</table>
</div>
<?php include_once "./diseño/pie.php" ?>