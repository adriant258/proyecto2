<?php
// app/controllers/Controlador_recibo_pago.php

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../models/Modelo_recibos.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

class ControladorReciboPago 
{
    /**
     * Método para mostrar el recibo de pago de forma limpia respetando MVC.
     */
    public function index(): void 
    {
        $reciboNo = $_SESSION['temp_recibo'] ?? $_GET['recibo_no'] ?? null;
        $cedula = $_SESSION['temp_cedula'] ?? $_GET['cedula'] ?? null;

        unset($_SESSION['temp_recibo'], $_SESSION['temp_cedula']);

        if (!$reciboNo || !$cedula) {
            echo "<div style='color: red; padding: 20px; font-family: sans-serif;'><strong>Error:</strong> Faltan parámetros de cédula o número de recibo, o la sesión expiró.</div>";
            return;
        }

        try {
            $database = new Database();
            $pdo = $database->getConnection('recibos');

            $modelo = new ModeloRecibos($pdo);
            $datosCrudos = $modelo->obtenerReciboPorParametros($reciboNo, $cedula);

            if (!$datosCrudos) {
                echo "<div style='color: orange; padding: 20px; font-family: sans-serif;'>No se encontró el recibo especificado para este usuario.</div>";
                return;
            }

            // --- LÓGICA DE NEGOCIO Y FORMATEO (MVC: Controlador procesa antes de pasar a la vista) ---

            // 1. Función para limpiar y dividir conceptos por barras o saltos de línea
            $dividirConceptos = function($cadena) {
                if (empty($cadena)) return [];
                $cadena = str_replace(['<br>', '<br/>', '<br />'], "\n", $cadena);
                $lineas = preg_split('/[\r\n|]+/', $cadena);
                return array_values(array_filter(array_map('trim', $lineas), function($val) {
                    return $val !== '';
                }));
            };

            // 2. Función para dar formato numérico (miles con '.', decimales con ',')
            $formatearNumero = function($valor) {
                if ($valor === '' || $valor === null) return '';
                $limpio = str_replace(['BS', 'Bs', 'bs', ' '], '', $valor);
                return is_numeric($limpio) ? number_format((float)$limpio, 2, ',', '.') : $valor;
            };

            // 3. Función para dar formato a los totales con el prefijo BS
            $formatearMoneda = function($valor) {
                if ($valor === '' || $valor === null || $valor === '-') return '-';
                $limpio = str_replace(['BS', 'Bs', 'bs', ' '], '', $valor);
                $limpio = str_replace(',', '', $limpio);
                return is_numeric($limpio) ? 'BS' . number_format((float)$limpio, 2, ',', '.') : $valor;
            };

            // 4. Estructurar el array definitivo que consumirá la vista
            $datos = [
                'recibo_no'               => $datosCrudos['recibo_no'] ?? '-',
                'periodo'                 => $datosCrudos['periodo'] ?? '-',
                'codigo'                  => $datosCrudos['codigo'] ?? '-',
                'nombre'                  => $datosCrudos['nombre'] ?? '-',
                'turno'                   => $datosCrudos['turno'] ?? '-',
                'cuadrilla'               => $datosCrudos['cuadrilla'] ?? '-',
                'cedula'                  => $datosCrudos['cedula'] ?? '-',
                'departamento'            => $datosCrudos['departamento'] ?? '-',
                'seccion'                 => $datosCrudos['seccion'] ?? '-',
                'cargo'                   => $datosCrudos['cargo'] ?? '-',
                
                // Listas desglosadas y formateadas para la tabla principal
                'asig_textos'             => $dividirConceptos($datosCrudos['asignaciones_texto'] ?? ''),
                'asig_cants'              => $dividirConceptos($datosCrudos['asignaciones_cant'] ?? ''),
                'asig_montos'             => array_map($formatearNumero, $dividirConceptos($datosCrudos['asignaciones_monto'] ?? '')),
                
                'ded_textos'              => $dividirConceptos($datosCrudos['deducciones_texto'] ?? ''),
                'ded_cants'               => $dividirConceptos($datosRut = $datosCrudos['deducciones_cant'] ?? ''),
                'ded_montos'              => array_map($formatearNumero, $dividirConceptos($datosCrudos['deducciones_monto'] ?? '')),
                
                'saldos'                  => array_map($formatearNumero, $dividirConceptos($datosCrudos['saldos_texto'] ?? '')),
                
                // Totales y montos generales ya formateados
                'total_asignaciones'      => $formatearMoneda($datosCrudos['total_asignaciones'] ?? 0),
                'total_deducciones'       => $formatearMoneda($datosCrudos['total_deducciones'] ?? 0),
                'neto_letras'             => $datosCrudos['neto_letras'] ?? '-',
                'neto_pagar'              => $formatearMoneda($datosCrudos['neto_pagar'] ?? 0),
                'salarios_devengados'     => $formatearMoneda($datosCrudos['salarios_devengados'] ?? 0),
                'disponible_prestaciones' => $formatearMoneda($datosCrudos['disponible_prestaciones'] ?? 0),
                'cuenta_bancaria'         => $datosCrudos['cuenta_bancaria'] ?? '-'
            ];

            // Cargar la vista limpia pasando únicamente la variable $datos procesada
            require_once __DIR__ . '/../views/vista_recibo_pago_print.php';

        } catch (Exception $e) {
            echo "<div style='color: red; padding: 20px; font-family: sans-serif;'><strong>Error de Conexión o SQL:</strong> " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    }

    /**
     * Método para procesar la importación masiva de recibos a través de un archivo Excel.
     */
    public function importarExcel(): void 
    {
        if (ob_get_length()) {
            ob_clean();
        }
        header('Content-Type: application/json; charset=utf-8');

        $pdo = null;

        try {
            if (!isset($_FILES['archivo_excel']) || $_FILES['archivo_excel']['error'] !== UPLOAD_ERR_OK) {
                echo json_encode(['status' => 'error', 'mensaje' => 'No se ha seleccionado ningún archivo Excel válido.']);
                exit;
            }

            $database = new Database();
            $pdo = $database->getConnection('recibos');
            $modelo = new ModeloRecibos($pdo);

            $archivoTemp = $_FILES['archivo_excel']['tmp_name'];
            $spreadsheet = IOFactory::load($archivoTemp);

            // Iniciar transacción atómica global (Todo o nada)
            $pdo->beginTransaction();

            // Definimos las pestañas maestras exactas de tu archivo Excel
            $hojas = ['Nómina', 'Utilidades', 'Vacaciones', 'Prestaciones Sociales'];
            $totalImportados = 0;

            foreach ($hojas as $nombreHoja) {
                $sheet = $spreadsheet->getSheetByName($nombreHoja);
                if (!$sheet) {
                    continue; // Si la pestaña no existe en el Excel, la salta con seguridad
                }

                $filas = $sheet->toArray(null, true, true, true);
                $totalFilas = count($filas);

                // Empezamos desde la fila 2 (la fila 1 contiene los títulos de las columnas)
                for ($i = 2; $i <= $totalFilas; $i++) {
                    $fila = $filas[$i];

                    // Validar si la fila está completamente vacía (Columnas A y B vacías)
                    if (empty($fila['A']) && empty($fila['B'])) {
                        continue;
                    }

                    // Usamos tu método inteligente del modelo para validar duplicados e insertar
                    $modelo->insertarReciboMasivo($fila, $nombreHoja);
                    $totalImportados++;
                }
            }

            // Confirmar transacción si todo salió bien
            $pdo->commit();
            echo json_encode([
                'status'  => 'ok',
                'mensaje' => "Se han importado {$totalImportados} registros exitosamente de forma masiva."
            ]);
            exit;

        } catch (Exception $e) {
            // Revertir cambios por completo si ocurre cualquier error de duplicidad o SQL
            if ($pdo instanceof PDO && $pdo->inTransaction()) {
                $pdo->rollBack();
            }
            echo json_encode([
                'status'  => 'error',
                'mensaje' => $e->getMessage()
            ]);
            exit;
        }
    }
}