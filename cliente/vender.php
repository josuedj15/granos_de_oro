<?php
session_start();
include_once "./diseño/encabezado.php";


if (!isset($_SESSION["carrito1"])) $_SESSION["carrito1"] = [];
$granTotal = 0;



// Obtener la lista de clientes desde la base de datos
include_once "../base/conexion.php";
$sentenciaClientes = $conexion->query("SELECT id, nombre FROM usuarios WHERE rol = 'cliente'");
$clientes = $sentenciaClientes->fetchAll(PDO::FETCH_OBJ);
?>
<div class="col-xs-12">
    <h1>Vender
        <small
            class="form-text text-muted">Estás comprando como: 
                <?php echo $_SESSION['usuario_nombre'] .' id:'. $_SESSION['usuario_id'] ?>
        </small>
        
    </h1>
    <?php
    if (isset($_GET["status"])) {
        if ($_GET["status"] === "1") {
    ?>
            <div class="alert alert-success">
                <strong>¡Correcto!</strong> Venta realizada correctamente
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
                <strong>Error:</strong> El producto que buscas no existe
            </div>
        <?php
        } else if ($_GET["status"] === "5") {
        ?>
            <div class="alert alert-danger">
                <strong>Error: </strong>El producto está agotado o no hay suficiente stock
            </div>
        <?php
        } else if ($_GET["status"] === "6") {
        ?>
            <div class="alert alert-danger">
                <strong>Error: </strong>Cantidad inválida
            </div>
        <?php
        } else {
        ?>
            <div class="alert alert-danger">
                <strong>Error:</strong> Algo salió mal mientras se realizaba la venta
            </div>
    <?php
        }
    }
    ?>
    <br>
    <form method="post" action="agregarAlCarrito.php">
        <label for="id">ID del producto:</label>
        <input autocomplete="off" autofocus class="form-control" name="id" required type="text" id="id" placeholder="Escribe el ID">
        <label for="cantidad">Cantidad:</label>
        <input class="form-control" name="cantidad" type="number" value="1" min="1">
        <button type="submit" class="btn btn-primary mt-2">Agregar al carrito</button>
    </form>
    <br><br>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Precio </th>
                <th>Cantidad</th>
                <th>Total</th>
                <th>Quitar</th>
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
                    <td><a class="btn btn-danger" href="<?php echo "quitarDelCarrito.php?indice=" . $indice ?>"><i class="fa fa-trash"></i></a></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <h3>Total: <?php echo $granTotal; ?></h3>
    <form action="./terminarVenta.php" method="POST">
        <input name="total" type="hidden" value="<?php echo $granTotal; ?>">
        <input name="cliente_id" type="hidden" value="<?php echo $_SESSION['usuario_id']; ?>" >
        <button type="submit" class="btn btn-success">Terminar venta</button>
        <a href="./cancelarVenta.php" class="btn btn-danger">Cancelar venta</a>
    </form>

</div>
<?php include_once "./diseño/pie.php" ?>