<?php
// app/controllers/Controlador_reporte_print.php

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../models/Modelo_reportes.php';
require_once __DIR__ . '/SecurityTrait.php';

class ControladorReportePrint 
{
    use SecurityTrait;

    public function index(): void 
    {
        $this->verificarSesion();

        if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] !== 'admin') {
            exit('Acceso no autorizado');
        }

        // Establecer la zona horaria aquí para todo el flujo
        date_default_timezone_set('America/Caracas');

        try {
            $database = new Database();
            $pdoRecibos = $database->getConnection('recibos');

            $filtroPeriodo = $_GET['periodo'] ?? '';
            $filtroBusqueda = trim($_GET['busqueda'] ?? '');

            $modeloReportes = new ModeloReportes($pdoRecibos);
            $listaReportes = $modeloReportes->obtenerReporteVisualizacion($filtroPeriodo, $filtroBusqueda);

            require_once __DIR__ . '/../views/vista_reporte_print.php';

        } catch (Exception $e) {
            echo "<div style='color: red; padding: 20px; font-family: sans-serif;'><strong>Error de Conexión o SQL:</strong> " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    }
}