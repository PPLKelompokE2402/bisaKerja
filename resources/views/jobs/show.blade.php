<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Lowongan - bisaKerja</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 p-8">
    <div class="max-w-3xl mx-auto bg-white p-8 rounded-xl border shadow-sm space-y-6">
        <a href="{{ route('jobs.index') }}" class="text-emerald-700 text-sm font-semibold hover:underline">← Kembali ke Pencarian</a>
        
        <div class="border-b pb-4">
            <h1 class="text-2xl font-bold text-gray-800">{{ $job->title }}</h1>
            <p class="text-gray-600 font-medium mt-1">📍 {{ $job->location }} | {{ $job->company }}</p>
            <div class="mt-3 text-emerald-700 font-bold text-lg">
                Rp {{ number_format($job->salary_min) }} - Rp {{ number_format($job->salary_max) }}
            </div>
        </div>

        <div>
            <h3 class="font-bold text-gray-800 mb-2">Deskripsi Pekerjaan:</h3>
            <p class="text-gray-600 leading-relaxed">{{ $job->description }}</p>
        </div>

        <div>
            <h3 class="font-bold text-gray-800 mb-2">Persyaratan:</h3>
            <p class="text-gray-600 whitespace-pre-line">{{ $job->requirements }}</p>
        </div>

        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-lg text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm">
                {{ session('error') }}
            </div>
        @endif

        <!-- Tombol Lamar Sekarang (PBI-06 / PBI-53) -->
        <div class="pt-4 border-t flex justify-between items-center">
            @php
                $alreadyApplied = false;
                if (auth()->check()) {
                    $alreadyApplied = \App\Models\JobApplication::where('user_id', auth()->id())
                        ->where('job_id', $job->id)
                        ->exists();
                }
            @endphp

            @if($alreadyApplied)
                <div class="inline-flex items-center gap-2 text-amber-800 bg-amber-50 px-3.5 py-1.5 rounded-lg text-xs font-semibold border border-amber-200">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Sudah Dilamar (Menunggu Peninjauan)
                </div>
                <a href="{{ route('jobs.apply', $job->id) }}" class="inline-block bg-amber-700 hover:bg-amber-800 text-white font-semibold px-6 py-2.5 rounded-lg transition text-sm">
                    Lihat Status Lamaran
                </a>
            @else
                <a href="{{ route('jobs.apply', $job->id) }}" class="inline-block bg-emerald-700 hover:bg-emerald-800 text-white font-semibold px-6 py-2.5 rounded-lg transition ml-auto">
                    Lamar Sekarang
                </a>
            @endif
        </div>
    </div>
</body>
</html>
