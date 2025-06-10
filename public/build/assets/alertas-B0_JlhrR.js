function r(o){const e=document.getElementById("custom-toast-container"),t=document.createElement("div");t.classList.add("custom-toast"),t.innerHTML=`
        <strong>📧 Nuevo correo recibido</strong>
        <div><strong>De:</strong> ${o.remitente}</div>
        <div><strong>Asunto:</strong> ${o.asunto}</div>
        <button class="toast-button">Ver</button>
    `,t.querySelector(".toast-button").addEventListener("click",()=>{window.open(`/correos/${o.id}`,"_blank")}),e.appendChild(t),setTimeout(()=>{c(o.id),t.remove()},5e3)}async function c(o){await fetch(`/notificaciones/correos/${o}/notificar`,{method:"POST",headers:{"Content-Type":"application/json","X-CSRF-TOKEN":document.querySelector('meta[name="csrf-token"]').getAttribute("content")},body:JSON.stringify({})})}async function n(){try{const e=await(await fetch("/notificaciones/correos")).json();e.forEach((t,s)=>{setTimeout(()=>{console.log("Mostrando correo:",t.id),r(t)},s*1500)}),console.log("Correos consultados:",e)}catch(o){console.error("Error consultando correos:",o)}}setInterval(n,15e3);document.addEventListener("DOMContentLoaded",n);
