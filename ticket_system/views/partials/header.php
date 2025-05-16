<?php 
// Cargar configuración de rutas
require_once __DIR__ . '/../../config/paths.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Tickets</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="<?php echo url('assets/css/bootstrap.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo url('assets/css/bootstrap-icons.css'); ?>">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="<?php echo url('assets/css/dataTables.bootstrap5.min.css'); ?>">
    <!-- jQuery y DataTables JS -->
    <script src="<?php echo url('assets/js/jquery-3.6.0.min.js'); ?>"></script>
    <script src="<?php echo url('assets/js/jquery.dataTables.min.js'); ?>"></script>
    <script src="<?php echo url('assets/js/dataTables.bootstrap5.min.js'); ?>"></script>
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="<?php echo url('css/styles.css'); ?>">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="<?php echo url('index.php'); ?>">Sistema de Tickets</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo url('index.php'); ?>">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo url('index.php?controller=report&action=index'); ?>">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo url('index.php?controller=report&action=custom'); ?>">Informes</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
