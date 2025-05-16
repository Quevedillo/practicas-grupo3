<?php
// Configuración de la aplicación
define('APP_NAME', 'Sistema de Tickets');
define('APP_VERSION', '1.0.0');

// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'ticket_system');

// Configuración de rutas
define('BASE_PATH', dirname(dirname(__FILE__)));
$base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
$script_name = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$base_url .= rtrim($script_name, '/');

define('BASE_URL', $base_url);

// Función para generar URLs absolutas
function url($path = '') {
    return rtrim(BASE_URL, '/') . '/' . ltrim($path, '/');
}

// Función para incluir archivos
function include_file($path) {
    $full_path = BASE_PATH . '/' . ltrim($path, '/');
    if (file_exists($full_path)) {
        return include $full_path;
    }
    return false;
}

// Función para redireccionar
function redirect($url) {
    header('Location: ' . url($url));
    exit();
}

// Función para cargar clases automáticamente
spl_autoload_register(function ($class_name) {
    $file = BASE_PATH . '/models/' . $class_name . '.php';
    if (file_exists($file)) {
        include $file;
    }
});

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Manejo de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Zona horaria
date_default_timezone_set('Europe/Madrid');
?>
