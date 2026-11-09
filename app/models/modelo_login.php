<?php
// app/Models/LoginModel.php

/**
 * ModeloLogin
 * 
 * Encapsula TODA la lógica de acceso a datos de la tabla 'usuarios'.
 * Los controladores NUNCA deben escribir SQL directamente.
 */
class ModeloLogin 
{
    // La conexión PDO que nos inyecta el controlador o archivo principal
    private PDO $pdo;

    public function __construct(PDO $pdo) 
    {
        $this->pdo = $pdo;
    }

    // =========================================================
    // MÉTODOS DE AUTENTICACIÓN (Login)
    // =========================================================

    /**
     * Busca un usuario por su correo electrónico.
     * Devuelve un array asociativo con los datos o false si no existe.
     */
    public function obtenerPorEmail(string $email): array|false 
    {
        $sql = "SELECT id, usuario, email, password, rol, intentos_fallidos, bloqueado_hasta 
                FROM usuarios 
                WHERE email = :email 
                LIMIT 1";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['email' => $email]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Verifica si un usuario está actualmente bloqueado.
     * Usa UNIX_TIMESTAMP para una comparación de enteros infalible, 
     * eliminando cualquier conflicto de zonas horarias entre PHP y MySQL.
     */
    public function verificarBloqueoUsuario(int $usuarioId): array 
    {
        $sql = "SELECT UNIX_TIMESTAMP(bloqueado_hasta) as bloqueo_ts FROM usuarios WHERE id = :id LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $usuarioId]);
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);

        // Si no hay usuario o el campo es NULL o cero, no está bloqueado
        if (!$fila || empty($fila['bloqueo_ts'])) {
            return ['bloqueado' => false, 'minutos_restantes' => 0];
        }

        $tsBloqueo = (int)$fila['bloqueo_ts'];
        $tsActual = time(); // Hora actual universal de PHP.

        // Si el timestamp de bloqueo es mayor al actual, está bloqueado
        if ($tsBloqueo > $tsActual) {
            $diffSegundos = $tsBloqueo - $tsActual; // <-- Corregido en una sola línea
            $minutosRestantes = (int)ceil($diffSegundos / 60); // Redondea hacia arriba
            
            // Protección: nunca devolver 0 si la condición de arriba se cumplió
            $minutosRestantes = max(1, $minutosRestantes);
            
            return [
                'bloqueado' => true, 
                'minutos_restantes' => $minutosRestantes
            ];
        }

        // El tiempo ya pasó.
        return ['bloqueado' => false, 'minutos_restantes' => 0];
    }

    /**
     * Registra un intento fallido de login.
     * Si llega a 3 intentos, bloquea la cuenta por 15 minutos usando DATE_ADD de MySQL.
     */
    public function registrarIntentoFallido(int $usuarioId, int $intentosActuales): int 
    {
        $nuevosIntentos = $intentosActuales + 1;

        if ($nuevosIntentos >= 3) {
            $sql = "UPDATE usuarios 
                    SET intentos_fallidos = :intentos, 
                        bloqueado_hasta = DATE_ADD(NOW(), INTERVAL 15 MINUTE) 
                    WHERE id = :id";
        } else {
            $sql = "UPDATE usuarios SET intentos_fallidos = :intentos WHERE id = :id";
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['intentos' => $nuevosIntentos, 'id' => $usuarioId]);

        return $nuevosIntentos;
    }

    /**
     * Reinicia los intentos fallidos, quita el bloqueo y actualiza el último login (tras login exitoso).
     */
    public function reiniciarIntentos(int $usuarioId): void 
    {
        $sql = "UPDATE usuarios 
                SET intentos_fallidos = 0, 
                    bloqueado_hasta = NULL, 
                    ultimo_login = NOW() 
                WHERE id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $usuarioId]);
    }

    /**
     * Alias para mantener compatibilidad si el controlador llama a registrarLoginExitoso
     */
    public function registrarLoginExitoso(int $usuarioId): void 
    {
        $this->reiniciarIntentos($usuarioId);
    }

    /**
     * Verifica si un correo electrónico ya está registrado.
     */
    public function emailExiste(string $email): bool 
    {
        $sql = "SELECT id FROM usuarios WHERE email = :email LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['email' => $email]);
        
        return $stmt->fetch() !== false;
    }

    /**
     * Crea un nuevo usuario en la base de datos.
     */
    public function crearUsuario($usuario, $cedula, $email, $passwordHash) 
    {
        $sql = "INSERT INTO usuarios (usuario, cedula, email, password) VALUES (:usuario, :cedula, :email, :password)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':usuario'   => $usuario,
            ':cedula'    => $cedula,
            ':email'     => $email,
            ':password'  => $passwordHash
        ]);
    }

   /**
    * Busca un usuario por su email o nombre de usuario para el Login.
    */
    public function obtenerPorEmailOUsuario($identificador) 
    {
        $sql = "SELECT id, usuario, cedula, email, password, rol, intentos_fallidos, bloqueado_hasta 
                FROM usuarios 
                WHERE email = :email OR usuario = :usuario 
                LIMIT 1";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':email'   => $identificador,
            ':usuario' => $identificador
        ]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}