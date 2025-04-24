<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

#Salir si alguno de los datos no está presente
if(
	!isset($_POST["nombre"]) || 
	!isset($_POST["descripcion"]) || 
	!isset($_POST["precio_compra"]) || 
	!isset($_POST["precio_venta"]) || 
	!isset($_POST["stock"]) ||
	!isset($_POST["unidad_medida"])
) exit();

#Si todo va bien, se ejecuta esta parte del código...

include_once "../base/conexion.php";
$nombre = $_POST["nombre"];
$descripcion = $_POST["descripcion"];
$precio_compra = $_POST["precio_compra"];
$precio_venta = $_POST["precio_venta"];
$stock = $_POST["stock"];
$unidad_medida = $_POST["unidad_medida"];

$sentencia = $conexion->prepare("INSERT INTO productos (nombre, descripcion, precio_compra, precio_venta, stock, unidad_medida) VALUES (?, ?, ?, ?, ?, ?);");
$resultado = $sentencia->execute([$nombre, $descripcion, $precio_compra, $precio_venta, $stock, $unidad_medida]);


if($resultado === TRUE){
	header("Location: ./listar.php");
	exit;
}
else echo "Algo salió mal. Por favor verifica que la tabla exista";


?>
<?php include_once "./diseño/pie.php" ?>