<?php
// Opcional: Asegurar sesión activa si se requiere validar variables de sesión en la vista
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal de Empleados - Acceso</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>

<body class="min-vh-100 d-flex align-items-center justify-content-center" style="background-color: #0b132b;">

<div class="container" style="max-width: 420px;">
    
    <!-- Formulario de Login -->
    <div id="loginForm" class="bg-white p-4 rounded-4 shadow">
        <h2 class="text-center mb-4 text-dark h3">Iniciar Sesión</h2>
        <form action="<?= BASE_URL ?>login" method="POST">
            <div class="mb-3">
                <label for="loginUser" class="form-label text-secondary fw-semibold">Usuario o Correo</label>
                <input type="text" class="form-control" id="loginUser" name="email" required placeholder="Ej: JuanPerez">
            </div>
            <div class="mb-3">
                <label for="loginPass" class="form-label text-secondary fw-semibold">Contraseña</label>
                <div class="position-relative">
                    <input type="password" class="form-control pe-5" id="loginPass" name="password" required placeholder="••••••••">
                    <i class="fa-solid fa-eye position-absolute top-50 end-0 translate-middle-y me-3 text-secondary" 
                       id="togglePass" 
                       style="cursor: pointer;"></i> 
                </div>
            </div>
            <button type="submit" name="login" class="btn btn-primary w-100 mt-2 fw-bold py-2">Ingresar</button>
        </form>
        <p class="text-center mt-3 text-secondary mb-0 small">
            ¿No tienes cuenta? <a href="#" id="showRegister" class="text-primary text-decoration-none fw-bold">Regístrate aquí</a>
        </p>
    </div>

    <!-- Formulario de Registro -->
    <div id="registerForm" class="bg-white p-4 rounded-4 shadow d-none">
        <h2 class="text-center mb-4 text-dark h3">Crear Cuenta</h2>
        <form action="<?= BASE_URL ?>registro" method="POST">
            <div class="mb-3">
                <label for="regUser" class="form-label text-secondary fw-semibold">Elige un Nombre de Usuario</label>
                <input type="text" class="form-control" id="regUser" name="nombre" required placeholder="Ej: Carlos95">
            </div>
            <div class="mb-3">
                <label for="regCedula" class="form-label text-secondary fw-semibold">Ingrese Cédula</label>
                <input type="text" class="form-control" id="regCedula" name="cedula" required placeholder="Ej: 12345678">
            </div>
            <div class="mb-3">
                <label for="regEmail" class="form-label text-secondary fw-semibold">Correo Electrónico</label>
                <input type="email" class="form-control" id="regEmail" name="email" required placeholder="carlos@gmail.com">
            </div>
            <div class="mb-3">
                <label for="regPass" class="form-label text-secondary fw-semibold">Contraseña</label>
                <div class="position-relative">
                    <input type="password" class="form-control pe-5" id="regPass" name="password" required placeholder="••••••••">
                    <i class="fa-solid fa-eye position-absolute top-50 end-0 translate-middle-y me-3 text-secondary" 
                        id="toggleRegPass" 
                        style="cursor: pointer;"></i>
                </div>
                <small class="text-muted d-mt-1" style="font-size: 11px;">Mínimo 8 caracteres, 1 mayúscula, 1 minúscula, 1 número y 1 especial</small>
            </div>
            <div class="mb-3">
                <label for="regConfirmPass" class="form-label text-secondary fw-semibold">Confirmar Contraseña</label>
                <div class="position-relative">
                    <input type="password" class="form-control pe-5" id="regConfirmPass" name="confirm_password" required placeholder="••••••••">
                    <i class="fa-solid fa-eye position-absolute top-50 end-0 translate-middle-y me-3 text-secondary" 
                        id="toggleRegConfirmPass" 
                        style="cursor: pointer;"></i>
                </div>
            </div>
            <button type="submit" name="registro" class="btn btn-primary w-100 mt-2 fw-bold py-2">Registrar Cuenta</button>
        </form>
        <p class="text-center mt-3 text-secondary mb-0 small">
            ¿Ya tienes cuenta? <a href="#" id="showLogin" class="text-primary text-decoration-none fw-bold">Inicia sesión</a>
        </p>
    </div>

</div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- LECTOR DE ALERTAS SWEETALERT (DESDE SESSION / URL) -->
    <?php if (isset($_SESSION['swal'])): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: '<?php echo $_SESSION['swal']['icon']; ?>',
                    title: '<?php echo $_SESSION['swal']['title']; ?>',
                    text: '<?php echo $_SESSION['swal']['text']; ?>',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Aceptar'
                });
            });
        </script>
        <?php unset($_SESSION['swal']); ?>
    <?php endif; ?>

    <?php if (isset($_GET['error']) || isset($_GET['registro'])): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const urlParams = new URLSearchParams(window.location.search);
                const error = urlParams.get('error');
                const registro = urlParams.get('registro');

                if (error) {
                    let titulo = '¡Atención!';
                    let texto = 'Ocurrió un error en el proceso.';
                    let icono = 'error';

                    if (error === 'campos_vacios') {
                        titulo = 'Campos vacíos';
                        texto = 'Por favor, completa todos los campos obligatorios.';
                    } else if (error === 'email_invalido') {
                        titulo = 'Correo inválido';
                        texto = 'El formato del correo electrónico no es válido.';
                    } else if (error === 'nombre_invalido') {
                        titulo = 'Nombre inválido';
                        texto = '<?php echo $_SESSION['error_msg'] ?? "El nombre contiene caracteres no permitidos."; unset($_SESSION['error_msg']); ?>';
                    } else if (error === 'password_debil') {
                        titulo = 'Contraseña insegura';
                        texto = '<?php echo $_SESSION['error_msg'] ?? "La contraseña no cumple con los requisitos mínimos de seguridad."; unset($_SESSION['error_msg']); ?>';
                    } else if (error === 'passwords_no_coinciden') {
                        titulo = 'Error de contraseñas';
                        texto = 'Las contraseñas ingresadas no coinciden.';
                    } else if (error === 'usuario_duplicado') {
                        titulo = 'Usuario existente';
                        texto = 'Ya existe un usuario registrado con este correo o cédula.';
                    } else if (error === 'credenciales_incorrectas') {
                        titulo = 'Acceso denegado';
                        texto = 'Correo, usuario o contraseña incorrectos.';
                    } else if (error === 'cuenta_bloqueada') {
                        titulo = '¡Cuenta Bloqueada!';
                        const minutos = urlParams.get('minutos') || '1';
                        texto = `Demasiados intentos fallidos. Por favor, intente nuevamente en ${minutos} minuto(s).`;
                        icono = 'warning';
                    }

                    Swal.fire({
                        icon: icono,
                        title: titulo,
                        text: texto,
                        confirmButtonColor: '#d33'
                    });
                }

                if (registro === 'exito') {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Registro exitoso!',
                        text: 'Tu cuenta ha sido creada correctamente. Ya puedes iniciar sesión.',
                        confirmButtonColor: '#28a745'
                    });
                }
            });
        </script>
    <?php endif; ?>

    <script>
        // Definimos BASE_URL global para JavaScript de forma dinámica
        const BASE_URL = '<?php echo BASE_URL; ?>';
    </script>
    <!-- Archivo JS externo de login -->
    <script src="<?php echo BASE_URL; ?>assets/js/login.js"></script>
</body>
</html>