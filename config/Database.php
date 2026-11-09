<?php
// config/Database.php

class Database {

    // Atributos privados para configuración de PDO
    private $host = '127.0.0.1';
    private $user = 'root';
    private $pass = '';
    private $charset = 'utf8mb4';
    
    // Almacén para guardar las conexiones activas
    private $connections = [];

    public function __construct() {
        // Conexión por defecto a la base de datos principal al instanciar
        $this->connect('default', 'app_login');
    }

    /**
     * Método privado que instancia PDO para una base de datos específica
     */
    public function connect($key, $dbName) {
        if (!isset($this->connections[$key])) {
            $dsn = "mysql:host={$this->host};dbname={$dbName};charset={$this->charset}";
            
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                $this->connections[$key] = new PDO($dsn, $this->user, $this->pass, $options);
            } catch (\PDOException $e) {
                throw new \PDOException("Error de conexión a la BD '{$dbName}': " . $e->getMessage(), (int)$e->getCode());
            }
        }
        
        return $this->connections[$key];
    }

    /**
     * Devuelve la conexión requerida ('default' o 'recibos')
     */
    public function getConnection($key = 'default') {
        // Si piden la conexión de recibos y no existe aún, la creamos al momento
        if ($key === 'recibos' && !isset($this->connections['recibos'])) {
            $this->connect('recibos', 'app_proyecto_db');
        }

        if (!isset($this->connections[$key])) {
            throw new \Exception("La conexión '{$key}' no está configurada.");
        }

        return $this->connections[$key];
    }
}