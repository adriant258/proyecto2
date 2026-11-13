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
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h3 class="mb-0">Todos los recibos</h3>
      <small class="text-muted">Consulta y administra todos los pagos registrados en el sistema</small>
    </div>
    <!-- Botón para abrir el Modal de Importación Excel -->
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCargarExcel">
      <i class="fas fa-file-excel me-1"></i> Cargar Recibos (Excel)
    </button>
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

<!-- Filtros y botón de impresión -->
<div class="card mb-3">
  <div class="card-body">
    <div class="row g-3 align-items-end">
      
      <!-- 1. Formulario principal para filtrar la vista (GET) con Tipo, Período y Búsqueda -->
      <form method="GET" action="" class="col-md-9 row g-3 align-items-end m-0 p-0" id="formFiltroReporte">
        <input type="hidden" name="modulo" value="reportes">
        
        <div class="col-md-3">
          <label class="form-label small fw-semibold">Tipo de Pago</label>
          <select name="tipo" class="form-select">
            <option value="">Todos los tipos</option>
            <option value="Recibo Regular" <?php echo (isset($_GET['tipo']) && $_GET['tipo'] === 'Recibo Regular') ? 'selected' : ''; ?>>Recibo Regular</option>
            <option value="Utilidades" <?php echo (isset($_GET['tipo']) && $_GET['tipo'] === 'Utilidades') ? 'selected' : ''; ?>>Utilidades</option>
            <option value="Vacaciones" <?php echo (isset($_GET['tipo']) && $_GET['tipo'] === 'Vacaciones') ? 'selected' : ''; ?>>Vacaciones</option>
            <option value="Prestaciones / Intereses" <?php echo (isset($_GET['tipo']) && $_GET['tipo'] === 'Prestaciones / Intereses') ? 'selected' : ''; ?>>Prestaciones / Intereses</option>
          </select>
        </div>

        <div class="col-md-3">
          <label class="form-label small fw-semibold">Período</label>
          <input type="text" name="periodo" class="form-control" value="<?php echo htmlspecialchars($_GET['periodo'] ?? ''); ?>" placeholder="Ej: Período 40">
        </div>
        
        <div class="col-md-3">
          <label class="form-label small fw-semibold">Cédula o Usuario</label>
          <input type="text" name="busqueda" class="form-control" value="<?php echo htmlspecialchars($_GET['busqueda'] ?? ''); ?>" placeholder="Cédula o nombre">
        </div>

        <div class="col-md-3">
          <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search me-1"></i>Filtrar</button>
        </div>
      </form>

      <!-- 2. Formulario independiente para imprimir por POST incluyendo el nuevo campo de período -->
      <div class="col-md-3">
        <label class="form-label small fw-semibold text-muted d-none d-md-block">&nbsp;</label>
        <form action="<?= BASE_URL ?>reportes" method="POST" target="_blank">
          <input type="hidden" name="accion" value="imprimir">
          <input type="hidden" name="tipo" value="<?php echo htmlspecialchars($_GET['tipo'] ?? ''); ?>">
          <input type="hidden" name="periodo" value="<?php echo htmlspecialchars($_GET['periodo'] ?? ''); ?>">
          <input type="hidden" name="busqueda" value="<?php echo htmlspecialchars($_GET['busqueda'] ?? ''); ?>">
          
          <button type="submit" class="btn btn-secondary w-100">
            <i class="fas fa-print me-1"></i>Imprimir PDF
          </button>
        </form>
      </div>

    </div>
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
<!-- ============ MODAL: CARGAR RECIBOS DESDE EXCEL ============ -->
<div class="modal fade" id="modalCargarExcel" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <form id="formCargarExcel" class="modal-content" enctype="multipart/form-data">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title"><i class="fas fa-file-excel me-2"></i>Importar Recibos de Pago</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label fw-semibold">Archivo Excel (.xlsx o .xls)</label>
          <input type="file" class="form-control" name="archivo_excel" id="archivoExcel" accept=".xlsx, .xls, .csv" required>
          <div class="form-text">Asegúrate de que las columnas coincidan con el formato requerido.</div>
        </div>
        <div class="alert alert-info small mb-0">
          <i class="fas fa-info-circle me-1"></i> Puedes descargar una <a href="../public/assets/plantillas/plantilla_maestra_ajeven.xlsx" download>plantilla de ejemplo aquí</a>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-success" id="btnSubirExcel">
          <i class="fas fa-upload me-1"></i> Procesar e Importar
        </button>
      </div>
    </form>
  </div>
</div>

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

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Definimos BASE_URL global para JavaScript de forma dinámica antes de cargar el script externo
    const BASE_URL = '<?php echo BASE_URL; ?>';
</script>
<!-- Archivo JS externo para el panel de administración -->
<script src="<?php echo BASE_URL; ?>assets/js/admin.js"></script>
</body>
</html>