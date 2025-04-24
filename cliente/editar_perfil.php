<?php
session_start();
include_once "./diseño/encabezado.php";
include_once "../base/conexion.php";

// Verificar si el usuario ha iniciado sesión y es un cliente
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] !== 'cliente') {
    header("Location: ../login.php"); // Redirigir si no es cliente o no ha iniciado sesión
    exit();
}

$cliente_id = $_SESSION['usuario_id'];

try {
    // Obtener datos del usuario (nombre, email)
    $stmt_usuario = $conexion->prepare("SELECT nombre, email FROM usuarios WHERE id = :id");
    $stmt_usuario->bindParam(':id', $cliente_id);
    $stmt_usuario->execute();
    $usuario = $stmt_usuario->fetch(PDO::FETCH_ASSOC);

    // Obtener datos del cliente (direccion, telefono)
    $stmt_cliente = $conexion->prepare("SELECT direccion, telefono FROM clientes WHERE id = :id");
    $stmt_cliente->bindParam(':id', $cliente_id);
    $stmt_cliente->execute();
    $cliente_info = $stmt_cliente->fetch(PDO::FETCH_ASSOC);

    if (!$usuario || !$cliente_info) {
        echo "<div class='alert alert-danger'>Error: No se encontraron los datos del perfil.</div>";
        exit();
    }
} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>Error al obtener los datos del perfil: " . $e->getMessage() . "</div>";
    exit();
}
?>

<div class="col-md-6 offset-md-3">
    <h2>Editar Perfil</h2>
    <form action="actualizar_perfil.php" method="POST">
        <div class="form-group">
            <label for="nombre">Nombre:</label>
            <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Correo Electrónico:</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
        </div>
        <div class="form-group">
            <label for="direccion">Dirección:</label>
            <input type="text" class="form-control" id="direccion" name="direccion" value="<?php echo htmlspecialchars($cliente_info['direccion'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="telefono">Teléfono:</label>
            <input type="text" class="form-control" id="telefono" name="telefono" value="<?php echo htmlspecialchars($cliente_info['telefono'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="nueva_password">Nueva Contraseña (dejar en blanco para no cambiar):</label>
            <input type="password" class="form-control" id="nueva_password" name="nueva_password">
            <small class="form-text text-muted">La contraseña debe tener al menos 6 caracteres.</small>
        </div>
        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        <a href="./listar.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<?php include_once "./diseño/pie.php"; ?>