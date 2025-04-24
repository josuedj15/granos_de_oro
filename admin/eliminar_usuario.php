<?php
session_start();
if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] !== 'admin') {
    header("Location: ../login/login.php"); // Redirigir si no es administrador
    exit();
}

if (!isset($_GET["id"])) {
    header("Location: ./listar_usuarios.php");
    exit();
}

include_once "../base/conexion.php";
$id = $_GET["id"];
$sentencia = $conexion->prepare("DELETE FROM usuarios WHERE id = ?");
$resultado = $sentencia->execute([$id]);

if ($resultado) {
    header("Location: ./listar_usuarios.php?mensaje=Usuario eliminado correctamente");
} else {
    header("Location: ./listar_usuarios.php?error=Error al eliminar el usuario");
}
exit();
?>