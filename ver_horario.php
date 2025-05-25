<?php
session_start();
require_once "conexion.php";

// Verificar autenticación
if (!isset($_SESSION["logged_in"]) || $_SESSION["logged_in"] !== true) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION["id"];

// Obtener horarios del usuario
$sql = "SELECT h.*, m.nombre AS materia_nombre, m.color AS materia_color 
        FROM horarios h 
        JOIN materias m ON h.materia_id = m.id 
        WHERE h.usuario_id = ? 
        ORDER BY FIELD(h.dia_semana, 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'), h.hora_inicio";

$stmt = mysqli_prepare($link, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$horarios = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Obtener materias del usuario
$sql_materias = "SELECT * FROM materias WHERE usuario_id = ?";
$stmt_materias = mysqli_prepare($link, $sql_materias);
mysqli_stmt_bind_param($stmt_materias, "i", $user_id);
mysqli_stmt_execute($stmt_materias);
$materias = mysqli_stmt_get_result($stmt_materias)->fetch_all(MYSQLI_ASSOC);

mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ver Horario - ACADEMEX</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <div class="container">
        <h1>Tu Horario Semanal</h1>
        
        <table class="horario-table">
            <thead>
                <tr>
                    <th>Materia</th>
                    <th>Día</th>
                    <th>Hora Inicio</th>
                    <th>Hora Fin</th>
                    <th>Aula</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($horarios as $h): ?>
                <tr>
                    <td>
                        <span class="materia-color" style="background-color: <?= $h['materia_color'] ?>"></span>
                        <?= htmlspecialchars($h['materia_nombre']) ?>
                    </td>
                    <td><?= htmlspecialchars($h['dia_semana']) ?></td>
                    <td><?= substr($h['hora_inicio'], 0, 5) ?></td>
                    <td><?= substr($h['hora_fin'], 0, 5) ?></td>
                    <td><?= htmlspecialchars($h['aula']) ?></td>
                    <td>
                        <a href="editar_horario.php?id=<?= $h['id'] ?>" class="btn-action btn-edit">Editar</a>
                        <a href="eliminar_horario.php?id=<?= $h['id'] ?>" class="btn-action btn-delete">Eliminar</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <h2>Tus Materias</h2>
        <table class="materias-table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Profesor</th>
                    <th>Créditos</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($materias as $m): ?>
                <tr>
                    <td><?= htmlspecialchars($m['nombre']) ?></td>
                    <td><?= htmlspecialchars($m['profesor']) ?></td>
                    <td><?= htmlspecialchars($m['creditos']) ?></td>
                    <td>
                        <a href="editar_materia.php?id=<?= $m['id'] ?>" class="btn-action btn-edit">Editar</a>
                        <a href="eliminar_materia.php?id=<?= $m['id'] ?>" class="btn-action btn-delete">Eliminar</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div class="action-buttons">
            <a href="agregar_materia.php" class="btn btn-primary">Agregar Nueva Materia</a>
            <a href="agregar_horario.php" class="btn btn-secondary">Agregar Nuevo Horario</a>
            <a href="bienvenida.php" class="btn btn-back">Volver</a>
        </div>
    </div>
</body>
</html>