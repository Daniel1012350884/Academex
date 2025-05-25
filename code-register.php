<?php
require_once "conexion.php";

$username = $email = $password = "";
$username_err = $email_err = $password_err = "";
$registration_success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Validar nombre de usuario
    if (empty(trim($_POST["username"]))) {
        $username_err = "Por favor ingrese un nombre de usuario";
    } else {
        $sql = "SELECT id FROM usuarios WHERE usuario = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            $param_username = trim($_POST["username"]);
            
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    $username_err = "Este nombre de usuario ya existe";
                } else {
                    $username = trim($_POST["username"]);
                }
            }
            mysqli_stmt_close($stmt);
        }
    }

    // Validar email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Por favor ingrese un correo electrónico";
    } elseif (!filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)) {
        $email_err = "Formato de correo inválido";
    } else {
        $sql = "SELECT id FROM usuarios WHERE email = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            $param_email = trim($_POST["email"]);
            
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    $email_err = "Este correo ya está registrado";
                } else {
                    $email = trim($_POST["email"]);
                }
            }
            mysqli_stmt_close($stmt);
        }
    }

    // Validar y hashear contraseña
    if (empty(trim($_POST["password"]))) {
        $password_err = "Por favor ingrese una contraseña";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "La contraseña debe tener al menos 6 caracteres";
    } else {
        $password = trim($_POST["password"]);
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    }

    // Registrar usuario si no hay errores
    if (empty($username_err) && empty($email_err) && empty($password_err)) {
        $sql = "INSERT INTO usuarios (usuario, email, contraseña) VALUES (?, ?, ?)";
        
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "sss", $username, $email, $hashed_password);
            
            if (mysqli_stmt_execute($stmt)) {
                $registration_success = true;
            } else {
                echo "Error: " . mysqli_error($link);
            }
            mysqli_stmt_close($stmt);
        }
    }
    
    mysqli_close($link);
}
?>