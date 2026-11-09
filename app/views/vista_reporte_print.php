<?php
// app/views/vista_reporte_print.php

?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Reporte de Visualización de Recibos - Ajeven</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="/app_proyecto/public/assets/css/reporte_print.css" rel="stylesheet">
</head>
<body onload="window.print();">
<div class="report-container">
  <div class="header-print d-flex justify-content-between align-items-center">
    <div>
      <h3 class="mb-0 fw-bold">Ajeven · Reporte de Visualización</h3>
      <p class="text-muted mb-0">Historial de consulta de documentos por parte de los empleados</p>
    </div>
    <div class="text-end">
      <small><strong>Fecha de Emisión:</strong> <?php echo date('d/m/Y H:i:s'); ?></small><br>
      <small><strong>Filtro Período:</strong> <?php echo !empty($filtroPeriodo) ? htmlspecialchars($filtroPeriodo) : 'Todos'; ?></small>
    </div>
  </div>

  <table class="table table-bordered align-middle shadow-sm">
    <thead class="table-dark text-center">
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
          <td class="text-center"><?php echo htmlspecialchars($rep['cedula']); ?></td>
          <td><?php echo htmlspecialchars($rep['email']); ?></td>
          <td class="text-center"><span class="badge bg-secondary"><?php echo htmlspecialchars($rep['tipo_recibo']); ?></span></td>
          <td><?php echo htmlspecialchars($rep['periodo'] ?? 'N/A'); ?></td>
          <td class="text-center"><?php echo htmlspecialchars($rep['fecha_visualizacion'] ?? 'No visualizado'); ?></td>
        </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="6" class="text-center text-muted py-4">No hay registros de visualización para los filtros seleccionados.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>

  <div class="mt-4 no-print text-center">
    <button onclick="window.print();" class="btn btn-primary"><i class="fas fa-print me-1"></i> Imprimir / Guardar PDF</button>
    <button onclick="window.close();" class="btn btn-secondary ms-2">Cerrar Ventana</button>
  </div>
</div>
</body>
</html>