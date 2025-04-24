<?php
session_start();
include_once "../base/conexion.php";

// Verificar si el usuario ha iniciado sesión y es un cliente
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] !== 'cliente') {
    header("Location: ../login.php"); // Redirigir si no es cliente o no ha iniciado sesión
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cliente_id = $_SESSION['usuario_id'];
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $direccion = trim($_POST['direccion']);
    $telefono = trim($_POST['telefono']);
    $nueva_password = $_POST['nueva_password'];

    // Validaciones básicas
    if (empty($nombre) || empty($email)) {
        header("Location: editar_perfil.php?error=El nombre y el correo electrónico son obligatorios.");
        exit();
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: editar_perfil.php?error=El formato del correo electrónico no es válido.");
        exit();
    }
    if (!empty($nueva_password) && strlen($nueva_password) < 6) {
        header("Location: editar_perfil.php?error=La nueva contraseña debe tener al menos 6 caracteres.");
        exit();
    }

    try {
        $conexion->beginTransaction(); // Iniciar una transacción para asegurar la integridad

        // Actualizar la tabla usuarios
        $sql_usuario = "UPDATE usuarios SET nombre = :nombre, email = :email";
        $params_usuario = [':nombre' => $nombre, ':email' => $email];
        if (!empty($nueva_password)) {
            $hashed_password = password_hash($nueva_password, PASSWORD_DEFAULT);
            $sql_usuario .= ", password = :password";
            $params_usuario[':password'] = $hashed_password;
        }
        $sql_usuario .= " WHERE id = :id";
        $params_usuario[':id'] = $cliente_id;
        $stmt_usuario = $conexion->prepare($sql_usuario);
        $stmt_usuario->execute($params_usuario);

        // Actualizar la tabla cliente
        $sql_cliente = "UPDATE clientes SET direccion = :direccion, telefono = :telefono WHERE id = :id";
        $stmt_cliente = $conexion->prepare($sql_cliente);
        $stmt_cliente->execute([
            ':direccion' => $direccion,
            ':telefono' => $telefono,
            ':id' => $cliente_id
        ]);

        $conexion->commit(); // Confirmar la transacción

        // Actualizar la variable de sesión con el nuevo nombre (opcional)
        $_SESSION['usuario_nombre'] = $nombre;
        header("Location: editar_perfil.php?success=Perfil actualizado correctamente.");
        exit();

    } catch (PDOException $e) {
        $conexion->rollBack(); // Revertir la transacción en caso de error
        header("Location: editar_perfil.php?error=Error al actualizar el perfil: " . $e->getMessage());
        exit();
    }
} else {
    // Si se intenta acceder directamente a este archivo sin enviar el formulario
    header("Location: editar_perfil.php");
    exit();
}
?>