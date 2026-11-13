// public/assets/js/admin.js

// Funcionalidad para ocultar/mostrar el panel lateral
document.getElementById('btnSidebar').onclick = () => {
    document.body.classList.toggle('sb-hidden');
};

function prepararCreacion() {
    document.getElementById('tituloModal').textContent = 'Nuevo usuario';
    document.getElementById('uId').value = '';
    document.getElementById('uUsuario').value = '';
    document.getElementById('uCedula').value = '';
    document.getElementById('uEmail').value = '';
    document.getElementById('uRol').value = 'usuario';
    document.getElementById('uEstado').value = 'activo';
    document.getElementById('uPass').value = '';
}

function editarUsuario(u) {
    document.getElementById('tituloModal').textContent = 'Editar usuario';
    document.getElementById('uId').value = u.id;
    document.getElementById('uUsuario').value = u.usuario;
    document.getElementById('uCedula').value = u.cedula;
    document.getElementById('uEmail').value = u.email;
    document.getElementById('uRol').value = u.rol;
    document.getElementById('uEstado').value = u.estado;
    document.getElementById('uPass').value = ''; 
    
    var modal = new bootstrap.Modal(document.getElementById('modalUsuario'));
    modal.show();
}

// Lógica de filtrado en tiempo real para la tabla de recibos
function filtrarTablaRecibos() {
    const tipoFiltro = document.getElementById('filtroTipo').value.toLowerCase();
    const periodoFiltro = document.getElementById('filtroPeriodoInput').value.toLowerCase();
    const textoFiltro = document.getElementById('filtroTexto').value.toLowerCase();
    
    const filas = document.querySelectorAll('#tablaCuerpo tr:not(#filaVacia):not(#filaVaciaBusqueda)');
    let visibles = 0;

    filas.forEach(fila => {
        const cedula = fila.cells[0]?.textContent.toLowerCase() || '';
        const trabajador = fila.cells[1]?.textContent.toLowerCase() || '';
        const tipo = fila.cells[3]?.textContent.toLowerCase() || '';
        const periodo = fila.cells[4]?.textContent.toLowerCase() || '';

        const matchTipo = tipoFiltro === '' || tipo.includes(tipoFiltro);
        const matchPeriodo = periodoFiltro === '' || periodo.includes(periodoFiltro);
        const matchTexto = textoFiltro === '' || cedula.includes(textoFiltro) || trabajador.includes(textoFiltro);

        if (matchTipo && matchPeriodo && matchTexto) {
            fila.style.display = '';
            visibles++;
        } else {
            fila.style.display = 'none';
        }
    });

    const filaVaciaBusqueda = document.getElementById('filaVaciaBusqueda');
    if (filaVaciaBusqueda) {
        filaVaciaBusqueda.style.display = visibles === 0 ? '' : 'none';
    }
}

function limpiarFiltrosRecibos() {
    document.getElementById('filtroTipo').value = '';
    document.getElementById('filtroPeriodoInput').value = '';
    document.getElementById('filtroTexto').value = '';
    filtrarTablaRecibos();
}

// ==========================================
// FUNCIONES SEGURAS PARA RECIBOS (ADMIN)
// ==========================================
function verRecibo(reciboNo, cedula, tipoDoc) {
    if (!cedula) { alert('No se encontró la cédula del usuario.'); return; }
    
    let moduloDestino = 'recibos';
    let tipoLower = (tipoDoc || '').toLowerCase();

    if (tipoLower.includes('vacacion')) moduloDestino = 'vacaciones';
    else if (tipoLower.includes('utilidad')) moduloDestino = 'utilidades';
    else if (tipoLower.includes('prestacion')) moduloDestino = 'prestaciones';

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
    let tipoLower = (tipoDoc || '').toLowerCase();

    if (tipoLower.includes('vacacion')) moduloDestino = 'vacaciones';
    else if (tipoLower.includes('utilidad')) moduloDestino = 'utilidades';
    else if (tipoLower.includes('prestacion')) moduloDestino = 'prestaciones';

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

// ==========================================
// IMPORTACIÓN MASIVA DE RECIBOS (EXCEL)
// ==========================================
const formCargarExcel = document.getElementById('formCargarExcel');

if (formCargarExcel) {
    formCargarExcel.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(formCargarExcel);
        const btnSubir = document.getElementById('btnSubirExcel');
        
        btnSubir.disabled = true;
        btnSubir.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Procesando...';

        fetch(BASE_URL + 'recibos/importar-excel', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(textoBruto => {
            btnSubir.disabled = false;
            btnSubir.innerHTML = '<i class="fas fa-upload me-1"></i> Procesar e Importar';
            
            try {
                const data = JSON.parse(textoBruto);
                if (data.status === 'ok') {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Importación Exitosa!',
                        text: data.mensaje,
                        confirmButtonColor: '#27ae60'
                    }).then(() => location.reload());
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Aviso',
                        text: data.mensaje,
                        confirmButtonColor: '#d33'
                    });
                }
            } catch (e) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error PHP / HTML',
                    text: 'El servidor devolvió HTML o un error fatal. Revisa la consola.',
                    confirmButtonColor: '#d33'
                });
            }
        })
    });
}

// ==========================================
// IMPRESIÓN DE REPORTE CON FILTROS ACTUALES
// ==========================================
const btnImprimirDirecto = document.getElementById('btnImprimirPdfDirecto');

if (btnImprimirDirecto) {
    btnImprimirDirecto.addEventListener('click', function(e) {
        e.preventDefault();

        // 1. Capturar los valores actuales que el usuario tiene en pantalla
        const selectTipo = document.getElementById('filtroTipoReporte');
        const inputBusqueda = document.getElementById('filtroBusquedaReporte');

        const tipoValor = selectTipo ? selectTipo.value : '';
        const busquedaValor = inputBusqueda ? inputBusqueda.value : '';

        // 2. Construir la URL de destino hacia tu controlador de reportes enviando los parámetros
        // Si tu controlador los procesa por GET o POST, adaptamos la peticion:
        // Crearemos un formulario temporal dinámico para enviarlo por POST de forma invisible:
        
        const formTemp = document.createElement('form');
        formTemp.method = 'POST';
        formTemp.action = `${BASE_URL}reportes`;
        formTemp.target = '_blank'; // Abre en pestaña nueva

        // Input oculto para la acción
        const inputAccion = document.createElement('input');
        inputAccion.type = 'hidden';
        inputAccion.name = 'accion';
        inputAccion.value = 'imprimir';
        formTemp.appendChild(inputAccion);

        // Input oculto para el tipo
        const inputTipo = document.createElement('input');
        inputTipo.type = 'hidden';
        inputTipo.name = 'tipo';
        inputTipo.value = tipoValor;
        formTemp.appendChild(inputTipo);

        // Input oculto para la búsqueda
        const inputBusq = document.createElement('input');
        inputBusq.type = 'hidden';
        inputBusq.name = 'busqueda';
        inputBusq.value = busquedaValor;
        formTemp.appendChild(inputBusq);

        // Inyectar al documento, enviar y remover
        document.body.appendChild(formTemp);
        formTemp.submit();
        document.body.removeChild(formTemp);
    });
}

window.onload = reiniciarTemporizador;
window.onmousemove = reiniciarTemporizador;
window.onkeypress = reiniciarTemporizador;
window.onclick = reiniciarTemporizador;
window.onscroll = reiniciarTemporizador;