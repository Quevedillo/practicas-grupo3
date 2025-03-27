<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login y Recuperar Contraseña</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script>
        function toggleRecuperar() {
            var recuperarDiv = document.getElementById("recuperarDiv");
            recuperarDiv.style.display = recuperarDiv.style.display === "none" ? "block" : "none";
        }
    </script>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <img href="logo" alt="logo" src="./logo.png">
                <h3 class="text-center">Iniciar Sesión</h3>
                <form action="backend/login.php" method="POST">
                    <label>Usuario:</label>
                    <input type="user" name="usuario" class="form-control my-2" required>
                    <label>Contraseña:</label>
                    <input type="password" name="contraseña" class="form-control my-2" required>
                    <button type="submit" class="btn btn-primary w-100">Iniciar Sesión</button>
                    <a href="#" class="d-block text-center mt-2" onclick="toggleRecuperar()">¿Olvidó tu contraseña?</a>
                    <a href="registro.html" class="btn btn-link w-100 mt-3">Registrarse</a>
                </form>
                <div id="recuperarDiv" style="display: none;" class="mt-3">
                    <form action="backend/recuperar.php" method="POST">
                        <input type="email" name="correo_recuperar" class="form-control my-2" placeholder="Introduce tu correo" required>
                        <button type="submit" class="btn btn-warning w-100">Recuperar contraseña</button>
                    </form>
                </div>
                
            </div>
        </div>
    </div>
</body>
</html>