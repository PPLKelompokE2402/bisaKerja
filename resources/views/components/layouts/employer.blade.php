@props(['title' => 'bisaKerja'])

<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title }} - bisaKerja</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-white font-sans text-gray-900 antialiased">
        <header class="flex h-16 items-center justify-between border-b border-gray-200 bg-white px-6 lg:px-12">
            <a href="{{ route('employer.vacancies.index') }}" class="text-xl font-semibold text-brand-700">bisaKerja</a>
            <span class="text-sm text-gray-600">Pemberi Kerja</span>
        </header>

        <div class="flex min-h-[calc(100vh-4rem)]">
            <aside class="hidden w-36 shrink-0 border-r border-gray-200 bg-white px-3 py-6 md:block lg:w-44">
                <nav class="flex flex-col gap-1 text-sm">
                    <a href="{{ route('employer.vacancies.index') }}"
                        @class([
                            'rounded-lg px-3 py-2 transition',
                            'bg-brand-100 font-medium text-brand-800' => request()->routeIs('employer.vacancies.*'),
                            'text-gray-700 hover:bg-gray-100' => ! request()->routeIs('employer.vacancies.*'),
                        ])>
                        Lowongan saya
                    </a>
                    <span class="cursor-not-allowed rounded-lg px-3 py-2 text-gray-700" title="Segera hadir">Pelamar</span>
                    <span class="cursor-not-allowed rounded-lg px-3 py-2 text-gray-700" title="Segera hadir">Chat</span>
                    <span class="cursor-not-allowed rounded-lg px-3 py-2 text-gray-700" title="Segera hadir">Profil</span>
                </nav>
            </aside>

            <main class="flex-1 bg-brand-50 px-6 py-8 lg:px-8">
                @if (session('status'))
                    <div class="mb-6 rounded-lg border border-brand-600/30 bg-white px-4 py-3 text-sm text-brand-800" role="status">
                        {{ session('status') }}
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </body>
</html>

