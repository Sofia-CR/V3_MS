document.getElementById('formulario').addEventListener('submit', async function (e) {
    e.preventDefault(); // Prevenir el envío del formulario

    const formData = new FormData(this); // Obtener los datos del formulario
    
    try {
        const respuesta = await fetch('../php/registro.php', {
            method: 'POST',
            body: formData
        });

        // Verifica que la respuesta sea exitosa
        if (!respuesta.ok) {
            throw new Error('Error en la respuesta del servidor');
        }

        const textoRespuesta = await respuesta.text();  
        console.log("Texto de la respuesta:", textoRespuesta);  // Ver qué devuelve el servidor

        try {
            const resultado = JSON.parse(textoRespuesta);  // Intentar parsear la respuesta como JSON
            console.log("Resultado (respuesta JSON):", resultado);

            const mensajeP = document.getElementById('mensaje-error'); // Apuntar al <p id="mensaje-error">

            if (resultado.tipo === 'success') {
                // Limpiar los campos de CURP y Contraseña antes de redirigir
                document.getElementById('curp').value = '';
                document.getElementById('password').value = '';
                
                // Redirigir inmediatamente a la página de login sin mostrar ningún mensaje
                window.location.href = 'login.html';
            } else {
                // Si hay un error, mostrar el mensaje de error
                mensajeP.style.color = 'red';
                mensajeP.textContent = resultado.mensaje;

                setTimeout(() => {
                    // Ocultar el mensaje de error después de 3 segundos
                    mensajeP.textContent = '';
                    // Limpiar los campos de texto **después** de que desaparezca el mensaje de error
                    document.getElementById('curp').value = '';
                    document.getElementById('password').value = '';
                }, 3000); // 3 segundos para desaparecer el mensaje de error
            }
        } catch (error) {
            console.error("Error al parsear JSON:", error);
            const mensajeP = document.getElementById('mensaje-error');
            mensajeP.style.color = 'red';
            mensajeP.textContent = "Hubo un problema con la respuesta del servidor. No se pudo procesar correctamente.";
            
            setTimeout(() => {
                // Ocultar el mensaje de error después de 3 segundos
                mensajeP.textContent = '';
                // Limpiar los campos de texto **después** de que desaparezca el mensaje de error
                document.getElementById('curp').value = '';
                document.getElementById('password').value = '';
            }, 3000);
        }
        
    } catch (error) {
        console.error("Error en la solicitud:", error);
        const mensajeP = document.getElementById('mensaje-error');
        mensajeP.style.color = 'red';
        mensajeP.textContent = "Hubo un problema con la solicitud. Por favor, intentalo más tarde.";
        
        setTimeout(() => {
            // Ocultar el mensaje de error después de 3 segundos
            mensajeP.textContent = '';
            // Limpiar los campos de texto **después** de que desaparezca el mensaje de error
            document.getElementById('curp').value = '';
            document.getElementById('password').value = '';
        }, 3000); // 3 segundos para desaparecer el mensaje de error
    }
});















