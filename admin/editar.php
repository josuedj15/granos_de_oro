<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if(!isset($_GET["id"])) exit();
$id_producto = $_GET["id"];
include_once "../base/conexion.php";
$sentencia_producto = $conexion->prepare("SELECT id, nombre, descripcion, precio_compra, precio_venta, unidad_medida FROM productos WHERE id = ?;");
$sentencia_producto->execute([$id_producto]);
$producto = $sentencia_producto->fetch(PDO::FETCH_OBJ);
if($producto === FALSE){
    echo "¡No existe algún producto con ese ID!";
    exit();
}

// Obtener todos los almacenes
$sentencia_almacenes = $conexion->query("SELECT id, nombre, ubicacion FROM almacenes");
$almacenes = $sentencia_almacenes->fetchAll(PDO::FETCH_OBJ);

// Obtener el stock actual del producto en cada almacén
$sentencia_stock = $conexion->prepare("SELECT almacen_id, stock FROM stock_almacen WHERE producto_id = ?");
$sentencia_stock->execute([$id_producto]);
$stock_producto_almacen = $sentencia_stock->fetchAll(PDO::FETCH_KEY_PAIR); // Almacena [almacen_id => stock]

?>
<?php include_once "./diseño/encabezado.php" ?>
    <div class="col-xs-12">
        <h1>Editar producto con el ID <?php echo $producto->id; ?></h1>
        <form method="post" action="./guardarDatosEditados.php">
            <input type="hidden" name="id" value="<?php echo $producto->id; ?>">

            <label for="nombre">Nombre del producto </label>
            <input value="<?php echo $producto->nombre ?>" class="form-control" name="nombre" required type="text" id="nombre" placeholder="Escribe el nombre">

            <label for="descripcion">Descripción:</label>
            <textarea required id="descripcion" name="descripcion" cols="30" rows="5" class="form-control"><?php echo $producto->descripcion ?></textarea>

            <label for="precioCompra">Precio de compra:</label>
            <input value="<?php echo $producto->precio_compra ?>" class="form-control" name="precio_compra" required type="number" id="precio_compra" placeholder="Precio de compra" step="0.01">

            <label for="precioVenta">Precio de venta:</label>
            <input value="<?php echo $producto->precio_venta ?>" class="form-control" name="precio_venta" required type="number" id="precio_venta" placeholder="Precio de venta" step="0.01">

            <label for="unidad_medida">Unidad de medida:</label>
            <input value="<?php echo $producto->unidad_medida ?>" class="form-control" name="unidad_medida" required type="text" id="unidad_medida" placeholder="tipo de medida ej: kg, toneladas, bolsas">

            <h3>Stock en Almacenes</h3>
            <?php if (!empty($almacenes)): ?>
                <?php foreach ($almacenes as $almacen): ?>
                    <div class="form-group">
                        <label for="stock_<?php echo $almacen->id; ?>"><?php echo htmlspecialchars($almacen->nombre); ?> (<?php echo htmlspecialchars($almacen->ubicacion ?? 'Sin ubicación'); ?>):</label>
                        <input type="number" class="form-control" id="stock_<?php echo $almacen->id; ?>" name="stock[<?php echo $almacen->id; ?>]" value="<?php echo $stock_producto_almacen[$almacen->id] ?? 0; ?>" min="0">
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No hay almacenes creados aún.</p>
            <?php endif; ?>

            <br><br><input class="btn btn-info" type="submit" value="Guardar">
            <a class="btn btn-warning" href="./listar.php">Cancelar</a>
        </form>
    </div>
<?php include_once "./diseño/pie.php" ?>