<?php
require '../base/conexion.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        header('Location: login.php?error=Por favor, introduce tu correo electrónico y contraseña.');
        exit;
    }

    try {
        // Buscar al usuario por su correo electrónico
        $stmt = $conexion->prepare("SELECT id, nombre, password, rol FROM usuarios WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {
            // Verificar la contraseña hasheada
            if (password_verify($password, $usuario['password'])) {
                // Contraseña correcta, iniciar sesión
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nombre'] = $usuario['nombre'];
                $_SESSION['usuario_rol'] = $usuario['rol'];
                 // Guardar el rol en la sesión

                // Redirigir según el rol
                switch ($usuario['rol']) {
                    case 'admin':
                        header('Location: ../admin/listar.php'); // Página de administración
                        break;
                    case 'cliente':
                        header('Location: ../cliente/listar.php'); // Área de cliente
                        break;
                    case 'empleado':
                        header('Location: ../empleado/listar.php'); // Módulo de recepción (o la página principal de empleado)
                        break;
                    default:
                        header('Location: login.php'); // Página por defecto si el rol no coincide
                        break;
                }
                exit;
            } else {
                // Contraseña incorrecta
                header('Location: login.php?error=Correo electrónico o contraseña incorrectos.');
                exit;
            }
        } else {
            // No se encontró ningún usuario con ese correo electrónico
            header('Location: login.php?error=Correo electrónico o contraseña incorrectos.');
            exit;
        }

    } catch (PDOException $e) {
        // Error al consultar la base de datos
        header('Location: login.php?error=Error al iniciar sesión: ' . $e->getMessage());
        exit;
    }
} else {
    // Si alguien intenta acceder a este archivo directamente sin enviar el formulario
    header('Location: login.php');
    exit;
}
?>