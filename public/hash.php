<?php
// archivo: generar_hash.php (ejecutar UNA VEZ y luego borrar)
$password = 'Admin123*';
echo password_hash($password, PASSWORD_BCRYPT);
// Copia el resultado y actualízalo en tu base de datos