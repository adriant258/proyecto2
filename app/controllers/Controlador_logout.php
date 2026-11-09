<?php
// app/controllers/Controlador_logout.php

class ControladorLogout 
{
    public function index(): void 
    {
        // 1. Limpiar todas las variables de sesión
        $_SESSION = array();

        // 2. Destruir la cookie de sesión si está habilitada en el navegador
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 4200,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // 3. Destruir completamente la sesión en el servidor
        session_destroy();

        // 4. Redirigir al usuario utilizando la ruta limpia del Front Controller
        header('Location: ' . BASE_URL . 'login?logout=success');
        exit;
    }
}