document.getElementById('loginForm').addEventListener('submit', function(event) {
    event.preventDefault();
    
    const usernameInput = document.getElementById('username');
    const passwordInput = document.getElementById('password');
    const mensajeError = document.getElementById('mensaje-error');
    
    fetch("../php/validarLogin.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ username: usernameInput.value, password: passwordInput.value })
    })
    .then(response => response.json())
    .then(data => {
        console.log("Respuesta del servidor:", data);

        if (data.success) {  
            localStorage.setItem("curpUsuario", data.curp);
            window.location.href = 'menu.html'; 
        } else {
            mensajeError.textContent = "Usuario o contraseña incorrectos. Inténtalo de nuevo.";
            mensajeError.style.display = "block";
            setTimeout(() => {
                mensajeError.textContent = "";
                mensajeError.style.display = "none";
                usernameInput.value = ""; 
                passwordInput.value = ""; 
            }, 1000);
        }
    })
    .catch(error => {
        console.error("Error en la autenticación:", error);
        mensajeError.textContent = "Hubo un problema con la autenticación. Inténtalo más tarde.";
        mensajeError.style.display = "block";

        setTimeout(() => {
            mensajeError.textContent = "";
            mensajeError.style.display = "none";
            usernameInput.value = "";
            passwordInput.value = "";
        }, 2000);
    });
});





