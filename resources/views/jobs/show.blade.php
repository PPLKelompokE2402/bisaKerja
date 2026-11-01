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

        <div class="pt-4 border-t flex justify-end">
            <button class="bg-emerald-700 hover:bg-emerald-800 text-white font-semibold px-6 py-2.5 rounded-lg transition">
                Lamar Sekarang
            </button>
        </div>
    </div>
</body>
</html>