<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white leading-tight">
            Lista de Clientes
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto px-4">
        <a href="{{ route('clientes.create') }}"
           class="mb-4 inline-block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">+ Nuevo Cliente</a>

        @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 shadow rounded overflow-hidden">
            <table class="w-full table-auto">
                <thead class="bg-gray-100 dark:bg-gray-700 text-left">
                    <tr class="text-sm text-gray-700 dark:text-gray-300">
                        <th class="p-4">Empresa</th>
                        <th class="p-4">CIF</th>
                        <th class="p-4">Contacto</th>
                        <th class="p-4">Email</th>
                        <th class="p-4">Teléfono</th>
                        <th class="p-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($clientes as $cliente)
                        <tr class="border-t border-gray-200 dark:border-gray-700 text-sm text-gray-800 dark:text-white">
                            <td class="p-4">{{ $cliente->nombre_empresa }}</td>
                            <td class="p-4">{{ $cliente->cif }}</td>
                            <td class="p-4">{{ $cliente->persona_contacto }}</td>
                            <td class="p-4">{{ $cliente->email }}</td>
                            <td class="p-4">{{ $cliente->telefono }}</td>
                            <td class="p-4 flex gap-2">
                                <a href="{{ route('clientes.edit', $cliente) }}" class="text-blue-500 hover:underline">Editar</a>
                                <form method="POST" action="{{ route('clientes.destroy', $cliente) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:underline"
                                            onclick="return confirm('¿Seguro que deseas eliminar este cliente?')">
                                        Eliminar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="p-4">
                {{ $clientes->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
