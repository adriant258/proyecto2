<?php
// app/models/Modelo_Usuarios.php

class ModeloUsuarios 
{
    private $pdo;

    public function __construct($pdo) 
    {
        $this->pdo = $pdo;
    }

    public function obtenerTodos(): array 
    {
        $stmt = $this->pdo->query("
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
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?? [];
    }

    public function guardar($id, $usuario, $cedula, $email, $rol, $estado, $password): bool 
    {
        if ($id > 0) {
            if (!empty($password)) {
                $passHash = password_hash($password, PASSWORD_DEFAULT);
                $sql = "UPDATE usuarios SET usuario = ?, cedula = ?, email = ?, rol = ?, password = ?, intentos_fallidos = 0, bloqueado_hasta = NULL WHERE id = ?";
                $stmt = $this->pdo->prepare($sql);
                return $stmt->execute([$usuario, $cedula, $email, $rol, $passHash, $id]);
            } else {
                $sql = "UPDATE usuarios SET usuario = ?, cedula = ?, email = ?, rol = ? WHERE id = ?";
                $stmt = $this->pdo->prepare($sql);
                return $stmt->execute([$usuario, $cedula, $email, $rol, $id]);
            }
        } else {
            $passHash = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO usuarios (usuario, cedula, email, rol, password, creado_en) VALUES (?, ?, ?, ?, ?, NOW())";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$usuario, $cedula, $email, $rol, $passHash]);
        }
    }

    public function eliminar(int $id): bool 
    {
        if ($id > 1) { // Protege al administrador principal (ID 1)
            $stmt = $this->pdo->prepare("DELETE FROM usuarios WHERE id = ?");
            return $stmt->execute([$id]);
        }
        return false;
    }
}