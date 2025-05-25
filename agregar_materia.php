<?php
session_start();
require_once "conexion.php";

// Verificar autenticación
if (!isset($_SESSION["logged_in"]) || $_SESSION["logged_in"] !== true) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION["id"];
$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST["nombre"]);
    $profesor = trim($_POST["profesor"]);
    $creditos = trim($_POST["creditos"]);
    $color = trim($_POST["color"]);

    if (!empty($nombre)) {
        $sql = "INSERT INTO materias (usuario_id, nombre, profesor, creditos, color) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, "issss", $user_id, $nombre, $profesor, $creditos, $color);
        
        if (mysqli_stmt_execute($stmt)) {
            $success = "Materia agregada correctamente";
            header("Location: ver_horario.php?success=1");
            exit;
        } else {
            $error = "Error al agregar materia: " . mysqli_error($link);
        }
    } else {
        $error = "El nombre de la materia es requerido";
    }
}

mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Materia - ACADEMEX</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <div class="container">
        <h1>Agregar Nueva Materia</h1>
        
        <?php if (!empty($error)): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>
        
        <form method="POST" class="materia-form">
            <div class="form-group">
                <label for="nombre">Nombre de la Materia*</label>
                <input type="text" id="nombre" name="nombre" required>
            </div>
            
            <div class="form-group">
                <label for="profesor">Profesor</label>
                <input type="text" id="profesor" name="profesor">
            </div>
            
            <div class="form-group">
                <label for="creditos">Créditos</label>
                <input type="number" id="creditos" name="creditos" min="1">
            </div>
            
            <div class="form-group">
                <label for="color">Color Identificador</label>
                <input type="color" id="color" name="color" value="#3aa8c1">
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Guardar Materia</button>
                <a href="ver_horario.php" class="btn btn-back">Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>