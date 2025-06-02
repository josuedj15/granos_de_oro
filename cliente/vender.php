<?php
session_start();
include_once "./diseño/encabezado.php"; // Ajusta la ruta si es necesario
include_once "../base/conexion.php"; // Ajusta la ruta si es necesario

// *** Verificación de permisos para el cliente ***
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] !== 'cliente') {
    header("Location: ../login/login.php"); // Redirigir si no es cliente o no ha iniciado sesión
    exit();
}

if (!isset($_SESSION["carrito1"])) $_SESSION["carrito1"] = [];
$granTotal = 0;

// Obtener la lista de almacenes para mostrar (informativo)
$sentenciaAlmacenes = $conexion->query("SELECT id, nombre FROM almacenes ORDER BY nombre;");
$almacenes = $sentenciaAlmacenes->fetchAll(PDO::FETCH_OBJ);
$sentenciaAlmacenes->closeCursor();

?>
<div class="col-xs-12">
    <h1>Vender
        <small class="form-text text-muted">Estás comprando como:
            <?php echo $_SESSION['usuario_nombre'] .' id:'. $_SESSION['usuario_id'] ?>
        </small>
    </h1>
    <?php
    if (isset($_GET["status"])) {
        if ($_GET["status"] === "1") {
    ?>
            <div class="alert alert-success">
                <strong>¡Correcto!</strong> Producto agregado al carrito o venta realizada.
            </div>
        <?php
        } else if ($_GET["status"] === "2") {
        ?>
            <div class="alert alert-info">
                <strong>Venta cancelada</strong>
            </div>
        <?php
        } else if ($_GET["status"] === "3") {
        ?>
            <div class="alert alert-info">
                <strong>Ok</strong> Producto quitado de la lista
            </div>
        <?php
        } else if ($_GET["status"] === "4") {
        ?>
            <div class="alert alert-warning">
                <strong>Error:</strong> El producto que buscas no existe o no hay stock disponible.
            </div>
        <?php
        } else if ($_GET["status"] === "5") {
        ?>
            <div class="alert alert-danger">
                <strong>Error:</strong> Stock insuficiente en los almacenes disponibles para la cantidad solicitada.
            </div>
        <?php
        } else if ($_GET["status"] === "6") {
        ?>
            <div class="alert alert-danger">
                <strong>Error:</strong> La cantidad a agregar debe ser mayor a cero.
            </div>
    <?php
        }
    }
    ?>

    <div class="form-group mb-3">
        <label for="almacen_id_preferencial">Almacenes disponibles (el sistema buscará stock automáticamente):</label>
        <select name="almacen_id_preferencial" id="almacen_id_preferencial" class="form-control" disabled>
            <option value="">Sistema buscará stock disponible</option>
            <?php foreach ($almacenes as $almacen) { ?>
                <option value="<?php echo $almacen->id; ?>">
                    <?php echo htmlspecialchars($almacen->nombre); ?>
                </option>
            <?php } ?>
        </select>
        <small class="form-text text-muted">El sistema tomará el stock del almacén con más disponibilidad y, si es necesario, de otros.</small>
    </div>

    <form method="POST" action="./agregarAlCarrito.php" id="form-agregar-producto">
        <div class="input-group">
            <input autocomplete="off" name="id" required type="number" id="id" placeholder="Código de barras del producto" class="form-control">
        </div>
        <div class="input-group mt-2">
            <input autocomplete="off" name="cantidad" required type="number" value="1" min="1" placeholder="Cantidad" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary mt-2">Agregar al carrito</button>
    </form>

    <br><br>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Precio</th>
                <th>Cantidad</th>
                <th>Total</th>
                <th>Almacén Origen</th> <th>Quitar</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($_SESSION["carrito1"] as $indice => $producto) {
                $granTotal += $producto->total;
            ?>
                <tr>
                    <td><?php echo $producto->id ?></td>
                    <td><?php echo $producto->nombre ?></td>
                    <td><?php echo $producto->descripcion ?></td>
                    <td><?php echo $producto->precio_venta ?></td>
                    <td><?php echo $producto->cantidad . ' ' . $producto->unidad_medida ?></td>
                    <td><?php echo $producto->total ?></td>
                    <td>
                        <?php
                        // Mostrar el nombre para la visualización simplificada (Múltiples o nombre único)
                        echo htmlspecialchars($producto->almacen_nombre_origen ?? 'N/A');
                        ?>
                    </td>
                    <td><a class="btn btn-danger" href="<?php echo "quitarDelCarrito.php?indice=" . $indice ?>"><i class="fa fa-trash"></i></a></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <h3>Total: <?php echo $granTotal; ?></h3>
    <form action="./terminarVenta.php" method="POST" id="form-terminar-venta">
        <input name="total" type="hidden" value="<?php echo $granTotal; ?>">
        <input name="cliente_id" type="hidden" value="<?php echo $_SESSION['usuario_id']; ?>" >
        <button type="submit" class="btn btn-success">Terminar venta</button>
        <a href="./cancelarVenta.php" class="btn btn-danger">Cancelar venta</a>
    </form>

</div>

<?php include_once "./diseño/pie.php" ?>