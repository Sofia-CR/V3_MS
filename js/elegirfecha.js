document.addEventListener("DOMContentLoaded", function () {
    console.log("El documento ha sido cargado correctamente.");

    const doctor = localStorage.getItem('doctor');
    const turno = localStorage.getItem('turnoSeleccionado');

    console.log("Doctor ID:", doctor);
    console.log("Turno:", turno);

    if (!doctor || !turno) {
        console.error("Faltan parámetros en la URL");
        return;
    }

    // Realizar fetch a PHP para obtener fechas ocupadas
    fetch('../php/obtenerfechas.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ doctor: doctor, turno: turno })
    })
        .then(response => {
            if (!response.ok) {
                console.error("Error en la respuesta del servidor:", response.status);
                return;
            }
            return response.json();
        })
        .then(data => {
            if (!data || !data.ocupadas) {
                console.error("Los datos recibidos no contienen la propiedad 'ocupadas'.");
                return;
            }

            const fechasOcupadas = data.ocupadas;
            console.log("Datos recibidos:", fechasOcupadas);

            // Agrupar citas por fecha y médico para identificar fechas completamente ocupadas
            const conteoPorFecha = {};
            fechasOcupadas.forEach(cita => {
                const clave = `${cita.fecha}_${cita.id_medico}`;
                if (!conteoPorFecha[clave]) {
                    conteoPorFecha[clave] = new Set();
                }
                conteoPorFecha[clave].add(cita.hora);
            });

            // Obtener fechas con 14 horarios ocupados
            const fechasBloqueadas = Object.entries(conteoPorFecha)
                .filter(([_, horas]) => horas.size === 14)
                .map(([clave]) => clave.split('_')[0]);

            console.log("Fechas completamente ocupadas:", fechasBloqueadas);

            let selectedDate = "";

            // Inicializar calendario
            $('#calendar').fullCalendar({
                locale: 'es',
                validRange: {
                    start: moment().startOf('month').format("YYYY-MM-DD"),
                    end: moment().add(3, 'months').endOf('month').format("YYYY-MM-DD"),
                },
                selectable: true,
                dayRender: function (date, cell) {
                    cell.empty();

                    // Bloquear días pasados
                    if (date.isBefore(moment(), 'day')) {
                        cell.addClass("fc-past");
                        cell.css({ "pointer-events": "none", "background-color": "#f0f0f0" });
                    }

                    // Bloquear días de otros meses
                    if (date.month() !== moment().month()) {
                        cell.addClass("fc-other-month");
                        cell.css("pointer-events", "none");
                    }

                    // Bloquear fechas completamente ocupadas
                    if (fechasBloqueadas.includes(date.format("YYYY-MM-DD"))) {
    cell.addClass("fecha-bloqueada");
    cell.css("background-color", "#d3d3d3");
    cell.css("pointer-events", "none");
}

                },
                select: function (startDate) {
                    const selectedCell = $(`.fc-day[data-date="${startDate.format('YYYY-MM-DD')}"]`);

                    // Verificar si la celda está bloqueada
                    if (selectedCell.hasClass("fc-past") || selectedCell.hasClass("fecha-bloqueada")) {
                        console.log("No se pueden seleccionar fechas pasadas o bloqueadas.");
                        return;
                    }

                    // Quitar color anterior si hay selección previa
                    if (selectedDate) {
                        $(`.fc-day[data-date="${selectedDate}"]`).css("background-color", "");
                    }

                    selectedDate = startDate.format("YYYY-MM-DD");
                    selectedCell.css("background-color", "#a4c8f1");

                    console.log("Fecha seleccionada:", selectedDate);
                    $('#selectedDate').text('Fecha seleccionada: ' + selectedDate).show();
                    $('#continueButton').show();

                    localStorage.setItem('selectedDate', selectedDate);
                }
            });

            // Botón para continuar a elegir horario
            document.getElementById("continueButton")?.addEventListener("click", function () {
                const selectedDate = localStorage.getItem("selectedDate");

                if (!selectedDate) {
                    alert("Por favor, selecciona una fecha antes de continuar.");
                    return;
                }

                console.log("Redirigiendo con fecha:", selectedDate);
                window.location.href = 'elegirhorario.html';
            });
        })
        .catch(error => {
            console.error("Error al obtener fechas:", error);
        });
});


















