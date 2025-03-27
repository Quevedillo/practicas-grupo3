<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <h3 class="text-center">Registro</h3>
                <form action="backend/registro.php" method="POST">
                    <input type="text" name="nombre" class="form-control my-2" placeholder="Nombre completo" required>
                    <input type="email" name="correo" class="form-control my-2" placeholder="Correo" required>
                    <input type="password" name="contraseña" class="form-control my-2" placeholder="Contraseña" required>
                    <select name="rol" class="form-control my-2">
                        <option value="cliente">Cliente</option>
                    </select>
                    <button type="submit" class="btn btn-success w-100">Registrarse</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
