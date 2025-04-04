<?php
session_start();

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

require 'database.php';

// Obtener los datos actuales del usuario
$sql = "SELECT username, email FROM users WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $_SESSION['id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Procesar formulario
$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nuevoUsername = trim($_POST['username']);
    $nuevoEmail = trim($_POST['email']);
    $nuevaPassword = trim($_POST['password']);

    if (!empty($nuevoUsername) && !empty($nuevoEmail)) {
        $actualizaSQL = "UPDATE users SET username = :username, email = :email";

        $parametros = [
            'username' => $nuevoUsername,
            'email' => $nuevoEmail,
        ];

        if (!empty($nuevaPassword)) {
            $actualizaSQL .= ", password = :password";
            $parametros['password'] = password_hash($nuevaPassword, PASSWORD_DEFAULT);
        }

        $actualizaSQL .= " WHERE id = :id";
        $parametros['id'] = $_SESSION['id'];

        $stmt = $pdo->prepare($actualizaSQL);
        if ($stmt->execute($parametros)) {
            $mensaje = "Perfil actualizado correctamente.";
            // Actualizar datos para mostrar tras guardar
            $user['username'] = $nuevoUsername;
            $user['email'] = $nuevoEmail;
        } else {
            $mensaje = "Error al actualizar el perfil.";
        }
    } else {
        $mensaje = "Nombre de usuario y correo no pueden estar vacíos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Perfil</title>
    <link rel="stylesheet" href="estilodashboard.css">
</head>
<body>
    <div class="container">
        <header class="header">
            <div class="logo">
                <img src="https://camaradesevilla.com/wp-content/uploads/2024/07/S00-logo-Grupo-Solutia-v01-1.png" alt="Logo del Sistema">
            </div>
            <div class="header-right">
                <div class="theme-toggle">
                    <button id="theme-button">Modo Oscuro</button>
                </div>
                <div class="user-menu">
                    <span>USUARIO ▼</span>
                    <div class="user-dropdown">
                        <a href="logout.php">Cerrar Sesión</a>
                    </div>
                </div>
            </div>
        </header>

        <nav class="navbar">
            <ul>
                <li><a href="dashboard.php">Panel</a></li>
                <li><a href="#">Mis Tickets</a></li>
                <li><a href="gestionPerfilUsuario.php" class="active">Perfil</a></li>
                <li><a href="clienteTecnico.php">Comunicación</a></li>
            </ul>
        </nav>

        <main class="main-content">
            <div class="profile-settings">
                <h2>Gestión de Perfil</h2>
                <?php if (!empty($mensaje)): ?>
                    <p class="mensaje"><?php echo htmlspecialchars($mensaje); ?></p>
                <?php endif; ?>
                <form method="POST" action="gestionPerfilUsuario.php" class="profile-form">
                    <label for="username">Nombre de Usuario:</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>

                    <label for="email">Correo Electrónico:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

                    <label for="password">Nueva Contraseña (opcional):</label>
                    <input type="password" id="password" name="password" placeholder="Deja en blanco si no deseas cambiarla">

                    <button type="submit">Actualizar Perfil</button>
                </form>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const themeButton = document.getElementById('theme-button');
            const body = document.body;

            if (localStorage.getItem('darkMode') === 'enabled') {
                body.classList.add('dark-mode');
                themeButton.textContent = 'Modo Claro';
            }

            themeButton.addEventListener('click', () => {
                body.classList.toggle('dark-mode');
                const isDark = body.classList.contains('dark-mode');
                localStorage.setItem('darkMode', isDark ? 'enabled' : 'disabled');
                themeButton.textContent = isDark ? 'Modo Claro' : 'Modo Oscuro';
            });
        });
    </script>
</body>
</html>
