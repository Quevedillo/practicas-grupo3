<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <h3 class="text-center">Iniciar Sesión</h3>
                <form action="backend/login.php" method="POST">
                    <input type="email" name="correo" class="form-control my-2" placeholder="Correo" required>
                    <input type="password" name="contraseña" class="form-control my-2" placeholder="Contraseña" required>
                    <button type="submit" class="btn btn-primary w-100">Ingresar</button>
                    <a href="#" class="d-block text-center mt-2" onclick="toggleRecuperar()">¿Olvidaste tu contraseña?</a>
                </form>
                <div id="recuperarDiv" style="display: none;" class="mt-3">
                    <form action="backend/recuperar.php" method="POST">
                        <input type="email" name="correo_recuperar" class="form-control my-2" placeholder="Introduce tu correo" required>
                        <button type="submit" class="btn btn-warning w-100">Recuperar contraseña</button>
                    </form>
                </div>
                <button class="btn btn-link w-100 mt-3" onclick="toggleRegistro()">Registrarse</button>
            </div>

            <div class="col-md-4" id="registroDiv" style="display: none;">
                <h3 class="text-center">Registro</h3>
                <form action="backend/registro.php" method="POST">
                    <input type="text" name="username" class="form-control my-2" placeholder="Nombre completo" required>
                    <input type="email" name="email" class="form-control my-2" placeholder="Correo" required>
                    <input type="password" name="password" class="form-control my-2" placeholder="Contraseña" required>
                    <select name="rol" class="form-control my-2">
                        <option value="admin">Admin</option>
                        <option value="tech">Técnico</option>
                        <option value="client">Cliente</option>
                    </select>
                    <button type="submit" class="btn btn-success w-100">Registrarse</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
