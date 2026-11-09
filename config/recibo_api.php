<?php
// public/recibos_api.php

header('Content-Type: application/json; charset=utf-8');

// Requerir tu clase de conexión existente
require_once __DIR__ . '/../config/Database.php';

class ReciboApi {
    private $pdo;

    public function __construct() {
        // Instanciamos tu clase Database especificando la base de datos de recibos
        $database = new Database();
        $this->pdo = $database->getConnection('recibos'); 
    }

    public function obtenerReciboDetallado() {
        // Obtener el número de recibo desde los parámetros GET de la URL (coincide con el JS)
        $reciboNo = isset($_GET['recibo_no']) ? trim($_GET['recibo_no']) : '';
$cedula = isset($_GET['cedula']) ? trim($_GET['cedula']) : '';

if (empty($reciboNo) || empty($cedula)) {
    http_response_code(400);
    echo json_encode(["success" => false, "error" => "Cédula y número de recibo son obligatorios"], JSON_UNESCAPED_UNICODE);
    exit;
}

            try {
    // Consulta filtrando estrictamente por cédula Y número de recibo
    $stmt = $this->pdo->prepare("SELECT * FROM recibos WHERE recibo_no = ? AND cedula = ?");
    $stmt->execute([$reciboNo, $cedula]);
    
    $recibo = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($recibo) {
                http_response_code(200);
                // Retornar la estructura exacta que espera tu JavaScript: data.success y data.recibo
                echo json_encode([
                    "success" => true,
                    "recibo" => $recibo
                ], JSON_UNESCAPED_UNICODE);
            } else {
                http_response_code(404);
                echo json_encode(["success" => false, "error" => "No se encontró el recibo especificado"], JSON_UNESCAPED_UNICODE);
            }

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["success" => false, "error" => "Error en la consulta: " . $e->getMessage()], JSON_UNESCAPED_UNICODE);
        }
    }
}

// Ejecutar la clase de la API al realizar la petición
$api = new ReciboApi();
$api->obtenerReciboDetallado();
?>