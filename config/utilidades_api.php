<?php
// config/utilidades_api.php

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

    $stmt = $pdo->prepare("SELECT * FROM Utilidades WHERE recibo_no = ? AND cedula = ?");
    $stmt->execute([$reciboNo, $cedula]);
    $recibo = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$recibo) {
        echo json_encode(['success' => false, 'message' => 'No se encontró el recibo de utilidades especificado.']);
        exit;
    }

    echo json_encode([
        'success' => true, 
        'recibo' => $recibo
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Error de BD: ' . $e->getMessage()
    ]);
}