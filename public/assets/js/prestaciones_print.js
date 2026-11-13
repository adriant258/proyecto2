// public/assets/js/prestaciones_print.js

document.addEventListener("DOMContentLoaded", function() {
    const urlParams = new URLSearchParams(window.location.search);
    
    // Descarga automática si viene el parámetro ?auto=1
    if (urlParams.get('auto') === '1') {
        setTimeout(() => {
            const elemento = document.querySelector('.receipt-container');
            const cedulaUser = window.CEDULA_USUARIO || '000000';
            const timestamp = new Date().getTime();
            const opciones = {
                margin:       5,
                filename:     `prestaciones_${cedulaUser}_${timestamp}.pdf`,
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2, useCORS: true },
                jsPDF:        { unit: 'mm', format: 'a4', orientation: 'landscape' }
            };
            
            html2pdf().from(elemento).set(opciones).save().then(() => {
                setTimeout(() => window.close(), 1500);
            }).catch(err => {
                console.error("Error al generar el PDF:", err);
            });
        }, 800);
    }
});