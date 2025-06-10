// // Crear contenedor para toasts múltiples
// if (!document.getElementById('toast-container')) {
//     const container = document.createElement('div');
//     container.id = 'toast-container';
//     container.style.position = 'fixed';
//     container.style.top = '20px';
//     container.style.right = '20px';
//     container.style.zIndex = 1060; // sobre cualquier modal
//     document.body.appendChild(container);
// }

// // Mixin personalizado para permitir varios toasts simultáneos
// const Toast = Swal.mixin({
//     toast: true,
//     position: 'top-end',
//     target: '#toast-container',
//     showConfirmButton: true,
//     confirmButtonText: 'Ver',
//     timer: 2000,
//     timerProgressBar: true,
//     showClass: {
//         popup: 'swal2-animate__fadeInRight'
//     },
//     hideClass: {
//         popup: 'swal2-animate__fadeOutRight'
//     },
//     customClass: {
//         popup: 'toast-popup',
//         confirmButton: 'toast-button'
//     },
//     willOpen: (popup) => {
//         popup.style.width = '300px';
//     }
// });

// async function revisarNuevosCorreos() {
//     try {
//         const response = await fetch('/notificaciones/correos');
//         const correos = await response.json();

//         correos.forEach(correo => {
//             Toast.fire({
//                 icon: 'info',
//                 title: '📧 Nuevo correo recibido',
//                 html: `<div style="font-size: 13px; text-align: left;">
//                           <strong>De:</strong> ${correo.remitente}<br>
//                           <strong>Asunto:</strong> ${correo.asunto}
//                        </div>`
//             }).then((result) => {
//                 if (result.isConfirmed) {
//                     window.open(`/correos/${correo.id}`, '_blank');
//                 }
//             });
//         });

//         console.log("Correos consultados:", correos);
//     } catch (error) {
//         console.error("Error consultando correos:", error);
//     }
// }



// setInterval(revisarNuevosCorreos, 150);
// document.addEventListener('DOMContentLoaded', revisarNuevosCorreos);

function mostrarToastPersonalizado(correo) {
    const container = document.getElementById('custom-toast-container');

    const toast = document.createElement('div');
    toast.classList.add('custom-toast');
    toast.innerHTML = `
        <strong>📧 Nuevo correo recibido</strong>
        <div><strong>De:</strong> ${correo.remitente}</div>
        <div><strong>Asunto:</strong> ${correo.asunto}</div>
        <button class="toast-button">Ver</button>
    `;

    toast.querySelector('.toast-button').addEventListener('click', () => {
        window.open(`/correos/${correo.id}`, '_blank');
    });

    container.appendChild(toast);

    setTimeout(() => {
        marcarComoNotificado(correo.id);
        toast.remove();
    }, 5000);
}
async function marcarComoNotificado(id) {
    await fetch(`/notificaciones/correos/${id}/notificar`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({})
    });
}

async function revisarNuevosCorreos() {
    try {
        const response = await fetch('/notificaciones/correos');
        const correos = await response.json();

        correos.forEach((correo, index) => {
            setTimeout(() => {
                console.log("Mostrando correo:", correo.id);
                mostrarToastPersonalizado(correo);
            }, index * 1500); // 1500 ms entre cada toast
        });

        console.log("Correos consultados:", correos);
    } catch (error) {
        console.error("Error consultando correos:", error);
    }
}

setInterval(revisarNuevosCorreos, 15000);
document.addEventListener('DOMContentLoaded', revisarNuevosCorreos);
