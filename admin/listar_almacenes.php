<?php
session_start();
if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

include_once "./diseño/encabezado.php";
include_once "../base/conexion.php";

$sentencia = $conexion->query("SELECT id, nombre, ubicacion FROM almacenes");
$almacenes = $sentencia->fetchAll(PDO::FETCH_OBJ);
?>

<div class="col-xs-12">
    <h1>Administrar Almacenes</h1>
    <div>
        <a class="btn btn-success" href="./crear_almacen.php">Agregar Nuevo Almacén <i class="fa fa-plus"></i></a>
    </div>
    <br>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Ubicación</th>
                <th>Editar</th>
                <th>Eliminar</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($almacenes as $almacen): ?>
                <tr>
                    <td><?php echo $almacen->id ?></td>
                    <td><?php echo $almacen->nombre ?></td>
                    <td><?php echo $almacen->ubicacion ?></td>
                    <td><a class="btn btn-warning" href="./editar_almacen.php?id=<?php echo $almacen->id ?>"><i class="fa fa-edit"></i> Editar</a></td>
                    <td><a class="btn btn-danger" href="./eliminar_almacen.php?id=<?php echo $almacen->id ?>" onclick="return confirm('¿Estás seguro de eliminar este almacén?')"><i class="fa fa-trash"></i> Eliminar</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include_once "./diseño/pie.php"; ?>