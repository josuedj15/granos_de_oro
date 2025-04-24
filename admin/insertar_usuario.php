<?php
session_start();
if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] !== 'admin') {
    header("Location: ../login/login.php"); // Redirigir si no es administrador
    exit();
}

include_once "./diseño/encabezado.php";
?>

<div class="col-md-6 offset-md-3">
    <h2>Agregar Nuevo Usuario</h2>
    <form action="./guardar_usuario.php" method="POST">
        <div class="form-group">
            <label for="nombre">Nombre:</label>
            <input type="text" class="form-control" id="nombre" name="nombre" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">Contraseña:</label>
            <input type="password" class="form-control" id="password" name="password" required>
            <small class="form-text text-muted">La contraseña debe tener al menos 6 caracteres.</small>
        </div>
        <div class="form-group">
            <label for="rol">Rol:</label>
            <select class="form-control" id="rol" name="rol" required>
                <option value="admin">Administrador</option>
                <option value="cliente">Cliente</option>
                <option value="empleado">Empleado</option>
            </select>
        </div>
        <button type="submit" class="btn btn-success">Guardar Usuario</button>
        <a href="./listar_usuarios.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<?php include_once "./diseño/pie.php"; ?>