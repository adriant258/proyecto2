/*
  Inicializador de SweetAlert2
 Lee los datos de la sesión (renderizados en el DOM por PHP) y muestra la alerta.
 */
document.addEventListener('DOMContentLoaded', function() {
    const swalDataElement = document.getElementById('swal-data');
    
    if (swalDataElement) {
        // Extraemos los datos de los atributos data
        const icon = swalDataElement.getAttribute('data-icon');
        const title = swalDataElement.getAttribute('data-title');
        const text = swalDataElement.getAttribute('data-text');
        
        // Disparamos SweetAlert2
        Swal.fire({
            icon: icon,
            title: title,
            text: text,
            confirmButtonColor: '#667eea', 
            confirmButtonText: 'Entendido',
            backdrop: true,
            allowOutsideClick: false
        }).then((result) => {
            // Opcional: Si es un exito en el registro, podriamos redirigir automaticamente
            // if (icon === 'success' && title.includes('Registro')) { ... }
        });
    }
});