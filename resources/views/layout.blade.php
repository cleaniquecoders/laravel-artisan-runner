<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Artisan Runner</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'media',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
                        mono: ['JetBrains Mono', 'Fira Code', 'monospace'],
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700|jetbrains-mono:400,500" rel="stylesheet" />
    @livewireStyles
</head>
<body class="h-full bg-gradient-to-br from-slate-50 via-white to-blue-50 font-sans antialiased dark:from-slate-950 dark:via-gray-900 dark:to-slate-900">
    <div class="min-h-full">
        {{-- Header --}}
        <header class="border-b border-slate-200/80 bg-white/70 backdrop-blur-lg dark:border-slate-700/50 dark:bg-slate-900/70">
            <div class="mx-auto flex max-w-5xl items-center justify-between px-6 py-4">
                <div class="flex items-center gap-3">
                    <picture>
                        <source media="(prefers-color-scheme: dark)" srcset="{{ asset('vendor/artisan-runner/logo-icon-dark.svg') }}">
                        <img src="{{ asset('vendor/artisan-runner/logo-icon-light.svg') }}" alt="Artisan Runner" class="h-9 w-9">
                    </picture>
                    <div>
                        <h1 class="text-lg font-bold tracking-tight text-slate-900 dark:text-white">Artisan Runner</h1>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Execute commands safely from the browser</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700 ring-1 ring-emerald-600/10 dark:bg-emerald-500/10 dark:text-emerald-400 dark:ring-emerald-500/20">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                        Connected
                    </span>
                </div>
            </div>
        </header>

        {{-- Content --}}
        <main class="mx-auto max-w-5xl px-6 py-8">
            @yield('content')
        </main>
    </div>
    @livewireScripts
</body>
</html>
