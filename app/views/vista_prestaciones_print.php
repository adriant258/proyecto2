<?php
// app/views/vista_prestaciones_print.php
// La variable $datos ya viene disponible desde el controlador.
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Librería para generar el PDF automáticamente desde el navegador -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <title>Intereses sobre Prestaciones</title>
    <!-- Enlace al archivo CSS  -->
    <link rel="stylesheet" href="/app_proyecto/public/assets/css/prestaciones_print.css">
</head>
<body>

<div class="receipt-container">
    <div class="header">
        <div class="logo-section">
            <div class="logo-placeholder">AJE</div>
            <div style="font-size: 9px;">SUCURSAL : PLANTA</div>
        </div>
        <div class="company-title">
            <h1>AJEVEN C.A</h1>
            <h2>INTERESES SOBRE PRESTACIONES</h2>
            <h3 id="periodo_reporte"><?php echo htmlspecialchars($datos['periodo_reporte'] ?? 'DESDE ENERO/2026 HASTA DICIEMBRE/2026'); ?></h3>
        </div>
        <div class="right-header">
            <div>EMISIÓN: <span id="fecha_emision"><?php echo htmlspecialchars($datos['fecha_emision'] ?? '-'); ?></span></div>
            <div id="pag_info">Página 1 de 1</div>
            <div id="formato_id">NOMI7143</div>
            <div id="usuario_reporte"><?php echo htmlspecialchars($datos['usuario_reporte'] ?? 'RSANGRONIS'); ?></div>
        </div>
    </div>

    <table class="grid-info">
        <tr>
            <td style="width: 15%;">Departamento:</td>
            <td style="width: 35%;" id="departamento"><?php echo htmlspecialchars($datos['departamento'] ?? '-'); ?></td>
            <td style="width: 10%;">C.I.:</td>
            <td style="width: 15%;" id="cedula"><?php echo htmlspecialchars($datos['cedula'] ?? '-'); ?></td>
            <td style="width: 10%;">FICHA:</td>
            <td style="width: 15%;" id="ficha"><?php echo htmlspecialchars($datos['ficha'] ?? '-'); ?></td>
        </tr>
        <tr>
            <td>Trabajador:</td>
            <td colspan="3" id="nombre"><?php echo htmlspecialchars($datos['nombre'] ?? '-'); ?></td>
            <td>FECHA INGRESO:</td>
            <td id="fecha_ingreso"><?php echo htmlspecialchars($datos['fecha_ingreso'] ?? '-'); ?></td>
        </tr>
    </table>

    <table class="main-table">
        <thead>
            <tr>
                <th>Mes</th>
                <th>Remuneraciones Devengadas</th>
                <th>Alícuota Bono Vac.</th>
                <th>Alícuota Utilidades</th>
                <th>Salario Integral</th>
                <th>Reserva Mensual (Abonos)</th>
                <th>Días Adic.</th>
                <th>Antigüedad Acumulada</th>
                <th>Préstamos (Otros)</th>
                <th>Anticipos Sobre Prestac.</th>
                <th>Saldo Neto Antigüedad</th>
                <th>Tasa de Interés</th>
                <th>Interés Mes</th>
                <th>Intereses Acumulados</th>
            </tr>
        </thead>
        <tbody id="tabla_detalles">
            <?php 
            $meses = $datos['detalles_meses'] ?? [];
            if (is_array($meses)):
                foreach ($meses as $m):
                    $claseHighlight = !empty($m['destacado']) ? 'bg-highlight' : '';
            ?>
                <tr class="<?php echo $claseHighlight; ?>">
                    <td class="text-left"><?php echo htmlspecialchars($m['mes'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($m['rem_dev'] ?? '0.00'); ?></td>
                    <td><?php echo htmlspecialchars($m['alic_bono'] ?? '0.00'); ?></td>
                    <td><?php echo htmlspecialchars($m['alic_util'] ?? '0.00'); ?></td>
                    <td><?php echo htmlspecialchars($m['sal_int'] ?? '0.00'); ?></td>
                    <td><?php echo htmlspecialchars($m['res_mensual'] ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars($m['dias_adic'] ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars($m['ant_acum'] ?? '0.00'); ?></td>
                    <td><?php echo htmlspecialchars($m['prestamos'] ?? '0.00'); ?></td>
                    <td><?php echo htmlspecialchars($m['anticipos'] ?? '0.00'); ?></td>
                    <td><?php echo htmlspecialchars($m['saldo_neto'] ?? '0.00'); ?></td>
                    <td><?php echo htmlspecialchars($m['tasa'] ?? '0.00'); ?></td>
                    <td><?php echo htmlspecialchars($m['interes_mes'] ?? '0.00'); ?></td>
                    <td><?php echo htmlspecialchars($m['interes_acum'] ?? '0.00'); ?></td>
                </tr>
            <?php 
                endforeach;
            endif; 
            ?>
        </tbody>
    </table>

    <div class="footer-signatures">
        <div class="signature-box">
            <p>Recibí Conforme</p>
            <div style="height: 40px;"></div>
            <div class="signature-line"></div>
            <p>C.I.: <span id="signature_ci"><?php echo htmlspecialchars($datos['cedula'] ?? '-'); ?></span></p>
        </div>
    </div>
</div>

<script>
    // Definimos la cédula globalmente de forma segura para el script externo
    window.CEDULA_USUARIO = "<?php echo htmlspecialchars($datos['cedula'] ?? '000000'); ?>";
</script>
<!-- Archivo JS externo para las prestaciones -->
<script src="/app_proyecto/public/assets/js/prestaciones_print.js"></script>
</body>
</html>