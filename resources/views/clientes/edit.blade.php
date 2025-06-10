<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white leading-tight">Editar Cliente</h2>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto px-4">
        <form method="POST" action="{{ route('clientes.update', $cliente) }}">
            @csrf
            @method('PUT')
            @include('clientes._form', ['cliente' => $cliente])
        </form>
    </div>
</x-app-layout>
