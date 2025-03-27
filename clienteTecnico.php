<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comunicación Cliente-Técnico</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h3 class="text-center">Comunicación Cliente-Técnico</h3>

                <!-- Sección para enviar mensajes (Cliente) -->
                <div class="mb-4">
                    <h5>Enviar un mensaje</h5>
                    <form id="mensajeForm">
                        <textarea id="mensaje" class="form-control my-2" placeholder="Escribe tu mensaje aquí..." required></textarea>
                        <button type="submit" class="btn btn-primary">Enviar</button>
                    </form>
                </div>

                <!-- Sección para ver mensajes y respuestas -->
                <div id="comunicacionSection">
                    <h5>Historial de comunicación</h5>
                    <div id="mensajes">
                        <!-- Aquí se cargarán los mensajes dinámicamente -->
                        <div class="message-box client-message">
                            <p><strong>Cliente:</strong> Hola, tengo un problema con mi equipo.</p>
                            <small>10/10/2023 14:30</small>
                        </div>
                        <div class="message-box tech-response">
                            <p><strong>Técnico:</strong> Hola, ¿podrías describir el problema con más detalle?</p>
                            <small>10/10/2023 15:00</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Simulación de envío de mensajes (esto debería ser reemplazado por una llamada a un backend)
        document.getElementById("mensajeForm").addEventListener("submit", function (e) {
            e.preventDefault();
            const mensaje = document.getElementById("mensaje").value;

            if (mensaje.trim() === "") {
                alert("Por favor, escribe un mensaje.");
                return;
            }

            // Crear un nuevo mensaje del cliente
            const nuevoMensaje = document.createElement("div");
            nuevoMensaje.classList.add("message-box", "client-message");
            nuevoMensaje.innerHTML = `
                <p><strong>Cliente:</strong> ${mensaje}</p>
                <small>${new Date().toLocaleString()}</small>
            `;

            // Agregar el mensaje al historial
            document.getElementById("mensajes").appendChild(nuevoMensaje);

            // Limpiar el campo de texto
            document.getElementById("mensaje").value = "";

            // Simular una respuesta del técnico (esto debería ser manejado por el backend)
            setTimeout(() => {
                const respuestaTecnico = document.createElement("div");
                respuestaTecnico.classList.add("message-box", "tech-response");
                respuestaTecnico.innerHTML = `
                    <p><strong>Técnico:</strong> Hemos recibido tu mensaje. Nos pondremos en contacto contigo pronto.</p>
                    <small>${new Date().toLocaleString()}</small>
                `;
                document.getElementById("mensajes").appendChild(respuestaTecnico);
            }, 2000); // Simula un retraso de 2 segundos
        });
    </script>
</body>
</html>