<?php
// app/controllers/Controlador_admin.php

class ControladorAdmin 
{
    public function index(): void
    {
        // Verificar que sea administrador
        if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] !== 'admin') {
            header('Location: ' . BASE_URL . 'login');
            exit;
        }

        require_once __DIR__ . '/../../config/Database.php';
        require_once __DIR__ . '/../models/Modelo_Usuarios.php';
        require_once __DIR__ . '/../models/Modelo_Recibos.php';
        require_once __DIR__ . '/../models/Modelo_reportes.php';

        $database = new Database();
        $pdo = $database->getConnection('default');
        $pdoRecibos = $database->getConnection('recibos');

        // Instanciar modelos
        $modeloUsuarios = new ModeloUsuarios($pdo);
        $modeloRecibos  = new ModeloRecibos($pdoRecibos);
        $modeloReportes = new ModeloReportes($pdoRecibos);

        // ==========================================
        // PROCESAR PETICIONES POST (CREAR, EDITAR, ELIMINAR)
        // ==========================================
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accion = $_POST['accion'] ?? '';

            try {
                if ($accion === 'guardar') {
                    $id       = intval($_POST['id'] ?? 0);
                    $usuario  = trim($_POST['usuario'] ?? '');
                    $cedula   = trim($_POST['cedula'] ?? '');
                    $email    = trim($_POST['email'] ?? '');
                    $rol      = trim($_POST['rol'] ?? 'usuario');
                    $estado   = trim($_POST['estado'] ?? 'activo');
                    $password = $_POST['password'] ?? '';

                    if (empty($usuario) || empty($email) || empty($cedula)) {
                        header('Location: ' . BASE_URL . 'admin?error=Campos obligatorios vacios');
                        exit;
                    }

                    if ($id === 0 && empty($password)) {
                        header('Location: ' . BASE_URL . 'admin?error=La contraseña es obligatoria');
                        exit;
                    }

                    // Delegamos la operación al modelo de usuarios
                    $modeloUsuarios->guardar($id, $usuario, $cedula, $email, $rol, $estado, $password);
                }

                if ($accion === 'eliminar') {
                    $id = intval($_POST['id'] ?? 0);
                    $modeloUsuarios->eliminar($id);
                }
            } catch (Exception $e) {
                // Manejo de errores silencioso en POST
            }

            header('Location: ' . BASE_URL . 'admin');
            exit;
        }

        // ==========================================
        // CARGA DE DATOS PARA LA VISTA (GET)
        // ==========================================
        try {
            $moduloActual   = $_GET['modulo'] ?? 'usuarios';
            $filtroTipo     = trim($_GET['tipo'] ?? '');
            $filtroPeriodo  = trim($_GET['periodo'] ?? ''); // <--- Capturamos el filtro de período
            $filtroBusqueda = trim($_GET['busqueda'] ?? '');

            // 1. Obtención de Recibos
            $listaRecibos = $modeloRecibos->obtenerTodosLosRecibos() ?? [];

            // 2. Obtención de Usuarios a través del Modelo
            $usuariosIniciales = $modeloUsuarios->obtenerTodos();

            // Estadísticas para tarjetas
            $totalUsuarios    = count($usuariosIniciales);
            $totalActivos     = count(array_filter($usuariosIniciales, fn($u) => $u['estado'] === 'activo'));
            $totalBloqueados  = count(array_filter($usuariosIniciales, fn($u) => $u['estado'] !== 'activo'));
            $totalAdmins      = count(array_filter($usuariosIniciales, fn($u) => $u['rol'] === 'admin'));

            // 3. Obtención de Reportes (filtrando por tipo, período y cédula/usuario)
            $listaReportes = $modeloReportes->obtenerReporteVisualizacion($filtroTipo, $filtroPeriodo, $filtroBusqueda);

            $nombreUsuario = $_SESSION['usuario_nombre'] ?? 'Administrador';

            // Cargar la vista de administración
            require_once __DIR__ . '/../views/vista_admin.php';

        } catch (Exception $e) {
            echo "<div style='color: red; padding: 20px; font-family: sans-serif;'>";
            echo "<strong>Error al cargar el panel:</strong> " . htmlspecialchars($e->getMessage());
            echo "</div>";
        }
    }
}