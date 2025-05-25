<?php
session_start();

// Redirigir si ya está autenticado
if(isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] === true){
    header("Location: bienvenida.php");
    exit;
}

require_once "conexion.php";

$email = $password = "";
$email_err = $password_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    
    // Validar email
    if(empty(trim($_POST["email"]))){
        $email_err = "Por favor ingrese su correo electrónico";
    } else{
        $email = trim($_POST["email"]);
    }
    
    // Validar contraseña
    if(empty(trim($_POST["password"]))){
        $password_err = "Por favor ingrese su contraseña";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Verificar credenciales
    if(empty($email_err) && empty($password_err)){
        $sql = "SELECT id, usuario, contraseña FROM usuarios WHERE email = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            $param_email = $email;
            
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    mysqli_stmt_bind_result($stmt, $id, $usuario, $hashed_password);
                    if(mysqli_stmt_fetch($stmt)){
                        if(password_verify($password, $hashed_password)){
                            // Iniciar sesión
                            $_SESSION["logged_in"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["usuario"] = $usuario;
                            $_SESSION["email"] = $email;
                            
                            header("Location: bienvenida.php");
                            exit;
                        } else{
                            $password_err = "La contraseña ingresada es incorrecta";
                        }
                    }
                } else{
                    $email_err = "No existe una cuenta con este correo electrónico";
                }
            }
            mysqli_stmt_close($stmt);
        }
    }
    mysqli_close($link);
}
?>