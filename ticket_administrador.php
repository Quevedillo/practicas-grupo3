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

    <form action="asignar_ticket.php" method="POST">
        <div class="mb-3">
            <label for="ticket" class="form-label">Seleccionar Ticket:</label>
            <select class="form-select" id="ticket" name="ticket_id" required>
                <option value="1">Ticket #1 - Problema con el sistema</option>
                <option value="2">Ticket #2 - Error en la base de datos</option>
                <option value="3">Ticket #3 - Fallo en el servidor</option>
            </select>
        </div>
    

        <div class="mb-3">
            <label for="tecnico" class="form-label">Seleccionar Técnico:</label>
            <select class="form-select" id="tecnico" name="tecnico_id" required>
                <option value="101">Técnico Juan Pérez</option>
                <option value="102">Técnico María Gómez</option>
                <option value="103">Técnico Carlos Ramírez</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Asignar Ticket</button>
    </form>

</body>
</html>