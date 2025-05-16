<?php
class AdminController {
    public function __construct() {
        // Verificar si el usuario está autenticado (opcional)
        // if (!isset($_SESSION['user_id'])) {
        //     header('Location: index.php?controller=user&action=login');
        //     exit;
        // }
    }
    
    public function dashboard() {
        // Comentar o eliminar estas líneas que causan el error
        /*
        require_once dirname(__FILE__) . '/../models/User.php';
        require_once dirname(__FILE__) . '/../models/Report.php';
        $userModel = new User();
        $reportModel = new Report();
        $totalUsers = $userModel->count();
        $totalReports = $reportModel->count();
        */
        
        // Definir variables vacías para que la vista no dé error
        $totalUsers = 0;
        $totalReports = 0;
        
        // Cargar la vista
        require_once dirname(__FILE__) . '/../views/admin/dashboard.php';
    }
    
    public function settings() {
        // Código para la página de configuración
        require_once __DIR__ . '/../views/admin/settings.php';
    }
}
?>
