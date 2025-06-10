<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white leading-tight">📧 Correos Entrantes</h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto px-4">
        <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
            <form method="GET" action="{{ route('correos.index') }}" class="mb-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Buscar -->
                    <div>
                        <label for="buscar" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Buscar</label>
                        <input type="text" name="buscar" id="buscar" value="{{ request('buscar') }}"
                            class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    </div>

                    <!-- Categoría -->
                    <div>
                        <label for="categoria" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Categoría</label>
                        <select name="categoria" id="categoria"
                            class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            <option value="">-- Todas --</option>
                            @foreach ($categorias as $cat)
                                <option value="{{ $cat }}" @if(request('categoria') == $cat) selected @endif>{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Fecha desde -->
                    <div>
                        <label for="fecha_inicio" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Desde</label>
                        <input type="date" name="fecha_inicio" id="fecha_inicio" value="{{ request('fecha_inicio') }}"
                            class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    </div>

                    <!-- Fecha hasta -->
                    <div>
                        <label for="fecha_fin" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Hasta</label>
                        <input type="date" name="fecha_fin" id="fecha_fin" value="{{ request('fecha_fin') }}"
                            class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    </div>
                </div>

                <div class="flex items-center mt-4 space-x-3">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Filtrar</button>
                    @if(request()->hasAny(['categoria', 'buscar', 'fecha_inicio', 'fecha_fin']))
                        <a href="{{ route('correos.index') }}" class="text-sm text-gray-500 dark:text-gray-300 hover:underline">Quitar filtros</a>
                    @endif
                </div>
            </form>

            <table class="min-w-full text-sm text-left">
                <thead class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                    <tr>
                        <th class="px-4 py-2">Remitente</th>
                        <th class="px-4 py-2">Categoria</th>
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
                            <td class="px-4 py-2">{{ $correo->categoria }}</td>
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
