<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script>
            // Este bloque se ejecuta antes de cargar el CSS para evitar parpadeo
            (() => {
                const theme = localStorage.getItem('theme')
                const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches
                if (theme === 'dark' || (!theme && systemPrefersDark)) {
                    document.documentElement.classList.add('dark')
                }
            })();
        </script>

    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
        <style>
            #custom-toast-container {
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 9999;
                display: flex;
                flex-direction: column;
                gap: 10px;
                align-items: flex-end;
            }

            .custom-toast {
                background: #fff;
                color: #000;
                padding: 12px 16px;
                border-radius: 10px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
                font-size: 14px;
                width: 300px;
                animation: slideIn 0.3s ease-out, fadeOut 0.3s ease-in 1.7s forwards;
                position: relative;
            }

            .custom-toast strong {
                display: block;
            }

            .custom-toast .toast-button {
                margin-top: 8px;
                background: #2563eb;
                color: #fff;
                padding: 4px 10px;
                border: none;
                border-radius: 6px;
                font-size: 13px;
                cursor: pointer;
            }

            @keyframes slideIn {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }

            @keyframes fadeOut {
                to { opacity: 0; transform: translateX(100%); height: 0; margin: 0; padding: 0; }
            }
            </style>

        <div id="custom-toast-container"></div>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            function updateThemeIcon() {
                const button = document.getElementById('themeToggle');
                const isDark = document.documentElement.classList.contains('dark');

                button.innerHTML = isDark
                    ? `<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m8.485-8.485h-1M4.515 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M12 5a7 7 0 000 14a7 7 0 000-14z" />
                       </svg>` // sol
                    : `<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="currentColor" viewBox="0 0 20 20">
                          <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
                       </svg>`; // luna
            }

            function toggleTheme() {
                const isDark = document.documentElement.classList.toggle('dark');
                localStorage.setItem('theme', isDark ? 'dark' : 'light');
                updateThemeIcon();
            }

            // Inicializa ícono al cargar la página
            document.addEventListener('DOMContentLoaded', updateThemeIcon);
        </script>

        @vite(['resources/js/alertas.js'])

    </body>
</html>
