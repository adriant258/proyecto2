<?php
header('Content-Type: application/json');
require_once __DIR__ . '/Database.php';

$recibo_no = $_GET['recibo_no'] ?? '';
$cedula = $_GET['cedula'] ?? '';

if (empty($recibo_no) || empty($cedula)) {
    echo json_encode(['success' => false, 'message' => 'Parámetros incompletos']);
    exit;
}

try {
    $database = new Database();
    // Asegúrate de usar la conexión correcta de tu base de datos (ej. 'recibos' o la que uses en tu proyecto)
    $pdo = $database->getConnection('recibos');

    $stmt = $pdo->prepare("SELECT recibo_no, cedula, nombre, ficha, departamento, fecha_ingreso, periodo_reporte, fecha_emision, usuario_reporte, detalles_meses FROM Intereses_Prestaciones WHERE recibo_no = ? AND cedula = ?");
    $stmt->execute([$recibo_no, $cedula]);
    $datos = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($datos) {
        echo json_encode(['success' => true, 'recibo' => $datos]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Registro no encontrado']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}