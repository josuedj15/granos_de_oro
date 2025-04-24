<?php
session_start();
if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] !== 'admin') {
    header("Location: ../login/login.php"); // Redirigir si no es administrador
    exit();
}

include_once "./diseño/encabezado.php";
include_once "../base/conexion.php";

$sentencia = $conexion->query("SELECT id, nombre, email, rol FROM usuarios");
$usuarios = $sentencia->fetchAll(PDO::FETCH_OBJ);
?>

<div class="col-xs-12">
    <h1>Administrar Usuarios</h1>
    <div>
        <a class="btn btn-success" href="./insertar_usuario.php">Agregar Nuevo Usuario <i class="fa fa-plus"></i></a>
    </div>
    <br>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Rol</th>
                <th>Editar</th>
                <th>Eliminar</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usuarios as $usuario): ?>
                <tr>
                    <td><?php echo $usuario->id ?></td>
                    <td><?php echo $usuario->nombre ?></td>
                    <td><?php echo $usuario->email ?></td>
                    <td><?php echo $usuario->rol ?></td>
                    <td><a class="btn btn-warning" href="./editar_usuario.php?id=<?php echo $usuario->id ?>"><i class="fa fa-edit"></i> Editar</a></td>
                    <td><a class="btn btn-danger" href="./eliminar_usuario.php?id=<?php echo $usuario->id ?>" onclick="return confirm('¿Estás seguro de eliminar este usuario?')"><i class="fa fa-trash"></i> Eliminar</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include_once "./diseño/pie.php"; ?>