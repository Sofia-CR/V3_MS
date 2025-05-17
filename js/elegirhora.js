document.addEventListener("DOMContentLoaded", function() {
    const doctor = localStorage.getItem("doctor");
    const turno = localStorage.getItem("turnoSeleccionado");
    const selectedDate = localStorage.getItem("selectedDate");
    const curpUsuario = localStorage.getItem("curpUsuario");

    if (!curpUsuario || !doctor || !turno || !selectedDate) {
        alert("Error: Faltan datos necesarios.");
        return;
    }

    cargarHorariosDisponibles();
});

function cargarHorariosDisponibles() {
    const doctor = localStorage.getItem("doctor");
    const turno = localStorage.getItem("turnoSeleccionado");
    const selectedDate = localStorage.getItem("selectedDate");
    const curpUsuario = localStorage.getItem("curpUsuario");

    // Llamar al backend para obtener horarios ocupados y disponibles
    fetch("../php/obtenerHora.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            curpUsuario: curpUsuario,
            doctor: doctor,
            turno: turno,
            selectedDate: selectedDate
        })
    })
    .then(response => response.json())
    .then(data => {
        const container = document.getElementById("horarios-container");
        container.innerHTML = ""; // Limpiar el contenedor antes de mostrar los nuevos horarios

        // Obtener horarios ocupados
        const horasOcupadas = data.horas_ocupadas;
        console.log("Horas ocupadas:", horasOcupadas);

        // Filtrar horarios disponibles, excluyendo los ocupados
        const horariosDisponibles = data.horarios_disponibles.filter(hora => {
            return !horasOcupadas.includes(hora);
        });

        // Verificar si hay horarios disponibles
        if (horariosDisponibles.length === 0) {
            container.innerHTML = `
                <div class="no-horarios-wrapper">
                    <p class="no-horarios-text">No hay horarios disponibles para esta fecha. Inténtalo nuevamente más tarde.</p>
                </div>
            `;
            return;
        }

        // Mostrar botones para horarios disponibles
        horariosDisponibles.forEach(hora => {
            const btn = document.createElement("button");
            btn.classList.add("btn", "btn-primary", "hour-button", "m-2");
            btn.textContent = hora;
            btn.onclick = function() {
                selectHour(hora);
            };

            const col = document.createElement("div");
            col.classList.add("col-auto");
            col.appendChild(btn);
            container.appendChild(col);
        });
    })
    .catch(error => {
        console.error("Error al obtener horarios:", error);
        alert("Hubo un problema al cargar los horarios. Intenta nuevamente.");
    });
}

function selectHour(hora) {
    const curpUsuario = localStorage.getItem("curpUsuario");
    const doctor = localStorage.getItem("doctor");
    const turno = localStorage.getItem("turnoSeleccionado");
    const fecha = localStorage.getItem("selectedDate");

    // Guardar la hora seleccionada
    localStorage.setItem("horaSeleccionada", hora);

    // Mostrar la hora seleccionada en la interfaz
    const selectedHourDiv = document.getElementById("selectedHour");
    selectedHourDiv.innerHTML = `
        <p class="hora-seleccionada">
            Has seleccionado la hora: <strong>${hora}</strong>
        </p>
    `;
}

function confirmAppointment() {
    const curpUsuario = localStorage.getItem("curpUsuario");
    const doctor = localStorage.getItem("doctor");
    const turno = localStorage.getItem("turnoSeleccionado");
    const selectedDate = localStorage.getItem("selectedDate");
    const horaSeleccionada = localStorage.getItem("horaSeleccionada");

    if (!curpUsuario || !doctor || !turno || !selectedDate || !horaSeleccionada) {
        alert("Error: Faltan datos necesarios para agendar la cita.");
        return;
    }

    fetch("../php/confirmacion.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            curpUsuario,
            id_medico: doctor,
            turno,
            fecha: selectedDate,
            hora: horaSeleccionada
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.paciente && data.medico) {
            const modal = document.getElementById("confirmationModal");
            const modalDetails = document.getElementById("modal-details");

            modalDetails.innerHTML = `
                <p><strong>Paciente:</strong> ${data.paciente}</p>
                <p><strong>Médico:</strong> ${data.medico}</p>
                <p><strong>Turno:</strong> ${turno}</p>
                <p><strong>Fecha:</strong> ${selectedDate}</p>
                <p><strong>Hora:</strong> ${horaSeleccionada}</p>
            `;

            modal.style.display = "flex";

            // Quitar mensaje anterior si existe
            const horaSeleccionadaElemento = document.querySelector(".hora-seleccionada");
            if (horaSeleccionadaElemento) {
                horaSeleccionadaElemento.remove();
            }

            // Agregar evento al botón del modal
            document.getElementById("confirm-final-button").onclick = finalizarAgendado;
        } else {
            alert("Hubo un error al obtener los datos.");
        }
    })
    .catch(error => {
        console.error("Error en la solicitud:", error);
       alert("Hubo un problema al confirmar la cita: " + error.message);
    });
}

function finalizarAgendado() {
    const curpUsuario = localStorage.getItem("curpUsuario");
    const doctor = localStorage.getItem("doctor");
    const turno = localStorage.getItem("turnoSeleccionado");
    const selectedDate = localStorage.getItem("selectedDate");
    const horaSeleccionada = localStorage.getItem("horaSeleccionada");

    if (!curpUsuario || !doctor || !turno || !selectedDate || !horaSeleccionada) {
        alert("Faltan datos para confirmar la cita.");
        return;
    }

    fetch("../php/agendarCita.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            curpUsuario,
            id_medico: doctor,
            turno,
            fecha: selectedDate,
            hora: horaSeleccionada
        })
    })
    .then(response => response.json())
    .then(resultado => {
        if (resultado.success) {
            console.log("Cita insertada correctamente.");

            // Cerrar modal
            const modal = document.getElementById("confirmationModal");
            if (modal) modal.style.display = "none";

            showToast("✅ Cita agendada con éxito");
            cargarHorariosDisponibles(); // 🔄 Actualiza la lista de horarios

            // Eliminar mensaje de hora seleccionada
            const horaSeleccionadaElemento = document.querySelector(".hora-seleccionada");
            if (horaSeleccionadaElemento) horaSeleccionadaElemento.remove();
        } else {
            console.log("Error al insertar la cita:", resultado.mensaje);
            alert("Hubo un problema al guardar la cita. " + resultado.mensaje);  // Mostrar alert solo si hay un problema
        }
    })
    .catch(error => {
        console.error("Error al llamar a agendarCita.php:", error);
        alert("Hubo un problema al confirmar la cita: " + error.message);
    });
}

function showToast(message) {
    var toast = document.getElementById('toast');
    toast.innerHTML = message;
    toast.classList.add('show');
    setTimeout(function() {
        toast.classList.remove('show');
    }, 1000); // El toast desaparecerá después de 3 segundos
}

function closeModal() {
    document.getElementById("confirmationModal").style.display = "none";
}
