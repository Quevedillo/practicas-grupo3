# Sistema de Tickets

Sistema de gestión de tickets para el seguimiento de incidencias.

## Estructura del Proyecto

```
ticket_system/
├── config/                  # Archivos de configuración
│   ├── config.php          # Configuración de la aplicación
│   └── database.php        # Configuración de la base de datos
├── controllers/            # Controladores de la aplicación
├── models/                 # Modelos de datos
├── views/                  # Vistas de la aplicación
│   ├── client/            # Vistas para clientes
│   ├── technician/        # Vistas para técnicos
│   └── errors/            # Páginas de error
├── assets/                 # Recursos estáticos
│   ├── css/               # Hojas de estilo
│   ├── js/                # Archivos JavaScript
│   └── img/               # Imágenes
├── .htaccess              # Configuración de Apache
└── index.php              # Punto de entrada de la aplicación
```

## Sistema de Rutas

Se ha implementado un sistema de enrutamiento que permite URLs amigables. Las rutas se definen en `index.php`.

### Rutas Disponibles

- `/` - Página de inicio
- `/login` - Inicio de sesión
- `/register` - Registro de usuarios
- `/dashboard` - Panel de control
- `/tickets` - Lista de tickets
- `/ticket/crear` - Crear nuevo ticket
- `/ticket/ver` - Ver detalles de un ticket
- `/perfil` - Perfil de usuario
- `/tecnico/dashboard` - Panel de control del técnico
- `/recuperar-contrasena` - Recuperación de contraseña
- `/reset-password` - Restablecer contraseña

## Configuración

1. Copiar el archivo `.env.example` a `.env` y configurar las variables de entorno.
2. Configurar la base de datos en `config/database.php`.
3. Asegurarse de que el módulo `mod_rewrite` esté habilitado en Apache.

## Requisitos

- PHP 7.4 o superior
- MySQL 5.7 o superior
- Servidor web Apache con mod_rewrite habilitado

## Instalación

1. Clonar el repositorio
2. Instalar dependencias con Composer: `composer install`
3. Configurar el servidor web para que apunte al directorio `public/`
4. Importar el esquema de la base de datos desde `database/schema.sql`
5. Configurar los permisos de escritura en los directorios necesarios

## Uso

1. Acceder a la aplicación a través del navegador
2. Iniciar sesión con las credenciales proporcionadas
3. Navegar por las diferentes secciones del sistema

## Notas de la Actualización

### Cambios en el Sistema de Rutas

- Se ha implementado un sistema de enrutamiento centralizado en `index.php`
- Todas las URLs ahora son relativas y se generan mediante la función `url()`
- Se ha añadido soporte para URLs amigables mediante `.htaccess`
- Se ha mejorado el manejo de errores con páginas personalizadas

### Mejoras de Seguridad

- Todas las rutas ahora pasan por el sistema de enrutamiento central
- Se ha mejorado la validación de entradas
- Se han corregido vulnerabilidades de inyección SQL

### Mejoras de Rendimiento

- Se ha implementado caché de archivos estáticos
- Se ha optimizado la carga de recursos
- Se ha reducido el número de consultas a la base de datos

## Soporte

Para soporte técnico, por favor contacte al administrador del sistema.
