<?php
// app/models/Modelo_Recibos.php

class ModeloRecibos 
{
    private PDO $pdo;

    public function __construct(PDO $pdo) 
    {
        $this->pdo = $pdo;
    }

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

    /**
     * Valida e inserta un recibo masivamente. Si encuentra duplicidad por número de recibo, lanza una excepción.
     */
    public function insertarReciboMasivo(array $row, string $tipoRecibo): bool
    {
        $tipoLower = strtolower(trim($tipoRecibo));
        
        // Extraer número de recibo y cédula según la pestaña/módulo
        if (str_contains($tipoLower, 'utilid')) {
            $reciboNo = trim($row['A'] ?? '');
            $cedula = trim($row['B'] ?? '');
            $tabla = 'utilidades';
        } else if (str_contains($tipoLower, 'vacac')) {
            $reciboNo = trim($row['A'] ?? '');
            $cedula = trim($row['B'] ?? '');
            $tabla = 'vacaciones_liquidaciones';
        } else if (str_contains($tipoLower, 'prestaciones')) {
            $reciboNo = trim($row['A'] ?? '');
            $cedula = trim($row['B'] ?? '');
            $tabla = 'intereses_prestaciones';
        } else {
            // Nómina (por defecto)
            $reciboNo = trim($row['A'] ?? '');
            $cedula = trim($row['B'] ?? '');
            $tabla = 'recibos';
        }

        if (empty($reciboNo) || empty($cedula)) {
            throw new Exception("Se encontró una fila con Cédula o Número de Recibo vacíos en la pestaña '{$tipoRecibo}'.");
        }

        // Validar si ya existe el número de recibo en la Base de Datos
        if ($this->existeDuplicado($tabla, $reciboNo)) {
            throw new Exception("Duplicidad detectada en la pestaña '{$tipoRecibo}': El recibo número '{$reciboNo}' ya se encuentra registrado en el sistema.");
        }

        // Si pasa la validación, procedemos con el INSERT correspondiente a tu tabla
        try {
            if ($tabla === 'utilidades') {
                $sql = "INSERT INTO utilidades (recibo_no, cedula, codigo, nombre, turno, cuadrilla, departamento, seccion, cargo, periodo, asignaciones_texto, asignaciones_cant, asignaciones_monto, deducciones_texto, deducciones_cant, deducciones_monto, saldos_texto, total_asignaciones, total_deducciones, neto_letras, neto_pagar, salarios_devengados, disponible_prestaciones, cuenta_bancaria) 
                        VALUES (:recibo_no, :cedula, :codigo, :nombre, :turno, :cuadrilla, :departamento, :seccion, :cargo, :periodo, :asignaciones_texto, :asignaciones_cant, :asignaciones_monto, :deducciones_texto, :deducciones_cant, :deducciones_monto, :saldos_texto, :total_asignaciones, :total_deducciones, :neto_letras, :neto_pagar, :salarios_devengados, :disponible_prestaciones, :cuenta_bancaria)";
                
                $stmt = $this->pdo->prepare($sql);
                return $stmt->execute([
                    ':recibo_no' => $reciboNo, ':cedula' => $cedula,
                    ':codigo' => $row['C'] ?? '', ':nombre' => $row['D'] ?? '',
                    ':turno' => $row['E'] ?? '', ':cuadrilla' => $row['F'] ?? '',
                    ':departamento' => $row['G'] ?? '', ':seccion' => $row['H'] ?? '',
                    ':cargo' => $row['I'] ?? '', ':periodo' => $row['J'] ?? '',
                    ':asignaciones_texto' => $row['K'] ?? '', ':asignaciones_cant' => $row['L'] ?? '',
                    ':asignaciones_monto' => $row['M'] ?? '', ':deducciones_texto' => $row['N'] ?? '',
                    ':deducciones_cant' => $row['O'] ?? '', ':deducciones_monto' => $row['P'] ?? '',
                    ':saldos_texto' => $row['Q'] ?? '', ':total_asignaciones' => $row['R'] ?? 0.00,
                    ':total_deducciones' => $row['S'] ?? 0.00, ':neto_letras' => $row['T'] ?? '',
                    ':neto_pagar' => $row['U'] ?? 0.00, ':salarios_devengados' => $row['V'] ?? 0.00,
                    ':disponible_prestaciones' => $row['W'] ?? 0.00, ':cuenta_bancaria' => $row['X'] ?? ''
                ]);
            } 
            else if ($tabla === 'vacaciones_liquidaciones') {
                $sql = "INSERT INTO vacaciones_liquidaciones (recibo_no, cedula, ficha, apellidos, nombres, cargo, departamento, salario_promedio, inicio_vacaciones, fecha_culminacion, fecha_reintegro, periodo_laboral, fecha_ingreso, fecha_vencimiento, vacaciones_a_disfrutar, total_asignaciones, total_deducciones, neto_pagar, tramitado_fecha, elaborado_por, aprobado_por) 
                        VALUES (:recibo_no, :cedula, :ficha, :apellidos, :nombres, :cargo, :departamento, :salario_promedio, :inicio_vacaciones, :fecha_culminacion, :fecha_reintegro, :periodo_laboral, :fecha_ingreso, :fecha_vencimiento, :vacaciones_a_disfrutar, :total_asignaciones, :total_deducciones, :neto_pagar, :tramitado_fecha, :elaborado_por, :aprobado_por)";
                
                $stmt = $this->pdo->prepare($sql);
                return $stmt->execute([
                    ':recibo_no' => $reciboNo, ':cedula' => $cedula,
                    ':ficha' => $row['C'] ?? '', ':apellidos' => $row['D'] ?? '',
                    ':nombres' => $row['E'] ?? '', ':cargo' => $row['F'] ?? '',
                    ':departamento' => $row['G'] ?? '', ':salario_promedio' => $row['H'] ?? 0.00,
                    ':inicio_vacaciones' => $row['I'] ?? null, ':fecha_culminacion' => $row['J'] ?? null,
                    ':fecha_reintegro' => $row['K'] ?? null, ':periodo_laboral' => $row['L'] ?? '',
                    ':fecha_ingreso' => $row['M'] ?? null, ':fecha_vencimiento' => $row['N'] ?? null,
                    ':vacaciones_a_disfrutar' => $row['O'] ?? 0, ':total_asignaciones' => $row['P'] ?? 0.00,
                    ':total_deducciones' => $row['Q'] ?? 0.00, ':neto_pagar' => $row['R'] ?? 0.00,
                    ':tramitado_fecha' => $row['S'] ?? null, ':elaborado_por' => $row['T'] ?? '',
                    ':aprobado_por' => $row['U'] ?? ''
                ]);
            }
            else if ($tabla === 'intereses_prestaciones') {
                $sql = "INSERT INTO intereses_prestaciones (recibo_no, cedula, nombre, ficha, departamento, fecha_ingreso, periodo_reporte, fecha_emision, usuario_reporte, detalles_meses) 
                        VALUES (:recibo_no, :cedula, :nombre, :ficha, :departamento, :fecha_ingreso, :periodo_reporte, :fecha_emision, :usuario_reporte, :detalles_meses)";
                
                $stmt = $this->pdo->prepare($sql);
                return $stmt->execute([
                    ':recibo_no' => $reciboNo, ':cedula' => $cedula,
                    ':nombre' => $row['C'] ?? '', ':ficha' => $row['D'] ?? '',
                    ':departamento' => $row['E'] ?? '', ':fecha_ingreso' => $row['F'] ?? '',
                    ':periodo_reporte' => $row['G'] ?? '', ':fecha_emision' => $row['H'] ?? '',
                    ':usuario_reporte' => $row['I'] ?? '', ':detalles_meses' => $row['J'] ?? '[]'
                ]);
            }
            else {
                // Nómina
                $sql = "INSERT INTO recibos (cedula, tipo, codigo, nombre, turno, cuadrilla, departamento, seccion, cargo, recibo_no, periodo, asignaciones_texto, asignaciones_cant, asignaciones_monto, deducciones_texto, deducciones_cant, deducciones_monto, saldos_texto, total_asignaciones, total_deducciones, neto_letras, neto_pagar, salarios_devengados, disponible_prestaciones, cuenta_bancaria) 
                        VALUES (:cedula, :tipo, :codigo, :nombre, :turno, :cuadrilla, :departamento, :seccion, :cargo, :recibo_no, :periodo, :asignaciones_texto, :asignaciones_cant, :asignaciones_monto, :deducciones_texto, :deducciones_cant, :deducciones_monto, :saldos_texto, :total_asignaciones, :total_deducciones, :neto_letras, :neto_pagar, :salarios_devengados, :disponible_prestaciones, :cuenta_bancaria)";
                
                $stmt = $this->pdo->prepare($sql);
                return $stmt->execute([
                    ':cedula' => $cedula, ':tipo' => $tipoRecibo,
                    ':codigo' => $row['D'] ?? '', ':nombre' => $row['E'] ?? '',
                    ':turno' => $row['F'] ?? '', ':cuadrilla' => $row['G'] ?? '',
                    ':departamento' => $row['H'] ?? '', ':seccion' => $row['I'] ?? '',
                    ':cargo' => $row['J'] ?? '', ':recibo_no' => $reciboNo,
                    ':periodo' => $row['K'] ?? '', ':asignaciones_texto' => $row['L'] ?? '',
                    ':asignaciones_cant' => $row['M'] ?? '', ':asignaciones_monto' => $row['N'] ?? '',
                    ':deducciones_texto' => $row['O'] ?? '', ':deducciones_cant' => $row['P'] ?? '',
                    ':deducciones_monto' => $row['Q'] ?? '', ':saldos_texto' => $row['R'] ?? '',
                    ':total_asignaciones' => $row['S'] ?? '', ':total_deducciones' => $row['T'] ?? '',
                    ':neto_letras' => $row['U'] ?? '', ':neto_pagar' => $row['V'] ?? '',
                    ':salarios_devengados' => $row['W'] ?? '', ':disponible_prestaciones' => $row['X'] ?? '',
                    ':cuenta_bancaria' => $row['Y'] ?? ''
                ]);
            }
        } catch (PDOException $e) {
            throw new Exception("Error en BD al insertar el recibo '{$reciboNo}' (Cédula: {$cedula}): " . $e->getMessage());
        }
    }

    /**
     * Valida si ya existe el número de recibo de forma independiente (permitiendo duplicidad de cédula)
     */
    private function existeDuplicado(string $tabla, string $reciboNo): bool
    {
        $sql = "SELECT COUNT(*) FROM {$tabla} WHERE recibo_no = :recibo_no";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':recibo_no' => $reciboNo]);
        return $stmt->fetchColumn() > 0;
    }
}