@props(['title' => 'bisaKerja'])

<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title }} - bisaKerja</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('styles')
        @stack('scripts')
    </head>
    <body class="min-h-screen bg-white font-sans text-gray-900 antialiased">
        <header class="flex h-16 items-center justify-between border-b border-gray-200 bg-white px-6 lg:px-12">
            <a href="{{ route('employer.vacancies.index') }}" class="text-xl font-semibold text-brand-700">bisaKerja</a>
            <span class="text-sm text-gray-600">{{ auth()->user()?->nama ?? 'Pemberi Kerja' }}</span>
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
                    @if (auth()->user()?->isEmployer())
                        <a href="{{ route('home', ['view' => 'pelamar-saya']) }}"
                            @class([
                                'flex items-center gap-2 rounded-lg px-3 py-2 transition',
                                'bg-brand-100 font-medium text-brand-800' => request()->routeIs('provider.applicants.*'),
                                'text-gray-700 hover:bg-gray-100' => ! request()->routeIs('provider.applicants.*'),
                            ]) @if (request()->routeIs('provider.applicants.*')) aria-current="page" @endif>
                            <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><circle cx="9" cy="8" r="3"/><path d="M3 21v-3a6 6 0 0 1 12 0v3M16 5a3 3 0 0 1 0 6m2 4a5 5 0 0 1 3 5"/></svg>
                            Pelamar Saya
                        </a>
                    @endif
                    <span class="cursor-not-allowed rounded-lg px-3 py-2 text-gray-700" title="Segera hadir">Chat</span>
                    <a href="{{ route('provider.biodata') }}" class="rounded-lg px-3 py-2 text-gray-700 hover:bg-gray-100">Profil</a>
                </nav>
            </aside>

            <main class="min-w-0 flex-1 bg-brand-50 px-6 py-8 lg:px-8">
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

