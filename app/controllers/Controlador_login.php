<?php
// app/controllers/Controlador_login.php

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../models/modelo_login.php';
require_once __DIR__ . '/SecurityTrait.php';

class ControladorLogin 
{
    use SecurityTrait;

    // Método para mostrar la vista del login
    public function index(): void
    {
        if (isset($_SESSION['usuario_id'])) {
            if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin') {
                header('Location: ' . BASE_URL . 'admin');
            } else {
                header('Location: ' . BASE_URL . 'empleado');
            }
            exit;
        }

        $rutaVista = __DIR__ . '/../views/vista_login.php';
        if (file_exists($rutaVista)) {
            require_once $rutaVista;
        } else {
            die("Error crítico: No se encontró el archivo de la vista en: " . $rutaVista);
        }
    }
    
    // Método para procesar el formulario
    public function procesar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . 'login');
            exit;
        }

        try {
            $database = new Database();
            $pdoLogin = $database->getConnection('default'); 
            $modeloLogin = new ModeloLogin($pdoLogin);

            if (isset($_POST['registro'])) {
                // ... (mantén tu código de registro aquí tal cual lo tienes)
            }

            if (isset($_POST['login'])) {
                $emailOrUser = trim($_POST['email'] ?? ''); 
                $password    = $_POST['password'] ?? '';

                if (empty($emailOrUser) || empty($password)) {
                    header('Location: ' . BASE_URL . 'login?error=campos_vacios');
                    exit;
                }

                $usuario = $modeloLogin->obtenerPorEmailOUsuario($emailOrUser);

                if (!$usuario) {
                    header('Location: ' . BASE_URL . 'login?error=credenciales_incorrectas');
                    exit;
                }

                $bloqueoInfo = $modeloLogin->verificarBloqueoUsuario($usuario['id']);
                if ($bloqueoInfo['bloqueado']) {
                    header('Location: ' . BASE_URL . 'login?error=cuenta_bloqueada&minutos=' . $bloqueoInfo['minutos_restantes']);
                    exit;
                }

                if (password_verify($password, $usuario['password'])) {
                    $modeloLogin->reiniciarIntentos($usuario['id']);
                    session_regenerate_id(true);

                    $_SESSION['usuario_id']     = $usuario['id'];
                    $_SESSION['usuario_nombre'] = $usuario['usuario']; 
                    $_SESSION['usuario_cedula'] = $usuario['cedula']; 
                    $_SESSION['usuario_email']  = $usuario['email'];
                    $_SESSION['usuario_rol']    = $usuario['rol'];    
                    $_SESSION['rol']            = $usuario['rol'];    

                    if ($usuario['rol'] === 'admin') {
                        header('Location: ' . BASE_URL . 'admin');
                        exit;
                    } else {
                        header('Location: ' . BASE_URL . 'empleado');
                        exit;
                    }
                } else {
                    $intentosActuales = $usuario['intentos_fallidos'] ?? 0;
                    $modeloLogin->registrarIntentoFallido($usuario['id'], $intentosActuales);
                    $bloqueoActualizado = $modeloLogin->verificarBloqueoUsuario($usuario['id']);

                    if ($bloqueoActualizado['bloqueado']) {
                        header('Location: ' . BASE_URL . 'login?error=cuenta_bloqueada&minutos=' . $bloqueoActualizado['minutos_restantes']);
                        exit;
                    } else {
                        header('Location: ' . BASE_URL . 'login?error=credenciales_incorrectas');
                        exit;
                    }
                } 
            }
        } catch (Exception $e) {
            echo "<div style='color: red; padding: 20px; font-family: sans-serif;'>";
            echo "<strong>Error crítico en el controlador:</strong> " . $e->getMessage();
            echo "</div>";
        }
    }
}