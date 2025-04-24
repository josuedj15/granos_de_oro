<?php include_once "./diseño/encabezado.php"; ?>

<div class="col-md-6 offset-md-3">
    <h2>¿Olvidaste tu Contraseña?</h2>
    <form action="generar_token.php" method="POST">
        <div class="form-group">
            <label for="email">Correo Electrónico:</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <button type="submit" class="btn btn-primary">Enviar Solicitud de Restablecimiento</button>
    </form>
    <p class="mt-3"><a href="login.php">Volver al inicio de sesión</a></p>
</div>

<?php include_once "./diseño/pie.php"; ?>