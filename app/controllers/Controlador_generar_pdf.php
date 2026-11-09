<?php
// app/controllers/Controlador_generar_pdf.php

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/SecurityTrait.php';

class ControladorGenerarPdf 
{
    use SecurityTrait;

    public function index(): void 
    {
        $numeroRecibo = $_GET['no'] ?? $_GET['recibo_no'] ?? null;
        $cedula = $_GET['cedula'] ?? null;

        if (!$numeroRecibo || !$cedula) {
            echo '<div style="color: red; padding: 20px; font-family: sans-serif;"><strong>Error:</strong> Faltan parámetros de recibo o cédula.</div>';
            return;
        }

        try {
            $database = new Database();
            $pdo = $database->getConnection('recibos');
            
            $stmt = $pdo->prepare("SELECT * FROM recibos WHERE recibo_no = ? AND cedula = ?");
            $stmt->execute([$numeroRecibo, $cedula]);
            $recibo = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$recibo) {
                echo '<div style="color: orange; padding: 20px; font-family: sans-serif;">Recibo no encontrado.</div>';
                return;
            }

            // Cargamos la vista de impresión pasándole la variable $recibo
            require_once __DIR__ . '/../views/vista_generar_pdf_print.php';

        } catch (Exception $e) {
            echo '<div style="color: red; padding: 20px; font-family: sans-serif;">Error al generar el PDF: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    }
}