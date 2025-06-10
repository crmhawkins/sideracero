<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white leading-tight">📨 Ver Correo</h2>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto px-4">
        <div class="bg-white dark:bg-gray-800 p-6 rounded shadow">
            @if ($correo->categoria)
                <div class="mb-4">
                    <strong class="text-gray-700 dark:text-gray-300">Categoría:</strong>
                    <p class="text-gray-900 dark:text-white">{{ $correo->categoria }}</p>
                </div>
            @endif
            <div class="mb-4">
                <strong class="text-gray-700 dark:text-gray-300">Remitente:</strong>
                <p class="text-gray-900 dark:text-white">{{ $correo->remitente }}</p>
            </div>

            <div class="mb-4">
                <strong class="text-gray-700 dark:text-gray-300">Asunto:</strong>
                <p class="text-gray-900 dark:text-white">{{ $correo->asunto }}</p>
            </div>

            <div class="mb-4">
                <strong class="text-gray-700 dark:text-gray-300">Cuerpo:</strong>
                <pre class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-100 p-4 rounded whitespace-pre-wrap">{{ $correo->cuerpo }}</pre>
            </div>
            @if ($correo->respuesta_sugerida)
                <div class="mb-4">
                    <strong class="text-gray-700 dark:text-gray-300">Respuesta sugerida:</strong>
                    <pre class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-100 p-4 rounded whitespace-pre-wrap">{{ $correo->respuesta_sugerida }}</pre>
                </div>
            @endif

            @if ($correo->adjunto_path)
                <div class="mb-4">
                    <strong class="text-gray-700 dark:text-gray-300">Adjunto:</strong>
                    <a href="{{ asset('storage/' . $correo->adjunto_path) }}" target="_blank" class="text-blue-500 hover:underline">Ver adjunto</a>
                </div>
            @endif

            <div class="mb-4">
                <strong class="text-gray-700 dark:text-gray-300">Analizado:</strong>
                <span class="{{ $correo->analizado ? 'text-green-500' : 'text-yellow-500' }}">
                    {{ $correo->analizado ? 'Sí' : 'No' }}
                </span>
            </div>

            @if ($correo->productos_detectados)
                <div class="mb-4">
                    <strong class="text-gray-700 dark:text-gray-300">Productos detectados:</strong>
                    <pre class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-100 p-4 rounded whitespace-pre-wrap">
                        {{ json_encode($correo->productos_detectados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}
                    </pre>

                </div>
            @endif


        </div>
    </div>
</x-app-layout>
