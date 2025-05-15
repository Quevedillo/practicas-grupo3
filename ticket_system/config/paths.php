<?php
// Configuración de rutas base
define('BASE_URL', ''); // Ruta base del proyecto (vacía si está en la raíz)

// URLs para redirecciones
define('URL_HOME', 'index.php');
define('URL_LOGIN', 'views/users/login.php');
define('URL_LOGOUT', 'sesion/logout.php');

// Rutas a recursos
define('URL_CSS', 'css/');
define('URL_JS', 'js/');
define('URL_IMG', 'img/');

// Rutas a controladores
define('URL_REPORTS', 'index.php?controller=report');

// Función para generar URLs completas
function url($path = '') {
    return BASE_URL . $path;
}

// Función para redirigir
function redirect($path) {
    header('Location: ' . url($path));
    exit();
}
?>
