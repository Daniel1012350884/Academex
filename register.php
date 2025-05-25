<?php
require_once "code-register.php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro Academex</title>
    <link rel="stylesheet" href="css/estilos.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <img src="images/logo.png" alt="Logo" class="logo-welcome">
</head>
<body>
    <div class="container-all">
        <div class="ctn-form">
            <h1 class="title">Registro de Usuario</h1>
            
            <?php if($registration_success): ?>
                <div class="msg-success">
                    ¡Registro exitoso! <a href="index.php">Iniciar sesión</a>
                </div>
            <?php else: ?>
                <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                    <label for="username">Nombre de usuario:</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>">
                    <div class="msg-error"><?php echo $username_err; ?></div>

                    <label for="email">Correo electrónico:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>">
                    <div class="msg-error"><?php echo $email_err; ?></div>

                    <label for="password">Contraseña:</label>
                    <input type="password" id="password" name="password">
                    <div class="msg-error"><?php echo $password_err; ?></div>

                    <input type="submit" value="Registrarse">
                </form>
            <?php endif; ?>
            
            <span class="text-footer">¿Ya tienes cuenta? <a href="index.php">Inicia sesión</a></span>
        </div>
        <div class="ctn-text"></div>
    </div>
</body>
</html>