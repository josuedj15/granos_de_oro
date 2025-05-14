<?php include_once "./diseño/encabezado.php" ?>
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<?php
include_once "../base/conexion.php";
$sentencia = $conexion->query("SELECT ventas.total, ventas.fecha_venta, ventas.id, ventas.cliente_id, GROUP_CONCAT( productos.nombre, '..',   productos.descripcion,'..', productos.unidad_medida, '..', productos_vendidos.cantidad SEPARATOR '__') AS productos FROM ventas INNER JOIN productos_vendidos ON productos_vendidos.id_venta = ventas.id INNER JOIN productos ON productos.id = productos_vendidos.id_producto GROUP BY ventas.id ORDER BY ventas.id;");
$ventas = $sentencia->fetchAll(PDO::FETCH_OBJ);
?>

<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="UTF-8">
	<title>Ventas</title>
	<link rel="stylesheet" href="./diseño/css/estilo.css">
	<style>
		@media print {
			body * {
				visibility: hidden;
			}

			.imprimir-area,
			.imprimir-area * {
				visibility: visible !important;
			}

			.imprimir-area {
				position: absolute;
				left: 0;
				top: 0;
				width: 100%;
			}

			.table-bordered {
				border-collapse: collapse !important;
				width: 100% !important;
			}

			.table-bordered,
			.table-bordered th,
			.table-bordered td {
				border: 1px solid black !important;
				padding: 3px !important;
				font-size: 10px !important;
			}

			.table-bordered table {
				width: 100% !important;
				border-collapse: collapse !important;
				margin-bottom: 5px !important;
			}

			.table-bordered table,
			.table-bordered table th,
			.table-bordered table td {
				border: 1px solid black !important;
				padding: 2px !important;
				font-size: 9px !important;
			}

			.no-imprimir {
				display: none !important;
			}
		}
	</style>
</head>

<body>

	<div class="col-xs-12">
		<h1>Ventas</h1>
		<div class="no-imprimir">
			<a class="btn btn-success" href="./vender.php">Nueva <i class="fa fa-plus"></i></a>
			<button class="btn btn-info" onclick="imprimirVentas()">Imprimir Ventas <i class="fa fa-print"></i></button>
		</div>
		<br>
		<div class="imprimir-area">
			<table class="table table-bordered">
				<thead>
					<tr>
						<th>Número</th>
						<th>ID cliente</th>
						<th>Fecha</th>
						<th>Productos vendidos</th>
						<th>Total</th>
						<th class="no-imprimir">Eliminar</th>
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
												<td><?php echo $producto[3] . ' ' . $producto[2] ?></td>
											</tr>
										<?php } ?>
									</tbody>
								</table>
							</td>
							<td><?php echo $venta->total ?></td>
							<td class="no-imprimir"><a class="btn btn-danger" href="<?php echo "eliminarVenta.php?id=" . $venta->id ?>"><i class="fa fa-trash"></i></a></td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
	</div>

	<script>
		function imprimirVentas() {
			window.print();
		}
	</script>

</body>

</html>
<?php include_once "./diseño/pie.php" ?>