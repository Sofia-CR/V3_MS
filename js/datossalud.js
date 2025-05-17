window.onload = function () {
    fetch('../php/buscarsalud.php')
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error("Error al obtener los datos de salud:", data.error);
            } else {
                // Inputs numéricos
                document.getElementById('Estatura').value = data.estatura || '';
                document.getElementById('Peso').value = data.peso || '';
                document.getElementById('Cintura').value = data.cintura || '';

                // Selects
                if (data.sexo) document.getElementById('Sexo').value = data.sexo;
                if (data.gsangre) document.getElementById('GrupoSanguineo').value = data.gsangre;
                if (data.csueno) document.getElementById('CalidadSueno').value = data.csueno;
                if (data.diabetes) document.getElementById('Diabetes').value = data.diabetes;
                if (data.hipertension) document.getElementById('Hipertension').value = data.hipertension;
                if (data.calcohol) document.getElementById('ConsumoAlcohol').value = data.calcohol;
                if (data.ctabaco) document.getElementById('ConsumoTabaco').value = data.ctabaco;
                if (data.embarazo) document.getElementById('Embarazo').value = data.embarazo;
                if (data.nafisica) document.getElementById('NivelActividadFisica').value = data.nafisica;
                if (data.visitas) document.getElementById('VisitasMedicas').value = data.visitas;

                // Campos de texto
                document.getElementById('HabitosAlimenticios').value = data.halimen || '';
                document.getElementById('Alergias').value = data.alergias || '';
                document.getElementById('HistorialEnfermedades').value = data.enfermedades || '';
                document.getElementById('CirugiasPrevias').value = data.cirujias || '';
                document.getElementById('MedicaciónActual').value = data.mactual || '';
                document.getElementById('AntecedentesFamiliares').value = data.antfami || '';
                document.getElementById('HistorialLesiones').value = data.lesiones || '';
                document.getElementById('ProblemasRespiratorios').value = data.prespiratorios || '';
                document.getElementById('ProblemasCardiovasculares').value = data.pcardiovasculares || '';
                document.getElementById('EnfermedadesMentales').value = data.ementales || '';
                document.getElementById('AfeccionesDermatologicas').value = data.adermatologicas || '';
                document.getElementById('TrastornosSueño').value = data.tsueños || '';
            }
        })
        .catch(err => console.error('Error al obtener los datos de salud:', err));
};
