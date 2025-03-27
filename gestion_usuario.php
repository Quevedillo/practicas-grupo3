<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h2 class="text-center">Gestión de Usuarios</h2>
        <button class="btn btn-success mb-3" onclick="mostrarFormulario('crear')">Crear Usuario</button>
        
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Rol</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="tablaUsuarios"></tbody>
        </table>
        
        <div id="formularioUsuarios" style="display: none;" class="card p-4 mt-4">
            <form action="backend/gestion_usuarios.php" method="POST">
                <input type="hidden" name="accion" id="formularioAccion">
                <div class="mb-3" id="campoID" style="display: none;">
                    <label for="id" class="form-label">ID de Usuario</label>
                    <input type="text" name="id" id="id" class="form-control" readonly>
                </div>
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre</label>
                    <input type="text" name="nombre" id="nombre" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="correo" class="form-label">Correo</label>
                    <input type="email" name="correo" id="correo" class="form-control">
                </div>
                <div class="mb-3" id="campoContraseña">
                    <label for="contraseña" class="form-label">Contraseña</label>
                    <input type="password" name="contraseña" id="contraseña" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="rol" class="form-label">Rol</label>
                    <select name="rol" id="rol" class="form-control">
                        <option value="cliente">Cliente</option>
                        <option value="tecnico">Técnico</option>
                        <option value="tecnico">Administrador</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary w-100">Confirmar</button>
            </form>
        </div>
    </div>
</body>
</html>
