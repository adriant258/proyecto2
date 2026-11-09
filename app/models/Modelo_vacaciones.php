<?php
// app/models/Modelo_vacaciones.php

class ModeloVacaciones {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function obtenerLiquidacionPorParametros($reciboNo, $cedula) {
        // 1. Obtener la cabecera de la liquidación de vacaciones
        $stmt = $this->pdo->prepare("SELECT * FROM vacaciones_liquidaciones WHERE recibo_no = ? AND cedula = ?");
        $stmt->execute([$reciboNo, $cedula]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$resultado) {
            return null;
        }

        $liquidacionId = $resultado['id'];

        // 2. Obtener las asignaciones asociadas usando el ID de la liquidación
        try {
            $stmtAsig = $this->pdo->prepare("SELECT concepto, dias, monto_dia, monto FROM vacaciones_asignaciones WHERE liquidacion_id = ?");
            $stmtAsig->execute([$liquidacionId]);
            $resultado['asignaciones'] = $stmtAsig->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $resultado['asignaciones'] = [];
        }

        // 3. Obtener las deducciones asociadas usando el ID de la liquidación
        try {
            $stmtDeduc = $this->pdo->prepare("SELECT concepto, dias, monto_dia, monto FROM vacaciones_deducciones WHERE liquidacion_id = ?");
            $stmtDeduc->execute([$liquidacionId]);
            $resultado['deducciones'] = $stmtDeduc->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $resultado['deducciones'] = [];
        }

        return $resultado;
    }
}