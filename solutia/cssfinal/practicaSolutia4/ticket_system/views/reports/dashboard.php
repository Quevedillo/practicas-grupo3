<?php
// Incluir header
require_once 'ticket_system/views/partials/header.php';

// Asegurarse de que las variables estén definidas
if (!isset($kpis)) $kpis = [];
if (!isset($trends)) $trends = [];
if (!isset($priorityStats)) $priorityStats = [];

// Agregar script de Chart.js para los gráficos
?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4a90e2;
            --success-color: #2ecc71;
            --warning-color: #f1c40f;
            --danger-color: #e74c3c;
            --info-color: #3498db;
            --text-color: #2c3e50;
            --light-text: #666;
            --bg-color: #f8f9fa;
            --card-bg: #fff;
            --border-color: rgba(0,0,0,0.1);
            --shadow: 0 2px 4px rgba(0,0,0,0.05);
            --transition: all 0.2s ease-in-out;
            --gradient-start: linear-gradient(135deg, #4a90e2, #357abd);
            --gradient-end: linear-gradient(135deg, #357abd, #4a90e2);
        }

        .navbar {
            padding: 0.6rem 0.8rem;
            background-color: rgba(255, 255, 255, 0.98);
            box-shadow: var(--shadow);
            border-bottom: 1px solid rgba(0,0,0,0.05);
            height: 56px;
        }

        .navbar-brand img {
            max-height: 32px;
            margin-right: 0.6rem;
        }

        .nav-link {
            padding: 0.4rem 0.9rem;
            font-size: 0.95rem;
            border-radius: 4px;
            transition: var(--transition);
            margin: 0 0.3rem;
        }

        .nav-link:hover {
            background-color: rgba(0, 0, 0, 0.03);
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .nav-link.active {
            background-color: rgba(0, 0, 0, 0.05);
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .navbar-toggler {
            padding: 0.3rem 0.5rem;
            border: 1px solid rgba(0,0,0,0.1);
            border-radius: 4px;
        }

        .navbar-toggler:focus {
            box-shadow: none;
        }

        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(0,0,0,0.5)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        .theme-toggle {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.3rem 0.8rem;
            border-radius: 4px;
            cursor: pointer;
            transition: var(--transition);
        }

        .theme-toggle:hover {
            background-color: rgba(0, 0, 0, 0.03);
        }

        .theme-toggle i {
            font-size: 1.1rem;
            color: rgba(0, 0, 0, 0.6);
        }

        .dropdown-toggle {
            padding: 0.3rem 0.8rem;
            font-size: 0.85rem;
            border-radius: 4px;
            border: 1px solid rgba(0,0,0,0.1);
        }

        .dropdown-toggle:hover {
            border-color: rgba(0,0,0,0.2);
        }

        .dropdown-menu {
            border-radius: 8px;
            border: 1px solid rgba(0,0,0,0.05);
            box-shadow: var(--shadow);
        }

        .dropdown-item {
            padding: 0.4rem 0.8rem;
            font-size: 0.85rem;
            border-radius: 4px;
        }

        .dropdown-item:hover {
            background-color: rgba(0, 0, 0, 0.03);
            color: var(--text-color);
        }

        .nav-link:hover {
            background-color: rgba(0, 0, 0, 0.05);
            transform: translateY(-1px);
        }

        .nav-link.active {
            background-color: rgba(0, 0, 0, 0.1);
            border-radius: 6px;
        }

        .card {
            border-radius: 8px;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow);
            transition: var(--transition);
            margin-bottom: 1rem;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .card.text-center {
            background-color: transparent;
            border: none;
            box-shadow: none;
            transition: var(--transition);
        }

        .card.text-center:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .card.text-center .card-body {
            padding: 1rem;
        }

        .card.text-center h2 {
            font-size: 1.8rem;
            margin: 0.5rem 0 0;
        }

        body.dark-mode {
            --primary-color: #4a90e2;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --danger-color: #e67e22;
            --info-color: #3498db;
            --text-color: #e0e0e0;
            --light-text: #999;
            --bg-color: #1a1a1a;
            --card-bg: #2d2d2d;
            --border-color: #444;
            --shadow: 0 4px 6px rgba(255,255,255,0.1);
        }

        .container {
            padding: 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        h1 {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--text-color);
            margin-bottom: 2rem;
            position: relative;
        }

        h1::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 80px;
            height: 4px;
            background: var(--gradient-start);
            opacity: 0.8;
        }

        .card {
            border-radius: 15px;
            border: 1px solid var(--border-color);
            transition: var(--transition);
            background: var(--card-bg);
            margin-bottom: 1.5rem;
            overflow: hidden;
            position: relative;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }

        .card-header {
            background: var(--card-bg);
            border-bottom: 2px solid var(--border-color);
            padding: 1.5rem;
            position: relative;
        }

        .card-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 2px;
            background: var(--gradient-start);
            opacity: 0.3;
        }

        .card-header h5 {
            margin: 0;
            font-weight: 600;
            color: var(--text-color);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .card-body {
            padding: 2rem;
        }

        .card.text-center {
            height: 100%;
            transition: var(--transition);
            position: relative;
        }

        .card.text-center::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--gradient-start);
            opacity: 0.1;
            z-index: 0;
        }

        .card.text-center .card-body {
            position: relative;
            z-index: 1;
        }

        .card.text-center:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }

        .card.text-center .card-title {
            font-size: 1.1rem;
            font-weight: 500;
            color: white;
            margin-bottom: 1rem;
            position: relative;
        }

        .card.text-center h2 {
            font-size: 2.5rem;
            font-weight: 700;
            color: white;
            margin: 0;
            position: relative;
        }

        .chart-container {
            height: 350px;
            position: relative;
        }

        .btn {
            padding: 0.8rem 1.5rem;
            font-weight: 500;
            transition: var(--transition);
            border-radius: 10px;
            position: relative;
            overflow: hidden;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--gradient-end);
            opacity: 0.1;
            z-index: 0;
        }

        .btn i {
            position: relative;
            z-index: 1;
            margin-right: 0.75rem;
            font-size: 1.25rem;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .btn-primary {
            background: linear-gradient(145deg, var(--primary-color), var(--primary-color) 90%);
            border: none;
            color: white;
        }

        .btn-success {
            background: linear-gradient(145deg, var(--success-color), var(--success-color) 90%);
            border: none;
            color: white;
        }

        .btn-warning {
            background: linear-gradient(145deg, var(--warning-color), var(--warning-color) 90%);
            border: none;
            color: white;
        }

        .btn-info {
            background: linear-gradient(145deg, var(--info-color), var(--info-color) 90%);
            border: none;
            color: white;
        }

        .btn-danger {
            background: linear-gradient(145deg, var(--danger-color), var(--danger-color) 90%);
            border: none;
            color: white;
        }

        .fas {
            font-size: 1.25rem;
            color: var(--text-color);
        }

        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            .card {
                margin-bottom: 1rem;
            }

            .chart-container {
                height: 300px;
            }
        }

        @media (max-width: 576px) {
            h1 {
                font-size: 2rem;
            }

            .card.text-center .card-title {
                font-size: 1rem;
            }

            .card.text-center h2 {
                font-size: 2rem;
            }
        }
        /* Estilos para el bloque de Informes */
        .card-header .text-primary {
            color: #000000 !important; /* Negro en modo claro */
        }
        
        body.dark-mode .card-header .text-primary {
            color: #ffffff !important; /* Blanco en modo oscuro */
        }
        
        /* Estilo para el texto descriptivo en el bloque de Informes */
        .card-body p {
            color: #000000; /* Negro en modo claro */
        }
        
        body.dark-mode .card-body p {
            color: #ffffff !important; /* Blanco en modo oscuro */
        }
    </style>

    <div class="container mt-4">
        <h1>Dashboard de Tickets</h1>
        
        <!-- KPIs principales -->
        <div class="row mt-4">
            <div class="col-md-3">
                <div class="card text-center bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total de Tickets</h5>
                        <h2><?php echo $kpis['total_tickets']; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center bg-warning text-white">
                    <div class="card-body">
                        <h5 class="card-title">Tickets Abiertos</h5>
                        <h2><?php echo $kpis['open_tickets']; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">Tickets Cerrados</h5>
                        <h2><?php echo $kpis['closed_tickets']; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center bg-info text-white">
                    <div class="card-body">
                        <h5 class="card-title">Tiempo Promedio</h5>
                        <h2><?php echo $kpis['avg_resolution_time']; ?> hrs</h2>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Gráficos -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Tickets por Estado</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Tickets por Prioridad</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="priorityChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Tendencia de Tickets (Últimos 6 meses)</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="trendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Enlace a Informes Personalizados -->
        <div class="row mt-4 mb-4">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h5 class="m-0 font-weight-bold text-primary">Informes</h5>
                    </div>
                    <div class="card-body">
                        <p>Para generar informes personalizados con filtros avanzados, haga clic en el siguiente botón:</p>
                        <a href="index.php?controller=report&action=custom" class="btn btn-primary">
                            <i class="fas fa-file-alt"></i> Informes Personalizados
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
<?php
// Incluir footer
require_once 'ticket_system/views/partials/footer.php';
?>
    
    <script>
        // Datos para los gráficos
        const statusLabels = [<?php 
            if (isset($kpis['tickets_by_status']) && is_array($kpis['tickets_by_status'])) {
                foreach ($kpis['tickets_by_status'] as $status) {
                    $statusLabel = '';
                    switch ($status['status']) {
                        case 'open': $statusLabel = 'Abierto'; break;
                        case 'in_progress': $statusLabel = 'En Progreso'; break;
                        case 'resolved': $statusLabel = 'Resuelto'; break;
                        case 'closed': $statusLabel = 'Cerrado'; break;
                        default: $statusLabel = $status['status']; break;
                    }
                    echo "'" . $statusLabel . "',";
                }
            } else {
                echo "'Abierto','En Progreso','Resuelto','Cerrado'";
            }
        ?>];
        
        const statusValues = [<?php 
            if (isset($kpis['tickets_by_status']) && is_array($kpis['tickets_by_status'])) {
                foreach ($kpis['tickets_by_status'] as $status) {
                    echo ($status['total'] ?? 0) . ",";
                }
            } else {
                echo "0,0,0,0";
            }
        ?>];
        
        const priorityLabels = [<?php 
            if (isset($priorityStats) && is_array($priorityStats)) {
                foreach ($priorityStats as $p) {
                    echo "'" . ucfirst($p['priority'] ?? 'Desconocido') . "',";
                }
            } else {
                echo "'Baja','Media','Alta','Urgente'";
            }
        ?>];
        
        const priorityValues = [<?php 
            if (isset($priorityStats) && is_array($priorityStats)) {
                foreach ($priorityStats as $p) {
                    echo ($p['total'] ?? 0) . ",";
                }
            } else {
                echo "0,0,0,0";
            }
        ?>];
        
        const trendLabels = [<?php 
            if (isset($trends) && is_array($trends)) {
                foreach ($trends as $trend) {
                    echo "'" . ($trend['period'] ?? '') . "',";
                }
            }
        ?>];
        
        const trendTickets = [<?php 
            if (isset($trends) && is_array($trends)) {
                foreach ($trends as $trend) {
                    echo ($trend['total_tickets'] ?? 0) . ",";
                }
            }
        ?>];
        
        const trendClosed = [<?php 
            if (isset($trends) && is_array($trends)) {
                foreach ($trends as $trend) {
                    echo ($trend['closed_tickets'] ?? 0) . ",";
                }
            }
        ?>];
        
        // Crear gráficos
        window.onload = function() {
            // Gráfico de estados
            if (statusLabels.length > 0) {
                new Chart(document.getElementById('statusChart'), {
                    type: 'pie',
                    data: {
                        labels: statusLabels,
                        datasets: [{
                            label: 'Tickets por Estado',
                            data: statusValues,
                            backgroundColor: [
                                'rgba(255, 206, 86, 0.7)', // Amarillo para Abierto
                                'rgba(54, 162, 235, 0.7)', // Azul para En Progreso
                                'rgba(75, 192, 192, 0.7)', // Verde claro para Resuelto
                                'rgba(153, 102, 255, 0.7)' // Morado para Cerrado
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'right',
                            },
                            title: {
                                display: true,
                                text: 'Distribución por Estado'
                            }
                        }
                    }
                });
            }
            
            // Gráfico de prioridades
            if (priorityLabels.length > 0) {
                new Chart(document.getElementById('priorityChart'), {
                    type: 'bar',
                    data: {
                        labels: priorityLabels,
                        datasets: [{
                            label: 'Tickets por Prioridad',
                            data: priorityValues,
                            backgroundColor: [
                                'rgba(75, 192, 192, 0.7)',
                                'rgba(255, 206, 86, 0.7)',
                                'rgba(255, 159, 64, 0.7)',
                                'rgba(255, 99, 132, 0.7)'
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                display: false
                            },
                            title: {
                                display: true,
                                text: 'Distribución por Prioridad'
                            }
                        }
                    }
                });
            }
            
            // Gráfico de tendencias
            if (trendLabels.length > 0) {
                new Chart(document.getElementById('trendChart'), {
                    type: 'line',
                    data: {
                        labels: trendLabels,
                        datasets: [{
                            label: 'Total Tickets',
                            data: trendTickets,
                            borderColor: 'rgba(54, 162, 235, 1)',
                            backgroundColor: 'rgba(54, 162, 235, 0.2)',
                            fill: true
                        }, {
                            label: 'Tickets Cerrados',
                            data: trendClosed,
                            borderColor: 'rgba(75, 192, 192, 1)',
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Tendencia de Tickets'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }
        };
    </script>
</body>
</html>
