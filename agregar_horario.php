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

// Obtener materias del usuario para el select
$sql_materias = "SELECT id, nombre FROM materias WHERE usuario_id = ?";
$stmt_materias = mysqli_prepare($link, $sql_materias);
mysqli_stmt_bind_param($stmt_materias, "i", $user_id);
mysqli_stmt_execute($stmt_materias);
$materias = mysqli_stmt_get_result($stmt_materias)->fetch_all(MYSQLI_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $materia_id = trim($_POST["materia_id"]);
    $dia = trim($_POST["dia"]);
    $hora_inicio = trim($_POST["hora_inicio"]);
    $hora_fin = trim($_POST["hora_fin"]);
    $aula = trim($_POST["aula"]);

    if (!empty($materia_id) && !empty($dia) && !empty($hora_inicio) && !empty($hora_fin)) {
        $sql = "INSERT INTO horarios (usuario_id, materia_id, dia_semana, hora_inicio, hora_fin, aula) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, "iissss", $user_id, $materia_id, $dia, $hora_inicio, $hora_fin, $aula);
        
        if (mysqli_stmt_execute($stmt)) {
            $success = "Horario agregado correctamente";
            header("Location: ver_horario.php?success=1");
            exit;
        } else {
            $error = "Error al agregar horario: " . mysqli_error($link);
        }
    } else {
        $error = "Todos los campos marcados con * son requeridos";
    }
}

mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Horario - ACADEMEX</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <div class="container">
        <h1>Agregar Nuevo Horario</h1>
        
        <?php if (!empty($error)): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>
        
        <form method="POST" class="horario-form">
            <div class="form-group">
                <label for="materia_id">Materia*</label>
                <select id="materia_id" name="materia_id" required>
                    <option value="">Seleccione una materia</option>
                    <?php foreach ($materias as $m): ?>
                    <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="dia">Día de la semana*</label>
                <select id="dia" name="dia" required>
                    <option value="">Seleccione un día</option>
                    <option value="Lunes">Lunes</option>
                    <option value="Martes">Martes</option>
                    <option value="Miércoles">Miércoles</option>
                    <option value="Jueves">Jueves</option>
                    <option value="Viernes">Viernes</option>
                    <option value="Sábado">Sábado</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="hora_inicio">Hora de inicio*</label>
                <input type="time" id="hora_inicio" name="hora_inicio" required>
            </div>
            
            <div class="form-group">
                <label for="hora_fin">Hora de fin*</label>
                <input type="time" id="hora_fin" name="hora_fin" required>
            </div>
            
            <div class="form-group">
                <label for="aula">Aula</label>
                <input type="text" id="aula" name="aula">
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Guardar Horario</button>
                <a href="ver_horario.php" class="btn btn-back">Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>