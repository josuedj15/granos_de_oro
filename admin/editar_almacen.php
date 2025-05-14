<?php
session_start();
if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

include_once "./diseño/encabezado.php";
include_once "../base/conexion.php";

if (!isset($_GET["id"])) {
    header("Location: ./listar_almacenes.php");
    exit();
}
$id = $_GET["id"];
$sentencia = $conexion->prepare("SELECT id, nombre, ubicacion FROM almacenes WHERE id = ?");
$sentencia->execute([$id]);
$almacen = $sentencia->fetch(PDO::FETCH_OBJ);

if (!$almacen) {
    header("Location: ./listar_almacenes.php?error=Almacén no encontrado");
    exit();
}
?>

<div class="col-md-6 offset-md-3">
    <h2>Editar Almacén</h2>
    <form action="./actualizar_almacen.php" method="POST">
        <input type="hidden" name="id" value="<?php echo $almacen->id ?>">
        <div class="form-group">
            <label for="nombre">Nombre:</label>
            <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($almacen->nombre) ?>" required>
        </div>
        <div class="form-group">
            <label for="ubicacion">Ubicación:</label>
            <input type="text" class="form-control" id="ubicacion" name="ubicacion" value="<?php echo htmlspecialchars($almacen->ubicacion) ?>">
        </div>
        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        <a href="./listar_almacenes.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<?php include_once "./diseño/pie.php"; ?>