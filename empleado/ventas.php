<?php include_once "./diseño/encabezado.php" ?>
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<?php
include_once "../base/conexion.php";
$sentencia = $conexion->query("SELECT ventas.total, ventas.fecha_venta, ventas.id, ventas.cliente_id, GROUP_CONCAT(	productos.nombre, '..',  productos.descripcion,'..', productos.unidad_medida, '..', productos_vendidos.cantidad SEPARATOR '__') AS productos FROM ventas INNER JOIN productos_vendidos ON productos_vendidos.id_venta = ventas.id INNER JOIN productos ON productos.id = productos_vendidos.id_producto GROUP BY ventas.id ORDER BY ventas.id;");
$ventas = $sentencia->fetchAll(PDO::FETCH_OBJ);
?>

<div class="col-xs-12">
	<h1>Ventas</h1>
	<div>
		<a class="btn btn-success" href="./vender.php">Nueva <i class="fa fa-plus"></i></a>
	</div>
	<br>
	<table class="table table-bordered">
		<thead>
			<tr>
				<th>Número</th>
				<th>ID cliente</th>
				<th>Fecha</th>
				<th>Productos vendidos</th>
				<th>Total</th>
				<th>Eliminar</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($ventas as $venta) { ?>
				<tr>
					<td><?php echo $venta->id ?></td>
					<td>
						<?php
						$sentenciaCliente = $conexion->prepare("SELECT * FROM clientes WHERE id = ?;");
						$sentenciaCliente->execute([$venta->cliente_id]);
						$cliente = $sentenciaCliente->fetch(PDO::FETCH_OBJ);
						echo $cliente->id;
						?>
					</td>
					<td><?php echo $venta->fecha_venta ?></td>
					<td>
						<table class="table table-bordered">
							<thead>
								<tr>
									<th>Nombre</th>
									<th>Descripción</th>
									<th>Cantidad</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach (explode("__", $venta->productos) as $productosConcatenados) {
									$producto = explode("..", $productosConcatenados)
								?>
									<tr>
										<td><?php echo $producto[0] ?></td>
										<td><?php echo $producto[1] ?></td>
										<td><?php echo $producto[3] .' '. $producto[2] ?></td>
									</tr>
								<?php } ?>
							</tbody>
						</table>
					</td>
					<td><?php echo $venta->total ?></td>
					<td><a class="btn btn-danger" href="<?php echo "eliminarVenta.php?id=" . $venta->id ?>"><i class="fa fa-trash"></i></a></td>
				</tr>
			<?php } ?>
		</tbody>
	</table>
</div>
<?php include_once "./diseño/pie.php" ?>