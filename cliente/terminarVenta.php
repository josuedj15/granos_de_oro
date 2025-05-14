<?php
session_start();
include_once("../errores.php");

if(!isset($_POST["total"]) || !isset($_POST["cliente_id"])) {
    exit("Error: Falta el total o el ID del cliente.");
}

$total = $_POST["total"];
$idCliente = $_POST["cliente_id"];
include_once "../base/conexion.php";

$ahora = date("Y-m-d H:i:s");

$sentenciaVenta = $conexion->prepare("INSERT INTO ventas(fecha_venta, total, cliente_id) VALUES (?, ?, ?);");
$resultadoVenta = $sentenciaVenta->execute([$ahora, $total, $idCliente]);

if (!$resultadoVenta) {
    echo "Error al insertar la venta:\n";
    print_r($sentenciaVenta->errorInfo());
    exit();
}

$idVenta = $conexion->lastInsertId();

$conexion->beginTransaction();
$sentenciaProductosVendidos = $conexion->prepare("INSERT INTO productos_vendidos(id_producto, id_venta, cantidad) VALUES (?, ?, ?);");
$sentenciaActualizarExistencia = $conexion->prepare("UPDATE productos SET stock = stock - ? WHERE id = ?;");

// Array para almacenar los detalles de la venta que mostraremos en la página de confirmación
$detallesVenta = [];

foreach ($_SESSION["carrito1"] as $producto) {
    $sentenciaProductosVendidos->execute([$producto->id, $idVenta, $producto->cantidad]);
    $sentenciaActualizarExistencia->execute([$producto->cantidad, $producto->id]);

    // Agregar detalles del producto al array
    $detallesVenta[] = [
        "id" => $producto->id,
        "nombre" => $producto->nombre,
        "descripcion" => $producto->descripcion,
        "precio_venta" => $producto->precio_venta,
        "cantidad" => $producto->cantidad,
        "unidad_medida" => $producto->unidad_medida,
        "total" => $producto->total
    ];
}

$conexion->commit();
unset($_SESSION["carrito1"]);
$_SESSION["carrito1"] = [];

// Guardar los detalles de la venta en la sesión para poder mostrarlos en la siguiente página
$_SESSION["detalles_venta"] = [
    "id_venta" => $idVenta,
    "fecha_venta" => $ahora,
    "total_venta" => $total,
    "cliente_id" => $idCliente,
    "productos" => $detallesVenta
];

header("Location: ./confirmacionVenta.php"); // Redirigir a la página de confirmación
exit; // Asegurar que no se ejecute más código después de la redirección
?>