<?php
session_start();
if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] !== 'admin') {
    header("Location: ../login/login.php"); // Redirigir si no es administrador
    exit();
}

if (!isset($_POST["nombre"]) || !isset($_POST["email"]) || !isset($_POST["password"]) || !isset($_POST["rol"])) {
    header("Location: ./insertar_usuario.php");
    exit();
}

include_once "../base/conexion.php";
$nombre = $_POST["nombre"];
$email = $_POST["email"];
$password = password_hash($_POST["password"], PASSWORD_DEFAULT); // Hash de la contraseña
$rol = $_POST["rol"];

$sentencia = $conexion->prepare("INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, ?)");
$resultado = $sentencia->execute([$nombre, $email, $password, $rol]);

if ($resultado) {
    header("Location: ./listar_usuarios.php?mensaje=Usuario agregado correctamente");
} else {
    header("Location: ./listar_usuarios.php?error=Error al agregar el usuario");
}
exit();
?>