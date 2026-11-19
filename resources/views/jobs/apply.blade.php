
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Pengajuan Lamaran - {{ $job->judul }} - bisaKerja</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            500: '#16a34a',
                            600: '#15803d',
                            700: '#166534',
                            800: '#14532d',
                        }
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-gray-50 min-h-screen flex">

    <!-- Sidebar -->
    <aside class="w-64 bg-white border-r min-h-screen p-6 hidden md:block">
        <div class="flex items-center gap-2 mb-8">
            <span class="text-2xl font-bold text-emerald-800">
                bisaKerja
            </span>
        </div>

        <nav class="space-y-2">
            <a href="{{ route('jobs.index') }}"
               class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-100 rounded-lg">
                Beranda
            </a>

            <a href="#"
               class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-100 rounded-lg">
                Lamaran Saya
            </a>

            <a href="#"
               class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-100 rounded-lg">
                Chat
            </a>

            <a href="#"
               class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-100 rounded-lg">
                Profil
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-1">

        <!-- Header -->
        <header class="bg-white border-b px-8 py-4 flex justify-between items-center">
            <h1 class="text-xl font-bold text-gray-800 md:hidden">
                bisaKerja
            </h1>

            <span class="text-xs font-semibold bg-emerald-100 text-emerald-800 px-3 py-1 rounded-full ml-auto">
                Pencari Kerja
            </span>
        </header>

        <div class="p-6 md:p-8 max-w-4xl mx-auto space-y-6">

            <!-- Tombol Kembali -->
            <div>
                <a href="{{ route('jobs.show', $job->id_lowongan) }}"
                   class="inline-flex items-center gap-1.5 text-emerald-700 hover:text-emerald-800 text-sm font-semibold transition">

                    <svg class="w-4 h-4"
                         fill="none"
                         stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M15 19l-7-7 7-7">
                        </path>
                    </svg>

                    Kembali ke Detail Lowongan
                </a>
            </div>

            <!-- Judul Halaman -->
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-emerald-800">
                    Form Pengajuan Lamaran Pekerjaan
                </h1>

                <p class="text-gray-600 text-sm mt-1">
                    Sampaikan ketertarikan dan alasan Anda melamar pekerjaan ini.
                </p>
            </div>

            <!-- Notifikasi Sukses -->
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-xl shadow-sm">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5"
                             fill="none"
                             stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>

                        <div>
                            <p class="font-semibold text-sm">
                                {{ session('success') }}
                            </p>

                            <p class="text-xs text-emerald-700 mt-1">
                                Lamaran Anda berhasil dikirim dan menunggu respons penyedia pekerjaan.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Notifikasi Error -->
            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-800 p-4 rounded-xl shadow-sm">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-red-600 shrink-0 mt-0.5"
                             fill="none"
                             stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>

                        <p class="font-semibold text-sm">
                            {{ session('error') }}
                        </p>
                    </div>
                </div>
            @endif

            <!-- Ringkasan Error Validasi -->
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-800 p-4 rounded-xl shadow-sm">
                    <p class="font-bold text-sm mb-2">
                        Mohon periksa kembali isian formulir:
                    </p>

                    <ul class="list-disc list-inside space-y-1 text-xs text-red-700">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Informasi Lowongan -->
            <div class="bg-white border rounded-2xl p-6 shadow-sm">
                <h2 class="text-lg font-bold text-emerald-800 mb-4">
                    Informasi Lowongan yang Dipilih
                </h2>

                <div class="space-y-4">

                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 border-b pb-4">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">
                                {{ $job->judul }}
                            </h3>

                            <p class="text-gray-600 text-sm mt-1">
                                📍 {{ $job->lokasi }}
                                &bull;
                                <span class="font-medium text-gray-700">
                                    {{ $job->employer->nama ?? 'Penyedia Pekerjaan' }}
                                </span>
                            </p>
                        </div>

                        <span class="inline-block self-start bg-emerald-50 text-emerald-700 text-xs font-semibold px-3 py-1 rounded-full border border-emerald-200">
                            {{ $job->kategori->nama_kategori ?? 'Umum' }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500 font-medium">
                            Upah yang Ditawarkan:
                        </span>

                        <span class="text-emerald-700 font-bold text-base">
                            Rp {{ number_format($job->upah, 0, ',', '.') }}
                        </span>
                    </div>

                    @if($job->deskripsi)
                        <div class="pt-2">
                            <p class="text-sm font-semibold text-gray-700 mb-1">
                                Deskripsi Pekerjaan
                            </p>

                            <p class="text-sm text-gray-600 whitespace-pre-line">
                                {{ $job->deskripsi }}
                            </p>
                        </div>
                    @endif

                </div>
            </div>

            <!-- Jika Sudah Pernah Melamar -->
            @if($existingApplication)

                <div class="bg-amber-50 border border-amber-200 text-amber-900 p-6 rounded-2xl shadow-sm">

                    <div class="flex items-start gap-4">

                        <div class="w-12 h-12 rounded-full bg-amber-100 flex items-center justify-center shrink-0 text-amber-600">
                            <svg class="w-6 h-6"
                                 fill="none"
                                 stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                        </div>

                        <div class="space-y-3 flex-1">

                            <h3 class="font-bold text-base text-amber-900">
                                Anda Sudah Mengajukan Lamaran
                            </h3>

                            <p class="text-sm text-amber-800">
                                Lamaran Anda untuk posisi
                                <strong>{{ $job->judul }}</strong>
                                telah tercatat pada
                                <strong>
                                    {{ \Carbon\Carbon::parse($existingApplication->tanggal_lamaran)->format('d M Y, H:i') }}
                                </strong>.
                            </p>

                            <div>
                                <span class="text-xs text-amber-800 font-medium">
                                    Status Pengajuan:
                                </span>

                                <span class="inline-block ml-2 px-2.5 py-1 bg-amber-200 text-amber-900 rounded-md font-bold uppercase text-xs tracking-wider">
                                    {{ $existingApplication->status_lamaran }}
                                </span>
                            </div>

                            @if($existingApplication->pesan_lamaran)
                                <div class="bg-white/70 border border-amber-200 rounded-lg p-3">
                                    <p class="text-xs font-semibold text-amber-900 mb-1">
                                        Pesan Lamaran Anda:
                                    </p>

                                    <p class="text-sm text-amber-800 whitespace-pre-line">
                                        {{ $existingApplication->pesan_lamaran }}
                                    </p>
                                </div>
                            @endif

                            <div class="pt-2">
                                <a href="{{ route('jobs.show', $job->id_lowongan) }}"
                                   class="inline-flex items-center gap-1.5 bg-amber-800 hover:bg-amber-900 text-white font-semibold px-4 py-2 rounded-lg text-xs transition">
                                    &larr; Kembali ke Detail Lowongan
                                </a>
                            </div>

                        </div>
                    </div>
                </div>

            @else

                <!-- Form Pengajuan Lamaran -->
                <form action="{{ route('jobs.apply.store', $job->id_lowongan) }}"
                      method="POST"
                      class="space-y-6">

                    @csrf

                    <div class="bg-white border rounded-2xl p-6 shadow-sm space-y-5">

                        <div>
                            <h2 class="text-lg font-bold text-emerald-800">
                                Pesan Lamaran
                            </h2>

                            <p class="text-gray-500 text-xs mt-1">
                                Sampaikan alasan dan ketertarikan Anda terhadap pekerjaan ini kepada penyedia pekerjaan.
                            </p>
                        </div>

                        <div>
                            <label for="pesan_lamaran"
                                   class="block text-sm font-semibold text-gray-700 mb-2">
                                Pesan untuk Penyedia Pekerjaan
                            </label>

                            <textarea
                                name="pesan_lamaran"
                                id="pesan_lamaran"
                                rows="6"
                                placeholder="Halo, saya tertarik untuk melamar pekerjaan ini. Saya memiliki kemampuan dan pengalaman yang sesuai dengan kebutuhan pekerjaan..."
                                class="w-full px-4 py-3 rounded-lg border @error('pesan_lamaran') border-red-500 ring-1 ring-red-500 @else border-gray-300 @enderror bg-white text-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 transition"
                            >{{ old('pesan_lamaran') }}</textarea>

                            @error('pesan_lamaran')
                                <p class="text-red-600 text-xs mt-1 font-medium">
                                    {{ $message }}
                                </p>
                            @enderror

                            <p class="text-xs text-gray-400 mt-2">
                                Pesan ini akan diterima oleh penyedia pekerjaan bersama pengajuan lamaran Anda.
                            </p>
                        </div>

                    </div>

                    <!-- Informasi Pengajuan -->
                    <div class="bg-blue-50 border border-blue-200 text-blue-800 p-4 rounded-xl text-xs flex items-start gap-2.5">
                        <svg class="w-4 h-4 text-blue-600 shrink-0 mt-0.5"
                             fill="none"
                             stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>

                        <span>
                            Pastikan lowongan yang dipilih sudah sesuai. Anda hanya dapat mengirim satu lamaran untuk setiap lowongan pekerjaan.
                        </span>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="flex flex-wrap items-center gap-3 pt-2">

                        <button
                            type="submit"
                            class="bg-emerald-800 hover:bg-emerald-900 text-white font-semibold px-6 py-2.5 rounded-lg text-sm transition shadow-sm">
                            Kirim Lamaran
                        </button>

                        <a href="{{ route('jobs.show', $job->id_lowongan) }}"
                           class="bg-white border border-gray-300 hover:bg-gray-100 text-gray-700 font-semibold px-6 py-2.5 rounded-lg text-sm transition">
                            Kembali
                        </a>

                    </div>

                </form>

            @endif

        </div>
    </main>

</body>
</html>
