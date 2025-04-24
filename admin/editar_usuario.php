<?php
session_start();
if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] !== 'admin') {
    header("Location: ../login/login.php"); // Redirigir si no es administrador
    exit();
}

include_once "./diseño/encabezado.php";
include_once "../base/conexion.php";

if (!isset($_GET["id"])) {
    header("Location: ./listar_usuarios.php");
    exit();
}
$id = $_GET["id"];
$sentencia = $conexion->prepare("SELECT id, nombre, email, rol FROM usuarios WHERE id = ?");
$sentencia->execute([$id]);
$usuario = $sentencia->fetch(PDO::FETCH_OBJ);

if (!$usuario) {
    header("Location: ./listar_usuarios.php?error=Usuario no encontrado");
    exit();
}
?>

<div class="col-md-6 offset-md-3">
    <h2>Editar Usuario</h2>
    <form action="./actualizar_usuario.php" method="POST">
        <input type="hidden" name="id" value="<?php echo $usuario->id ?>">
        <div class="form-group">
            <label for="nombre">Nombre:</label>
            <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($usuario->nombre) ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($usuario->email) ?>" required>
        </div>
        <div class="form-group">
            <label for="rol">Rol:</label>
            <select class="form-control" id="rol" name="rol" required>
                <option value="admin" <?php if ($usuario->rol === 'admin') echo 'selected'; ?>>Administrador</option>
                <option value="cliente" <?php if ($usuario->rol === 'cliente') echo 'selected'; ?>>Cliente</option>
                <option value="empleado" <?php if ($usuario->rol === 'empleado') echo 'selected'; ?>>Empleado</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        <a href="./listar_usuarios.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<?php include_once "./diseño/pie.php"; ?>