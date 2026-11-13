<?php
// app/views/vista_empleado.php
// Las variables $nombreUsuario, $cedulaEmpleado, $listaRecibos, $conteo y $totalRecibos ya vienen inyectadas desde el controlador.
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="Panel de empleado" />
    <title>Ajeven · Mis recibos</title>

    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet" />

    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/sweetalert2.min.css" />
    <script src="<?php echo BASE_URL; ?>assets/js/sweetalert2@11.js"></script>

    <!-- Enlace al archivo CSS externo -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/empleado.css" />
</head>
<body>

<!-- ============ NAVBAR SUPERIOR ============ -->
<nav class="topbar d-flex align-items-center px-3">
    <a class="brand text-decoration-none" href="#">Ajeven</a>
    <button class="btn btn-sm text-white me-auto" id="sidebarToggle" aria-label="Menú"><i class="fas fa-bars"></i></button>

    <div class="dropdown">
        <button class="btn btn-sm text-white dropdown-toggle" data-bs-toggle="dropdown">
            <i class="fas fa-circle text-success me-1" style="font-size:.5rem"></i>
            <?php echo htmlspecialchars($nombreUsuario ?? 'Empleado'); ?>
            <i class="fas fa-user-circle ms-1"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end shadow">
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-danger" href="<?php echo BASE_URL; ?>logout"><i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión</a></li>
        </ul>
    </div>
</nav>

<!-- ============ SIDEBAR ============ -->
<aside class="sidebar">
    <nav class="nav flex-column mt-2">
        <a class="nav-link active" href="#!" data-filtro="todos">
            <i class="fas fa-th-large"></i>Todos los recibos
            <span class="badge bg-light text-dark"><?php echo $totalRecibos; ?></span>
        </a>
        <a class="nav-link" href="#!" data-filtro="semanal">
            <i class="fas fa-calendar-week"></i>Semanal
            <span class="badge bg-primary"><?php echo $conteo['semanal']; ?></span>
        </a>
        <a class="nav-link" href="#!" data-filtro="mensual">
            <i class="fas fa-calendar-alt"></i>Mensual
            <span class="badge bg-secondary"><?php echo $conteo['mensual']; ?></span>
        </a>
        <a class="nav-link" href="#!" data-filtro="vacaciones">
            <i class="fas fa-umbrella-beach"></i>Vacaciones
            <span class="badge bg-warning text-dark"><?php echo $conteo['vacaciones']; ?></span>
        </a>
        <a class="nav-link" href="#!" data-filtro="utilidades">
            <i class="fas fa-chart-line"></i>Utilidades
            <span class="badge bg-dark"><?php echo $conteo['utilidades']; ?></span>
        </a>
        <a class="nav-link" href="#!" data-filtro="prestaciones">
            <i class="fas fa-piggy-bank"></i>Prestaciones Sociales
            <span class="badge bg-danger"><?php echo $conteo['prestaciones']; ?></span>
        </a>
    </nav>
    <div class="foot">
        Iniciado sesión como:<br>
        <strong class="text-white"><i class="fas fa-circle text-success me-1" style="font-size:.5rem"></i><?php echo htmlspecialchars($nombreUsuario ?? 'Empleado'); ?></strong>
    </div>
</aside>

<!-- ============ CONTENIDO PRINCIPAL ============ -->
<main class="content">
    <div class="d-flex align-items-center gap-3 mb-4">
        <span class="avatar"><?php echo htmlspecialchars(mb_strtoupper(mb_substr($nombreUsuario ?? 'E', 0, 1))); ?></span>
        <div>
            <h3 class="mb-0">Bienvenido, <?php echo htmlspecialchars($nombreUsuario ?? 'Empleado'); ?></h3>
            <small class="text-muted">Consulta y descarga tus recibos de pago</small>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3"><div class="card stat p-3"><small class="text-muted">Total recibos</small><h4 class="mb-0"><?php echo $totalRecibos; ?></h4></div></div>
        <div class="col-6 col-md-3"><div class="card stat p-3" style="border-color:#6c757d"><small class="text-muted">Mensuales</small><h4 class="mb-0"><?php echo $conteo['mensual']; ?></h4></div></div>
        <div class="col-6 col-md-3"><div class="card stat p-3" style="border-color:#f2994a"><small class="text-muted">Vacaciones</small><h4 class="mb-0"><?php echo $conteo['vacaciones']; ?></h4></div></div>
        <div class="col-6 col-md-3"><div class="card stat p-3" style="border-color:#27ae60"><small class="text-muted">Utilidades</small><h4 class="mb-0"><?php echo $conteo['utilidades']; ?></h4></div></div>
    </div>

    <div class="card" id="seccionTabla">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0" id="tituloTabla">Todos los recibos</h5>
                <small class="text-muted" id="contadorFilas"></small>
            </div>
            <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Período / Fecha</th>
                            <th class="text-center">Concepto / Tipo</th>
                            <th>Monto Neto</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaCuerpo">
                        <?php if (empty($listaRecibos)): ?>
                            <tr id="filaVacia">
                                <td colspan="4" class="text-center text-muted py-4">No hay recibos registrados para este usuario.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($listaRecibos as $recibo):
                                [$tipoClase, $badgeColor, $textoTipo] = ControladorEmpleado::tipoRecibo($recibo);
                                $neto = $recibo['neto_pagar'] ?? '0.00';
                                if (is_numeric($neto)) { $neto = number_format((float)$neto, 2, ',', '.'); }
                            ?>
                                <tr data-tipo="<?php echo $tipoClase; ?>">
                                    <td><?php echo htmlspecialchars($recibo['periodo'] ?? 'N/A'); ?></td>
                                    <td class="text-center">
                                        <span class="badge <?php echo $badgeColor; ?> fs-6 fw-normal"><?php echo $textoTipo; ?></span>
                                    </td>
                                    <td class="fw-bold text-success"><?php echo htmlspecialchars($neto); ?></td>
                                    <td class="text-center text-nowrap">
                                        <button class="btn btn-info btn-sm text-white me-1"
                                               data-recibo="<?php echo htmlspecialchars($recibo['recibo_no']); ?>"
                                               data-cedula="<?php echo htmlspecialchars($cedulaEmpleado ?? ''); ?>"
                                               data-tipo="<?php echo htmlspecialchars($recibo['tipo_doc'] ?? 'recibo'); ?>"
                                               onclick="verRecibo(this.dataset.recibo, this.dataset.cedula, this.dataset.tipo)">
                                            <i class="fas fa-eye me-1"></i> Visualizar
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger"
                                                data-recibo="<?php echo htmlspecialchars($recibo['recibo_no']); ?>"
                                                data-cedula="<?php echo htmlspecialchars($cedulaEmpleado ?? ''); ?>"
                                                data-tipo="<?php echo htmlspecialchars($recibo['tipo_doc'] ?? 'recibo'); ?>"
                                                onclick="descargarReciboPDF(this.dataset.recibo, this.dataset.cedula, this.dataset.tipo)">
                                            <i class="fas fa-file-pdf me-1"></i> Descargar PDF
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <tr id="filaVacia" style="display:none">
                                <td colspan="4" class="text-center text-muted py-4">No hay recibos de este tipo.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- LECTOR DE ALERTAS SWEETALERT (DESDE SESSION) -->
<?php if (isset($_SESSION['swal'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const swal = <?php echo json_encode($_SESSION['swal'], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
            Swal.fire({
                icon: swal.icon,
                title: swal.title,
                text: swal.text,
                confirmButtonText: 'Aceptar',
                confirmButtonColor: '#2f80ed'
            });
        });
    </script>
<?php unset($_SESSION['swal']); endif; ?>

<script>
    // Definimos BASE_URL global para JavaScript de forma dinámica antes de cargar el script externo
    const BASE_URL = '<?php echo BASE_URL; ?>';
</script>
<!-- Archivo JS externo para el panel de empleado -->
<script src="<?php echo BASE_URL; ?>assets/js/empleado.js"></script>
</body>
</html>