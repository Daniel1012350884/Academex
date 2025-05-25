<?php
session_start();

// Verificar autenticación
if (!isset($_SESSION["logged_in"]) || $_SESSION["logged_in"] !== true || !isset($_SESSION["id"])) {
    header("Location: index.php");
    exit;
}

require_once "conexion.php";

$user_id = $_SESSION["id"];
$error = "";
$success = "";

// Procesar formularios
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Agregar nueva materia
    if (isset($_POST["agregar_materia"])) {
        $nombre = trim($_POST["nombre_materia"]);
        $profesor = trim($_POST["profesor"]);
        $creditos = trim($_POST["creditos"]);
        $color = trim($_POST["color"]);

        if (!empty($nombre)) {
            $sql = "INSERT INTO materias (usuario_id, nombre, profesor, creditos, color) VALUES (?, ?, ?, ?, ?)";
            if ($stmt = mysqli_prepare($link, $sql)) {
                mysqli_stmt_bind_param($stmt, "issss", $user_id, $nombre, $profesor, $creditos, $color);
                if (mysqli_stmt_execute($stmt)) {
                    $success = "Materia agregada correctamente";
                } else {
                    $error = "Error al agregar materia";
                }
                mysqli_stmt_close($stmt);
            }
        } else {
            $error = "El nombre de la materia es requerido";
        }
    }

    // Agregar nuevo horario
    if (isset($_POST["agregar_horario"])) {
        $materia_id = trim($_POST["materia"]);
        $dia = trim($_POST["dia"]);
        $hora_inicio = trim($_POST["hora_inicio"]);
        $hora_fin = trim($_POST["hora_fin"]);
        $aula = trim($_POST["aula"]);

        if (!empty($materia_id) && !empty($dia) && !empty($hora_inicio) && !empty($hora_fin)) {
            $sql = "INSERT INTO horarios (usuario_id, materia_id, dia_semana, hora_inicio, hora_fin, aula) VALUES (?, ?, ?, ?, ?, ?)";
            if ($stmt = mysqli_prepare($link, $sql)) {
                mysqli_stmt_bind_param($stmt, "iissss", $user_id, $materia_id, $dia, $hora_inicio, $hora_fin, $aula);
                if (mysqli_stmt_execute($stmt)) {
                    $success = "Horario agregado correctamente";
                } else {
                    $error = "Error al agregar horario";
                }
                mysqli_stmt_close($stmt);
            }
        } else {
            $error = "Todos los campos son requeridos";
        }
    }

    // Eliminar materia
    if (isset($_POST["eliminar_materia"])) {
        $materia_id = trim($_POST["materia_id"]);

        // Primero eliminar horarios asociados
        $sql = "DELETE FROM horarios WHERE materia_id = ? AND usuario_id = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "ii", $materia_id, $user_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }

        // Luego eliminar materia
        $sql = "DELETE FROM materias WHERE id = ? AND usuario_id = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "ii", $materia_id, $user_id);
            if (mysqli_stmt_execute($stmt)) {
                $success = "Materia eliminada correctamente";
            } else {
                $error = "Error al eliminar materia";
            }
            mysqli_stmt_close($stmt);
        }
    }

    // Eliminar horario
    if (isset($_POST["eliminar_horario"])) {
        $horario_id = trim($_POST["horario_id"]);

        $sql = "DELETE FROM horarios WHERE id = ? AND usuario_id = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "ii", $horario_id, $user_id);
            if (mysqli_stmt_execute($stmt)) {
                $success = "Horario eliminado correctamente";
            } else {
                $error = "Error al eliminar horario";
            }
            mysqli_stmt_close($stmt);
        }
    }
}

// Obtener materias
$materias = [];
$sql = "SELECT * FROM materias WHERE usuario_id = ?";
if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($result)) {
        $materias[] = $row;
    }
    mysqli_stmt_close($stmt);
}

// Obtener horarios
$horarios = [];
$sql = "SELECT h.*, m.nombre AS materia_nombre, m.color AS materia_color 
        FROM horarios h 
        JOIN materias m ON h.materia_id = m.id 
        WHERE h.usuario_id = ? 
        ORDER BY FIELD(h.dia_semana, 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'), h.hora_inicio";
if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($result)) {
        $horarios[] = $row;
    }
    mysqli_stmt_close($stmt);
}

mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Horario Académico - ACADEMEX</title>
    <link rel="stylesheet" href="css/horari.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <div class="container">
        <h1>Horario Académico</h1>

        <div class="action-buttons">
            <a href="#materias" class="btn">Ver Materias</a>
            <a href="#agregar-horario" class="btn">Agregar Horario</a>
            <a href="#horarios" class="btn">Ver Horario</a>
        </div>

        <?php if (!empty($error)): ?><div class="error"><?= $error ?></div><?php endif; ?>
        <?php if (!empty($success)): ?><div class="success"><?= $success ?></div><?php endif; ?>

        <h2 id="horarios">Tus Horarios</h2>
        <table class="horario-table">
            <thead>
                <tr>
                    <th>Materia</th>
                    <th>Día</th>
                    <th>Inicio</th>
                    <th>Fin</th>
                    <th>Aula</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($horarios as $h): ?>
                <tr>
                    <td><span class="materia-color" style="background-color: <?= $h['materia_color'] ?>"></span> <?= htmlspecialchars($h['materia_nombre']) ?></td>
                    <td><?= htmlspecialchars($h['dia_semana']) ?></td>
                    <td><?= substr($h['hora_inicio'], 0, 5) ?></td>
                    <td><?= substr($h['hora_fin'], 0, 5) ?></td>
                    <td><?= htmlspecialchars($h['aula']) ?></td>
                    <td>
                        <form method="POST" style="display:inline">
                            <input type="hidden" name="horario_id" value="<?= $h['id'] ?>">
                            <button type="submit" name="eliminar_horario" class="btn btn-delete">Eliminar</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h2 id="agregar-horario">Agregar Nuevo Horario</h2>
        <form method="POST" class="form">
            <input type="hidden" name="agregar_horario" value="1">
            <select name="materia" required>
                <option value="">Seleccione Materia</option>
                <?php foreach ($materias as $m): ?>
                <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
            <select name="dia" required>
                <option value="">Seleccione Día</option>
                <option value="Lunes">Lunes</option>
                <option value="Martes">Martes</option>
                <option value="Miércoles">Miércoles</option>
                <option value="Jueves">Jueves</option>
                <option value="Viernes">Viernes</option>
                <option value="Sábado">Sábado</option>
            </select>
            <input type="time" name="hora_inicio" required>
            <input type="time" name="hora_fin" required>
            <input type="text" name="aula" placeholder="Aula">
            <button type="submit" class="btn btn-primary">Guardar</button>
        </form>

        <h2 id="materias">Materias</h2>
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
                        <form method="POST" style="display:inline">
                            <input type="hidden" name="materia_id" value="<?= $m['id'] ?>">
                            <button type="submit" name="eliminar_materia" class="btn btn-delete">Eliminar</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h2>Agregar Nueva Materia</h2>
        <form method="POST" class="form">
            <input type="hidden" name="agregar_materia" value="1">
            <input type="text" name="nombre_materia" placeholder="Nombre*" required>
            <input type="text" name="profesor" placeholder="Profesor">
            <input type="number" name="creditos" placeholder="Créditos" min="1">
            <input type="color" name="color" value="#3aa8c1">
            <button type="submit" class="btn btn-primary">Guardar Materia</button>
        </form>

        <div class="action-buttons">
            <a href="bienvenida.php" class="btn btn-back">Volver</a>
        </div>
    </div>
</body>
</html>
