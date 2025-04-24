<?php
require '../base/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'];
    $user_id = $_POST['user_id'];
    $nueva_password = $_POST['nueva_password'];
    $confirmar_password = $_POST['confirmar_password'];

    if ($nueva_password !== $confirmar_password) {
        header("Location: restablecer_password.php?token=" . $token . "&error=Las contraseñas no coinciden.");
        exit();
    }

    if (strlen($nueva_password) < 6) {
        header("Location: restablecer_password.php?token=" . $token . "&error=La contraseña debe tener al menos 6 caracteres.");
        exit();
    }

    try {
        $hashed_password = password_hash($nueva_password, PASSWORD_DEFAULT);

        // Actualizar la contraseña del usuario
        $stmt_update_password = $conexion->prepare("UPDATE usuarios SET password = :password WHERE id = :user_id");
        $stmt_update_password->bindParam(':password', $hashed_password);
        $stmt_update_password->bindParam(':user_id', $user_id);
        $stmt_update_password->execute();

        // Eliminar el token de restablecimiento (opcional, por seguridad)
        $stmt_delete_token = $conexion->prepare("DELETE FROM password_resets WHERE token = :token");
        $stmt_delete_token->bindParam(':token', $token);
        $stmt_delete_token->execute();

        $_SESSION['success'] = "Tu contraseña ha sido restablecida exitosamente. Ahora puedes iniciar sesión.";
        header("Location: login.php");
        exit();

    } catch (PDOException $e) {
        header("Location: restablecer_password.php?token=" . $token . "&error=Error al actualizar la contraseña: " . $e->getMessage());
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}
?>