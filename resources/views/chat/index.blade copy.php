<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white leading-tight">💬 Chat con la IA</h2>
    </x-slot>

    <div class="relative max-w-4xl mx-auto px-4 h-[calc(100vh-8rem)]">
        {{-- Contenedor de mensajes --}}
        <div id="chat-container" class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 h-full overflow-y-auto pb-32 space-y-4" style="    margin-top: 20px;
    height: fit-content;">
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

        {{-- Formulario fijo abajo --}}
        <form method="POST" action="{{ route('chat.enviar') }}" id="chat-form" class="fixed bottom-0 left-0 right-0 max-w-4xl mx-auto px-4 py-4 bg-white dark:bg-gray-900 border-t dark:border-gray-700 flex items-center space-x-2 z-50">
            @csrf
            <input type="text" name="mensaje" id="mensaje" placeholder="Escribe tu mensaje..." class="flex-1 px-4 py-2 rounded-lg border dark:bg-gray-700 dark:text-white focus:outline-none focus:ring focus:border-blue-300" required autocomplete="off">
            <button type="submit" id="enviar-btn" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Enviar</button>
            <div id="spinner" class="hidden animate-spin ml-2 h-6 w-6 border-4 border-blue-500 border-t-transparent rounded-full"></div>
        </form>
    </div>

    <script>
        const chatContainer = document.getElementById('chat-container');
        chatContainer.scrollTop = chatContainer.scrollHeight;

        const form = document.getElementById('chat-form');
        const spinner = document.getElementById('spinner');
        const enviarBtn = document.getElementById('enviar-btn');
        const mensaje = document.getElementById('mensaje');

        form.addEventListener('submit', () => {
            enviarBtn.disabled = true;
            // mensaje.disabled = true;
            spinner.classList.remove('hidden');
        });
    </script>
</x-app-layout>
