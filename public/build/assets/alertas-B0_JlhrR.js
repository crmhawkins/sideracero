// function r(o){const e=document.getElementById("custom-toast-container"),t=document.createElement("div");t.classList.add("custom-toast"),t.innerHTML=`
//         <strong>📧 Nuevo correo recibido</strong>
//         <div><strong>De:</strong> ${o.remitente}</div>
//         <div><strong>Asunto:</strong> ${o.asunto}</div>
//         <button class="toast-button">Ver</button>
//     `,t.querySelector(".toast-button").addEventListener("click",()=>{window.open(`/correos/${o.id}`,"_blank")}),e.appendChild(t),setTimeout(()=>{c(o.id),t.remove()},5e3)}async function c(o){await fetch(`/notificaciones/correos/${o}/notificar`,{method:"POST",headers:{"Content-Type":"application/json","X-CSRF-TOKEN":document.querySelector('meta[name="csrf-token"]').getAttribute("content")},body:JSON.stringify({})})}async function n(){try{const e=await(await fetch("/notificaciones/correos")).json();e.forEach((t,s)=>{setTimeout(()=>{console.log("Mostrando correo:",t.id),r(t)},s*1500)}),console.log("Correos consultados:",e)}catch(o){console.error("Error consultando correos:",o)}}setInterval(n,15e3);document.addEventListener("DOMContentLoaded",n);


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

setInterval(revisarNuevosCorreos, 1500);
document.addEventListener('DOMContentLoaded', revisarNuevosCorreos);
