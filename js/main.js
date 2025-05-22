// Configuración de horas de la agenda
const horas = [
    '08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00'
];

// Fecha actual (puedes cambiarla para pruebas)
let fechaActual = new Date();

// Formatea la fecha a YYYY-MM-DD
function formatFecha(fecha) {
    return fecha.toISOString().split('T')[0];
}

// Formatea la fecha a texto largo en español
function fechaLarga(fecha) {
    return fecha.toLocaleDateString('es-ES', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
}

// Cargar terapeutas y citas y construir la agenda
async function cargarAgenda() {
    // 1. Obtener terapeutas
    const terapeutas = await fetch('../api/terapeutas.php').then(r => r.json());
    // 2. Obtener horarios
    let horarios = window.horariosAgenda;
    if (!horarios) {
        horarios = await fetch('../api/horarios.php').then(r => r.json());
        window.horariosAgenda = horarios;
    }
    // 3. Obtener citas del día
    let citas = await fetch(`../api/agendar.php?fecha=${formatFecha(fechaActual)}`).then(r => r.json());
    if (!Array.isArray(citas)) citas = [];

    // 4. Construir encabezado de la tabla
    const thead = document.createElement('thead');
    const trHead = document.createElement('tr');
    let thHora = document.createElement('th');
    thHora.textContent = 'Hora';
    trHead.appendChild(thHora);
    terapeutas.forEach(t => {
        let th = document.createElement('th');
        th.innerHTML = `${t.nombre}<br><span class="especialidad">${t.especialidad || ''}</span>`;
        trHead.appendChild(th);
    });
    thead.appendChild(trHead);

    // 5. Construir cuerpo de la tabla
    const tbody = document.createElement('tbody');
    horarios.forEach(horario => {
        let tr = document.createElement('tr');
        let tdHora = document.createElement('td');
        tdHora.textContent = `${horario.hora_inicio.substring(0,5)} - ${horario.hora_fin.substring(0,5)}`;
        tr.appendChild(tdHora);
        terapeutas.forEach(t => {
            let td = document.createElement('td');
            // Buscar cita para este terapeuta y bloque horario
            let cita = citas.find(c => c.terapeuta_id == t.id && c.hora_inicio.substring(0,5) === horario.hora_inicio.substring(0,5));
            if (cita) {
                td.innerHTML = `<div class=\"cita cita-clickable\" data-cita-id=\"${cita.id}\">${cita.paciente_nombre}<br><span class=\"rol\">(${t.especialidad ? t.especialidad.substring(0,3) : ''})</span></div>`;
            }
            tr.appendChild(td);
        });
        tbody.appendChild(tr);
    });

    // 6. Reemplazar la tabla en el DOM
    const tabla = document.querySelector('.agenda-table');
    tabla.innerHTML = '';
    tabla.appendChild(thead);
    tabla.appendChild(tbody);

    // 7. Actualizar la fecha en el encabezado
    document.getElementById('fecha-hoy').textContent = fechaLarga(fechaActual);
}

// MODALES Y FORMULARIOS
function abrirModal(id) {
    document.getElementById(id).classList.add('show');
}
function cerrarModal(id) {
    document.getElementById(id).classList.remove('show');
}

document.addEventListener('DOMContentLoaded', () => {
    cargarAgenda();

    // Botones para abrir modales
    document.querySelector('.menu .btn:nth-child(2)').onclick = () => abrirModal('modalPaciente');
    document.querySelector('.menu .btn:nth-child(3)').onclick = () => {
        cargarEspecialidades();
        abrirModal('modalTerapeuta');
    };

    // Botones para abrir modales adicionales
    document.querySelector('.menu .btn:nth-child(5)').onclick = () => {
        cargarEspecialidadesEnModal();
        abrirModal('modalEspecialidad');
    };
    document.querySelector('.menu .btn:nth-child(6)').onclick = () => {
        cargarListaTerapeutas();
        abrirModal('modalGestionarTerapeutas');
    };

    // Botones para abrir modal de horarios
    document.querySelector('.menu .btn:nth-child(4)').onclick = () => {
        cargarHorarios();
        abrirModal('modalHorarios');
    };

    // Botón para abrir modal de secuencia (nuevo flujo)
    document.querySelector('.menu .btn:nth-child(1)').onclick = () => {
        cargarPacientes();
        inicializarSecuenciaPasos();
        document.getElementById('msgSecuencia').innerHTML = '';
        document.getElementById('sugerenciasHorarios').innerHTML = '';
        abrirModal('modalSecuencia');
    };

    // Cerrar modales
    document.querySelectorAll('.close').forEach(el => {
        el.onclick = () => cerrarModal(el.getAttribute('data-close'));
    });
    window.onclick = function(event) {
        document.querySelectorAll('.modal').forEach(modal => {
            if (event.target === modal) cerrarModal(modal.id);
        });
    };

    // Enviar formulario paciente
    document.getElementById('formPaciente').onsubmit = async function(e) {
        e.preventDefault();
        const data = Object.fromEntries(new FormData(this));
        const res = await fetch('../api/pacientes.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        if (res.ok) {
            cerrarModal('modalPaciente');
            this.reset();
            alert('Paciente añadido correctamente');
        }
    };

    // Enviar formulario terapeuta
    document.getElementById('formTerapeuta').onsubmit = async function(e) {
        e.preventDefault();
        const data = Object.fromEntries(new FormData(this));
        const res = await fetch('../api/terapeutas.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        if (res.ok) {
            cerrarModal('modalTerapeuta');
            this.reset();
            alert('Terapeuta añadido correctamente');
            cargarAgenda();
        }
    };

    // Enviar formulario especialidad
    document.getElementById('formEspecialidad').onsubmit = async function(e) {
        e.preventDefault();
        const data = Object.fromEntries(new FormData(this));
        const res = await fetch('../api/especialidades.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        if (res.ok) {
            cerrarModal('modalEspecialidad');
            this.reset();
            alert('Especialidad añadida correctamente');
        }
    };

    // Enviar formulario horario (con duración)
    document.getElementById('formHorario').onsubmit = async function(e) {
        e.preventDefault();
        const data = Object.fromEntries(new FormData(this));
        const res = await fetch('../api/horarios.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        if (res.ok) {
            this.reset();
            cargarHorarios();
        } else {
            const error = await res.json();
            alert(error.error || 'Error al crear los horarios');
        }
    };

    // Enviar formulario de secuencia (solo frontend, sin backend aún)
    document.getElementById('formSecuencia').onsubmit = async function(e) {
        e.preventDefault();
        document.getElementById('msgSecuencia').innerHTML = '<span style="color:#888;">Procesando...</span>';
        // Aquí irá la llamada real al backend
        setTimeout(() => {
            document.getElementById('msgSecuencia').innerHTML = '<span style="color:green;">(Simulación) Secuencia agendada correctamente.</span>';
            this.reset();
        }, 1200);
    };

    // Añadir paso a la secuencia
    document.getElementById('btnAddPaso').onclick = () => {
        agregarPasoSecuencia();
    };

    // Buscar horarios disponibles (real)
    document.getElementById('btnBuscarHorarios').onclick = async function() {
        document.getElementById('msgSecuencia').innerHTML = '';
        document.getElementById('sugerenciasHorarios').innerHTML = '<span style="color:#888;">Buscando horarios...</span>';
        // Recolectar datos del formulario
        const paciente_id = document.getElementById('selectPaciente').value;
        const fecha = document.querySelector('#formSecuencia input[name="fecha"]').value;
        const pasos = [];
        document.querySelectorAll('#secuenciaPasos .paso-secuencia').forEach(div => {
            const especialidad_id = div.querySelector('select:nth-child(2)').value;
            const terapeuta_id = div.querySelector('select:nth-child(3)').value;
            pasos.push({ especialidad_id, terapeuta_id });
        });
        if (!paciente_id || !fecha || pasos.length === 0) {
            document.getElementById('sugerenciasHorarios').innerHTML = '<span style="color:#e53e3e;">Completa todos los campos y pasos.</span>';
            return;
        }
        // Llamar al backend
        const res = await fetch('../api/agendar_secuencia.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ paciente_id, fecha, pasos, accion: 'sugerir' })
        });
        const data = await res.json();
        if (data.opciones && data.opciones.length) {
            mostrarSugerenciasHorarios(data.opciones, paciente_id, fecha, pasos);
        } else {
            document.getElementById('sugerenciasHorarios').innerHTML = '<span style="color:#e53e3e;">No hay horarios disponibles que cumplan con la secuencia.</span>';
        }
    };
});

// Cargar especialidades en el select del modal terapeuta
async function cargarEspecialidades() {
    const select = document.getElementById('selectEspecialidad');
    select.innerHTML = '';
    const especialidades = await fetch('../api/especialidades.php').then(r => r.json());
    especialidades.forEach(e => {
        let opt = document.createElement('option');
        opt.value = e.id;
        opt.textContent = e.nombre;
        select.appendChild(opt);
    });
}

// Cargar lista de terapeutas en el modal de gestión
async function cargarListaTerapeutas() {
    const cont = document.getElementById('listaTerapeutas');
    cont.innerHTML = '<p>Cargando...</p>';
    const terapeutas = await fetch('../api/terapeutas.php').then(r => r.json());
    if (!terapeutas.length) {
        cont.innerHTML = '<p>No hay terapeutas registrados.</p>';
        return;
    }
    let html = '<table style="width:100%;border-collapse:collapse;">';
    html += '<tr><th>Nombre</th><th>Especialidad</th><th></th></tr>';
    terapeutas.forEach(t => {
        html += `<tr><td>${t.nombre}</td><td>${t.especialidad || ''}</td><td><button class='btn btn-danger' onclick='eliminarTerapeuta(${t.id})'><i class="fa fa-trash"></i></button></td></tr>`;
    });
    html += '</table>';
    cont.innerHTML = html;
}

// Eliminar terapeuta
window.eliminarTerapeuta = async function(id) {
    if (!confirm('¿Seguro que deseas eliminar este terapeuta?')) return;
    const res = await fetch(`../api/terapeutas.php?id=${id}`, { method: 'DELETE' });
    if (res.ok) {
        alert('Terapeuta eliminado');
        cargarListaTerapeutas();
        cargarAgenda();
    }
}

// Cargar lista de horarios en el modal y para la agenda
async function cargarHorarios() {
    const cont = document.getElementById('listaHorarios');
    if (cont) cont.innerHTML = '<p>Cargando...</p>';
    const horarios = await fetch('../api/horarios.php').then(r => r.json());
    if (cont) {
        if (!horarios.length) {
            cont.innerHTML = '<p>No hay horarios configurados.</p>';
        } else {
            let html = '<table style="width:100%;border-collapse:collapse;">';
            html += '<tr><th>Inicio</th><th>Fin</th><th></th></tr>';
            horarios.forEach(h => {
                html += `<tr><td>${h.hora_inicio.substring(0,5)}</td><td>${h.hora_fin.substring(0,5)}</td><td><button class='btn btn-danger' onclick='eliminarHorario(${h.id})'><i class="fa fa-trash"></i></button></td></tr>`;
            });
            html += '</table>';
            cont.innerHTML = html;
        }
    }
    window.horariosAgenda = horarios; // Para usar en la agenda
    cargarAgenda();
}

// Eliminar horario
window.eliminarHorario = async function(id) {
    if (!confirm('¿Seguro que deseas eliminar este horario?')) return;
    const res = await fetch(`../api/horarios.php?id=${id}`, { method: 'DELETE' });
    if (res.ok) {
        cargarHorarios();
    }
}

// Cargar pacientes en el select del modal secuencia
async function cargarPacientes() {
    const select = document.getElementById('selectPaciente');
    select.innerHTML = '';
    const pacientes = await fetch('../api/pacientes.php').then(r => r.json());
    pacientes.forEach(p => {
        let opt = document.createElement('option');
        opt.value = p.id;
        opt.textContent = p.nombre + (p.email ? ' (' + p.email + ')' : '');
        select.appendChild(opt);
    });
}

// Cargar secuencias en el select del modal secuencia
async function cargarSecuencias() {
    const select = document.getElementById('selectSecuencia');
    select.innerHTML = '';
    const secuencias = await fetch('../api/secuencias.php').then(r => r.json());
    secuencias.forEach(s => {
        let opt = document.createElement('option');
        opt.value = s.id;
        opt.textContent = s.nombre;
        select.appendChild(opt);
    });
}

// Inicializar pasos de secuencia (al abrir modal)
function inicializarSecuenciaPasos() {
    const cont = document.getElementById('secuenciaPasos');
    cont.innerHTML = '';
    agregarPasoSecuencia();
}

let pasoSecuenciaId = 0;

// Agregar un paso a la secuencia
async function agregarPasoSecuencia() {
    const cont = document.getElementById('secuenciaPasos');
    const pasoId = ++pasoSecuenciaId;
    // Crear contenedor del paso
    const div = document.createElement('div');
    div.className = 'paso-secuencia';
    div.style.marginBottom = '10px';
    div.style.display = 'flex';
    div.style.alignItems = 'center';
    div.style.gap = '8px';
    div.id = 'paso-' + pasoId;
    // Select de especialidad
    const selectEsp = document.createElement('select');
    selectEsp.name = 'especialidad_' + pasoId;
    selectEsp.required = true;
    selectEsp.style.minWidth = '140px';
    // Cargar especialidades
    const especialidades = await fetch('../api/especialidades.php').then(r => r.json());
    especialidades.forEach(e => {
        let opt = document.createElement('option');
        opt.value = e.id;
        opt.textContent = e.nombre;
        selectEsp.appendChild(opt);
    });
    // Select de terapeuta
    const selectTer = document.createElement('select');
    selectTer.name = 'terapeuta_' + pasoId;
    selectTer.required = true;
    selectTer.style.minWidth = '140px';
    // Cargar terapeutas de la especialidad seleccionada
    async function cargarTerapeutasParaEspecialidad() {
        const terapeutas = await fetch('../api/terapeutas.php').then(r => r.json());
        selectTer.innerHTML = '';
        terapeutas.filter(t => t.especialidad_id == selectEsp.value).forEach(t => {
            let opt = document.createElement('option');
            opt.value = t.id;
            opt.textContent = t.nombre;
            selectTer.appendChild(opt);
        });
    }
    selectEsp.onchange = cargarTerapeutasParaEspecialidad;
    await cargarTerapeutasParaEspecialidad();
    // Botón eliminar paso
    const btnDel = document.createElement('button');
    btnDel.type = 'button';
    btnDel.className = 'btn btn-danger';
    btnDel.innerHTML = '<i class="fa fa-trash"></i>';
    btnDel.onclick = () => {
        div.remove();
    };
    // Numeración
    const num = document.createElement('span');
    num.textContent = (cont.children.length + 1) + '.';
    num.style.fontWeight = 'bold';
    num.style.minWidth = '18px';
    // Agregar al contenedor
    div.appendChild(num);
    div.appendChild(selectEsp);
    div.appendChild(selectTer);
    div.appendChild(btnDel);
    cont.appendChild(div);
}

// Mostrar sugerencias de horarios (real)
function mostrarSugerenciasHorarios(opciones, paciente_id, fecha, pasos) {
    const cont = document.getElementById('sugerenciasHorarios');
    if (!opciones.length) {
        cont.innerHTML = '<span style="color:#e53e3e;">No hay horarios disponibles que cumplan con la secuencia.</span>';
        return;
    }
    let html = '<div style="max-height:220px;overflow-y:auto;">';
    opciones.forEach((op, i) => {
        html += `<div style='border:1px solid #d1d5db;border-radius:8px;padding:10px;margin-bottom:10px;background:#f8fafc;'>`;
        html += `<b>Opción ${i+1}: ${op.inicio} - ${op.fin}</b><ul style='margin:6px 0 0 18px;'>`;
        op.pasos.forEach(p => {
            html += `<li>${p.nombre} con ${p.terapeuta} (${p.hora})</li>`;
        });
        html += '</ul>';
        html += `<button class='btn btn-primary' style='margin-top:8px;' onclick='agendarSecuencia(${i})'>Agendar esta secuencia</button>`;
        html += '</div>';
    });
    html += '</div>';
    cont.innerHTML = html;
    // Guardar datos para agendar
    window._datosSecuencia = { paciente_id, fecha, pasos, opciones };
}

// Agendar la secuencia seleccionada
window.agendarSecuencia = async function(idx) {
    const { paciente_id, fecha, pasos } = window._datosSecuencia;
    document.getElementById('msgSecuencia').innerHTML = '<span style="color:#888;">Agendando...</span>';
    const res = await fetch('../api/agendar_secuencia.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ paciente_id, fecha, pasos, accion: 'agendar', opcion: idx })
    });
    const data = await res.json();
    if (data.success) {
        document.getElementById('msgSecuencia').innerHTML = '<span style="color:green;">¡Secuencia agendada correctamente!</span>';
        document.getElementById('sugerenciasHorarios').innerHTML = '';
        cargarAgenda();
    } else {
        document.getElementById('msgSecuencia').innerHTML = '<span style="color:#e53e3e;">No se pudo agendar la secuencia.</span>';
    }
};

// Cargar especialidades en el modal de Añadir Especialidad
async function cargarEspecialidadesEnModal() {
    const cont = document.getElementById('listaEspecialidadesModal');
    if (!cont) return;
    cont.innerHTML = '<p>Cargando...</p>';
    const especialidades = await fetch('../api/especialidades.php').then(r => r.json());
    if (!especialidades.length) {
        cont.innerHTML = '<p>No hay especialidades registradas.</p>';
        return;
    }
    let html = '<ul style="padding-left:18px;">';
    especialidades.forEach(e => {
        html += `<li>${e.nombre}</li>`;
    });
    html += '</ul>';
    cont.innerHTML = html;
}

document.addEventListener('click', async function(e) {
    if (e.target.closest('.cita-clickable')) {
        const citaId = e.target.closest('.cita-clickable').getAttribute('data-cita-id');
        mostrarModalCita(citaId);
    }
});

// Modal para mostrar info de paciente y secuencia de citas
function crearModalCita() {
    if (document.getElementById('modalCita')) return;
    const modal = document.createElement('div');
    modal.className = 'modal';
    modal.id = 'modalCita';
    modal.innerHTML = `
        <div class="modal-content" style="min-width:350px;max-width:95vw;">
            <span class="close" data-close="modalCita">&times;</span>
            <h3><i class="fa-solid fa-user"></i> Detalle de Paciente y Secuencia</h3>
            <div id="detalleCitaPaciente"></div>
        </div>
    `;
    document.body.appendChild(modal);
    document.querySelector('#modalCita .close').onclick = () => cerrarModal('modalCita');
}
crearModalCita();

async function mostrarModalCita(citaId) {
    const res = await fetch(`../api/agendar.php?id=${citaId}`);
    const cita = await res.json();
    if (!cita || !cita.paciente_id) return;
    // Obtener todas las citas de ese paciente para ese día
    const res2 = await fetch(`../api/agendar.php?paciente_id=${cita.paciente_id}`);
    const citas = await res2.json();
    // Filtrar por fecha
    const citasDia = citas.filter(c => c.fecha === cita.fecha);
    let html = `<b>Paciente:</b> ${cita.paciente_nombre}<br>`;
    if (cita.paciente_email) html += `<b>Email:</b> ${cita.paciente_email}<br>`;
    html += `<b>Fecha:</b> ${cita.fecha}<br><br>`;
    html += `<b>Secuencia de citas:</b><ul style='margin:6px 0 0 18px;'>`;
    citasDia.forEach(c => {
        html += `<li>${c.especialidad_nombre} con ${c.terapeuta_nombre} (${c.hora_inicio.substring(0,5)}-${c.hora_fin.substring(0,5)})</li>`;
    });
    html += '</ul>';
    document.getElementById('detalleCitaPaciente').innerHTML = html;
    abrirModal('modalCita');
} 