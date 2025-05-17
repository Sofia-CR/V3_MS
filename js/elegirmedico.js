document.addEventListener("DOMContentLoaded", function() {
    // Recuperamos el turno desde localStorage
    const turno = localStorage.getItem("turnoSeleccionado");

    if (!turno) {
        console.error("No se encontró el turno en localStorage");
        return;
    }

    console.log("Turno recibido desde localStorage:", turno);

    fetch("../php/eligemedico.php?turno=" + turno)
        .then(response => response.text())
        .then(data => {
            document.getElementById("medicos-container").innerHTML = data;
            document.querySelectorAll(".medico-card").forEach(card => {
                card.addEventListener("click", function() {
                    const doctor = this.getAttribute("data-doctor-id");

                    console.log("🩺 Médico seleccionado - ID:", doctor); 

                    // Guardar el médico seleccionado en localStorage
                    localStorage.setItem("doctor", doctor);
                });
            });
        })
        .catch(error => console.error("Error al cargar los médicos:", error));
});

// Asegurarte de que el botón de continuar existe
document.getElementById("continuar-btn")?.addEventListener("click", function() {
    const doctor = localStorage.getItem("doctor");
    const turno = localStorage.getItem("turnoSeleccionado");

    console.log("Médico guardado en `localStorage`, listo para continuar:", doctor);
    console.log("Turno guardado en `localStorage`:", turno);

    if (!doctor) {
        alert("Por favor, selecciona un médico antes de continuar.");
        return;
    }

    // Si el médico está seleccionado, redirigir a elegir fecha con turno y médico en la URL
    window.location.href = 'elegirfecha.html';
});
















