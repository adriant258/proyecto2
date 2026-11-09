<?php
// app/models/Modelo_Recibos.php

class ModeloRecibos 
{
    private PDO $pdo;

    public function __construct(PDO $pdo) 
    {
        $this->pdo = $pdo;
    }

    
    // * Obtiene todos los recibos de la base de datos (Ideal para el Administrador)

   /**
     * Obtiene todos los recibos de la base de datos de todos los módulos para el Administrador
     */
 /**
     * Obtiene todos los recibos de la base de datos de todos los módulos para el Administrador
     */
    public function obtenerTodosLosRecibos(): array 
    {
        $todosLosPagos = [];

        // 1. Recibos Principales
        try {
            $stmt = $this->pdo->prepare("SELECT id, cedula, nombre, cargo, tipo, recibo_no, periodo, neto_pagar AS monto, 'recibo' AS modulo_origen FROM recibos ORDER BY id DESC");
            $stmt->execute();
            $recibos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($recibos) { $todosLosPagos = array_merge($todosLosPagos, $recibos); }
        } catch (Exception $e) {}

        // 2. Utilidades (Usando nombre, cargo, periodo y neto_pagar reales)
        try {
            $stmt = $this->pdo->prepare("SELECT id, cedula, nombre, cargo, 'Utilidades' AS tipo, recibo_no, periodo, neto_pagar AS monto, 'utilidades' AS modulo_origen FROM utilidades ORDER BY id DESC");
            $stmt->execute();
            $utilidades = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($utilidades) { $todosLosPagos = array_merge($todosLosPagos, $utilidades); }
        } catch (Exception $e) {}

        // 3. Vacaciones Liquidaciones (Uniendo nombres y apellidos, usando periodo_laboral y neto_pagar)
        try {
            $stmt = $this->pdo->prepare("SELECT id, cedula, CONCAT(nombres, ' ', apellidos) AS nombre, cargo, 'Vacaciones' AS tipo, recibo_no, periodo_laboral AS periodo, neto_pagar AS monto, 'vacaciones' AS modulo_origen FROM vacaciones_liquidaciones ORDER BY id DESC");
            $stmt->execute();
            $vacaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($vacaciones) { $todosLosPagos = array_merge($todosLosPagos, $vacaciones); }
        } catch (Exception $e) {}

        // 4. Intereses Prestaciones (Procesando el JSON de detalles_meses para calcular el 75%)
        try {
            $stmt = $this->pdo->prepare("SELECT id, cedula, nombre, '' AS cargo, 'Prestaciones Sociales' AS tipo, recibo_no, periodo_reporte AS periodo, detalles_meses, 'prestaciones' AS modulo_origen FROM intereses_prestaciones ORDER BY id DESC");
            $stmt->execute();
            $prestacionesRegs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if ($prestacionesRegs) {
                foreach ($prestacionesRegs as &$row) {
                    $netoCalculado = 0.00;
                    if (!empty($row['detalles_meses'])) {
                        $meses = json_decode($row['detalles_meses'], true);
                        if (is_array($meses) && !empty($meses)) {
                            $ultimoMes = end($meses);
                            $saldoNetoStr = $ultimoMes['saldo_neto'] ?? '0'; 
                            $limpio = str_replace(',', '', $saldoNetoStr); 
                            $calculoBase = floatval($limpio) * 0.75;          
                            $netoCalculado = floor($calculoBase * 100) / 100;
                        }
                    }
                    $row['monto'] = $netoCalculado;
                    unset($row['detalles_meses']); // Limpiamos el campo JSON para que no afecte
                }
                unset($row);
                $todosLosPagos = array_merge($todosLosPagos, $prestacionesRegs);
            }
        } catch (Exception $e) {}

        return $todosLosPagos;
    }
      /**
     * Obtiene los recibos filtrados estrictamente por la cédula del empleado (Ideal para el Usuario/Empleado)
     */
    public function obtenerRecibosPorCedula($cedula): array 
    {
        $sql = "SELECT * FROM recibos WHERE cedula = :cedula";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['cedula' => $cedula]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene un recibo único y específico por su número y cédula (Ideal para imprimir/ver el recibo detallado)
     */
    public function obtenerReciboPorParametros($reciboNo, $cedula) 
    {
        $sql = "SELECT * FROM recibos WHERE recibo_no = :recibo_no AND cedula = :cedula";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'recibo_no' => $reciboNo,
            'cedula' => $cedula
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}