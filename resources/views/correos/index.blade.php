<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white leading-tight">📧 Correos Entrantes</h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto px-4">
        <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
            <table class="min-w-full text-sm text-left">
                <thead class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                    <tr>
                        <th class="px-4 py-2">Remitente</th>
                        <th class="px-4 py-2">Asunto</th>
                        <th class="px-4 py-2">Recibido</th>
                        <th class="px-4 py-2">Analizado</th>
                        <th class="px-4 py-2">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($correos as $correo)
                        <tr class="text-gray-700 dark:text-gray-300">
                            <td class="px-4 py-2">{{ $correo->remitente }}</td>
                            <td class="px-4 py-2">{{ $correo->asunto }}</td>
                            <td class="px-4 py-2">{{ $correo->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-2">
                                @if($correo->analizado)
                                    <span class="text-green-600 font-semibold">Sí</span>
                                @else
                                    <span class="text-yellow-500">No</span>
                                @endif
                            </td>
                            <td class="px-4 py-2">
                                <a href="{{ route('correos.show', $correo) }}" class="text-blue-600 hover:underline">Ver</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-4 text-center text-gray-500">No hay correos aún.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
