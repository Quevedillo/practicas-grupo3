<?php
// Iniciar sesión si es necesario
session_start();

// Aquí deberías incluir la lógica para verificar si el usuario está autenticado
$nombreUsuario = "Usuario"; // Reemplaza esto con el nombre real del usuario

// Procesar el formulario cuando se envía
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    echo "Formulario enviado. Procesar los datos aquí.";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Tickets</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 800px;
            margin: 0 auto;
            border: 1px solid #ccc;
        }
        .header {
            background-color: #f0f0f0;
            padding: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .nav {
            background-color: #e0e0e0;
            padding: 10px;
        }
        .content {
            padding: 20px;
        }
        form {
            display: grid;
            gap: 10px;
        }
        input[type="text"], select, textarea {
            width: 100%;
            padding: 5px;
        }
        textarea {
            height: 100px;
        }
        .buttons {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div>LOGO</div>
            <div><?php echo $nombreUsuario; ?> ▼</div>
        </div>
        <div class="nav">
            <a href="#">Panel</a> |
            <a href="#">Mis Tickets</a> |
            <a href="#">Perfil</a>
        </div>
        <div class="content">
            <h2>Nuevo Ticket</h2>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
                <div>
                    <label for="titulo">Título:</label>
                    <input type="text" id="titulo" name="titulo" required>
                </div>
                <div>
                    <label for="categoria">Categoría:</label>
                    <select id="categoria" name="categoria" required>
                        <option value="">Seleccione una categoría</option>
                        <option value="1">Hardware</option>
                        <option value="2">Software</option>
                        <option value="2">Red</option>
                        <option value="2">Otro</option>
                        <!-- Añade más opciones según sea necesario -->
                    </select>
                </div>
                <div>
                    <label for="prioridad">Prioridad:</label>
                    <select id="prioridad" name="prioridad" required>
                        <option value="">Seleccione una prioridad</option>
                        <option value="baja">Baja</option>
                        <option value="media">Media</option>
                        <option value="alta">Alta</option>
                        <option value="alta">URGENTE</option>
                    </select>
                </div>
                <div>
                    <label for="descripcion">Descripción:</label>
                    <textarea id="descripcion" name="descripcion" required></textarea>
                </div>
                <div>
                    <label for="archivo">Adjuntar archivo:</label>
                    <input type="file" id="archivo" name="archivo">
                </div>
                <div class="buttons">
                    <button type="button" onclick="window.location.href='#';">Cancelar</button>
                    <button type="submit">Crear Ticket</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
