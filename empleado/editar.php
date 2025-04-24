<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if(!isset($_GET["id"])) exit();
$id = $_GET["id"];
include_once "../base/conexion.php";
$sentencia = $conexion->prepare("SELECT * FROM productos WHERE id = ?;");
$sentencia->execute([$id]);
$producto = $sentencia->fetch(PDO::FETCH_OBJ);
if($producto === FALSE){
	echo "¡No existe algún producto con ese ID!";
	exit();
}

?>
<?php include_once "./diseño/encabezado.php" ?>
	<div class="col-xs-12">
		<h1>Editar producto con el ID <?php echo $producto->id; ?></h1>
		<form method="post" action="./guardarDatosEditados.php">
			<input type="hidden" name="id" value="<?php echo $producto->id; ?>">
	
			<label for="codigo">Nombre del producto </label>
			<input value="<?php echo $producto->nombre ?>" class="form-control" name="nombre" required type="text" id="nombre" placeholder="Escribe el código">

			<label for="descripcion">Descripción:</label>
			<textarea required id="descripcion" name="descripcion" cols="30" rows="5" class="form-control"><?php echo $producto->descripcion ?></textarea>

			<label for="precioCompra">Precio de compra:</label>
			<input value="<?php echo $producto->precio_compra ?>" class="form-control" name="precio_compra" required type="number" id="precio_compra" placeholder="Precio de compra" step="0.01">

			<label for="precioVenta">Precio de venta:</label>
			<input value="<?php echo $producto->precio_venta ?>" class="form-control" name="precio_venta" required type="number" id="precio_venta" placeholder="Precio de venta" step="0.01">

			<label for="existencia">Existencia/stock:</label>
			<input value="<?php echo $producto->stock ?>" class="form-control" name="stock" required type="number" id="stock" placeholder="Cantidad o existencia" step="0.01">

			<label for="unidad_medida">Unidad de medida:</label>
			<input value="<?php echo $producto->unidad_medida ?>" class="form-control" name="unidad_medida" required type="text" id="unidad_medida" placeholder="tipo de medida ej: kg, toneladas, bolsas">

			<br><br><input class="btn btn-info" type="submit" value="Guardar">
			<a class="btn btn-warning" href="../empleado/listar.php">Cancelar</a>
		</form>
	</div>
<?php include_once "./diseño/pie.php" ?>
