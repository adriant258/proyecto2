<?php
// app/models/Modelo_prestaciones.php

class ModeloPrestaciones {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function obtenerReciboPorParametros($reciboNo, $cedula) {
        $stmt = $this->pdo->prepare("SELECT * FROM intereses_prestaciones WHERE recibo_no = ? AND cedula = ?");
        $stmt->execute([$reciboNo, $cedula]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($resultado && isset($resultado['detalles_meses']) && is_string($resultado['detalles_meses'])) {
            $resultado['detalles_meses'] = json_decode($resultado['detalles_meses'], true);
        }

        return $resultado;
    }
}