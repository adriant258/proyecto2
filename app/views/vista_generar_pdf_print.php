<?php
// app/views/vista_comprobante.php
// La variable $recibo ya viene disponible desde el controlador.
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recibo <?php echo htmlspecialchars($recibo['recibo_no'] ?? ''); ?></title>
    <!-- Enlace al archivo CSS externo -->
    <link rel="stylesheet" href="/app_proyecto/public/assets/css/comprobante.css">
</head>
<body onload="window.print()">
    <div class="recibo-box">
        <h2>AJEVEN - Comprobante de Pago</h2>
        <p><strong>Nº de Recibo:</strong> <?php echo htmlspecialchars($recibo['recibo_no'] ?? ''); ?></p>
        <table>
            <tr><th>Trabajador:</th><td><?php echo htmlspecialchars($recibo['nombre'] ?? ''); ?></td></tr>
            <tr><th>Cédula:</th><td><?php echo htmlspecialchars($recibo['cedula'] ?? ''); ?></td></tr>
            <tr><th>Cargo:</th><td><?php echo htmlspecialchars($recibo['cargo'] ?? ''); ?></td></tr>
            <tr><th>Tipo de Pago:</th><td><?php echo htmlspecialchars($recibo['tipo'] ?? ''); ?></td></tr>
            <tr><th>Período:</th><td><?php echo htmlspecialchars($recibo['periodo'] ?? ''); ?></td></tr>
            <tr><th>Monto Total:</th><td>$ <?php echo number_format($recibo['monto'] ?? 0, 2); ?></td></tr>
            <tr><th>Estado:</th><td><?php echo htmlspecialchars($recibo['estado'] ?? ''); ?></td></tr>
        </table>
    </div>
</body>
</html>