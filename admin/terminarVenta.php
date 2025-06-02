<?php
session_start();
include_once("../errores.php"); // Asegúrate de que este archivo existe y maneja errores
include_once "../base/conexion.php"; // Asegúrate de que la ruta sea correcta

// *** Verificación de permisos para el administrador/empleado ***
// Ajusta los roles según tu sistema (ej. 'administrador', 'empleado')
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['usuario_rol']) ||
    ($_SESSION['usuario_rol'] !== 'administrador' && $_SESSION['usuario_rol'] !== 'admin')) {
    header("Location: ../login/login.php"); // Redirigir si no tiene permisos
    exit();
}

if(!isset($_POST["total"]) || !isset($_POST["cliente_id"])) {
    exit("Error: Faltan datos necesarios para terminar la venta.");
}

$total = $_POST["total"];
// El ID del cliente ahora viene del formulario de venta, que el administrador seleccionó
$idCliente = $_POST["cliente_id"]; 

$ahora = date("Y-m-d H:i:s");

// Iniciar una transacción para asegurar la integridad de los datos
$conexion->beginTransaction();

try {
    // 1. Insertar la nueva venta en la tabla 'ventas'
    $sentenciaVenta = $conexion->prepare("INSERT INTO ventas(fecha_venta, total, cliente_id) VALUES (?, ?, ?);");
    $resultadoVenta = $sentenciaVenta->execute([$ahora, $total, $idCliente]);

    if (!$resultadoVenta) {
        throw new Exception("Error al insertar la venta.");
    }
    $idVenta = $conexion->lastInsertId();

    // 2. Preparar sentencias para productos vendidos y actualización de stock
    $sentenciaProductosVendidos = $conexion->prepare("INSERT INTO productos_vendidos(id_producto, id_venta, cantidad) VALUES (?, ?, ?);");
    // Esta sentencia actualizará el stock en la tabla 'stock_almacen' para un producto y almacén específico
    $sentenciaActualizarExistencia = $conexion->prepare("UPDATE stock_almacen SET stock = stock - ? WHERE producto_id = ? AND almacen_id = ?;");

    $detallesVenta = []; // Para almacenar los detalles que se mostrarán en la página de confirmación

    // 3. Recorrer cada producto en el carrito
    if (!isset($_SESSION["carrito1"]) || empty($_SESSION["carrito1"])) {
        throw new Exception("El carrito está vacío. No se puede completar la venta.");
    }

    foreach ($_SESSION["carrito1"] as $producto) {
        // Asegúrate de que el producto tiene la información de fulfillment_sources
        if (!isset($producto->fulfillment_sources) || !is_array($producto->fulfillment_sources)) {
            throw new Exception("Error: Información de stock de almacén faltante para el producto " . $producto->nombre);
        }

        // Insertar el producto vendido con la CANTIDAD TOTAL comprada
        $sentenciaProductosVendidos->execute([$producto->id, $idVenta, $producto->cantidad]);
        
        // Iterar sobre las 'fuentes de cumplimiento' para descontar stock de CADA almacén involucrado
        foreach ($producto->fulfillment_sources as $source) {
            $cantidad_a_descontar = $source['cantidad'];
            $almacen_id_origen = $source['almacen_id'];
            $almacen_nombre = $source['almacen_nombre'];

            // *** DEPURACIÓN (DESCOMENTAR PARA VER QUÉ SE ESTÁ DESCONTANDO) ***
            // echo "Intentando descontar: " . $cantidad_a_descontar . " unidades del producto " . $producto->nombre . " (ID:" . $producto->id . ") del almacén " . $almacen_nombre . " (ID: " . $almacen_id_origen . ")<br>";
            // *** FIN DEPURACIÓN ***

            $resultadoActualizacion = $sentenciaActualizarExistencia->execute([$cantidad_a_descontar, $producto->id, $almacen_id_origen]);

            if (!$resultadoActualizacion) {
                // Si la actualización de stock falla para cualquier almacén, se lanza una excepción
                throw new Exception("Error al actualizar el stock para el producto " . $producto->nombre . " en el almacén " . $almacen_nombre . ".");
            }
        }

        // Preparar los detalles del producto para la confirmación de la venta
        $detallesVenta[] = [
            "id" => $producto->id,
            "nombre" => $producto->nombre,
            "descripcion" => $producto->descripcion,
            "precio_venta" => $producto->precio_venta,
            "cantidad" => $producto->cantidad, // Cantidad total del producto
            "unidad_medida" => $producto->unidad_medida,
            "total" => $producto->total,
            "almacen_origen_display" => $producto->almacen_nombre_origen, // 'Múltiples' o nombre del único
            "fulfillment_breakdown" => $producto->fulfillment_sources // Desglose completo para confirmación
        ];
    }

    // Si todo fue exitoso, confirmar la transacción
    $conexion->commit();

    // Limpiar el carrito después de una venta exitosa
    unset($_SESSION["carrito1"]);
    $_SESSION["carrito1"] = [];
    
    // Si la venta se hizo para un cliente específico (admin lo seleccionó), también limpiamos la sesión de cliente seleccionado
    unset($_SESSION['cliente_id_selected_for_sale']);

    // Guardar los detalles de la venta en la sesión para mostrar al administrador en confirmacionVenta.php
    $_SESSION["detalles_venta"] = [
        "id_venta" => $idVenta,
        "fecha_venta" => $ahora,
        "total_venta" => $total,
        "cliente_id" => $idCliente,
        "productos" => $detallesVenta
    ];

    // Redirigir a la página de confirmación
    header("Location: ./vender.php?status=1");

} catch (Exception $e) {
    // Si algo falla, revertir la transacción y mostrar un mensaje de error
    $conexion->rollBack();
    echo "Error en la venta: " . $e->getMessage();
    // También puedes redirigir con un status de error más amigable
    // header("Location: ./vender.php?status=error_venta&mensaje=" . urlencode($e->getMessage()));
    exit();
}
?>