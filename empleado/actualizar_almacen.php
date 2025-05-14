<?php
session_start();
if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] !== 'empleado') {
    header("Location: ../login/login.php");
    exit();
}

if (!isset($_POST["id"]) || !isset($_POST["nombre"])) {
    header("Location: ./listar_almacenes.php");
    exit();
}

include_once "../base/conexion.php";
$id = $_POST["id"];
$nombre = $_POST["nombre"];
$ubicacion = $_POST["ubicacion"];

$sentencia = $conexion->prepare("UPDATE almacenes SET nombre = ?, ubicacion = ? WHERE id = ?");
$resultado = $sentencia->execute([$nombre, $ubicacion, $id]);

if ($resultado) {
    header("Location: ./listar_almacenes.php?mensaje=Almacén actualizado correctamente");
} else {
    header("Location: ./listar_almacenes.php?error=Error al actualizar el almacén");
}
exit();
?>