<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include_once "../base/conexion.php"; // Asegúrate de que la ruta sea correcta

// *** Verificación de permisos para el administrador/empleado ***
// Ajusta los roles según tu sistema (ej. 'administrador', 'empleado')
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['usuario_rol']) ||
    ($_SESSION['usuario_rol'] !== 'administrador' && $_SESSION['usuario_rol'] !== 'admin')) {
    header("Location: ../login/login.php"); // Redirigir si no tiene permisos
    exit();
}

if (!isset($_POST["id"]) || !isset($_POST["cantidad"])) {
    header("Location: ./vender.php?status=error_datos");
    exit;
}

$id_producto = $_POST["id"];
$cantidad_agregar = intval($_POST["cantidad"]);

if ($cantidad_agregar <= 0) {
    header("Location: ./vender.php?status=6"); // Cantidad inválida
    exit;
}

// 1. Obtener los detalles base del producto desde la tabla 'productos'
$sentenciaProductoBase = $conexion->prepare("SELECT id, nombre, descripcion, precio_compra, precio_venta, unidad_medida FROM productos WHERE id = ? LIMIT 1;");
$sentenciaProductoBase->execute([$id_producto]);
$producto_base = $sentenciaProductoBase->fetch(PDO::FETCH_OBJ);
$sentenciaProductoBase->closeCursor();

// Si el producto no existe, redirige con un mensaje de error
if (!$producto_base) {
    header("Location: ./vender.php?status=4"); // Producto no encontrado
    exit;
}

// 2. Obtener la cantidad actual del producto que ya está en el carrito
// Para la sección de administración, el carrito sigue siendo $_SESSION["carrito1"]
$current_cart_quantity_for_product = 0;
$existing_cart_product_index = false;

if (!isset($_SESSION["carrito1"])) {
    $_SESSION["carrito1"] = []; // Si el carrito no existe, lo inicializa
}

// Recorre el carrito para ver si el producto ya existe y obtener su cantidad actual
foreach ($_SESSION["carrito1"] as $index => $item_in_cart) {
    if ($item_in_cart->id === $id_producto) {
        $current_cart_quantity_for_product = $item_in_cart->cantidad;
        $existing_cart_product_index = $index;
        break;
    }
}

// 3. Calcular la cantidad TOTAL que se necesitará de este producto después de agregar
$required_total_quantity = $current_cart_quantity_for_product + $cantidad_agregar;

// 4. Obtener todo el stock disponible para este producto en TODOS los almacenes
// Se prioriza el stock más alto y luego por el ID del almacén para consistencia
$sentenciaStockAlmacenes = $conexion->prepare("SELECT sa.almacen_id, sa.stock, a.nombre AS nombre_almacen 
                                              FROM stock_almacen sa 
                                              INNER JOIN almacenes a ON sa.almacen_id = a.id
                                              WHERE sa.producto_id = ? AND sa.stock > 0 
                                              ORDER BY sa.stock DESC, sa.almacen_id ASC;");
$sentenciaStockAlmacenes->execute([$id_producto]);
$almacenes_disponibles = $sentenciaStockAlmacenes->fetchAll(PDO::FETCH_OBJ);
$sentenciaStockAlmacenes->closeCursor();

// 5. Calcular el stock total global disponible sumando el stock de todos los almacenes
$total_stock_global = 0;
foreach ($almacenes_disponibles as $almacen_info) {
    $total_stock_global += $almacen_info->stock;
}

// 6. Validar si hay stock suficiente en total (sumando todos los almacenes) para la cantidad requerida
if ($total_stock_global < $required_total_quantity) {
    header("Location: ./vender.php?status=5"); // Stock insuficiente en total
    exit;
}

// 7. Asignar la cantidad requerida a los almacenes disponibles (lógica de cumplimiento múltiple)
$cantidad_restante_a_asignar = $required_total_quantity; // Cantidad total a cubrir
$fulfillment_sources = []; // Array para almacenar el desglose de dónde se tomará el stock
$almacen_nombre_display = ''; // Variable para mostrar el nombre del almacén (o "Múltiples")

foreach ($almacenes_disponibles as $almacen_info) {
    if ($cantidad_restante_a_asignar <= 0) {
        break; // Ya se asignó toda la cantidad necesaria
    }

    $cantidad_a_tomar_de_este_almacen = min($cantidad_restante_a_asignar, $almacen_info->stock);
    
    if ($cantidad_a_tomar_de_este_almacen > 0) {
        $fulfillment_sources[] = [
            'almacen_id' => $almacen_info->almacen_id,
            'cantidad' => $cantidad_a_tomar_de_este_almacen,
            'almacen_nombre' => $almacen_info->nombre_almacen
        ];
        $cantidad_restante_a_asignar -= $cantidad_a_tomar_de_este_almacen;

        // Guarda el nombre del primer almacén del que se toma stock para mostrar
        if (empty($almacen_nombre_display)) {
            $almacen_nombre_display = $almacen_info->nombre_almacen;
        }
    }
}

// Si se usaron múltiples almacenes para el cumplimiento, se actualiza el nombre a "Múltiples"
if (count($fulfillment_sources) > 1) {
    $almacen_nombre_display = 'Múltiples';
}

// 8. Actualizar o añadir el producto al carrito con la nueva estructura de desglose de stock
$producto_para_carrito = $producto_base;
$producto_para_carrito->cantidad = $required_total_quantity; // Cantidad total del producto en el carrito
$producto_para_carrito->total = $producto_para_carrito->precio_venta * $required_total_quantity;
$producto_para_carrito->fulfillment_sources = $fulfillment_sources; // Asigna el array con el desglose de stock por almacén
$producto_para_carrito->almacen_nombre_origen = $almacen_nombre_display; // Nombre para mostrar en la interfaz (Múltiples o nombre único)

// Si el producto ya estaba en el carrito, actualiza su entrada; si no, lo agrega como nuevo
if ($existing_cart_product_index !== false) {
    $_SESSION["carrito1"][$existing_cart_product_index] = $producto_para_carrito;
} else {
    $_SESSION["carrito1"][] = $producto_para_carrito;
}

header("Location: ./vender.php?status=1");
exit;
?>