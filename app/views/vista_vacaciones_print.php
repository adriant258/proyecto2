<?php
// app/views/vista_vacaciones_print.php
// La variable $datos ya viene disponible desde el controlador.
$nombreCompleto = trim(($datos['apellidos'] ?? '') . ' ' . ($datos['nombres'] ?? ''));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Librería para generar el PDF automáticamente desde el navegador -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <title>Liquidación de Vacaciones</title>
    <!-- Enlace al archivo CSS externo renombrado -->
    <link rel="stylesheet" href="/app_proyecto/public/assets/css/vacaciones_print.css">
</head>
<body>

<div class="receipt-container">
    <div class="header">
        <div class="logo-section">
            <div class="logo-placeholder">AJE</div>
        </div>
        <div class="company-title">
            <h1>LIQUIDACION DE VACACIONES</h1>
        </div>
        <div class="right-header">
            <div>Pagina 1 de 1</div>
            <div>KRLIQVAC</div>
            <div id="recibo_no"><?php echo htmlspecialchars($datos['recibo_no'] ?? '-'); ?></div>
        </div>
    </div>

    <!-- Cabecera de datos -->
    <table class="grid-info">
        <tr>
            <td style="width: 15%;">Ficha</td>
            <td style="width: 35%;" id="ficha"><?php echo htmlspecialchars($datos['ficha'] ?? '-'); ?></td>
            <td style="width: 25%;">Salario Promedio</td>
            <td style="width: 25%; text-align: right;" id="salario_promedio"><?php echo htmlspecialchars($datos['salario_promedio'] ?? '-'); ?></td>
        </tr>
        <tr>
            <td>Apellidos</td>
            <td id="apellidos"><?php echo htmlspecialchars($datos['apellidos'] ?? '-'); ?></td>
            <td>Inicio de Vacaciones</td>
            <td style="text-align: center;" id="inicio_vacaciones"><?php echo htmlspecialchars($datos['inicio_vacaciones'] ?? '-'); ?></td>
        </tr>
        <tr>
            <td>Nombres</td>
            <td id="nombres"><?php echo htmlspecialchars($datos['nombres'] ?? '-'); ?></td>
            <td>Fecha de Culminación</td>
            <td style="text-align: center;" id="fecha_culminacion"><?php echo htmlspecialchars($datos['fecha_culminacion'] ?? '-'); ?></td>
        </tr>
        <tr>
            <td>Cedula Identidad</td>
            <td id="cedula"><?php echo htmlspecialchars($datos['cedula'] ?? '-'); ?></td>
            <td>Fecha de Reintegro</td>
            <td style="text-align: center;" id="fecha_reintegro"><?php echo htmlspecialchars($datos['fecha_reintegro'] ?? '-'); ?></td>
        </tr>
        <tr>
            <td>Cargo</td>
            <td id="cargo"><?php echo htmlspecialchars($datos['cargo'] ?? '-'); ?></td>
            <td colspan="2" rowspan="3" style="font-size: 9px; vertical-align: top;">
                <div><strong>Período Laboral:</strong> <span id="periodo"><?php echo htmlspecialchars($datos['periodo'] ?? '-'); ?></span></div>
                <div><strong>Fecha Ingreso:</strong> <span id="fecha_ingreso"><?php echo htmlspecialchars($datos['fecha_ingreso'] ?? '-'); ?></span></div>
                <div><strong>Fecha Vencimiento:</strong> <span id="fecha_vencimiento"><?php echo htmlspecialchars($datos['fecha_vencimiento'] ?? '-'); ?></span></div>
            </td>
        </tr>
        <tr>
            <td>Departamento</td>
            <td id="departamento"><?php echo htmlspecialchars($datos['departamento'] ?? '-'); ?></td>
        </tr>
        <tr>
            <td>Vacaciones a Disfrutar</td>
            <td id="vacaciones_a_disfrutar"><?php echo htmlspecialchars($datos['vacaciones_a_disfrutar'] ?? '-'); ?></td>
        </tr>
    </table>

    <!-- Tabla de Asignaciones -->
    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 55%;">ASIGNACIONES</th>
                <th style="width: 10%; text-align: center;">DIAS</th>
                <th style="width: 15%; text-align: right;">MONTO DIA</th>
                <th style="width: 20%; text-align: right;">MONTO</th>
            </tr>
        </thead>
        <tbody id="tabla_asignaciones">
    <?php 
    $asignaciones = $datos['asignaciones'] ?? [];
    if (is_array($asignaciones) && count($asignaciones) > 0):
        foreach ($asignaciones as $item):
            $concepto = $item['concepto'] ?? $item['descripcion'] ?? $item['nombre'] ?? '';
            $dias = $item['dias'] ?? $item['cantidad'] ?? '';
            $montoDia = $item['monto_dia'] ?? $item['valor_dia'] ?? '';
            $monto = $item['monto'] ?? $item['importe'] ?? '';
    ?>
        <tr>
            <td><?php echo htmlspecialchars($concepto); ?></td>
            <td style="text-align: center;"><?php echo htmlspecialchars($dias); ?></td>
            <td style="text-align: right;"><?php echo htmlspecialchars($montoDia); ?></td>
            <td style="text-align: right;"><?php echo htmlspecialchars($monto); ?></td>
        </tr>
    <?php 
        endforeach;
    else:
    ?>
        <tr>
            <td colspan="4" style="text-align: center; color: #555; font-family: Arial, sans-serif;">Sin asignaciones registradas</td>
        </tr>
    <?php endif; ?>
</tbody>
    </table>
    <table class="main-table" style="margin-top: -1px; margin-bottom: 10px;">
        <tr class="totals-bar">
            <td style="width: 80%; font-weight: bold;">TOTAL ASIGNACIONES</td>
            <td style="width: 20%; text-align: right; font-family: monospace; font-weight: bold;" id="tot_asig"><?php echo htmlspecialchars($datos['total_asignaciones'] ?? '-'); ?></td>
        </tr>
    </table>

    <!-- Tabla de Deducciones -->
    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 55%;">DEDUCCIONES</th>
                <th style="width: 10%; text-align: center;">DIAS</th>
                <th style="width: 15%; text-align: right;">MONTO DIA</th>
                <th style="width: 20%; text-align: right;">MONTO</th>
            </tr>
        </thead>
        <tbody id="tabla_deducciones">
    <?php 
    $deducciones = $datos['deducciones'] ?? [];
    if (is_array($deducciones) && count($deducciones) > 0):
        foreach ($deducciones as $item):
            $concepto = $item['concepto'] ?? $item['descripcion'] ?? $item['nombre'] ?? '';
            $dias = $item['dias'] ?? $item['cantidad'] ?? '';
            $montoDia = $item['monto_dia'] ?? $item['valor_dia'] ?? '';
            $monto = $item['monto'] ?? $item['importe'] ?? '';
    ?>
        <tr>
            <td><?php echo htmlspecialchars($concepto); ?></td>
            <td style="text-align: center;"><?php echo htmlspecialchars($dias); ?></td>
            <td style="text-align: right;"><?php echo htmlspecialchars($montoDia); ?></td>
            <td style="text-align: right;"><?php echo htmlspecialchars($monto); ?></td>
        </tr>
    <?php 
        endforeach;
    else:
    ?>
        <tr>
            <td colspan="4" style="text-align: center; color: #555; font-family: Arial, sans-serif;">Sin deducciones registradas</td>
        </tr>
    <?php endif; ?>
</tbody>
    </table>
    <table class="main-table" style="margin-top: -1px; margin-bottom: 10px;">
        <tr class="totals-bar">
            <td style="width: 80%; font-weight: bold;">TOTAL DEDUCCIONES</td>
            <td style="width: 20%; text-align: right; font-family: monospace; font-weight: bold;" id="tot_deduc"><?php echo htmlspecialchars($datos['total_deducciones'] ?? '-'); ?></td>
        </tr>
    </table>

    <!-- Neto a Cobrar -->
    <div class="literal-amount">
        <div style="width: 70%;">NETO A COBRAR</div>
        <div style="width: 30%; text-align: right; font-family: monospace;" id="neto_pagar"><?php echo htmlspecialchars($datos['neto_pagar'] ?? '-'); ?></div>
    </div>

    <!-- Cuadro de Trámite de Nómina -->
    <div class="box-tramite">
        <div style="font-size: 9px; margin-bottom: 2px;">TRAMITADO NOMINA DE FECHA</div>
        <div id="tramitado_fecha" style="font-size: 10px;"><?php echo htmlspecialchars($datos['tramitado_fecha'] ?? '-'); ?></div>
    </div>

    <!-- Párrafo de conformidad del empleado -->
    <div class="legal-notice">
        YO <span id="conf_nombre"><?php echo htmlspecialchars($nombreCompleto); ?></span>, C.I. <span id="conf_cedula"><?php echo htmlspecialchars($datos['cedula'] ?? '-'); ?></span>, RECIBO EN ESTE MOMENTO EL PAGO CORRESPONDIENTE A MIS VACACIONES, PERIODO <span id="conf_periodo"><?php echo htmlspecialchars($datos['periodo'] ?? '-'); ?></span> A MI ENTERA SATISFACCION Y QUEDO DE ACUERDO CON LA FECHA DE REINTEGRO A MIS LABORES ORDINARIAS
    </div>

    <!-- Firmas oficiales -->
    <div class="footer-signatures">
        <div class="signature-box">
            <div>ELABORADO POR</div>
            <div class="signature-line"></div>
            <div id="elaborado_por" style="font-weight: normal; font-size: 9px;"><?php echo htmlspecialchars($datos['elaborado_por'] ?? '-'); ?></div>
        </div>
        <div class="signature-box">
            <div>APROBADO POR</div>
            <div class="signature-line"></div>
            <div id="aprobado_por" style="font-weight: normal; font-size: 9px;"><?php echo htmlspecialchars($datos['aprobado_por'] ?? '-'); ?></div>
        </div>
        <div class="signature-box">
            <div>RECIBI CONFORME</div>
            <div class="signature-line"></div>
            <div style="font-weight: normal; font-size: 9px;">C.I.: <span id="signature_ci"><?php echo htmlspecialchars($datos['cedula'] ?? '-'); ?></span></div>
        </div>
    </div>
</div>

<script>
    // Definimos el identificador globalmente de forma segura para el script externo
    window.RECIBO_NUMERO = "<?php echo htmlspecialchars($datos['recibo_no'] ?? $datos['cedula'] ?? 'vacaciones'); ?>";
</script>
<!-- Archivo JS externo para liquidación de vacaciones -->
<script src="/app_proyecto/public/assets/js/vacaciones_print.js"></script>
</body>
</html>