<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengajuan Lamaran - {{ $job->title }} - bisaKerja</title>
    <!-- Tailwind CSS CDN -->
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

    <!-- Sidebar Left (Konsisten dengan Desain Mockup) -->
    <aside class="w-64 bg-white border-r min-h-screen p-6 hidden md:block">
        <div class="flex items-center gap-2 mb-8">
            <span class="text-2xl font-bold text-emerald-800">bisaKerja</span>
        </div>
        <nav class="space-y-2">
            <a href="{{ route('jobs.index') }}" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-100 rounded-lg">
                <span>Beranda</span>
            </a>
            <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-100 rounded-lg">
                <span>Lamaran Saya</span>
            </a>
            <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-100 rounded-lg">
                <span>Chat</span>
            </a>
            <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-100 rounded-lg">
                <span>Profil</span>
            </a>
        </nav>
    </aside>

    <!-- Main Content Area -->
    <main class="flex-1">
        <!-- Header Nav -->
        <header class="bg-white border-b px-8 py-4 flex justify-between items-center">
            <h1 class="text-xl font-bold text-gray-800 md:hidden">bisaKerja</h1>
            <span class="text-xs font-semibold bg-emerald-100 text-emerald-800 px-3 py-1 rounded-full ml-auto">Pencari Kerja</span>
        </header>

        <div class="p-6 md:p-8 max-w-4xl mx-auto space-y-6">

            <!-- Tombol Kembali -->
            <div>
                <a href="{{ route('jobs.show', $job->id) }}" class="inline-flex items-center gap-1 text-emerald-700 hover:text-emerald-800 text-sm font-semibold transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Kembali ke Detail Lowongan
                </a>
            </div>

            <!-- Judul Halaman -->
            <div>
                <h1 class="text-3xl font-bold text-emerald-800">Pengajuan Lamaran</h1>
                <p class="text-gray-600 text-sm mt-1">Lengkapi berkas CV untuk melamar lowongan pekerjaan yang dipilih</p>
            </div>

            <!-- Notifikasi Pesan Sukses -->
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-xl flex items-start gap-3">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <p class="font-semibold text-sm">{{ session('success') }}</p>
                        <p class="text-xs text-emerald-700 mt-0.5">Lamaran Anda telah tercatat di sistem dan tersimpan dengan aman.</p>
                    </div>
                </div>
            @endif

            <!-- Notifikasi Pesan Error -->
            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-800 p-4 rounded-xl flex items-start gap-3">
                    <svg class="w-5 h-5 text-red-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <p class="font-semibold text-sm">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            <!-- Notifikasi Jika Sudah Pernah Melamar -->
            @if($existingApplication)
                <div class="bg-amber-50 border border-amber-200 text-amber-900 p-5 rounded-2xl flex items-start gap-4">
                    <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center shrink-0 text-amber-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-sm text-amber-900">Anda Sudah Mengajukan Lamaran</h3>
                        <p class="text-xs text-amber-800 mt-1">
                            Anda telah mengajukan lamaran untuk posisi ini pada 
                            <strong>{{ $existingApplication->created_at->format('d M Y, H:i') }}</strong>. 
                            Status saat ini: <span class="inline-block px-2 py-0.5 bg-amber-200 text-amber-900 rounded font-semibold uppercase text-[11px]">{{ $existingApplication->status }}</span>.
                        </p>
                    </div>
                </div>
            @endif

            <!-- Card 1: Informasi Lowongan Pekerjaan (AC-01 & AC-02) -->
            <div class="bg-white border rounded-2xl p-6 shadow-sm">
                <h2 class="text-lg font-bold text-emerald-800 mb-3">Informasi Lowongan yang Dilamar</h2>
                <div class="space-y-2">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-2 border-b pb-4">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">{{ $job->title }}</h3>
                            <p class="text-gray-600 text-sm mt-1">
                                📍 {{ $job->location }} &bull; <span class="font-medium text-gray-700">{{ $job->company }}</span>
                            </p>
                        </div>
                        <span class="inline-block self-start md:self-auto bg-emerald-50 text-emerald-700 text-xs font-semibold px-3 py-1 rounded-full border border-emerald-200">
                            {{ $job->type }}
                        </span>
                    </div>
                    <div class="pt-2 flex items-center justify-between">
                        <span class="text-sm text-gray-500">Estimasi Gaji:</span>
                        <span class="text-emerald-700 font-bold text-base">
                            Rp {{ number_format($job->salary_min) }} - Rp {{ number_format($job->salary_max) }}
                        </span>
                    </div>
                </div>
            </div>

            @if(!$existingApplication)
            <!-- Form Pengajuan Lamaran -->
            <form action="{{ route('jobs.apply.store', $job->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- Card 2: Informasi Pelamar (Data Akun Login) -->
                <div class="bg-white border rounded-2xl p-6 shadow-sm space-y-4">
                    <h2 class="text-lg font-bold text-emerald-800">Informasi Pribadi & Kontak</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">
                                Nama Pelamar <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                value="{{ auth()->user()->name ?? 'Pencari Kerja' }}" 
                                readonly 
                                class="w-full px-4 py-2.5 rounded-lg border border-gray-200 bg-gray-100 text-gray-700 text-sm cursor-not-allowed"
                            >
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">
                                Email Pelamar <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="email" 
                                value="{{ auth()->user()->email ?? '-' }}" 
                                readonly 
                                class="w-full px-4 py-2.5 rounded-lg border border-gray-200 bg-gray-100 text-gray-700 text-sm cursor-not-allowed"
                            >
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 italic">
                        * Data akun diambil otomatis dari akun yang sedang login untuk memastikan keaslian lamaran.
                    </p>
                </div>

                <!-- Card 3: Dokumen Unggah CV (Sesuai Screenshot Mockup) -->
                <div class="bg-white border rounded-2xl p-6 shadow-sm space-y-4">
                    <h2 class="text-lg font-bold text-emerald-800">Dokumen Persyaratan Lamaran</h2>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-800 mb-2">
                            Unggah CV / Resume <span class="text-red-500">*</span>
                        </label>

                        <!-- Dropzone Container (Meniru persis komponen upload di screenshot) -->
                        <div 
                            id="dropzone"
                            class="border-2 border-dashed border-emerald-300 bg-emerald-50/30 rounded-2xl p-8 text-center transition hover:bg-emerald-50/60 cursor-pointer"
                        >
                            <!-- Ikon Cloud Upload -->
                            <div class="mx-auto w-12 h-12 text-emerald-600 mb-3 flex items-center justify-center">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                            </div>

                            <p class="text-sm text-gray-700 font-medium">
                                Tarik berkas CV (PDF) ke sini atau 
                                <span class="text-emerald-700 font-semibold underline">Klik untuk memilih berkas</span>
                            </p>
                            <p class="text-xs text-gray-500 mt-1">Format PDF, DOC, atau DOCX. Ukuran maksimal 5MB.</p>

                            <!-- Hidden file input -->
                            <input 
                                type="file" 
                                name="cv" 
                                id="cv_input" 
                                accept=".pdf,.doc,.docx" 
                                class="hidden" 
                                required
                            >

                            <!-- Indikator Berkas Terpilih -->
                            <div id="file_preview" class="hidden mt-4 inline-flex items-center gap-2 bg-white px-4 py-2 rounded-lg border border-emerald-300 text-xs font-semibold text-emerald-800 shadow-sm">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span id="file_name">nama_berkas.pdf</span>
                                <span id="file_size" class="text-gray-400 font-normal"></span>
                                <button type="button" id="remove_file_btn" class="ml-2 text-red-500 hover:text-red-700 font-bold">&times;</button>
                            </div>
                        </div>

                        <!-- Pesan Error Validasi CV -->
                        @error('cv')
                            <p class="text-red-600 text-xs mt-2 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Bar Catatan (Sesuai Screenshot Mockup) -->
                <div class="bg-white border rounded-xl p-4 flex items-center gap-3 text-gray-600 text-xs shadow-sm">
                    <svg class="w-5 h-5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span><strong>Catatan:</strong> Dokumen CV wajib dilampirkan dan disimpan secara privat di server untuk keperluan seleksi kerja oleh perusahaan.</span>
                </div>

                <!-- Tombol Aksi (Sesuai Screenshot Mockup) -->
                <div class="flex items-center gap-3 pt-2">
                    <button 
                        type="submit" 
                        class="bg-emerald-800 hover:bg-emerald-900 text-white font-semibold px-6 py-2.5 rounded-lg text-sm transition shadow-sm"
                    >
                        Kirim Lamaran
                    </button>
                    <a 
                        href="{{ route('jobs.show', $job->id) }}" 
                        class="bg-white border border-gray-300 hover:bg-gray-100 text-gray-700 font-semibold px-6 py-2.5 rounded-lg text-sm transition"
                    >
                        Batal
                    </a>
                </div>
            </form>
            @endif

        </div>
    </main>

    <!-- JavaScript Interaktif untuk Dropzone Upload Berkas -->
    <script>
        const dropzone = document.getElementById('dropzone');
        const cvInput = document.getElementById('cv_input');
        const filePreview = document.getElementById('file_preview');
        const fileName = document.getElementById('file_name');
        const fileSize = document.getElementById('file_size');
        const removeFileBtn = document.getElementById('remove_file_btn');

        if (dropzone && cvInput) {
            dropzone.addEventListener('click', (e) => {
                if (e.target !== removeFileBtn) {
                    cvInput.click();
                }
            });

            dropzone.addEventListener('dragover', (e) => {
                e.preventDefault();
                dropzone.classList.add('border-emerald-600', 'bg-emerald-100/50');
            });

            dropzone.addEventListener('dragleave', () => {
                dropzone.classList.remove('border-emerald-600', 'bg-emerald-100/50');
            });

            dropzone.addEventListener('drop', (e) => {
                e.preventDefault();
                dropzone.classList.remove('border-emerald-600', 'bg-emerald-100/50');
                if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
                    cvInput.files = e.dataTransfer.files;
                    handleFileSelection();
                }
            });

            cvInput.addEventListener('change', handleFileSelection);

            function handleFileSelection() {
                if (cvInput.files && cvInput.files[0]) {
                    const file = cvInput.files[0];
                    fileName.textContent = file.name;
                    fileSize.textContent = `(${ (file.size / 1024 / 1024).toFixed(2) } MB)`;
                    filePreview.classList.remove('hidden');
                } else {
                    filePreview.classList.add('hidden');
                }
            }

            if (removeFileBtn) {
                removeFileBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    cvInput.value = '';
                    filePreview.classList.add('hidden');
                });
            }
        }
    </script>

</body>
</html>

