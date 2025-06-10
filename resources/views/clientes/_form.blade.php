@php
    $fields = [
        'nombre_empresa' => 'Empresa',
        'cif' => 'CIF',
        'persona_contacto' => 'Persona de contacto',
        'email' => 'Email',
        'telefono' => 'Teléfono',
        'telefono_secundario' => 'Teléfono secundario',
        'direccion' => 'Dirección',
        'codigo_postal' => 'Código Postal',
        'ciudad' => 'Ciudad',
        'provincia' => 'Provincia',
        'pais' => 'País',
        'web' => 'Web',
        'sector' => 'Sector',
    ];
@endphp

@foreach ($fields as $name => $label)
    <div class="mb-4">
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            {{ $label }}
        </label>
        <input type="text" name="{{ $name }}" id="{{ $name }}"
               value="{{ old($name, $cliente->$name ?? '') }}"
               class="mt-1 block w-full rounded-md bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white" />
        @error($name)
            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
        @enderror
    </div>
@endforeach

<div class="mb-4">
    <label for="notas" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Notas</label>
    <textarea name="notas" id="notas" rows="3"
              class="mt-1 block w-full rounded-md bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white">{{ old('notas', $cliente->notas ?? '') }}</textarea>
</div>

<button type="submit"
        class="mt-4 px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded shadow">
    Guardar
</button>
