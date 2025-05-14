<?php
session_start();
if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] !== 'admin') {
    header("Location: ../login/login.php");
    exit();
}

if (!isset($_POST["id"]) || !isset($_POST["nombre"]) || !isset($_POST["precio_compra"]) || !isset($_POST["precio_venta"]) || !isset($_POST["unidad_medida"])) {
    header("Location: ./listar.php");
    exit();
}

include_once "../base/conexion.php";
$id_producto = $_POST["id"];
$nombre = $_POST["nombre"];
$descripcion = $_POST["descripcion"];
$precio_compra = $_POST["precio_compra"];
$precio_venta = $_POST["precio_venta"];
$unidad_medida = $_POST["unidad_medida"];
$stock_almacenes = $_POST["stock"] ?? [];

try {
    $conexion->beginTransaction();

    // Actualizar la información básica del producto
    $sentencia_producto = $conexion->prepare("UPDATE productos SET nombre = ?, descripcion = ?, precio_compra = ?, precio_venta = ?, unidad_medida = ? WHERE id = ?");
    $resultado_producto = $sentencia_producto->execute([$nombre, $descripcion, $precio_compra, $precio_venta, $unidad_medida, $id_producto]);

    // Actualizar el stock en los almacenes
    // Primero, eliminar los registros de stock existentes para este producto
    $sentencia_eliminar_stock = $conexion->prepare("DELETE FROM stock_almacen WHERE producto_id = ?");
    $resultado_eliminar_stock = $sentencia_eliminar_stock->execute([$id_producto]);

    // Luego, insertar los nuevos registros de stock
    $sentencia_insertar_stock = $conexion->prepare("INSERT INTO stock_almacen (producto_id, almacen_id, stock) VALUES (?, ?, ?)");
    foreach ($stock_almacenes as $almacen_id => $stock) {
        if (isset($almacen_id) && is_numeric($almacen_id) && isset($stock) && is_numeric($stock)) {
            $resultado_insertar_stock = $sentencia_insertar_stock->execute([$id_producto, $almacen_id, $stock]);
        }
    }

    $conexion->commit();
    header("Location: ./listar.php?mensaje=Producto actualizado correctamente");

} catch (PDOException $e) {
    $conexion->rollBack();
    header("Location: ./editar.php?id=" . $id_producto . "&error=Error al actualizar el producto: " . $e->getMessage());
}
exit();
?>