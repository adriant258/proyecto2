<?php
// app/models/Modelo_reportes.php

class ModeloReportes {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function obtenerReportes($filtroTipo = '', $filtroBusqueda = '') {
    $sql = "
        SELECT * FROM (
            SELECT 
                u.usuario COLLATE utf8mb4_general_ci AS usuario, 
                u.cedula COLLATE utf8mb4_general_ci AS cedula, 
                u.email COLLATE utf8mb4_general_ci AS email, 
                'Recibo Regular' COLLATE utf8mb4_general_ci AS tipo_recibo, 
                r.periodo COLLATE utf8mb4_general_ci AS periodo, 
                r.fecha_visualizacion 
            FROM app_login.usuarios u 
            INNER JOIN app_proyecto_db.recibos r 
            ON u.cedula COLLATE utf8mb4_general_ci = r.cedula COLLATE utf8mb4_general_ci
            
            UNION ALL
            
            SELECT 
                u.usuario COLLATE utf8mb4_general_ci, 
                u.cedula COLLATE utf8mb4_general_ci, 
                u.email COLLATE utf8mb4_general_ci, 
                'Utilidades' COLLATE utf8mb4_general_ci, 
                ut.periodo COLLATE utf8mb4_general_ci, 
                ut.fecha_visualizacion 
            FROM app_login.usuarios u 
            INNER JOIN app_proyecto_db.utilidades ut 
            ON u.cedula COLLATE utf8mb4_general_ci = ut.cedula COLLATE utf8mb4_general_ci
            
            UNION ALL
            
            SELECT 
                u.usuario COLLATE utf8mb4_general_ci, 
                u.cedula COLLATE utf8mb4_general_ci, 
                u.email COLLATE utf8mb4_general_ci, 
                'Vacaciones' COLLATE utf8mb4_general_ci, 
                vl.periodo_laboral COLLATE utf8mb4_general_ci AS periodo, 
                vl.fecha_visualizacion 
            FROM app_login.usuarios u 
            INNER JOIN app_proyecto_db.vacaciones_liquidaciones vl 
            ON u.cedula COLLATE utf8mb4_general_ci = vl.cedula COLLATE utf8mb4_general_ci
            
            UNION ALL
            
            SELECT 
                u.usuario COLLATE utf8mb4_general_ci, 
                u.cedula COLLATE utf8mb4_general_ci, 
                u.email COLLATE utf8mb4_general_ci, 
                'Prestaciones / Intereses' COLLATE utf8mb4_general_ci, 
                ip.periodo_reporte COLLATE utf8mb4_general_ci AS periodo, 
                ip.fecha_visualizacion 
            FROM app_login.usuarios u 
            INNER JOIN app_proyecto_db.intereses_prestaciones ip 
            ON u.cedula COLLATE utf8mb4_general_ci = ip.cedula COLLATE utf8mb4_general_ci
        ) AS reporte_general
        WHERE 1=1
    ";

    $params = [];
    
    // Filtramos por tipo_recibo en lugar de periodo
    if (!empty($filtroTipo)) {
        $sql .= " AND tipo_recibo COLLATE utf8mb4_general_ci = ?";
        $params[] = $filtroTipo;
    }
    
    if (!empty($filtroBusqueda)) {
        $sql .= " AND (cedula COLLATE utf8mb4_general_ci LIKE ? OR usuario COLLATE utf8mb4_general_ci LIKE ?)";
        $params[] = "%$filtroBusqueda%";
        $params[] = "%$filtroBusqueda%";
    }

    $sql .= " ORDER BY fecha_visualizacion DESC";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC) ?? [];
}
    public function obtenerReporteVisualizacion(string $filtroPeriodo = '', string $filtroBusqueda = ''): array 
    {
        // Reutilizamos la misma consulta robusta de obtenerReportes pero asegurando el manejo de fechas nulas
        $sql = "
            SELECT * FROM (
                SELECT 
                    u.usuario COLLATE utf8mb4_general_ci AS usuario, 
                    u.cedula COLLATE utf8mb4_general_ci AS cedula, 
                    u.email COLLATE utf8mb4_general_ci AS email, 
                    'Recibo Regular' COLLATE utf8mb4_general_ci AS tipo_recibo, 
                    r.periodo COLLATE utf8mb4_general_ci AS periodo, 
                    COALESCE(r.fecha_visualizacion, 'No visualizado') AS fecha_visualizacion 
                FROM app_login.usuarios u 
                INNER JOIN app_proyecto_db.recibos r 
                ON u.cedula COLLATE utf8mb4_general_ci = r.cedula COLLATE utf8mb4_general_ci
                
                UNION ALL
                
                SELECT 
                    u.usuario COLLATE utf8mb4_general_ci, 
                    u.cedula COLLATE utf8mb4_general_ci, 
                    u.email COLLATE utf8mb4_general_ci, 
                    'Utilidades' COLLATE utf8mb4_general_ci, 
                    ut.periodo COLLATE utf8mb4_general_ci, 
                    COALESCE(ut.fecha_visualizacion, 'No visualizado') AS fecha_visualizacion 
                FROM app_login.usuarios u 
                INNER JOIN app_proyecto_db.utilidades ut 
                ON u.cedula COLLATE utf8mb4_general_ci = ut.cedula COLLATE utf8mb4_general_ci
                
                UNION ALL
                
                SELECT 
                    u.usuario COLLATE utf8mb4_general_ci, 
                    u.cedula COLLATE utf8mb4_general_ci, 
                    u.email COLLATE utf8mb4_general_ci, 
                    'Vacaciones' COLLATE utf8mb4_general_ci, 
                    vl.periodo_laboral COLLATE utf8mb4_general_ci AS periodo, 
                    COALESCE(vl.fecha_visualizacion, 'No visualizado') AS fecha_visualizacion 
                FROM app_login.usuarios u 
                INNER JOIN app_proyecto_db.vacaciones_liquidaciones vl 
                ON u.cedula COLLATE utf8mb4_general_ci = vl.cedula COLLATE utf8mb4_general_ci
                
                UNION ALL
                
                SELECT 
                    u.usuario COLLATE utf8mb4_general_ci, 
                    u.cedula COLLATE utf8mb4_general_ci, 
                    u.email COLLATE utf8mb4_general_ci, 
                    'Prestaciones / Intereses' COLLATE utf8mb4_general_ci, 
                    ip.periodo_reporte COLLATE utf8mb4_general_ci AS periodo, 
                    COALESCE(ip.fecha_visualizacion, 'No visualizado') AS fecha_visualizacion 
                FROM app_login.usuarios u 
                INNER JOIN app_proyecto_db.intereses_prestaciones ip 
                ON u.cedula COLLATE utf8mb4_general_ci = ip.cedula COLLATE utf8mb4_general_ci
            ) AS reporte_general
            WHERE 1=1
        ";

        $params = [];
        
        if (!empty($filtroPeriodo) && $filtroPeriodo !== 'Todos' && $filtroPeriodo !== 'Todos los períodos') {
            $sql .= " AND periodo COLLATE utf8mb4_general_ci = ?";
            $params[] = $filtroPeriodo;
        }
        
        if (!empty($filtroBusqueda)) {
            $sql .= " AND (cedula COLLATE utf8mb4_general_ci LIKE ? OR usuario COLLATE utf8mb4_general_ci LIKE ?)";
            $params[] = "%$filtroBusqueda%";
            $params[] = "%$filtroBusqueda%";
        }

        $sql .= " ORDER BY (fecha_visualizacion = 'No visualizado') ASC, fecha_visualizacion DESC";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?? [];
        } catch (Exception $e) {
            return [];
        }
    }
}