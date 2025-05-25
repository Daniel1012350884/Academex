<?php
// Configuración de la base de datos
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'jesus262');
define('DB_NAME', 'login_tuto');

// Establecer conexión con manejo de errores mejorado
try {
    $link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
    
    if (!$link) {
        throw new Exception("Error de conexión: " . mysqli_connect_error());
    }
    
    // Configurar charset (mejor usar esta forma que garantiza el resultado)
    if (!mysqli_set_charset($link, "utf8mb4")) {
        throw new Exception("Error al configurar charset: " . mysqli_error($link));
    }
    
    // Opcional: Configurar el timezone si trabajas con fechas
    mysqli_query($link, "SET time_zone = '-05:00'"); // Ejemplo para zona horaria de Perú
    
} catch (Exception $e) {
    // Manejo seguro de errores (puedes registrar el error en un archivo log en producción)
    error_log($e->getMessage());
    die("Error crítico en la conexión a la base de datos. Por favor intente más tarde.");
}
?>