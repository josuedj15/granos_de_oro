<?php
session_start();
if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] !== 'admin') {
    header("Location: ../login/login.php"); // Redirigir si no es administrador
    exit();
}

if (!isset($_POST["id"])) {
    header("Location: ./listar_usuarios.php");
    exit();
}

include_once "../base/conexion.php";
$id = $_POST["id"];
$nombre = $_POST["nombre"];
$email = $_POST["email"];
$rol = $_POST["rol"];

$sentencia = $conexion->prepare("UPDATE usuarios SET nombre = ?, email = ?, rol = ? WHERE id = ?");
$resultado = $sentencia->execute([$nombre, $email, $rol, $id]);

if ($resultado) {
    header("Location: ./listar_usuarios.php?mensaje=Usuario actualizado correctamente");
} else {
    header("Location: ./listar_usuarios.php?error=Error al actualizar el usuario");
}
exit();
?>