<?php
require '../base/conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $direccion = trim($_POST['direccion']); // Recibir la dirección del formulario
    $telefono = trim($_POST['telefono']);   // Recibir el teléfono del formulario

    // Validación básica (puedes agregar más validaciones)
    if (empty($nombre) || empty($email) || empty($password) ) {
        header('Location: registro.php?error=Todos los campos son obligatorios');
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('Location: registro.php?error=El formato del correo electrónico no es válido');
        exit;
    }

    if (strlen($password) < 6) {
        header('Location: registro.php?error=La contraseña debe tener al menos 6 caracteres');
        exit;
    }

    try {
        // Verificar si el correo electrónico ya existe
        $stmt_check = $conexion->prepare("SELECT COUNT(*) FROM usuarios WHERE email = :email");
        $stmt_check->bindParam(':email', $email);
        $stmt_check->execute();

        if ($stmt_check->fetchColumn() > 0) {
            header('Location: registro.php?error=Este correo electrónico ya está registrado');
            exit;
        }

        // Hashear la contraseña de forma segura
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insertar el nuevo usuario en la base de datos con el rol de 'cliente' por defecto
        $stmt_insert_usuario = $conexion->prepare("INSERT INTO usuarios (nombre, email, password, rol) VALUES (:nombre, :email, :password, 'cliente')");
        $stmt_insert_usuario->bindParam(':nombre', $nombre);
        $stmt_insert_usuario->bindParam(':email', $email);
        $stmt_insert_usuario->bindParam(':password', $hashed_password);
        $stmt_insert_usuario->execute();

        // Obtener el ID del usuario recién insertado
        $usuario_id = $conexion->lastInsertId();

        // Insertar los datos del cliente en la tabla 'cliente'
        $stmt_insert_cliente = $conexion->prepare("INSERT INTO clientes (id, direccion, telefono) VALUES (?, ?, ?)");
        $stmt_insert_cliente->execute([$usuario_id, $direccion, $telefono]);

        header('Location: registro.php?success=Registro exitoso. Ahora puedes iniciar sesión.');
        exit;
    } catch (PDOException $e) {
        // Si ocurre algún error con la base de datos
        header('Location: registro.php?error=Error al registrar el usuario: ' . $e->getMessage());
        exit;
    }
} else {
    // Si alguien intenta acceder a este archivo directamente sin enviar el formulario
    header('Location: registro.php');
    exit;
}
