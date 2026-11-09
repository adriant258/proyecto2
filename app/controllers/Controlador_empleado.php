<?php
// app/controllers/Controlador_empleado.php

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../models/Modelo_recibos.php';
require_once __DIR__ . '/SecurityTrait.php'; 

class ControladorEmpleado 
{
    use SecurityTrait;

    // Convertimos la función en un método estático de la clase
    public static function tipoRecibo(array $recibo): array {
        $periodo  = strtolower($recibo['periodo'] ?? '');
        $reciboNo = strtolower($recibo['recibo_no'] ?? '');
        $tipoDoc  = strtolower($recibo['tipo_doc'] ?? '');

        if ($tipoDoc === 'prestaciones' || str_contains($periodo, 'prestaciones') || str_contains($periodo, 'intereses')) {
            return ['prestaciones', 'bg-danger', 'Prestaciones Sociales'];
        } elseif ($tipoDoc === 'utilidades' || str_contains($periodo, 'utilidades') || str_contains($periodo, 'utilidad')) {
            return ['utilidades', 'bg-dark', 'Utilidades'];
        } elseif (str_contains($periodo, 'mensual') || str_contains($periodo, 'mes')) {
            return ['mensual', 'bg-secondary', 'Mensual'];
        } elseif (str_contains($periodo, 'vacaciones') || str_contains($reciboNo, 'vac')) {
            return ['vacaciones', 'bg-warning text-dark', 'Vacaciones'];
        }
        return ['semanal', 'bg-primary', 'Semanal'];
    }

    public function index(): void 
    {
        $this->verificarSesion(); 

        $cedulaEmpleado = $_SESSION['usuario_cedula'] ?? $_SESSION['cedula'] ?? '';
        $nombreUsuario  = $_SESSION['usuario_nombre'] ?? 'Empleado';

        try {
            $database = new Database();
            $pdo = $database->getConnection('recibos'); 
            
            $modeloRecibos = new ModeloRecibos($pdo);
            $listaRecibosNorm = $modeloRecibos->obtenerRecibosPorCedula($cedulaEmpleado) ?? [];

            foreach ($listaRecibosNorm as &$r) {
                $r['tipo_doc'] = 'recibo';
            }
            unset($r);

            $stmtVacaciones = $pdo->prepare("SELECT recibo_no, periodo_laboral as periodo, neto_pagar, 'vacaciones' as tipo_doc FROM vacaciones_liquidaciones WHERE cedula = ?");
            $stmtVacaciones->execute([$cedulaEmpleado]);
            $listaVacaciones = $stmtVacaciones->fetchAll(PDO::FETCH_ASSOC) ?? [];

            $stmtUtil = $pdo->prepare("SELECT recibo_no, periodo, neto_pagar, 'utilidades' as tipo_doc FROM utilidades WHERE cedula = ?");
            $stmtUtil->execute([$cedulaEmpleado]);
            $listaUtilidades = $stmtUtil->fetchAll(PDO::FETCH_ASSOC) ?? [];

            $stmtPrestaciones = $pdo->prepare("SELECT recibo_no, periodo_reporte as periodo, detalles_meses, 'prestaciones' as tipo_doc FROM intereses_prestaciones WHERE cedula = ?");
            $stmtPrestaciones->execute([$cedulaEmpleado]);
            $prestacionesRegs = $stmtPrestaciones->fetchAll(PDO::FETCH_ASSOC) ?? [];

            $listaPrestaciones = [];
            foreach ($prestacionesRegs as $row) {
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
                $row['neto_pagar'] = $netoCalculado;
                $listaPrestaciones[] = $row;
            }

            if (!empty($cedulaEmpleado)) {
                if (count($listaRecibosNorm) > 0) {
                    $pdo->prepare("UPDATE recibos SET fecha_visualizacion = NOW() WHERE cedula = ? AND fecha_visualizacion IS NULL")->execute([$cedulaEmpleado]);
                }
                if (count($listaVacaciones) > 0) {
                    $pdo->prepare("UPDATE vacaciones_liquidaciones SET fecha_visualizacion = NOW() WHERE cedula = ? AND fecha_visualizacion IS NULL")->execute([$cedulaEmpleado]);
                }
                if (count($listaUtilidades) > 0) {
                    $pdo->prepare("UPDATE utilidades SET fecha_visualizacion = NOW() WHERE cedula = ? AND fecha_visualizacion IS NULL")->execute([$cedulaEmpleado]);
                }
                if (count($prestacionesRegs) > 0) {
                    $pdo->prepare("UPDATE intereses_prestaciones SET fecha_visualizacion = NOW() WHERE cedula = ? AND fecha_visualizacion IS NULL")->execute([$cedulaEmpleado]);
                }
            }

            $listaRecibos = array_merge($listaRecibosNorm, $listaVacaciones, $listaUtilidades, $listaPrestaciones);
            
            // Calculas contadores usando el método estático
            $conteo = ['semanal' => 0, 'mensual' => 0, 'vacaciones' => 0, 'utilidades' => 0, 'prestaciones' => 0];
            foreach ($listaRecibos as $r) { 
                $conteo[self::tipoRecibo($r)[0]]++; 
            }
            $totalRecibos = array_sum($conteo);

            require_once __DIR__ . '/../views/vista_empleado.php';

        } catch (Exception $e) {
            echo "<div style='color: red; padding: 20px;'><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    }
}