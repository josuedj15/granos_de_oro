<?php
include_once("../errores.php");

// Asegúrate de tener la conexión a tu base de datos configurada correctamente
require '../base/conexion.php';

// Script para hashear contraseñas existentes en la tabla 'usuarios'

try {
    // Seleccionar todos los usuarios que aún no tienen su contraseña hasheada
    // Puedes identificar esto buscando contraseñas que no parezcan hashes (por ejemplo, muy cortas)
    $stmt_select = $conexion->query("SELECT id, password FROM usuarios WHERE LENGTH(password) < 60");
    $usuarios = $stmt_select->fetchAll(PDO::FETCH_ASSOC);

    $actualizados = 0;
    $errores = [];

    foreach ($usuarios as $usuario) {
        $password_plana = $usuario['password'];

        // Hashear la contraseña
        $hashed_password = password_hash($password_plana, PASSWORD_DEFAULT);

        // Actualizar la contraseña en la base de datos
        $stmt_update = $conexion->prepare("UPDATE usuarios SET password = :password WHERE id = :id");
        $stmt_update->bindParam(':password', $hashed_password);
        $stmt_update->bindParam(':id', $usuario['id']);

        if ($stmt_update->execute()) {
            $actualizados++;
        } else {
            $errores[] = "Error al actualizar la contraseña del usuario con ID: " . $usuario['id'];
        }
    }

    echo "Se han intentado actualizar " . count($usuarios) . " contraseñas.<br>";
    echo "Se actualizaron exitosamente " . $actualizados . " contraseñas.<br>";

    if (!empty($errores)) {
        echo "<br>Errores:<br>";
        foreach ($errores as $error) {
            echo "- " . $error . "<br>";
        }
    }

    echo "<br>¡Proceso completado! Recuerda eliminar o proteger este script.";

} catch (PDOException $e) {
    echo "Error de base de datos: " . $e->getMessage();
}
?>