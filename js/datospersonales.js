// Script que se ejecuta al cargar la página
window.onload = function() {
    fetch('../php/datospersonales.php', {
        method: 'GET',
        credentials: 'include'
    })
    .then(response => response.json())
    .then(data => {
        if (data.curp) {
            console.log("CURP desde sesión:", data.curp);
            document.getElementById('curp').value = data.curp;
        } else {
            console.error("Error:", data.error);
        }
    });
};

document.addEventListener("DOMContentLoaded", function () {
    // 1. OBTENER DATOS Y MOSTRARLOS
    fetch('../php/buscardatospersonales.php')
        .then(res => res.json())
        .then(data => {
            if (data.error) {
                console.log(data.error);
            } else {
                document.querySelector('[name="nombre"]').value = data.nombre || '';
                document.querySelector('[name="apaterno"]').value = data.apaterno || '';
                document.querySelector('[name="amaterno"]').value = data.amaterno || '';
                document.querySelector('[name="edad"]').value = data.edad || '';
                document.querySelector('[name="calle"]').value = data.calle || '';
                document.querySelector('[name="colonia"]').value = data.colonia || '';
                document.querySelector('[name="municipio"]').value = data.municipio || '';
                document.querySelector('[name="cp"]').value = data.cp || '';
                document.querySelector('[name="nExt"]').value = data.nexterior || '';
                document.querySelector('[name="nInt"]').value = data.ninterior || '';
                document.querySelector('[name="telefono"]').value = data.telefono || '';
                document.querySelector('[name="correo"]').value = data.correo || '';

                //Para el campo Estado (select)
                const estadoSelect = document.getElementById('estado');
                if (estadoSelect && data.estado) {
                    estadoSelect.value = data.estado;
                }
            }
        })
        .catch(err => console.error('Error al obtener los datos:', err));

    // 2. VALIDACIONES Y FORMATOS
    const form = document.querySelector("form");
    const cpInput = document.getElementById("cp");
    const telefonoInput = document.getElementById("telefono");
    const cpError = document.getElementById("cp-error");
    const telefonoError = document.getElementById("telefono-error");

    const soloLetrasInputs = ["nombre", "apaterno", "amaterno", "municipio"];
    const letrasNumerosInputs = ["calle", "colonia"];
    const soloNumerosInputs = ["edad", "numExterior", "numInterior"];

    // Formatear campos de solo letras
    soloLetrasInputs.forEach(id => {
        const input = document.getElementById(id);
        if (input) {
            input.addEventListener("input", () => {
                input.value = input.value.toUpperCase().replace(/[^A-ZÑÁÉÍÓÚ ]/g, '');
            });
        }
    });

    // Formatear campos de letras y números
    letrasNumerosInputs.forEach(id => {
        const input = document.getElementById(id);
        if (input) {
            input.addEventListener("input", () => {
                input.value = input.value.toUpperCase().replace(/[^A-Z0-9ÑÁÉÍÓÚ ]/g, '');
            });
        }
    });

    // Validar solo números en inputs
    soloNumerosInputs.forEach(id => {
        const input = document.getElementById(id);
        if (input) {
            input.addEventListener("keydown", function (e) {
                const invalidKeys = ["e", "E", "+", "-", ".", ","];
                const controlKeys = ["Backspace", "ArrowLeft", "ArrowRight", "Tab", "Delete"];
                if (invalidKeys.includes(e.key) || (!controlKeys.includes(e.key) && isNaN(parseInt(e.key)))) {
                    e.preventDefault();
                }
            });
            input.addEventListener("input", function () {
                let cleaned = this.value.replace(/[^0-9]/g, "");
                let value = parseInt(cleaned);
                if (!isNaN(value)) {
                    if (id === "edad" && value > 150) value = 150;
                    this.value = value;
                } else {
                    this.value = "";
                }
            });
        }
    });

    // Validación para Código Postal
    cpInput.addEventListener("input", function () {
        let cleaned = this.value.replace(/[^0-9]/g, "").slice(0, 5);
        this.value = cleaned;
        cpError.style.display = (this.value.length > 0 && this.value.length < 5) ? "block" : "none";
    });

    // Validación para Teléfono
    telefonoInput.addEventListener("input", function () {
        let cleaned = this.value.replace(/[^0-9]/g, "").slice(0, 10);
        this.value = cleaned;
        telefonoError.style.display = (this.value.length > 0 && this.value.length < 10) ? "block" : "none";
    });

    // Validaciones finales antes de enviar el formulario
    form.addEventListener("submit", function (e) {
        let valid = true;

        if (telefonoInput.value.length !== 10) {
            telefonoError.style.display = "block";
            telefonoInput.focus();
            valid = false;
        } else {
            telefonoError.style.display = "none";
        }

        if (cpInput.value.length > 0 && cpInput.value.length < 5) {
            cpError.style.display = "block";
            cpInput.focus();
            valid = false;
        } else {
            cpError.style.display = "none";
        }

        if (!valid) e.preventDefault();
    });
});
