<?php
session_start();
include_once "./diseño/encabezado.php";
include_once "../errores.php";
include_once "../base/conexion.php";

// Verificar si hay un usuario logueado y si es un cliente
if (isset($_SESSION['usuario_id']) && isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] === 'cliente') {
	$cliente_id = $_SESSION['usuario_id'];
	// Modificar la consulta SQL para filtrar por el ID del cliente
	$sentencia = $conexion->prepare("SELECT ventas.total, ventas.fecha_venta, ventas.id, ventas.cliente_id, GROUP_CONCAT(productos.nombre, '..', productos.descripcion, '..', productos.unidad_medida, '..', productos_vendidos.cantidad SEPARATOR '__') AS productos FROM ventas INNER JOIN productos_vendidos ON productos_vendidos.id_venta = ventas.id INNER JOIN productos ON productos.id = productos_vendidos.id_producto WHERE ventas.cliente_id = :cliente_id GROUP BY ventas.id ORDER BY ventas.id;");
	$sentencia->bindParam(':cliente_id', $cliente_id, PDO::PARAM_INT);
	$sentencia->execute();
	$ventas = $sentencia->fetchAll(PDO::FETCH_OBJ);
} else {
	// Si no hay un cliente logueado, o no es un cliente, podrías mostrar un mensaje
	// o redirigir a otra página (por ejemplo, la de inicio de sesión)
	echo "<div class='alert alert-warning'>No tienes permiso para ver esta página o no has iniciado sesión como cliente.</div>";
	// O podrías mostrar todas las ventas (si es lo que deseas para otros roles):
	// $sentencia = $conexion->query("SELECT ...");
	// $ventas = $sentencia->fetchAll(PDO::FETCH_OBJ);
	exit(); // Detener la ejecución del script
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="UTF-8">
	<title>Compras</title>
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
		<h1>Compras
			<small class="form-text text-muted">Estás viendo las compras de: <?php echo $_SESSION['usuario_nombre']; ?></small>
		</h1>
		<div class="no-imprimir">
			<a class="btn btn-success" href="./vender.php">Nueva <i class="fa fa-plus"></i></a>
			<button class="btn btn-info" onclick="window.print()">Imprimir Compras <i class="fa fa-print"></i></button>
		</div>
		<br>
		<div class="imprimir-area">
			<table class="table table-bordered">
				<thead>
					<tr>
						<th>Número</th>
						<th>ID cliente</th>
						<th>Nombre</th>
						<th>Fecha</th>
						<th>Productos vendidos</th>
						<th>Total</th>
						<th class="no-imprimir">Acciones</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($ventas as $venta) { ?>
						<tr>
							<td><?php echo $venta->id ?></td>
							<td><?php echo $venta->cliente_id ?></td>
							<td>
								<?php
								$sentenciaCliente = $conexion->prepare("SELECT nombre FROM usuarios WHERE id = ?;");
								$sentenciaCliente->execute([$venta->cliente_id]);
								$cliente = $sentenciaCliente->fetch(PDO::FETCH_OBJ);
								echo $cliente->nombre;
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
							<td class="no-imprimir">
								<a href="./confirmarpago.php?id=<?php echo $venta->id; ?>" class="btn btn-primary">Ver Detalles de Pago</a>
							</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
	</div>
</body>

</html>
<?php include_once "./diseño/pie.php" ?>