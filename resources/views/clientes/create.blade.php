<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white leading-tight">Nuevo Cliente</h2>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto px-4">
        <form method="POST" action="{{ route('clientes.store') }}" class="bg-white dark:bg-gray-800 p-6 rounded shadow">
            @csrf
            @include('clientes._form', ['cliente' => null])
        </form>
    </div>
</x-app-layout>
