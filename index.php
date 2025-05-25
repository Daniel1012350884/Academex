<?php
require_once "code-login.php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login Academex</title>
    <link rel="stylesheet" href="css/estilos.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <img src="images/logo.png" alt="Logo" class="logo-welcome">
</head>
<body>
    <div class="container-all">
        <div class="ctn-form">
            <h1 class="title">Inicio de Sesión</h1>
            
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                <label for="email">Correo electrónico:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>">
                <div class="msg-error"><?php echo $email_err; ?></div>

                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password">
                <div class="msg-error"><?php echo $password_err; ?></div>

                <input type="submit" value="Iniciar Sesión">
            </form>
            
            <span class="text-footer">¿Nuevo en Academex? <a href="register.php">Crea una cuenta</a></span>
        </div>
        <div class="ctn-text"></div>
    </div>
</body>
</html>