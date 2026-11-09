<?php
// app/models/Modelo_utilidades.php

class ModeloUtilidades {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function obtenerUtilidadPorParametros($reciboNo, $cedula) {
        $stmt = $this->pdo->prepare("SELECT * FROM utilidades WHERE recibo_no = ? AND cedula = ?");
        $stmt->execute([$reciboNo, $cedula]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}