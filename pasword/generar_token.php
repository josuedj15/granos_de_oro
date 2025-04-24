<?php
require '../base/conexion.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    // Verificar si el correo electrónico existe en la base de datos
    $stmt_check = $conexion->prepare("SELECT id FROM usuarios WHERE email = :email");
    $stmt_check->bindParam(':email', $email);
    $stmt_check->execute();
    $usuario = $stmt_check->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        $token = bin2hex(random_bytes(32)); // Generar un token único y seguro
        $expiry = date("Y-m-d H:i:s", strtotime('+1 hour')); // Token expira en 1 hora
        $user_id = $usuario['id'];

        // Guardar el token en la base de datos (necesitas una tabla para esto)
        $stmt_insert_token = $conexion->prepare("INSERT INTO password_resets (user_id, token, expiry) VALUES (:user_id, :token, :expiry)");
        $stmt_insert_token->bindParam(':user_id', $user_id);
        $stmt_insert_token->bindParam(':token', $token);
        $stmt_insert_token->bindParam(':expiry', $expiry);
        $stmt_insert_token->execute();

        $reset_link = "http://tu_dominio.com/restablecer_password.php?token=" . $token; // Generar el enlace

        // Enviar correo electrónico con el enlace (necesitarás configurar una función de envío de correo)
        $to = $email;
        $subject = "Restablecimiento de Contraseña";
        $message = "Por favor, haz clic en el siguiente enlace para restablecer tu contraseña:\n\n" . $reset_link . "\n\nEste enlace expirará en 1 hora.";
        $headers = 'From: webmaster@tu_dominio.com' . "\r\n" .
                   'Reply-To: webmaster@tu_dominio.com' . "\r\n" .
                   'X-Mailer: PHP/' . phpversion();

        mail($to, $subject, $message, $headers); // Función básica de envío de correo (requiere configuración)

        $_SESSION['info'] = "Se ha enviado un enlace de restablecimiento a tu correo electrónico. Por favor, revisa tu bandeja de entrada (y la carpeta de spam).";
        header("Location: olvidar_password.php");
        exit();

    } else {
        $_SESSION['error'] = "No se encontró ninguna cuenta con ese correo electrónico.";
        header("Location: olvidar_password.php");
        exit();
    }
} else {
    header("Location: olvidar_password.php");
    exit();
}
?>