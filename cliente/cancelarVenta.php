<?php
session_start();
include_once "../base/conexion.php"; // Asegúrate de que la ruta sea correcta

// *** Verificación de permisos para el cliente ***
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] !== 'cliente') {
    header("Location: ../login/login.php"); // Redirigir si no es cliente o no ha iniciado sesión
    exit();
}

unset($_SESSION["carrito1"]);
$_SESSION["carrito1"] = [];

header("Location: ./vender.php?status=2");
exit();
?>