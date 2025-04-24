<?php
include_once "./diseño/encabezado.php";
require '../base/conexion.php';

$token = $_GET['token'] ?? null;
$error = null;

if (!$token) {
    $error = "Token de restablecimiento inválido.";
} else {
    // Verificar si el token es válido y no ha expirado
    $stmt_check_token = $conexion->prepare("SELECT user_id FROM password_resets WHERE token = :token AND expiry > NOW()");
    $stmt_check_token->bindParam(':token', $token);
    $stmt_check_token->execute();
    $reset_data = $stmt_check_token->fetch(PDO::FETCH_ASSOC);

    if (!$reset_data) {
        $error = "El enlace de restablecimiento es inválido o ha expirado.";
    } else {
        $user_id = $reset_data['user_id'];
    }
}
?>

<div class="col-md-6 offset-md-3">
    <h2>Restablecer Contraseña</h2>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
        <p><a href="login.php">Volver al inicio de sesión</a></p>
    <?php elseif (isset($user_id)): ?>
        <form action="actualizar_password.php" method="POST">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">
            <div class="form-group">
                <label for="nueva_password">Nueva Contraseña:</label>
                <input type="password" class="form-control" id="nueva_password" name="nueva_password" required>
            </div>
            <div class="form-group">
                <label for="confirmar_password">Confirmar Nueva Contraseña:</label>
                <input type="password" class="form-control" id="confirmar_password" name="confirmar_password" required>
            </div>
            <button type="submit" class="btn btn-primary">Restablecer Contraseña</button>
        </form>
    <?php endif; ?>
</div>

<?php include_once "./diseño/pie.php"; ?>