<?php
$host = 'localhost'; // Generalmente es localhost
$dbname = 'g_de_oro'; // El nombre de tu base de datos
$usuario = 'root'; // Tu nombre de usuario de MySQL (puede variar)
$contrasena = 'root1179'; // Tu contraseña de MySQL (puede estar vacía por defecto)

try {
    $conexion = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $usuario, $contrasena);
    // Establecer el modo de error PDO a excepción
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Para depuración, puedes activar esto para ver los errores SQL
    // $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    die("Error al conectar a la base de datos: " . $e->getMessage());
}
?>