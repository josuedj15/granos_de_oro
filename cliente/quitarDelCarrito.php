<?php
session_start();
include_once "../base/conexion.php"; // Asegúrate de que la ruta sea correcta

// *** Verificación de permisos para el cliente ***
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] !== 'cliente') {
    header("Location: ../login/login.php"); // Redirigir si no es cliente o no ha iniciado sesión
    exit();
}

if(!isset($_GET["indice"])) {
    header("Location: ./vender.php?status=error_quitar"); // Puedes definir un status de error para esto
    exit();
}

$indice = $_GET["indice"];

if (isset($_SESSION["carrito1"]) && is_array($_SESSION["carrito1"]) && isset($_SESSION["carrito1"][$indice])) {
    array_splice($_SESSION["carrito1"], $indice, 1);
}

header("Location: ./vender.php?status=3");
exit();
?>