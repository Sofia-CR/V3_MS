document.addEventListener("DOMContentLoaded", () => {
    const curp = localStorage.getItem("curpUsuario");

    if (!curp) {
        alert("No se encontró la CURP del usuario. Redirigiendo al login...");
        window.location.href = "login.html";
        return;
    }

    // Verificar si la CURP existe en la base de datos (usuarios)
    fetch("../php/verificarUsuario.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ curp: curp })
    })
    .then(response => response.json())
    .then(data => {
    if (!data.existe) {
    // Crear el contenedor del mensaje
    const mensaje = document.createElement("div");
    mensaje.innerHTML = `
        <div class="no-horarios-wrapper">
            <p class="no-horarios-text">Para poder agendar una cita, primero debes llenar tus datos personales.</p>
        </div>
    `;

    // Insertar el mensaje antes de redirigir
    document.querySelector(".container").appendChild(mensaje);


    // Esperar 2 segundos y redirigir
    setTimeout(() => {
        window.location.href = "datospersonales.html";
    }, 2000);
} else {
    console.log("Usuario verificado:", curp);
}

})

    .catch(error => {
        console.error("Error al verificar usuario:", error);
        alert("Hubo un error al verificar el usuario.");
        window.location.href = "login.html";
    });
});


