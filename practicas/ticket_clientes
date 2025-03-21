<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Creación y Seguimiento de Tickets</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script>
        function cargarTickets() {
            fetch('backend/lista_tickets.php')
                .then(response => response.json())
                .then(data => {
                    let tablaTickets = document.getElementById('tablaTickets');
                    tablaTickets.innerHTML = '';
                    data.forEach(ticket => {
                        tablaTickets.innerHTML += `
                            <tr>
                                <td>${ticket.id}</td>
                                <td>${ticket.asunto}</td>
                                <td>${ticket.estado}</td>
                                <td>${ticket.fecha_creacion}</td>
                                <td>
                                    <button class="btn btn-primary btn-sm" onclick="verDetalle(${ticket.id})">Ver</button>
                                </td>
                            </tr>`;
                    });
                });
        }

        function verDetalle(id) {
            window.location.href = `detalle_ticket.php?id=${id}`;
        }

        window.onload = cargarTickets;
    </script>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h2 class="text-center">Creación y Seguimiento de Tickets</h2>
        <button class="btn btn-success mb-3" onclick="document.getElementById('formularioTicket').style.display = 'block'">Nuevo Ticket</button>
        
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Asunto</th>
                    <th>Estado</th>
                    <th>Fecha de Creación</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="tablaTickets"></tbody>
        </table>
        
        <div id="formularioTicket" style="display: none;" class="card p-4 mt-4">
            <form action="backend/crear_ticket.php" method="POST">
                <div class="mb-3">
                    <label for="asunto" class="form-label">Asunto</label>
                    <input type="text" name="asunto" id="asunto" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción</label>
                    <textarea name="descripcion" id="descripcion" class="form-control" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary w-100">Enviar</button>
            </form>
        </div>
    </div>
</body>
</html>
