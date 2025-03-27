<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asignar Ticket a Técnico</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container mt-5">

    <h2 class="mb-4">Asignar Ticket a Técnico</h2>

    <!-- Tabla para mostrar tickets -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID de Ticket</th>
                <th>Usuario</th>
                <th>Categoria</th>
                <th>Titulo</th>
                <th>Descripcion</th>
                <th>Prioridad</th>
                <th>Estado</th>
                <th>Fecha de creación</th>
                <th>Fecha de actualizacion</th>
            </tr>
        </thead>
        <tbody>
            <!--Crear conexion con la base de datos -->
            <tr>
                <!--Mostrar informacion de la tabla tickets mediante php-->
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>
                    <a href="asignarTecnico.php?=<?php echo $ticket['id']; ?>" class="btn btn-warning btn-sm">Asignar Técnico</a>
                </td>
            </tr>
        </tbody>
    </table>
    <a href="logout.php" class="btn btn-danger btn-sm mt-2">Cerrar Sesión</a>
</body>
</html>
