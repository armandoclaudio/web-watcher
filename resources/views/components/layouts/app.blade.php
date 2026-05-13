<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900 antialiased">
    <div class="min-h-screen">
        <header class="bg-white border-b border-gray-200">
            <div class="max-w-4xl mx-auto px-4 py-4 flex items-center justify-between">
                <a href="{{ route('alerts.index') }}" class="font-semibold text-gray-900">Web Watcher</a>
                <a href="{{ route('alerts.create') }}" class="text-sm bg-gray-900 text-white px-3 py-1.5 rounded-md hover:bg-gray-700 transition-colors">
                    New Alert
                </a>
            </div>
        </header>

        <main class="max-w-4xl mx-auto px-4 py-8">
            @if (session('success'))
                <div class="mb-6 text-sm text-green-700 bg-green-50 border border-green-200 rounded-md px-4 py-3">
                    {{ session('success') }}
                </div>
            @endif

            {{ $slot }}
        </main>
    </div>
</body>
</html>
