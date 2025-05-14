<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

#Salir si alguno de los datos básicos del producto no está presente
if (
    !isset($_POST["nombre"]) || empty($_POST["nombre"]) ||
    !isset($_POST["descripcion"]) || empty($_POST["descripcion"]) ||
    !isset($_POST["precio_compra"]) || empty($_POST["precio_compra"]) ||
    !isset($_POST["precio_venta"]) || empty($_POST["precio_venta"]) ||
    !isset($_POST["unidad_medida"]) || empty($_POST["unidad_medida"]) ||
    !isset($_POST["stock"]) || !is_array($_POST["stock"])
) {
    exit("Error: Faltan datos del producto.");
}

include_once "../base/conexion.php";
$nombre = $_POST["nombre"];
$descripcion = $_POST["descripcion"];
$precio_compra = $_POST["precio_compra"];
$precio_venta = $_POST["precio_venta"];
$unidad_medida = $_POST["unidad_medida"];
$stock_almacenes = $_POST["stock"] ?? []; // Obtener el array de stock por almacén

try {
    $conexion->beginTransaction();

    // Insertar la información básica del producto (sin el stock global)
    $sentencia_producto = $conexion->prepare("INSERT INTO productos (nombre, descripcion, precio_compra, precio_venta, unidad_medida) VALUES (?, ?, ?, ?, ?)");
    $resultado_producto = $sentencia_producto->execute([$nombre, $descripcion, $precio_compra, $precio_venta, $unidad_medida]);

    if ($resultado_producto === TRUE) {
        $id_producto = $conexion->lastInsertId(); // Obtener el ID del producto recién insertado

        // Insertar el stock inicial en la tabla stock_almacen
        $sentencia_stock = $conexion->prepare("INSERT INTO stock_almacen (producto_id, almacen_id, stock) VALUES (?, ?, ?)");
        foreach ($stock_almacenes as $almacen_id => $stock) {
            if (isset($almacen_id) && is_numeric($almacen_id) && isset($stock) && is_numeric($stock) && $stock > 0) { // Verificar que los datos del stock sean válidos
                $sentencia_stock->execute([$id_producto, $almacen_id, $stock]);
            }
        }

        $conexion->commit();
        header("Location: ./listar.php?mensaje=Producto agregado correctamente");
        exit;
    } else {
        $conexion->rollBack();
        echo "Algo salió mal al agregar el producto.";
    }
} catch (PDOException $e) {
    $conexion->rollBack();
    echo "Error de base de datos: " . $e->getMessage();
}

?>
<?php include_once "./diseño/pie.php" ?>