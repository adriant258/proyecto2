<?php
// app/controllers/Controlador_recibo_pago.php

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../models/Modelo_recibos.php';

class ControladorReciboPago 
{
    public function index(): void 
    {
        // 1. Obtener los parámetros desde la sesión temporal o respaldo GET
        $reciboNo = $_SESSION['temp_recibo'] ?? $_GET['recibo_no'] ?? null;
        $cedula = $_SESSION['temp_cedula'] ?? $_GET['cedula'] ?? null;

        // 2. Limpiar las variables temporales de sesión para mayor seguridad
        unset($_SESSION['temp_recibo'], $_SESSION['temp_cedula']);

        if (!$reciboNo || !$cedula) {
            echo "<div style='color: red; padding: 20px; font-family: sans-serif;'><strong>Error:</strong> Faltan parámetros de cédula o número de recibo, o la sesión expiró.</div>";
            return;
        }

        try {
            $database = new Database();
            $pdo = $database->getConnection('recibos');

            // Usamos la clase unificada ModeloRecibos
            $modelo = new ModeloRecibos($pdo);
            $datos = $modelo->obtenerReciboPorParametros($reciboNo, $cedula);

            if (!$datos) {
                echo "<div style='color: orange; padding: 20px; font-family: sans-serif;'>No se encontró el recibo especificado para este usuario.</div>";
                return;
            }

            // Cargamos la vista pasándole los datos limpios
            require_once __DIR__ . '/../views/vista_recibo_pago_print.php';

        } catch (Exception $e) {
            echo "<div style='color: red; padding: 20px; font-family: sans-serif;'><strong>Error de Conexión o SQL:</strong> " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    }
}