<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recibo <?php echo htmlspecialchars($recibo['recibo_no']); ?></title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .recibo-box { border: 1px solid #ccc; padding: 20px; max-width: 600px; margin: auto; }
        h2 { text-align: center; color: #333; }
        table { width: 100%; margin-top: 20px; border-collapse: collapse; }
        th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
    </style>
</head>
<body onload="window.print()">
    <div class="recibo-box">
        <h2>AJEVEN - Comprobante de Pago</h2>
        <p><strong>Nº de Recibo:</strong> <?php echo htmlspecialchars($recibo['recibo_no']); ?></p>
        <table>
            <tr><th>Trabajador:</th><td><?php echo htmlspecialchars($recibo['nombre']); ?></td></tr>
            <tr><th>Cédula:</th><td><?php echo htmlspecialchars($recibo['cedula']); ?></td></tr>
            <tr><th>Cargo:</th><td><?php echo htmlspecialchars($recibo['cargo']); ?></td></tr>
            <tr><th>Tipo de Pago:</th><td><?php echo htmlspecialchars($recibo['tipo']); ?></td></tr>
            <tr><th>Período:</th><td><?php echo htmlspecialchars($recibo['periodo']); ?></td></tr>
            <tr><th>Monto Total:</th><td>$ <?php echo number_format($recibo['monto'], 2); ?></td></tr>
            <tr><th>Estado:</th><td><?php echo htmlspecialchars($recibo['estado']); ?></td></tr>
        </table>
    </div>
</body>
</html>