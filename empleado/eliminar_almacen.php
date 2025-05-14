<?php
session_start();
if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] !== 'empleado') {
    header("Location: ../login/login.php");
    exit();
}

if (!isset($_GET["id"])) {
    header("Location: ./listar_almacenes.php");
    exit();
}

include_once "../base/conexion.php";
$id = $_GET["id"];
$sentencia = $conexion->prepare("DELETE FROM almacenes WHERE id = ?");
$resultado = $sentencia->execute([$id]);

if ($resultado) {
    header("Location: ./listar_almacenes.php?mensaje=Almacén eliminado correctamente");
} else {
    header("Location: ./listar_almacenes.php?error=Error al eliminar el almacén");
}
exit();
?>