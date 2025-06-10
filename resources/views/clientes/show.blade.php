<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white leading-tight">
            Detalles del Cliente
        </h2>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto px-4">
        <div class="bg-white dark:bg-gray-800 shadow rounded p-6">
            <dl class="divide-y divide-gray-200 dark:divide-gray-700">

                @foreach([
                    'nombre_empresa' => 'Empresa',
                    'cif' => 'CIF',
                    'persona_contacto' => 'Persona de Contacto',
                    'email' => 'Email',
                    'telefono' => 'Teléfono',
                    'telefono_secundario' => 'Teléfono Secundario',
                    'direccion' => 'Dirección',
                    'codigo_postal' => 'Código Postal',
                    'ciudad' => 'Ciudad',
                    'provincia' => 'Provincia',
                    'pais' => 'País',
                    'web' => 'Web',
                    'sector' => 'Sector',
                    'notas' => 'Notas',
                ] as $field => $label)
                    @php $valor = $cliente->$field @endphp
                    @if(!is_null($valor) && $valor !== '')
                        <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-300">{{ $label }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white sm:mt-0 sm:col-span-2">
                                @if($field === 'web')
                                    <a href="{{ $valor }}" target="_blank" class="text-blue-500 underline">{{ $valor }}</a>
                                @else
                                    {{ $valor }}
                                @endif
                            </dd>
                        </div>
                    @endif
                @endforeach

            </dl>

            <div class="mt-6 flex gap-3">
                <a href="{{ route('clientes.edit', $cliente) }}"
                   class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Editar</a>
                <a href="{{ route('clientes.index') }}"
                   class="px-4 py-2 bg-gray-300 dark:bg-gray-700 text-black dark:text-white rounded hover:bg-gray-400 dark:hover:bg-gray-600">Volver</a>
            </div>
        </div>
    </div>
</x-app-layout>
