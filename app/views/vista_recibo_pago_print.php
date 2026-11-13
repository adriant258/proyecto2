<?php
// app/views/vista_recibo_pago_print.php
// La vista solo recibe los datos listos para mostrarse.
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <title>Recibo de Pago Dinámico</title>
    <link rel="stylesheet" href="/app_proyecto/public/assets/css/recibo_print.css">
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
            <h2>RECIBO DE PAGO</h2>
        </div>
        <div class="right-header">
            <div>NOMKR</div>
            <div id="recibo_no"><?php echo htmlspecialchars($datos['recibo_no']); ?></div>
            <div id="periodo"><?php echo htmlspecialchars($datos['periodo']); ?></div>
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
            <td><?php echo htmlspecialchars($datos['codigo']); ?></td>
            <td><?php echo htmlspecialchars($datos['nombre']); ?></td>
            <td style="text-align: center;"><?php echo htmlspecialchars($datos['turno']); ?></td>
            <td><?php echo htmlspecialchars($datos['cuadrilla']); ?></td>
            <td><?php echo htmlspecialchars($datos['cedula']); ?></td>
        </tr>
        <tr>
            <th colspan="2">Departamento</th>
            <th colspan="2">Seccion</th>
            <th>Cargo</th>
        </tr>
        <tr>
            <td colspan="2"><?php echo htmlspecialchars($datos['departamento']); ?></td>
            <td colspan="2"><?php echo htmlspecialchars($datos['seccion']); ?></td>
            <td><?php echo htmlspecialchars($datos['cargo']); ?></td>
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
            <?php
            // Calculamos el máximo de filas basándonos en los arrays ya procesados por el controlador
            $maxFilas = max(
                count($datos['asig_textos']), 
                count($datos['ded_textos']), 
                1
            );

            for ($i = 0; $i < $maxFilas; $i++):
                $aTxt   = $datos['asig_textos'][$i] ?? '';
                $aCant  = $datos['asig_cants'][$i] ?? '';
                $aMonto = $datos['asig_montos'][$i] ?? '';

                $dTxt   = $datos['ded_textos'][$i] ?? '';
                $dCant  = $datos['ded_cants'][$i] ?? '';
                $dMonto = $datos['ded_montos'][$i] ?? '';
                
                $sVal   = $datos['saldos'][$i] ?? '';

                if ($aTxt === '' && $aCant === '' && $aMonto === '' && $dTxt === '' && $dCant === '' && $dMonto === '' && $sVal === '') {
                    continue;
                }
            ?>
                <tr>
                    <td><?php echo htmlspecialchars($aTxt); ?></td>
                    <td style="text-align: right;"><?php echo htmlspecialchars($aCant); ?></td>
                    <td style="text-align: right;"><?php echo htmlspecialchars($aMonto); ?></td>
                    <td><?php echo htmlspecialchars($dTxt); ?></td>
                    <td style="text-align: right;"><?php echo htmlspecialchars($dCant); ?></td>
                    <td style="text-align: right;"><?php echo htmlspecialchars($dMonto); ?></td>
                    <td style="text-align: right;"><?php echo htmlspecialchars($sVal); ?></td>
                </tr>
            <?php endfor; ?>
            
            <tr class="totals-bar">
                <td colspan="2">Total Asignaciones y Deducciones</td>
                <td style="text-align: right;"><?php echo htmlspecialchars($datos['total_asignaciones']); ?></td>
                <td colspan="2"></td>
                <td style="text-align: right;"><?php echo htmlspecialchars($datos['total_deducciones']); ?></td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <div class="literal-amount">
        <div style="width: 50%;"><?php echo htmlspecialchars($datos['neto_letras']); ?></div>
        <div style="width: 30%; text-align: right; font-weight: bold;">Neto a Pagar</div>
        <div style="width: 20%; text-align: right; font-family: monospace;"><?php echo htmlspecialchars($datos['neto_pagar']); ?></div>
    </div>

    <table class="extra-info-grid">
        <tr>
            <td class="bg-gray" style="width: 20%;">SALARIOS DEVENGADOS</td>
            <td style="text-align: right; width: 20%;"><?php echo htmlspecialchars($datos['salarios_devengados']); ?></td>
            <td class="bg-gray text-center" style="width: 30%;">DISPONIBLE PRESTACIONES</td>
            <td style="text-align: right; width: 30%;"><?php echo htmlspecialchars($datos['disponible_prestaciones']); ?></td>
        </tr>
    </table>

    <div class="bank-deposit-note">
        ESTE MONTO NETO AQUI REFLEJADO FUE DEPOSITADO EN LA CTA NRO. <span><?php echo htmlspecialchars($datos['cuenta_bancaria']); ?></span> DEL BANCO BANCO MERCANTIL
    </div>

    <div class="footer-signatures">
        <div class="signature-box">
            <p>Recibi Conforme</p>
            <div style="height: 45px;"></div>
            <div class="signature-line"></div>
            <p>C.I.: <span><?php echo htmlspecialchars($datos['cedula']); ?></span></p>
        </div>
    </div>
</div>

<script>
    window.RECIBO_NUMERO = "<?php echo htmlspecialchars($datos['recibo_no']); ?>";
</script>
<script src="/app_proyecto/public/assets/js/recibo_print.js"></script>
</body>
</html>