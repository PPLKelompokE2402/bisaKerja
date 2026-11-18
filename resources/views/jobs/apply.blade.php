<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Pengajuan Lamaran - {{ $job->title }} - bisaKerja</title>
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
                <a href="{{ route('jobs.show', $job->id) }}" class="inline-flex items-center gap-1.5 text-emerald-700 hover:text-emerald-800 text-sm font-semibold transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Kembali ke Detail Lowongan
                </a>
            </div>

            <!-- Judul Halaman -->
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-emerald-800">Form Pengajuan Lamaran Pekerjaan</h1>
                <p class="text-gray-600 text-sm mt-1">Lengkapi data diri dan berkas lamaran untuk posisi yang Anda tuju</p>
            </div>

            <!-- Notifikasi Pesan Sukses -->
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-xl flex items-start gap-3 shadow-sm">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <p class="font-semibold text-sm">{{ session('success') }}</p>
                        <p class="text-xs text-emerald-700 mt-0.5">Lamaran Anda telah tercatat dengan status PENDING dan tersimpan secara aman di sistem.</p>
                    </div>
                </div>
            @endif

            <!-- Notifikasi Pesan Error -->
            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-800 p-4 rounded-xl flex items-start gap-3 shadow-sm">
                    <svg class="w-5 h-5 text-red-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <p class="font-semibold text-sm">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            <!-- Ringkasan Error Validasi -->
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-800 p-4 rounded-xl text-xs space-y-1 shadow-sm">
                    <p class="font-bold text-sm">Mohon periksa kembali isian formulir:</p>
                    <ul class="list-disc list-inside space-y-0.5 text-red-700">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Bagian A: Informasi Lowongan Pekerjaan (Hanya Tampil, Tidak Boleh Diedit) -->
            <div class="bg-white border rounded-2xl p-6 shadow-sm">
                <h2 class="text-lg font-bold text-emerald-800 mb-3">Informasi Lowongan yang Dipilih</h2>
                <div class="space-y-3">
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
                    <div class="flex items-center justify-between pt-1">
                        <span class="text-sm text-gray-500 font-medium">Estimasi Gaji:</span>
                        <span class="text-emerald-700 font-bold text-base">
                            Rp {{ number_format($job->salary_min) }} - Rp {{ number_format($job->salary_max) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Jika Pengguna Sudah Pernah Mengajukan Lamaran untuk Lowongan Ini -->
            @if($existingApplication)
                <div class="bg-amber-50 border border-amber-200 text-amber-900 p-6 rounded-2xl flex items-start gap-4 shadow-sm">
                    <div class="w-12 h-12 rounded-full bg-amber-100 flex items-center justify-center shrink-0 text-amber-600 mt-0.5">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="space-y-2 flex-1">
                        <h3 class="font-bold text-base text-amber-900">Anda Sudah Mengajukan Lamaran</h3>
                        <p class="text-sm text-amber-800">
                            Lamaran Anda untuk posisi <strong>{{ $job->title }}</strong> di <strong>{{ $job->company }}</strong> telah tercatat pada 
                            <strong>{{ $existingApplication->created_at->format('d M Y, H:i') }}</strong>.
                        </p>
                        <div class="pt-1 flex items-center gap-2">
                            <span class="text-xs text-amber-800 font-medium">Status Pengajuan:</span>
                            <span class="inline-block px-2.5 py-1 bg-amber-200 text-amber-900 rounded-md font-bold uppercase text-xs tracking-wider">
                                {{ $existingApplication->status }}
                            </span>
                        </div>
                        <div class="pt-3">
                            <a href="{{ route('jobs.show', $job->id) }}" class="inline-flex items-center gap-1.5 bg-amber-800 hover:bg-amber-900 text-white font-semibold px-4 py-2 rounded-lg text-xs transition">
                                &larr; Kembali ke Detail Lowongan
                            </a>
                        </div>
                    </div>
                </div>
            @else
                <!-- Form Pengajuan Lamaran Pekerjaan (Tampil Jika Belum Pernah Melamar) -->
                <form action="{{ route('jobs.apply.store', $job->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <!-- Bagian B: Data Diri Pelamar (Bisa Diedit Bebas oleh Pelamar) -->
                    <div class="bg-white border rounded-2xl p-6 shadow-sm space-y-5">
                        <div>
                            <h2 class="text-lg font-bold text-emerald-800">Data Diri Pelamar</h2>
                            <p class="text-gray-500 text-xs mt-0.5">Semua data diri di bawah ini dapat Anda ubah dan sesuaikan khusus untuk lamaran ini.</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <!-- 1. Nama Lengkap (Text, Wajib, Bisa Diedit) -->
                            <div class="md:col-span-2">
                                <label for="applicant_name" class="block text-sm font-semibold text-gray-700 mb-1">
                                    Nama Lengkap <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    name="applicant_name" 
                                    id="applicant_name"
                                    value="{{ old('applicant_name', auth()->user()->name ?? '') }}" 
                                    placeholder="Masukkan nama lengkap Anda"
                                    required
                                    class="w-full px-4 py-2.5 rounded-lg border @error('applicant_name') border-red-500 ring-1 ring-red-500 @else border-gray-300 @enderror bg-white text-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 transition"
                                >
                                @error('applicant_name')
                                    <p class="text-red-600 text-xs mt-1 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- 2. Tanggal Lahir (Date, Wajib, Bisa Diedit, Tidak Boleh Tanggal Masa Depan) -->
                            <div>
                                <label for="date_of_birth" class="block text-sm font-semibold text-gray-700 mb-1">
                                    Tanggal Lahir <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="date" 
                                    name="date_of_birth" 
                                    id="date_of_birth"
                                    value="{{ old('date_of_birth') }}" 
                                    max="{{ date('Y-m-d') }}"
                                    required
                                    class="w-full px-4 py-2.5 rounded-lg border @error('date_of_birth') border-red-500 ring-1 ring-red-500 @else border-gray-300 @enderror bg-white text-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 transition"
                                >
                                @error('date_of_birth')
                                    <p class="text-red-600 text-xs mt-1 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- 3. Umur (Number, Wajib, Bisa Diedit, Angka Masuk Akal) -->
                            <div>
                                <label for="age" class="block text-sm font-semibold text-gray-700 mb-1">
                                    Umur (Tahun) <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="number" 
                                    name="age" 
                                    id="age"
                                    value="{{ old('age') }}" 
                                    placeholder="Contoh: 23"
                                    min="15" 
                                    max="99" 
                                    required
                                    class="w-full px-4 py-2.5 rounded-lg border @error('age') border-red-500 ring-1 ring-red-500 @else border-gray-300 @enderror bg-white text-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 transition"
                                >
                                @error('age')
                                    <p class="text-red-600 text-xs mt-1 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- 4. Tempat Tinggal (Text, Wajib, Bebas Diedit) -->
                            <div class="md:col-span-2">
                                <label for="residence" class="block text-sm font-semibold text-gray-700 mb-1">
                                    Tempat Tinggal <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    name="residence" 
                                    id="residence"
                                    value="{{ old('residence') }}" 
                                    placeholder="Masukkan tempat tinggal kamu"
                                    required
                                    class="w-full px-4 py-2.5 rounded-lg border @error('residence') border-red-500 ring-1 ring-red-500 @else border-gray-300 @enderror bg-white text-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 transition"
                                >
                                @error('residence')
                                    <p class="text-red-600 text-xs mt-1 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- 5. Keahlian (Textarea, Wajib, Bebas Diketik Sendiri) -->
                            <div class="md:col-span-2">
                                <label for="skills" class="block text-sm font-semibold text-gray-700 mb-1">
                                    Keahlian yang Dimiliki <span class="text-red-500">*</span>
                                </label>
                                <textarea 
                                    name="skills" 
                                    id="skills"
                                    rows="4" 
                                    placeholder="Contoh: HTML, CSS, JavaScript, Laravel, komunikasi, teamwork, dan problem solving."
                                    required
                                    class="w-full px-4 py-2.5 rounded-lg border @error('skills') border-red-500 ring-1 ring-red-500 @else border-gray-300 @enderror bg-white text-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 transition"
                                >{{ old('skills') }}</textarea>
                                @error('skills')
                                    <p class="text-red-600 text-xs mt-1 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Bagian C: Upload CV (OPSIONAL — Pengguna Tetap Dapat Melamar Tanpa CV) -->
                    <div class="bg-white border rounded-2xl p-6 shadow-sm space-y-3">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-bold text-emerald-800">Dokumen CV / Resume</h2>
                            <span class="text-xs bg-gray-100 text-gray-600 font-semibold px-2.5 py-1 rounded-full">Opsional</span>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Unggah Dokumen CV <span class="text-gray-400 font-normal text-xs">(Opsional)</span>
                            </label>

                            <!-- Dropzone Container -->
                            <div 
                                id="dropzone"
                                class="border-2 border-dashed border-emerald-300 bg-emerald-50/30 rounded-2xl p-8 text-center transition hover:bg-emerald-50/60 cursor-pointer"
                            >
                                <div class="mx-auto w-12 h-12 text-emerald-600 mb-3 flex items-center justify-center">
                                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                    </svg>
                                </div>

                                <p class="text-sm text-gray-700 font-medium">
                                    Tarik berkas CV ke sini atau 
                                    <span class="text-emerald-700 font-semibold underline">Klik untuk memilih berkas</span>
                                </p>
                                <p class="text-xs text-gray-500 mt-1">Format PDF, DOC, atau DOCX. Ukuran maksimal 2MB.</p>

                                <!-- Hidden file input (TANPA atribut required karena opsional) -->
                                <input 
                                    type="file" 
                                    name="cv" 
                                    id="cv_input" 
                                    accept=".pdf,.doc,.docx" 
                                    class="hidden"
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

                            @error('cv')
                                <p class="text-red-600 text-xs mt-2 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Keterangan Wajib Sesuai Instruksi -->
                        <div class="bg-blue-50 border border-blue-200 text-blue-800 p-3.5 rounded-xl text-xs flex items-center gap-2.5">
                            <svg class="w-4 h-4 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Upload CV bersifat opsional. Kamu tetap dapat mengirim lamaran tanpa melampirkan CV.</span>
                        </div>
                    </div>

                    <!-- Tombol Aksi (Bagian D) -->
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
                            Kembali
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
