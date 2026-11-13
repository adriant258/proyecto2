<?php
// app/views/vista_utilidades_print.php
// La variable $datos ya viene disponible desde el controlador.
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Librería para generar el PDF automáticamente desde el navegador -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <title>Recibo de Utilidades</title>
    <!-- Enlace al archivo CSS externo -->
    <link rel="stylesheet" href="/app_proyecto/public/assets/css/utilidades_print.css">
</head>
<body>

<div class="receipt-container">
    <div class="header">
        <div class="logo-section">
            <div class="logo-placeholder">AJE</div>
            <div>SUCURSAL : PLANTA</div>
        </div>
        <div class="company-title">
            <h1>AJEVEN C.A</h1>
            <h2>RECIBO DE UTILIDADES</h2>
        </div>
        <div class="right-header">
            <div>NOMKR</div>
            <div id="recibo_no"><?php echo htmlspecialchars($datos['recibo_no'] ?? '-'); ?></div>
            <div id="periodo"><?php echo htmlspecialchars($datos['periodo'] ?? '-'); ?></div>
        </div>
    </div>

    <table class="grid-info">
        <tr>
            <th style="width: 10%;">Codigo</th>
            <th style="width: 50%;">Nombre</th>
            <th style="width: 8%;">Turno</th>
            <th style="width: 18%;">Cuadrilla</th>
            <th style="width: 14%;">Cedula</th>
        </tr>
        <tr>
            <td id="codigo"><?php echo htmlspecialchars($datos['codigo'] ?? '-'); ?></td>
            <td id="nombre"><?php echo htmlspecialchars($datos['nombre'] ?? '-'); ?></td>
            <td id="turno" style="text-align: center;"><?php echo htmlspecialchars($datos['turno'] ?? '-'); ?></td>
            <td id="cuadrilla"><?php echo htmlspecialchars($datos['cuadrilla'] ?? '-'); ?></td>
            <td id="cedula"><?php echo htmlspecialchars($datos['cedula'] ?? '-'); ?></td>
        </tr>
        <tr>
            <th colspan="2">Departamento</th>
            <th colspan="2">Seccion</th>
            <th>Cargo</th>
        </tr>
        <tr>
            <td colspan="2" id="departamento"><?php echo htmlspecialchars($datos['departamento'] ?? '-'); ?></td>
            <td colspan="2" id="seccion"><?php echo htmlspecialchars($datos['seccion'] ?? '-'); ?></td>
            <td id="cargo"><?php echo htmlspecialchars($datos['cargo'] ?? '-'); ?></td>
        </tr>
    </table>

    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 25%;">ASIGNACIONES</th>
                <th style="width: 10%;">CANTIDAD</th>
                <th style="width: 12%;">MONTO</th>
                <th style="width: 25%;">DEDUCCIONES</th>
                <th style="width: 10%;">CANTIDAD</th>
                <th style="width: 12%;">MONTO</th>
                <th style="width: 11%;">SALDOS</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td id="asig_txt"><?php echo htmlspecialchars($datos['asignaciones_texto'] ?? ''); ?></td>
                <td id="asig_cant" style="text-align: right;"><?php echo htmlspecialchars($datos['asignaciones_cant'] ?? ''); ?></td>
                <td id="asig_monto" style="text-align: right;"><?php echo htmlspecialchars($datos['asignaciones_monto'] ?? ''); ?></td>
                <td id="deduc_txt"><?php echo htmlspecialchars($datos['deducciones_texto'] ?? ''); ?></td>
                <td id="deduc_cant" style="text-align: right;"><?php echo htmlspecialchars($datos['deducciones_cant'] ?? ''); ?></td>
                <td id="deduc_monto" style="text-align: right;"><?php echo htmlspecialchars($datos['deducciones_monto'] ?? ''); ?></td>
                <td id="saldos" style="text-align: right;"><?php echo htmlspecialchars($datos['saldos_texto'] ?? ''); ?></td>
            </tr>
            <tr class="totals-bar">
                <td colspan="2">Total Asignaciones y Deducciones</td>
                <td id="tot_asig" style="text-align: right;"><?php echo htmlspecialchars($datos['total_asignaciones'] ?? '-'); ?></td>
                <td colspan="2"></td>
                <td id="tot_deduc" style="text-align: right;"><?php echo htmlspecialchars($datos['total_deducciones'] ?? '-'); ?></td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <div class="literal-amount">
        <div id="neto_letras" style="width: 50%;"><?php echo htmlspecialchars($datos['neto_letras'] ?? '-'); ?></div>
        <div style="width: 30%; text-align: right; font-weight: bold;">Neto a Pagar</div>
        <div id="neto_pagar" style="width: 20%; text-align: right; font-family: monospace;"><?php echo htmlspecialchars($datos['neto_pagar'] ?? '-'); ?></div>
    </div>

    <table class="extra-info-grid">
        <tr>
            <td class="bg-gray" style="width: 20%;">SALARIOS DEVENGADOS</td>
            <td id="salarios_devengados" style="text-align: right; width: 20%;"><?php echo htmlspecialchars($datos['salarios_devengados'] ?? '-'); ?></td>
            <td class="bg-gray text-center" style="width: 30%;">DISPONIBLE PRESTACIONES</td>
            <td id="disponible_prestaciones" style="text-align: right; width: 30%;"><?php echo htmlspecialchars($datos['disponible_prestaciones'] ?? '-'); ?></td>
        </tr>
    </table>

    <div class="bank-deposit-note">
        ESTE MONTO NETO AQUI REFLEJADO FUE DEPOSITADO EN LA CTA NRO. <span id="cuenta_bancaria"><?php echo htmlspecialchars($datos['cuenta_bancaria'] ?? '-'); ?></span> DEL BANCO BANCO MERCANTIL
    </div>

    <div class="footer-signatures">
        <div class="signature-box">
            <p>Recibi Conforme</p>
            <div style="height: 45px;"></div>
            <div class="signature-line"></div>
            <p>C.I.: <span id="signature_ci"><?php echo htmlspecialchars($datos['cedula'] ?? '-'); ?></span></p>
        </div>
    </div>
</div>

<script>
    // Definimos el número de recibo globalmente de forma segura para el script externo
    window.RECIBO_NUMERO = "<?php echo htmlspecialchars($datos['recibo_no'] ?? '000'); ?>";
</script>
<!-- Archivo JS externo para el recibo de utilidades -->
<script src="/app_proyecto/public/assets/js/utilidades_print.js"></script>
</body>
</html>