// public/assets/js/empleado.js

document.getElementById('sidebarToggle').onclick = () => document.body.classList.toggle('sb-hidden');
if (innerWidth < 992) document.body.classList.remove('sb-hidden');

const titulos = {
    todos: 'Todos los recibos', 
    semanal: 'Recibos semanales', 
    mensual: 'Recibos mensuales',
    vacaciones: 'Vacaciones', 
    utilidades: 'Utilidades', 
    prestaciones: 'Prestaciones Sociales'
};

function filtrarTabla(tipo) {
    let visibles = 0;
    document.querySelectorAll('#tablaCuerpo tr[data-tipo]').forEach(fila => {
        const mostrar = tipo === 'todos' || fila.dataset.tipo === tipo;
        fila.style.display = mostrar ? '' : 'none';
        if (mostrar) {
            visibles++;
            fila.classList.add('fila-destacada');
            setTimeout(() => fila.classList.remove('fila-destacada'), 1500);
        }
    });
    const vacia = document.getElementById('filaVacia');
    if (vacia && document.querySelector('#tablaCuerpo tr[data-tipo]')) vacia.style.display = visibles ? 'none' : '';
    document.getElementById('tituloTabla').textContent = titulos[tipo] || 'Recibos';
    document.getElementById('contadorFilas').textContent = visibles + ' registro(s)';
    document.getElementById('seccionTabla').scrollIntoView({behavior:'smooth', block:'center'});
}

document.querySelectorAll('[data-filtro]').forEach(a => a.addEventListener('click', () => {
    document.querySelectorAll('[data-filtro]').forEach(x => x.classList.remove('active'));
    a.classList.add('active');
    filtrarTabla(a.dataset.filtro);
    if (innerWidth < 992) document.body.classList.remove('sb-hidden');
}));

function verRecibo(reciboNo, cedula, tipoDoc) {
    if (!cedula) { alert('No se encontró la cédula del usuario.'); return; }
    
    let moduloDestino = 'recibos';
    if (tipoDoc === 'vacaciones') moduloDestino = 'vacaciones';
    else if (tipoDoc === 'utilidades') moduloDestino = 'utilidades';
    else if (tipoDoc === 'prestaciones') moduloDestino = 'prestaciones';

    // Enviar datos por POST mediante fetch para ocultarlos de la URL
    fetch(`${BASE_URL}${moduloDestino}/preparar`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `recibo_no=${encodeURIComponent(reciboNo)}&cedula=${encodeURIComponent(cedula)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'ok') {
            window.open(`${BASE_URL}${moduloDestino}`, '_blank');
        } else {
            alert('Error al preparar la visualización del recibo.');
        }
    })
    .catch(() => alert('Error de conexión con el servidor.'));
}

function descargarReciboPDF(reciboNo, cedula, tipoDoc) {
    if (!cedula) { alert('No se encontró la cédula del usuario.'); return; }
    
    let moduloDestino = 'recibos';
    if (tipoDoc === 'vacaciones') moduloDestino = 'vacaciones';
    else if (tipoDoc === 'utilidades') moduloDestino = 'utilidades';
    else if (tipoDoc === 'prestaciones') moduloDestino = 'prestaciones';

    // Enviar datos por POST mediante fetch y añadir parámetro auto=1 para descarga directa
    fetch(`${BASE_URL}${moduloDestino}/preparar`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `recibo_no=${encodeURIComponent(reciboNo)}&cedula=${encodeURIComponent(cedula)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'ok') {
            window.open(`${BASE_URL}${moduloDestino}?auto=1`, '_blank');
        } else {
            alert('Error al preparar la descarga del recibo.');
        }
    })
    .catch(() => alert('Error de conexión con el servidor.'));
}

document.getElementById('contadorFilas').textContent = document.querySelectorAll('#tablaCuerpo tr[data-tipo]').length + ' registro(s)';

// ==========================================
// CONTROL DE INACTIVIDAD Y SESIÓN
// ==========================================
const TIEMPO_LIMITE = 15 * 60 * 1000; // 15 minutos en milisegundos
let temporizadorInactividad;

function reiniciarTemporizador() {
    clearTimeout(temporizadorInactividad);
    temporizadorInactividad = setTimeout(mostrarAlertaInactividad, TIEMPO_LIMITE);
}

function mostrarAlertaInactividad() {
    Swal.fire({
        title: '¡Sesión a punto de expirar!',
        text: 'Has estado inactivo por un tiempo. ¿Deseas continuar en la sesión o cerrarla?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Continuar sesión',
        cancelButtonText: 'Cerrar sesión',
        confirmButtonColor: '#2f80ed',
        cancelButtonColor: '#d33',
        allowOutsideClick: false,
        allowEscapeKey: false,
        timer: 30000,
        timerProgressBar: true
    }).then((result) => {
        if (result.isConfirmed) {
            reiniciarTemporizador();
            fetch(BASE_URL + '?route=api/renovar-sesion').catch(() => {});
        } else {
            window.location.href = BASE_URL + 'logout';
        }
    });
}

window.onload = reiniciarTemporizador;
window.onmousemove = reiniciarTemporizador;
window.onkeypress = reiniciarTemporizador;
window.onclick = reiniciarTemporizador;
window.onscroll = reiniciarTemporizador;