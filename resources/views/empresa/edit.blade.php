<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
            Configuración de la Empresa
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-4 text-green-800 bg-green-100 dark:bg-green-900 dark:text-green-200 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ $empresa ? route('empresa.update') : route('empresa.store') }}"
                  enctype="multipart/form-data"
                  class="bg-white dark:bg-gray-800 p-6 rounded shadow">
                @csrf
                @if($empresa)
                    @method('PUT')
                @endif

                @foreach (['nombre', 'cif', 'direccion', 'codigo_postal', 'ciudad', 'provincia', 'telefono', 'email', 'web'] as $campo)
                    <div class="mb-4">
                        <label for="{{ $campo }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            {{ ucfirst(str_replace('_', ' ', $campo)) }}
                        </label>
                        <input type="text"
                               name="{{ $campo }}"
                               id="{{ $campo }}"
                               value="{{ old($campo, $empresa->$campo ?? '') }}"
                               class="mt-1 block w-full rounded-md bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white focus:ring focus:ring-blue-500 focus:border-blue-500" />
                        @error($campo)
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                @endforeach

                <div class="mb-4">
                    <label for="mapa" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Mapa (iframe)
                    </label>
                    <textarea name="mapa" id="mapa" rows="3"
                              class="mt-1 block w-full rounded-md bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white focus:ring focus:ring-blue-500 focus:border-blue-500">{{ old('mapa', $empresa->mapa ?? '') }}</textarea>
                </div>

                <div class="mb-4">
                    <label for="notas" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Notas
                    </label>
                    <textarea name="notas" id="notas" rows="3"
                              class="mt-1 block w-full rounded-md bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white focus:ring focus:ring-blue-500 focus:border-blue-500">{{ old('notas', $empresa->notas ?? '') }}</textarea>
                </div>

                <div class="mb-4">
                    <label for="logo" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Logo
                    </label>
                    <input type="file" name="logo" id="logo"
                           class="mt-1 block w-full text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md" />
                    @if($empresa && $empresa->logo_path)
                        <div class="mt-2">
                            <img src="{{ asset('storage/' . $empresa->logo_path) }}" class="h-16">
                        </div>
                    @endif
                </div>

                <button type="submit"
                        class="mt-4 px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded shadow">
                    Guardar
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
