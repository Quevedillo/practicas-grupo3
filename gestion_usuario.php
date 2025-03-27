<?php
session_start();
require 'database.php'; // Asegúrate de tener este archivo con la conexión a la BD

// Verificar si el usuario es administrador
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Procesar el formulario de creación/edición
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $rol = $_POST['rol'];
    $accion = $_POST['accion'];
    
    // Validaciones básicas
    if (empty($nombre) || empty($correo) || empty($rol)) {
        $_SESSION['error'] = "Todos los campos son obligatorios";
    } else {
        try {
            if ($accion == 'crear') {
                // Validar contraseña solo para creación
                if (empty($_POST['contraseña'])) {
                    $_SESSION['error'] = "La contraseña es obligatoria para nuevos usuarios";
                } else {
                    $contraseña = password_hash($_POST['contraseña'], PASSWORD_DEFAULT);
                    
                    $stmt = $conn->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$nombre, $contraseña, $correo, $rol]);
                    $_SESSION['exito'] = "Usuario creado correctamente";
                }
            } elseif ($accion == 'editar' && !empty($_POST['id'])) {
                // Actualizar usuario (sin cambiar contraseña)
                $id = $_POST['id'];
                $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, role = ? WHERE id = ?");
                $stmt->execute([$nombre, $correo, $rol, $id]);
                $_SESSION['exito'] = "Usuario actualizado correctamente";
            }
        } catch(PDOException $e) {
            if ($e->getCode() == '23000') {
                $_SESSION['error'] = "El correo o nombre de usuario ya existe";
            } else {
                $_SESSION['error'] = "Error en la base de datos: " . $e->getMessage();
            }
        }
    }
    
    header("Location: gestion_usuario.php");
    exit();
}

// Procesar eliminación de usuario
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    
    try {
        // No permitir eliminarse a sí mismo
        if ($id != $_SESSION['user_id']) {
            $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$id]);
            $_SESSION['exito'] = "Usuario eliminado correctamente";
        } else {
            $_SESSION['error'] = "No puedes eliminarte a ti mismo";
        }
    } catch(PDOException $e) {
        $_SESSION['error'] = "Error al eliminar usuario: " . $e->getMessage();
    }
    
    header("Location: gestion_usuario.php");
    exit();
}

// Obtener todos los usuarios
$usuarios = [];
try {
    $stmt = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $_SESSION['error'] = "Error al cargar usuarios: " . $e->getMessage();
}

// Obtener usuario para edición si se solicita
$usuario_editar = null;
if (isset($_GET['editar'])) {
    $id = $_GET['editar'];
    try {
        $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $usuario_editar = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        $_SESSION['error'] = "Error al cargar usuario: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .action-btn {
            margin: 0 2px;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h2 class="text-center">Gestión de Usuarios</h2>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['exito'])): ?>
            <div class="alert alert-success"><?= $_SESSION['exito'] ?></div>
            <?php unset($_SESSION['exito']); ?>
        <?php endif; ?>

        <button class="btn btn-success mb-3" onclick="mostrarFormulario('crear')">Crear Usuario</button>
        
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Rol</th>
                    <th>Fecha Creación</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $usuario): ?>
                    <tr>
                        <td><?= htmlspecialchars($usuario['id']) ?></td>
                        <td><?= htmlspecialchars($usuario['username']) ?></td>
                        <td><?= htmlspecialchars($usuario['email']) ?></td>
                        <td><?= ucfirst(htmlspecialchars($usuario['role'])) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($usuario['created_at'])) ?></td>
                        <td>
                            <a href="gestion_usuario.php?editar=<?= $usuario['id'] ?>" class="btn btn-sm btn-warning action-btn">Editar</a>
                            <a href="gestion_usuario.php?eliminar=<?= $usuario['id'] ?>" class="btn btn-sm btn-danger action-btn" onclick="return confirm('¿Estás seguro de eliminar este usuario?')">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div id="formularioUsuarios" style="display: <?= ($usuario_editar || isset($_GET['crear'])) ? 'block' : 'none' ?>;" class="card p-4 mt-4">
            <form method="POST" action="gestion_usuario.php">
                <input type="hidden" name="accion" id="formularioAccion" value="<?= $usuario_editar ? 'editar' : 'crear' ?>">
                <input type="hidden" name="id" id="id" value="<?= $usuario_editar ? $usuario_editar['id'] : '' ?>">
                
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre de usuario</label>
                    <input type="text" name="nombre" id="nombre" class="form-control" required 
                           value="<?= $usuario_editar ? htmlspecialchars($usuario_editar['username']) : '' ?>">
                </div>
                
                <div class="mb-3">
                    <label for="correo" class="form-label">Correo electrónico</label>
                    <input type="email" name="correo" id="correo" class="form-control" required
                           value="<?= $usuario_editar ? htmlspecialchars($usuario_editar['email']) : '' ?>">
                </div>
                
                <div class="mb-3" id="campoContraseña" style="display: <?= $usuario_editar ? 'none' : 'block' ?>;">
                    <label for="contraseña" class="form-label">Contraseña</label>
                    <input type="password" name="contraseña" id="contraseña" class="form-control" <?= $usuario_editar ? '' : 'required' ?>>
                    <?php if ($usuario_editar): ?>
                        <small class="text-muted">Dejar en blanco para mantener la contraseña actual</small>
                    <?php endif; ?>
                </div>
                
                <div class="mb-3">
                    <label for="rol" class="form-label">Rol</label>
                    <select name="rol" id="rol" class="form-control" required>
                        <option value="client" <?= ($usuario_editar && $usuario_editar['role'] == 'client') ? 'selected' : '' ?>>Cliente</option>
                        <option value="tech" <?= ($usuario_editar && $usuario_editar['role'] == 'tech') ? 'selected' : '' ?>>Técnico</option>
                        <option value="admin" <?= ($usuario_editar && $usuario_editar['role'] == 'admin') ? 'selected' : '' ?>>Administrador</option>
                    </select>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="button" class="btn btn-secondary me-md-2" onclick="ocultarFormulario()">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Confirmar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function mostrarFormulario(accion) {
            document.getElementById('formularioUsuarios').style.display = 'block';
            document.getElementById('formularioAccion').value = accion;
            
            if (accion === 'crear') {
                document.getElementById('campoContraseña').style.display = 'block';
                document.getElementById('contraseña').required = true;
                document.getElementById('id').value = '';
                document.getElementById('nombre').value = '';
                document.getElementById('correo').value = '';
                document.getElementById('rol').value = 'client';
            }
        }
        
        function ocultarFormulario() {
            document.getElementById('formularioUsuarios').style.display = 'none';
            window.location.href = 'gestion_usuario.php';
        }
        
        // Mostrar formulario si hay errores después de enviar
        <?php if (isset($_SESSION['form_data'])): ?>
            mostrarFormulario('<?= $_SESSION['form_data']['accion'] ?>');
            <?php unset($_SESSION['form_data']); ?>
        <?php endif; ?>
    </script>
</body>
</html>