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
        require_once __DIR__ . '/../models/Modelo_Recibos.php';
        require_once __DIR__ . '/../models/Modelo_reportes.php';

        $database = new Database();
        $pdo = $database->getConnection('default');
        $pdoRecibos = $database->getConnection('recibos');

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

                    if ($id > 0) {
                        if (!empty($password)) {
                            $passHash = password_hash($password, PASSWORD_DEFAULT);
                            $sql = "UPDATE usuarios SET usuario = ?, cedula = ?, email = ?, rol = ?, password = ?, intentos_fallidos = 0, bloqueado_hasta = NULL WHERE id = ?";
                            $stmt = $pdo->prepare($sql);
                            $stmt->execute([$usuario, $cedula, $email, $rol, $passHash, $id]);
                        } else {
                            $sql = "UPDATE usuarios SET usuario = ?, cedula = ?, email = ?, rol = ? WHERE id = ?";
                            $stmt = $pdo->prepare($sql);
                            $stmt->execute([$usuario, $cedula, $email, $rol, $id]);
                        }
                    } else {
                        if (empty($password)) {
                            header('Location: ' . BASE_URL . 'admin?error=La contraseña es obligatoria');
                            exit;
                        }
                        $passHash = password_hash($password, PASSWORD_DEFAULT);
                        $sql = "INSERT INTO usuarios (usuario, cedula, email, rol, password, creado_en) VALUES (?, ?, ?, ?, ?, NOW())";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute([$usuario, $cedula, $email, $rol, $passHash]);
                    }
                }

                if ($accion === 'eliminar') {
                    $id = intval($_POST['id'] ?? 0);
                    if ($id > 1) {
                        $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
                        $stmt->execute([$id]);
                    }
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
            $filtroTipo     = $_GET['tipo'] ?? ''; // Cambiado de periodo a tipo
            $filtroBusqueda = trim($_GET['busqueda'] ?? '');

            // 1. Obtención de Recibos mediante su Modelo
            $modeloRecibos = new ModeloRecibos($pdoRecibos);
            $listaRecibos = $modeloRecibos->obtenerTodosLosRecibos() ?? [];

            // 2. Obtención de Usuarios
            $stmtU = $pdo->query("
                SELECT 
                    id, usuario, cedula, email, rol, 
                    creado_en AS creado, 
                    CASE 
                        WHEN bloqueado_hasta IS NOT NULL AND bloqueado_hasta > NOW() THEN 'bloqueado'
                        ELSE 'activo'
                    END AS estado 
                FROM usuarios 
                ORDER BY id ASC
            ");
            $usuariosIniciales = $stmtU->fetchAll(PDO::FETCH_ASSOC) ?? [];

            // Estadísticas para tarjetas
            $totalUsuarios = count($usuariosIniciales);
            $totalActivos = count(array_filter($usuariosIniciales, fn($u) => $u['estado'] === 'activo'));
            $totalBloqueados = count(array_filter($usuariosIniciales, fn($u) => $u['estado'] !== 'activo'));
            $totalAdmins = count(array_filter($usuariosIniciales, fn($u) => $u['rol'] === 'admin'));

            // 3. Obtención de Reportes mediante el Modelo dedicado (filtrando por tipo)
            $modeloReportes = new ModeloReportes($pdoRecibos);
            $listaReportes = $modeloReportes->obtenerReportes($filtroTipo, $filtroBusqueda);

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