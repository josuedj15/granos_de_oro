<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_POST["id"]) || !isset($_POST["cantidad"])) {
    return;
}

$id_producto = $_POST["id"];
$cantidad_agregar = intval($_POST["cantidad"]); // Convertimos la cantidad a un entero y la validamos

if ($cantidad_agregar <= 0) {
    header("Location: ./vender.php?status=6"); // Puedes definir un nuevo status para cantidad inválida
    exit;
}

include_once "../base/conexion.php";
$sentencia = $conexion->prepare("SELECT * FROM productos WHERE id = ? LIMIT 1;");
$sentencia->execute([$id_producto]);
$producto_db = $sentencia->fetch(PDO::FETCH_OBJ);

# Si no existe, salimos y lo indicamos
if (!$producto_db) {
    header("Location: ./vender.php?status=4");
    exit;
}
# Si no hay suficiente stock...
if ($producto_db->stock < $cantidad_agregar) {
    header("Location: ./vender.php?status=5"); // El status 5 ahora indicará stock insuficiente
    exit;
}

session_start();
if (!isset($_SESSION["carrito1"])) {
    $_SESSION["carrito1"] = [];
}
# Buscar producto dentro del carrito
$indice = false;
for ($i = 0; $i < count($_SESSION["carrito1"]); $i++) {
    if ($_SESSION["carrito1"][$i]->id === $id_producto) {
        $indice = $i;
        break;
    }
}
# Si no existe, lo agregamos como nuevo
if ($indice === false) {
    $producto_db->cantidad = $cantidad_agregar;
    $producto_db->total = $producto_db->precio_venta * $cantidad_agregar;
    $_SESSION["carrito1"][] = $producto_db;
} else {
    # Si ya existe, se agrega la cantidad
    $cantidadExistenteEnCarrito = $_SESSION["carrito1"][$indice]->cantidad;
    $nuevaCantidad = $cantidadExistenteEnCarrito + $cantidad_agregar;
    # Pero espera, tal vez ya no haya suficiente stock
    if ($nuevaCantidad > $producto_db->stock) {
        header("Location: ./vender.php?status=5"); // Stock insuficiente
        exit;
    }
    $_SESSION["carrito1"][$indice]->cantidad = $nuevaCantidad;
    $_SESSION["carrito1"][$indice]->total = $_SESSION["carrito1"][$indice]->cantidad * $_SESSION["carrito1"][$indice]->precio_venta;
}
header("Location: ./vender.php");
?>