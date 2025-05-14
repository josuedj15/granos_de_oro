<?php
session_start();
if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

include_once "./diseño/encabezado.php";
?>

<div class="col-md-6 offset-md-3">
    <h2>Agregar Nuevo Almacén</h2>
    <form action="./guardar_almacen.php" method="POST">
        <div class="form-group">
            <label for="nombre">Nombre:</label>
            <input type="text" class="form-control" id="nombre" name="nombre" required>
        </div>
        <div class="form-group">
            <label for="ubicacion">Ubicación:</label>
            <input type="text" class="form-control" id="ubicacion" name="ubicacion">
        </div>
        <button type="submit" class="btn btn-success">Guardar Almacén</button>
        <a href="./listar_almacenes.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<?php include_once "./diseño/pie.php"; ?>