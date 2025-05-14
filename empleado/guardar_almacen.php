<?php
session_start();
if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] !== 'empleado') {
    header("Location: ../login/login.php");
    exit();
}

if (!isset($_POST["nombre"])) {
    header("Location: ./crear_almacen.php");
    exit();
}

include_once "../base/conexion.php";
$nombre = $_POST["nombre"];
$ubicacion = $_POST["ubicacion"];

$sentencia = $conexion->prepare("INSERT INTO almacenes (nombre, ubicacion) VALUES (?, ?)");
$resultado = $sentencia->execute([$nombre, $ubicacion]);

if ($resultado) {
    header("Location: ./listar_almacenes.php?mensaje=Almacén agregado correctamente");
} else {
    header("Location: ./listar_almacenes.php?error=Error al agregar el almacén");
}
exit();
?>