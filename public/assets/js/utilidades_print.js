// public/assets/js/utilidades_print.js

document.addEventListener("DOMContentLoaded", function() {
    const urlParams = new URLSearchParams(window.location.search);
    
    // Verificar si la URL trae el parámetro de descarga automática (?auto=1)
    if (urlParams.get('auto') === '1') {
        setTimeout(() => {
            const elemento = document.querySelector('.receipt-container');
            const reciboNoVal = window.RECIBO_NUMERO || '000';
            const timestamp = new Date().getTime();
            const opciones = {
                margin:       5,
                filename:     `recibo_utilidades_${reciboNoVal}_${timestamp}.pdf`,
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2, useCORS: true },
                jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
            };
            
            // Genera la descarga automática y cierra la ventana tras completarse
            html2pdf().from(elemento).set(opciones).save().then(() => {
                setTimeout(() => window.close(), 1500);
            }).catch(err => {
                console.error("Error al generar el PDF:", err);
            });
        }, 800);
    }
});