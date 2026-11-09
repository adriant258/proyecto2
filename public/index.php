<?php
// public/index.php
// Front Controller principal del sistema Ajeven

// 1. Configurar la cookie de sesión para que expire al cerrar el navegador
session_set_cookie_params([
    'lifetime' => 0, 
    'path' => '/',
    'secure' => false, // Cambiar a true si usas HTTPS en producción
    'httponly' => true,
    'samesite' => 'Strict'
]);

session_start();

// 2. Detección automática de BASE_URL
$scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME']);
$scriptDir = rtrim(dirname($scriptName), '/');
$baseUrl = ($scriptDir === '/' || $scriptDir === '') ? '/' : $scriptDir . '/';
define('BASE_URL', $baseUrl);

// 3. Control de tiempo de inactividad en el servidor (Ejemplo: 15 minutos = 900 segundos)
$uriActual = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if (strpos($uriActual, 'login') === false && isset($_SESSION['usuario_rol'])) {
    $tiempo_limite_inactividad = 15 * 60; 

    if (isset($_SESSION['ultimo_acceso'])) {
        $inactivo = time() - $_SESSION['ultimo_acceso'];
        if ($inactivo > $tiempo_limite_inactividad) {
            // Destruir sesión por inactividad
            $_SESSION = array();
            session_destroy();
            header('Location: ' . BASE_URL . 'login?expired=1');
            exit;
        }
    }
    // Actualizar el sello de tiempo del último acceso en cada petición válida
    $_SESSION['ultimo_acceso'] = time();
}

// 4. Endpoint interno para que JavaScript renueve la sesión silenciosamente
if (isset($_GET['route']) && $_GET['route'] === 'api/renovar-sesion') {
    $_SESSION['ultimo_acceso'] = time();
    echo json_encode(['status' => 'success']);
    exit;
}

// 5. Endpoint genérico para preparar datos en sesión y evitar exponerlos en la URL GET
$uriSolicitada = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$rutaRelativa = str_replace(BASE_URL, '', urldecode($uriSolicitada));
$rutaRelativa = '/' . ltrim($rutaRelativa, '/');
$segmentos = explode('/', trim($rutaRelativa, '/'));

if (isset($segmentos[1]) && $segmentos[1] === 'preparar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['temp_recibo'] = $_POST['recibo_no'] ?? null;
    $_SESSION['temp_cedula'] = $_POST['cedula'] ?? null;
    echo json_encode(['status' => 'ok']);
    exit;
}

// 6. Obtención y limpieza de la URI
$modulo = $segmentos[0] ?? '';
$accion = $segmentos[1] ?? '';

// Si es una petición POST al login sin acción explícita, redirigir al método procesar
if ($modulo === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST' && empty($accion)) {
    $accion = 'procesar';
} else {
    $accion = $accion !== '' ? $accion : 'index';
}

// Guardar ID u otros parámetros si existen en la URL
if (isset($segmentos[2])) {
    $_GET['id'] = $segmentos[2];
}

// 7. Mapeo de módulos a archivos físicos y nombres de clases
$controladoresPermitidos = [
    ''              => ['archivo' => 'Controlador_login.php', 'clase' => 'ControladorLogin'],
    'login'         => ['archivo' => 'Controlador_login.php', 'clase' => 'ControladorLogin'],
    'admin'         => ['archivo' => 'Controlador_admin.php', 'clase' => 'ControladorAdmin'],
    'empleado'      => ['archivo' => 'Controlador_empleado.php', 'clase' => 'ControladorEmpleado'],
    'recibos'       => ['archivo' => 'Controlador_recibo_pago.php', 'clase' => 'ControladorReciboPago'],
    'utilidades'    => ['archivo' => 'Controlador_utilidades.php', 'clase' => 'ControladorUtilidades'],
    'vacaciones'    => ['archivo' => 'Controlador_vacaciones.php', 'clase' => 'ControladorVacaciones'],
    'prestaciones'  => ['archivo' => 'Controlador_prestaciones.php', 'clase' => 'ControladorPrestaciones'],
    'reportes'      => ['archivo' => 'Controlador_reporte_print.php', 'clase' => 'ControladorReportePrint'],
    'logout'        => ['archivo' => 'Controlador_logout.php', 'clase' => 'ControladorLogout']
];

// Si no se escribe módulo, redirigir al login
if ($modulo === '') {
    header('Location: ' . BASE_URL . 'login');
    exit;
}

if (!array_key_exists($modulo, $controladoresPermitidos)) {
    http_response_code(404);
    die("<h1>404 - Módulo no encontrado</h1>");
}

$infoControlador = $controladoresPermitidos[$modulo];
$archivoControlador = __DIR__ . '/../app/controllers/' . $infoControlador['archivo'];
$nombreClase = $infoControlador['clase'];

if (file_exists($archivoControlador)) {
    require_once $archivoControlador;

    if (class_exists($nombreClase)) {
        $instancia = new $nombreClase();

        if (method_exists($instancia, $accion)) {
            // Ejecutar el método correspondiente (ej. index o procesar)
            $instancia->$accion();
        } else {
            http_response_code(500);
            die("<h1>500 - Error interno</h1><p>El método '{$accion}' no existe en el controlador.</p>");
        }
    } else {
        http_response_code(500);
        die("<h1>500 - Error interno</h1><p>La clase '{$nombreClase}' no está definida en el archivo.</p>");
    }
} else {
    http_response_code(500);
    die("<h1>500 - Error interno</h1><p>No se encontró el archivo del controlador: {$infoControlador['archivo']}</p>");
}