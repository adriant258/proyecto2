<?php
// app/views/vista_admin.php
$nombreUsuario     = $nombreUsuario ?? 'Administrador';
$usuariosIniciales = $usuariosIniciales ?? [];
$listaRecibos      = $listaRecibos ?? [];
$listaReportes     = $listaReportes ?? [];
$listaPeriodos     = $listaPeriodos ?? [];
$filtroPeriodo     = $filtroPeriodo ?? '';
$filtroBusqueda    = $filtroBusqueda ?? '';
$moduloActual      = $moduloActual ?? 'usuarios';

// Los totales ya vienen calculados desde el controlador
$totalUsuarios     = $totalUsuarios ?? 0;
$totalActivos      = $totalActivos ?? 0;
$totalBloqueados   = $totalBloqueados ?? 0;
$totalAdmins       = $totalAdmins ?? 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Ajeven · Panel de Administración</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
<!-- SweetAlert2 -->
<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/sweetalert2.min.css" />
<script src="<?php echo BASE_URL; ?>assets/js/sweetalert2@11.js"></script>

<link href="/app_proyecto/public/assets/css/admin.css" rel="stylesheet">
</head>
<body>

<!-- ============ NAVBAR SUPERIOR ============ -->
<nav class="topbar d-flex align-items-center px-3">
  <a class="brand text-decoration-none" href="#">Ajeven</a>
  <!-- Botón para ocultar/mostrar el panel lateral -->
  <button class="btn btn-sm text-white me-auto ms-2" id="btnSidebar" aria-label="Menú"><i class="fas fa-bars"></i></button>

  <div class="dropdown">
    <button class="btn btn-sm text-white dropdown-toggle" data-bs-toggle="dropdown">
      <i class="fas fa-circle text-success me-1" style="font-size:.5rem"></i>
      <?php echo htmlspecialchars($nombreUsuario); ?>
    </button>
    <ul class="dropdown-menu dropdown-menu-end shadow">
      <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>logout"><i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión</a></li>
    </ul>
  </div>
</nav>

<!-- ============ SIDEBAR ============ -->
<aside class="sidebar" id="sidebar">
  <nav class="nav flex-column mt-2">
    <a class="nav-link <?php echo ($moduloActual === 'usuarios') ? 'active' : ''; ?>" href="?modulo=usuarios"><i class="fas fa-users"></i>Gestión de Usuarios</a>
    <a class="nav-link <?php echo ($moduloActual === 'recibos') ? 'active' : ''; ?>" href="?modulo=recibos"><i class="fas fa-file-invoice-dollar"></i>Recibos de Pago</a>
    <a class="nav-link <?php echo ($moduloActual === 'reportes') ? 'active' : ''; ?>" href="?modulo=reportes"><i class="fas fa-chart-bar"></i>Reportes de Visualización</a>
  </nav>
</aside>

<!-- ============ CONTENIDO ============ -->
<main class="content">

<?php if ($moduloActual === 'usuarios'): ?>
<!-- ========== MÓDULO 1: USUARIOS ========== -->
<section class="vista activa">
  <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <div>
      <h3 class="mb-0">Gestión de Usuarios</h3>
      <small class="text-muted">Administra los accesos al sistema</small>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalUsuario" onclick="prepararCreacion()">
      <i class="fas fa-plus me-1"></i>Nuevo usuario
    </button>
  </div>

  <!-- Tarjetas de Estadísticas / Contadores -->
  <div class="row g-3 mb-3">
    <div class="col-6 col-md-3">
      <div class="card stat p-3"><small class="text-muted">Total</small><h4 class="mb-0"><?php echo $totalUsuarios; ?></h4></div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card stat p-3" style="border-color:#27ae60"><small class="text-muted">Activos</small><h4 class="mb-0"><?php echo $totalActivos; ?></h4></div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card stat p-3" style="border-color:#e74c3c"><small class="text-muted">Bloqueados</small><h4 class="mb-0"><?php echo $totalBloqueados; ?></h4></div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card stat p-3" style="border-color:#f2994a"><small class="text-muted">Administradores</small><h4 class="mb-0"><?php echo $totalAdmins; ?></h4></div>
    </div>
  </div>

  <div class="card">
    <div class="card-body">
      <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
        <table class="table table-hover align-middle mb-0">
          <thead>
            <tr><th>ID</th><th>Usuario</th><th>Cédula</th><th>Correo</th><th>Rol</th><th>Estado</th><th>Registro</th><th class="text-center">Acciones</th></tr>
          </thead>
          <tbody>
            <?php if (count($usuariosIniciales) > 0): ?>
              <?php foreach ($usuariosIniciales as $u): ?>
              <tr>
                <td><?php echo $u['id']; ?></td>
                <td class="fw-semibold"><?php echo htmlspecialchars($u['usuario']); ?></td>
                <td><?php echo htmlspecialchars($u['cedula']); ?></td>
                <td><?php echo htmlspecialchars($u['email']); ?></td>
                <td><span class="badge <?php echo $u['rol'] === 'admin' ? 'bg-primary' : 'bg-secondary'; ?>"><?php echo ucfirst($u['rol']); ?></span></td>
                <td><span class="badge <?php echo $u['estado'] === 'activo' ? 'bg-success' : 'bg-danger'; ?>"><?php echo ucfirst($u['estado']); ?></span></td>
                <td><?php echo htmlspecialchars($u['creado']); ?></td>
                <td class="text-center text-nowrap">
                  <button class="btn btn-sm btn-outline-primary" title="Editar" onclick='editarUsuario(<?php echo json_encode($u); ?>)'><i class="fas fa-pen"></i></button>
                  <?php if ($u['id'] != 1): ?>
                  <form action="" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de eliminar este usuario?');">
                    <input type="hidden" name="accion" value="eliminar">
                    <input type="hidden" name="id" value="<?php echo $u['id']; ?>">
                    <button class="btn btn-sm btn-outline-danger" title="Eliminar"><i class="fas fa-trash"></i></button>
                  </form>
                  <?php endif; ?>
                </td>
              </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="8" class="text-center text-muted py-4">No hay usuarios registrados.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</section>

<?php elseif ($moduloActual === 'recibos'): ?>
<!-- ========== MÓDULO 2: RECIBOS ========== -->
<section class="vista activa">
  <div class="mb-3">
    <h3 class="mb-0">Todos los recibos</h3>
    <small class="text-muted">Consulta y administra todos los pagos registrados en el sistema</small>
  </div>

  <!-- Barra de Filtros de Búsqueda -->
  <div class="card mb-3">
    <div class="card-body">
      <div class="row g-3 align-items-end">
        <div class="col-md-3">
          <label class="form-label small fw-semibold">Filtrar por Tipo</label>
          <select id="filtroTipo" class="form-select" onchange="filtrarTablaRecibos()">
            <option value="">Todos los tipos</option>
            <option value="Nómina">Nómina</option>
            <option value="Utilidades">Utilidades</option>
            <option value="Vacaciones">Vacaciones</option>
            <option value="Prestaciones Sociales">Prestaciones Sociales</option>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label small fw-semibold">Filtrar por Período</label>
          <input type="text" id="filtroPeriodoInput" class="form-control" placeholder="Escribe para buscar período..." onkeyup="filtrarTablaRecibos()">
        </div>
        <div class="col-md-4">
          <label class="form-label small fw-semibold">Buscar por Nombre o Cédula</label>
          <input type="text" id="filtroTexto" class="form-control" placeholder="Cédula o nombre del trabajador..." onkeyup="filtrarTablaRecibos()">
        </div>
        <div class="col-md-1">
          <button class="btn btn-secondary w-100" title="Limpiar filtros" onclick="limpiarFiltrosRecibos()"><i class="fas fa-redo"></i></button>
        </div>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-body">
      <!-- Contenedor con scroll vertical y altura máxima (500px) -->
      <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
        <table class="table table-hover align-middle mb-0">
          <thead style="position: sticky; top: 0; background-color: #1a252f; color: #fff; z-index: 5;">
            <tr>
              <th>Cédula</th>
              <th>Trabajador</th>
              <th>Cargo</th>
              <th>Concepto / Tipo</th>
              <th>Período</th>
              <th class="text-end">Monto Neto</th>
              <th class="text-center">Acciones</th>
            </tr>
          </thead>
          <tbody id="tablaCuerpo">
            <?php if (empty($listaRecibos)): ?>
                <tr id="filaVacia">
                    <td colspan="7" class="text-center text-muted py-4">No hay recibos registrados en el sistema.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($listaRecibos as $recibo):
                    $montoLimpio = floatval(str_ireplace(['bs', ' ', ','], ['', '', '.'], $recibo['monto'] ?? 0));
                    $tipoDoc = !empty($recibo['tipo']) ? trim($recibo['tipo']) : 'Nómina';
                    
                    // Colores dinámicos para los badges según el tipo de recibo
                    $badgeClass = 'bg-primary';
                    if (stripos($tipoDoc, 'Vacacion') !== false) {
                        $badgeClass = 'bg-warning text-dark';
                    } elseif (stripos($tipoDoc, 'Utilidad') !== false) {
                        $badgeClass = 'bg-dark';
                    } elseif (stripos($tipoDoc, 'Prestacion') !== false) {
                        $badgeClass = 'bg-danger';
                    }
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($recibo['cedula'] ?? ''); ?></td>
                        <td class="fw-semibold"><?php echo htmlspecialchars(!empty($recibo['nombre']) ? $recibo['nombre'] : 'Trabajador'); ?></td>
                        <td>
    <?php 
        $cargo = trim($recibo['cargo'] ?? '');
        echo htmlspecialchars(($cargo === '' || strcasecmp($cargo, 'N/D') === 0) ? '' : $cargo); 
    ?>
</td>
                        <td>
                            <span class="badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($tipoDoc); ?></span>
                        </td>
                        <td><?php echo htmlspecialchars($recibo['periodo'] ?? 'N/A'); ?></td>
                        <td class="text-end fw-bold text-success">BS <?php echo number_format($montoLimpio, 2, ',', '.'); ?></td>
                        <td class="text-center text-nowrap">
                            <!-- Botón Visualizar -->
                            <button class="btn btn-info btn-sm text-white me-1"
                                    data-recibo="<?php echo htmlspecialchars($recibo['recibo_no'] ?? $recibo['id'] ?? ''); ?>"
                                    data-cedula="<?php echo htmlspecialchars($recibo['cedula'] ?? ''); ?>"
                                    data-tipo="<?php echo htmlspecialchars($recibo['tipo'] ?? 'recibo'); ?>"
                                    onclick="verRecibo(this.dataset.recibo, this.dataset.cedula, this.dataset.tipo)">
                                    <i class="fas fa-eye me-1"></i> Visualizar
                            </button>
                            
                            <!-- Botón Descargar PDF -->
                            <button class="btn btn-sm btn-outline-danger"
                                    data-recibo="<?php echo htmlspecialchars($recibo['recibo_no'] ?? $recibo['id'] ?? ''); ?>"
                                    data-cedula="<?php echo htmlspecialchars($recibo['cedula'] ?? ''); ?>"
                                    data-tipo="<?php echo htmlspecialchars($recibo['tipo'] ?? 'recibo'); ?>"
                                    onclick="descargarReciboPDF(this.dataset.recibo, this.dataset.cedula, this.dataset.tipo)">
                                    <i class="fas fa-file-pdf me-1"></i> Descargar PDF
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <tr id="filaVaciaBusqueda" style="display:none">
                    <td colspan="7" class="text-center text-muted py-4">No se encontraron recibos con los filtros aplicados.</td>
                </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</section>
<?php elseif ($moduloActual === 'reportes'): ?>
<!-- ========== MÓDULO 3: REPORTES DE VISUALIZACIÓN ========== -->
<section class="vista activa">
  <div class="mb-3">
    <h3 class="mb-0">Reporte de Visualización de Recibos</h3>
    <small class="text-muted">Historial detallado de recibos consultados por los empleados</small>
  </div>

<!-- Filtro por período, búsqueda y botón de impresión -->
<div class="card mb-3">
  <div class="card-body">
    <form method="GET" action="" class="row g-3 align-items-end">
      <input type="hidden" name="modulo" value="reportes">
      
      <div class="col-md-4">
       <label class="form-label small fw-semibold">Filtrar por Tipo de Pago</label>
        <select name="tipo" class="form-select">
          <option value="">Todos los tipos</option>
           <option value="Recibo Regular" <?php echo (isset($_GET['tipo']) && $_GET['tipo'] === 'Recibo Regular') ? 'selected' : ''; ?>>Recibo Regular</option>
            <option value="Utilidades" <?php echo (isset($_GET['tipo']) && $_GET['tipo'] === 'Utilidades') ? 'selected' : ''; ?>>Utilidades</option>
             <option value="Vacaciones" <?php echo (isset($_GET['tipo']) && $_GET['tipo'] === 'Vacaciones') ? 'selected' : ''; ?>>Vacaciones</option>
              <option value="Prestaciones / Intereses" <?php echo (isset($_GET['tipo']) && $_GET['tipo'] === 'Prestaciones / Intereses') ? 'selected' : ''; ?>>Prestaciones / Intereses</option>
              </select>
      </div>
      <div class="col-md-3">
        <label class="form-label small fw-semibold">Buscar por Cédula o Usuario</label>
        <input type="text" name="busqueda" class="form-control" value="<?php echo htmlspecialchars($filtroBusqueda ?? ''); ?>" placeholder="Cédula o nombre">
      </div>

      <div class="col-md-2">
        <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search me-1"></i>Filtrar</button>
      </div>

      <div class="col-md-3">
        <!-- Enlace actualizado apuntando al Controlador MVC -->
        <a href="<?= BASE_URL ?>reportes?periodo=<?php echo urlencode($filtroPeriodo); ?>&busqueda=<?php echo urlencode($filtroBusqueda ?? ''); ?>" target="_blank" class="btn btn-secondary w-100">
        <i class="fas fa-print me-1"></i>Imprimir PDF
       </a>
      </div>
    </form>
  </div>
</div>

  <!-- Tabla de Reportes -->
  <div class="card">
    <div class="card-body">
      <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
        <table class="table table-hover align-middle mb-0">
          <thead>
            <tr>
              <th>Usuario</th>
              <th>Cédula</th>
              <th>Correo</th>
              <th>Tipo de Recibo</th>
              <th>Período</th>
              <th>Fecha de Visualización</th>
            </tr>
          </thead>
          <tbody>
            <?php if (count($listaReportes) > 0): ?>
              <?php foreach ($listaReportes as $rep): ?>
              <tr>
                <td class="fw-semibold"><?php echo htmlspecialchars($rep['usuario']); ?></td>
                <td><?php echo htmlspecialchars($rep['cedula']); ?></td>
                <td><?php echo htmlspecialchars($rep['email']); ?></td>
                <td><span class="badge bg-info text-dark"><?php echo htmlspecialchars($rep['tipo_recibo']); ?></span></td>
                <td><?php echo htmlspecialchars($rep['periodo'] ?? 'N/A'); ?></td>
                <td><?php echo htmlspecialchars($rep['fecha_visualizacion'] ?? 'No visualizado'); ?></td>
              </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="6" class="text-center text-muted py-4">No hay registros de visualización para este filtro.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</section>
<?php endif; ?>

</main>

<!-- ============ MODAL: CREAR / EDITAR USUARIO ============ -->
<div class="modal fade" id="modalUsuario" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <form class="modal-content" action="" method="POST">
      <input type="hidden" name="accion" value="guardar">
      <input type="hidden" name="id" id="uId">
      <div class="modal-header">
        <h5 class="modal-title" id="tituloModal">Nuevo usuario</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3"><label class="form-label">Usuario</label><input class="form-control" name="usuario" id="uUsuario" required></div>
        <div class="row">
          <div class="col-md-6 mb-3"><label class="form-label">Cédula</label><input class="form-control" name="cedula" id="uCedula" required></div>
          <div class="col-md-6 mb-3"><label class="form-label">Correo</label><input type="email" class="form-control" name="email" id="uEmail" required></div>
        </div>
        <div class="row">
          <div class="col-md-6 mb-3"><label class="form-label">Rol</label>
            <select class="form-select" name="rol" id="uRol"><option value="usuario">Usuario</option><option value="admin">Admin</option></select></div>
          <div class="col-md-6 mb-3"><label class="form-label">Estado</label>
            <select class="form-select" name="estado" id="uEstado"><option value="activo">Activo</option><option value="bloqueado">Bloqueado</option></select></div>
        </div>
        <div class="mb-1"><label class="form-label">Contraseña</label>
          <input type="password" class="form-control" name="password" id="uPass" placeholder="Déjalo vacío para no cambiarla al editar">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Guardar</button>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Definimos BASE_URL global para JavaScript de forma dinámica
const BASE_URL = '<?php echo BASE_URL; ?>';

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
// CONTROL DE INACTIVIDAD Y SESIÓN (ADMIN)
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
</script>
</body>
</html>