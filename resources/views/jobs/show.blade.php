
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

        <a href="{{ route('jobs.index') }}"
           class="text-emerald-700 text-sm font-semibold hover:underline">
            ← Kembali ke Pencarian
        </a>

        <!-- Informasi Lowongan -->
        <div class="border-b pb-4">

            <h1 class="text-2xl font-bold text-gray-800">
                {{ $job->judul }}
            </h1>

            <p class="text-gray-600 font-medium mt-2">
                📍 {{ $job->lokasi }}
                @if($job->employer)
                    | {{ $job->employer->nama }}
                @endif
            </p>

            @if($job->kategori)
                <p class="text-sm text-emerald-700 font-semibold mt-2">
                    {{ $job->kategori->nama_kategori }}
                </p>
            @endif

            <div class="mt-4 text-emerald-700 font-bold text-xl">
                Rp {{ number_format((float) $job->upah, 0, ',', '.') }}
            </div>

            @if($job->waktu_pekerjaan)
                <p class="text-sm text-gray-500 mt-2">
                    Tanggal Pelaksanaan:
                    {{ $job->waktu_pekerjaan->format('d M Y') }}
                </p>
            @endif

        </div>

        <!-- Deskripsi -->
        <div>
            <h3 class="font-bold text-gray-800 mb-2">
                Deskripsi Pekerjaan
            </h3>

            <p class="text-gray-600 leading-relaxed whitespace-pre-line">
                {{ $job->deskripsi }}
            </p>
        </div>

        <!-- Notifikasi -->
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

        <!-- Tombol Lamaran -->
        <div class="pt-4 border-t flex justify-end">

            <a href="{{ route('jobs.apply', $job->id_lowongan) }}"
               class="inline-block bg-emerald-700 hover:bg-emerald-800 text-white font-semibold px-6 py-2.5 rounded-lg transition">
                Lamar Sekarang
            </a>

        </div>

    </div>

</body>
</html>
