<?php

/*
  SecurityTrait
 
 Centraliza las herramientas de seguridad, validacin y redireccion 
 compartidas por todos los controladores de la aplicacion.
 */
trait SecurityTrait 
{
    /*
     Limpia y valida un correo electronico.
     */
    public function sanitizarEmail(string $email): string|false
    {
        $emailLimpio = trim($email);
        if (!filter_var($emailLimpio, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        return $emailLimpio;
    }

    /*
      Sanitiza cadenas de texto para prevenir ataques XSS.
     */
    public function sanitizarTexto(string $texto): string
    {
        $textoLimpio = trim($texto);
        return htmlspecialchars($textoLimpio, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /*
      Garantiza que la sesion este iniciada y que el usuario este autenticado.
     */
    public function verificarSesion(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['usuario_id'])) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            
            // Usamos SweetAlert para avisar que debe iniciar sesión
            $_SESSION['swal'] = [
                'icon'  => 'warning',
                'title' => 'Sesión requerida',
                'text'  => 'Debes iniciar sesión para acceder a esta sección.'
            ];
            $this->redireccionar('auth/login');
        }
    }

    /*
      Comprueba si el rol del usuario autenticado tiene acceso permitido.
     */
    public function verificarRol(array $rolesPermitidos): void
    {
        $this->verificarSesion(); // Primero aseguramos que haya sesión

        $rolActual = $_SESSION['rol'] ?? 'usuario';

        if (!in_array($rolActual, $rolesPermitidos, true)) {
            // Unificamos el mensaje de error para que use SweetAlert
            $_SESSION['swal'] = [
                'icon'  => 'error',
                'title' => 'Acceso denegado',
                'text'  => 'No tienes los permisos necesarios para ver esta sección.'
            ];
            $this->redireccionar('dashboard/index');
        }
    }

    /*
     Destruye la sesion de forma agresiva porque el usuario ha sido bloqueado.
     */
    public function destruirSesionPorBloqueo(int $minutosRestantes): void
    {
        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(), 
                '', 
                time() - 42000,
                $params["path"], 
                $params["domain"],
                $params["secure"], 
                $params["httponly"]
            );
        }

        session_destroy();
        session_start();
        
        // Unificamos el mensaje de bloqueo para que use SweetAlert
        $_SESSION['swal'] = [
            'icon'  => 'warning',
            'title' => 'Cuenta bloqueada',
            'text'  => "Tu cuenta ha sido bloqueada por seguridad. Intente nuevamente en {$minutosRestantes} minuto(s)."
        ];

        $this->redireccionar('auth/login');
    }

    /*
      Valida que una contraseña cumpla con los requisitos de seguridad.
     */
    public function validarPasswordFuerte(string $password): string|true 
    {
        if (strlen($password) < 8) {
            return "La contraseña debe tener al menos 8 caracteres.";
        }
        if (!preg_match('/[A-Z]/', $password)) {
            return "La contraseña debe contener al menos una letra mayúscula.";
        }
        if (!preg_match('/[a-z]/', $password)) {
            return "La contraseña debe contener al menos una letra minúscula.";
        }
        if (!preg_match('/[0-9]/', $password)) {
            return "La contraseña debe contener al menos un número.";
        }
        if (!preg_match('/[^A-Za-z0-9]/', $password)) {
            return "La contraseña debe contener al menos un carácter especial (ej. !@#$).";
        }
        
        return true; // Si pasa todas las pruebas, es fuerte
    }

    /*
     Valida que un nombre completo solo contenga letras y espacios.
     (Evita inyeccion de números o simbolos en el nombre).
     */
    public function validarNombreCompleto(string $nombre): string|true 
    {
        $nombreLimpio = trim($nombre);
        
        if (strlen($nombreLimpio) < 3) {
            return "El nombre debe tener al menos 3 caracteres.";
        }
        
        if (strlen($nombreLimpio) > 100) {
            return "El nombre no puede exceder los 100 caracteres.";
        }

        // Regex: solo letras (con tildes y ñ) y espacios. Sin números ni símbolos.
        if (!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/', $nombreLimpio)) {
            return "El nombre solo debe contener letras y espacios (sin números ni caracteres especiales).";
        }
        
        return true;
    }

    /*
     Helper privado para centralizar las redirecciones y cortar la ejecucion.
     Ahora es "a prueba de subcarpetas" gracias a BASE_URL.
     */
    private function redireccionar(string $url): void
    {
        // ltrim quita la barra inicial si la hay, y le pega el BASE_URL
        $urlCompleta = BASE_URL . ltrim($url, '/');
        
        header("Location: {$urlCompleta}");
        exit; // CRTICO: Siempre usar exit despues de un header Location
    }
}