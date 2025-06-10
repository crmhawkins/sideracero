<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white leading-tight">💬 Chat con la IA</h2>
    </x-slot>

    <div class="relative max-w-4xl mx-auto px-4 h-[calc(100vh-8rem)]">
        {{-- Contenedor mensajes --}}
        <div id="chat-container" class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 h-full overflow-y-auto pb-32 space-y-4 mt-4">
            @foreach ($mensajes as $mensaje)
                <div class="flex flex-col space-y-1">
                    <div class="self-start bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white p-3 rounded-lg max-w-xl">
                        <strong>🧑 Tú:</strong> {{ $mensaje->mensaje }}
                    </div>
                    @if ($mensaje->respuesta)
                        <div class="self-end bg-blue-100 dark:bg-blue-700 text-gray-900 dark:text-white p-3 rounded-lg max-w-xl">
                            <strong>🤖 IA:</strong>
                            <div class="mt-1">{!! $mensaje->respuesta !!}</div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        {{-- Formulario --}}
        <form id="chat-form" class="fixed bottom-0 left-0 right-0 max-w-4xl mx-auto px-4 py-4 bg-white dark:bg-gray-900 border-t dark:border-gray-700 flex items-center space-x-2 z-50">
            @csrf
            <input type="text" name="mensaje" id="mensaje" placeholder="Escribe tu mensaje..." class="flex-1 px-4 py-2 rounded-lg border dark:bg-gray-700 dark:text-white focus:outline-none" required autocomplete="off">
            <button type="submit" id="enviar-btn" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Enviar</button>
        </form>
    </div>

    {{-- SweetAlert2 CDN (si no lo tienes ya) --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        const form = document.getElementById('chat-form');
        const input = document.getElementById('mensaje');
        const enviarBtn = document.getElementById('enviar-btn');
        const chatContainer = document.getElementById('chat-container');

        form.addEventListener('submit', async function (e) {
            e.preventDefault();

            const mensaje = input.value.trim();
            if (!mensaje) return;

            // Agrega mensaje del usuario al chat
            const userMsg = `
                <div class="flex flex-col space-y-1">
                    <div class="self-start bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white p-3 rounded-lg max-w-xl">
                        <strong>🧑 Tú:</strong> ${mensaje}
                    </div>
                    <div class="self-end bg-blue-100 dark:bg-blue-700 text-gray-900 dark:text-white p-3 rounded-lg max-w-xl" id="escribiendo">
                        <strong>🤖 IA:</strong> <div class="mt-1 italic">Escribiendo...</div>
                    </div>
                </div>`;
            chatContainer.insertAdjacentHTML('beforeend', userMsg);
            chatContainer.scrollTop = chatContainer.scrollHeight;

            // Desactiva el formulario
            input.disabled = true;
            enviarBtn.disabled = true;

            try {
                const token = document.querySelector('input[name="_token"]').value;
                const response = await fetch("{{ route('chat.enviar') }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": token,
                        "Accept": "application/json",
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({ mensaje })
                });

                const data = await response.json();

                // Elimina "escribiendo"
                document.getElementById("escribiendo")?.remove();

                if (data.html) {
                    chatContainer.insertAdjacentHTML('beforeend', data.html);
                    chatContainer.scrollTop = chatContainer.scrollHeight;
                } else {
                    Swal.fire("Error", "La IA no devolvió ninguna respuesta válida.", "warning");
                }

            } catch (error) {
                console.error(error);
                Swal.fire("Error", "No se pudo contactar con el servidor.", "error");
            }

            // Resetea
            input.value = "";
            input.disabled = false;
            enviarBtn.disabled = false;
            input.focus();
        });
    </script>
</x-app-layout>
