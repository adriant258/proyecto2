<?php
// config/vacaciones_api.php

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/Database.php';

$reciboNo = $_GET['recibo_no'] ?? null;
$cedula = $_GET['cedula'] ?? null;

if (!$reciboNo || !$cedula) {
    echo json_encode(['success' => false, 'message' => 'Faltan parámetros obligatorios.']);
    exit;
}

try {
    $database = new Database();
    $pdo = $database->getConnection('recibos'); 

    // 1. Obtener la cabecera de la liquidación de vacaciones
    $stmt = $pdo->prepare("SELECT * FROM vacaciones_liquidaciones WHERE recibo_no = ? AND cedula = ?");
    $stmt->execute([$reciboNo, $cedula]);
    $liquidacion = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$liquidacion) {
        echo json_encode(['success' => false, 'message' => 'No se encontró la liquidación de vacaciones especificada.']);
        exit;
    }

    // 2. Obtener las asignaciones asociadas
    $stmtAsig = $pdo->prepare("SELECT concepto, dias, monto_dia, monto FROM vacaciones_asignaciones WHERE liquidacion_id = ?");
    $stmtAsig->execute([$liquidacion['id']]);
    $liquidacion['asignaciones'] = $stmtAsig->fetchAll(PDO::FETCH_ASSOC);

    // 3. Obtener las deducciones asociadas
    $stmtDeduc = $pdo->prepare("SELECT concepto, dias, monto_dia, monto FROM vacaciones_deducciones WHERE liquidacion_id = ?");
    $stmtDeduc->execute([$liquidacion['id']]);
    $liquidacion['deducciones'] = $stmtDeduc->fetchAll(PDO::FETCH_ASSOC);

    // 4. Responder con éxito en formato JSON
    echo json_encode([
        'success' => true, 
        'liquidacion' => $liquidacion
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Error de BD: ' . $e->getMessage()
    ]);
}