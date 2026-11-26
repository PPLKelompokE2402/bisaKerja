<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - bisaKerja</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-[#f3faf5] font-sans text-gray-800 antialiased">

    <!-- Header -->
    <header class="sticky top-0 z-30 flex h-16 items-center justify-between border-b border-[#e5eee7] bg-white px-6 lg:px-10">
        <a href="{{ route('jobs.index') }}" class="flex items-center gap-2.5">
            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-700 text-lg font-bold text-white">b</span>
            <span class="text-xl font-bold text-brand-800">bisaKerja</span>
        </a>

        <div class="flex items-center gap-3">
            <span class="rounded-full bg-brand-100 px-3 py-1 text-xs font-semibold text-brand-800">
                Pencari Kerja
            </span>
            @include('components.logout-form', ['buttonClass' => 'rounded-lg border border-[#dce8df] bg-white px-3.5 py-2 text-xs font-semibold text-gray-700 transition hover:bg-brand-50 cursor-pointer'])
        </div>
    </header>

    <!-- Mobile Navigation -->
    <nav class="flex items-center justify-between gap-2 border-b border-[#e5eee7] bg-white px-4 py-2.5 text-sm md:hidden" aria-label="Navigasi pencari kerja">
        <a href="{{ route('jobs.index') }}" class="rounded-lg px-3 py-2 text-gray-600 transition hover:bg-brand-50 hover:text-brand-800">
            Beranda
        </a>
        <a href="{{ route('seeker.applications') }}" class="rounded-lg px-3 py-2 text-gray-600 transition hover:bg-brand-50 hover:text-brand-800">
            Lamaran Saya
        </a>
        <a href="{{ route('profile.edit') }}" aria-current="page" class="rounded-lg bg-brand-100 px-3 py-2 font-semibold text-brand-800">
            Profil
        </a>
    </nav>

    <div class="flex min-h-[calc(100vh-4rem)]">

        <!-- Sidebar -->
        <aside class="sticky top-16 hidden h-[calc(100vh-4rem)] w-64 shrink-0 border-r border-[#e5eee7] bg-white p-6 md:block">
            <nav class="space-y-2 text-sm font-medium text-gray-600" aria-label="Menu pencari kerja">
                <a href="{{ route('jobs.index') }}"
                   class="flex items-center rounded-xl px-4 py-3 transition hover:bg-brand-50 hover:text-brand-800">
                    Beranda
                </a>
                <a href="{{ route('seeker.applications') }}"
                   class="flex items-center rounded-xl px-4 py-3 transition hover:bg-brand-50 hover:text-brand-800">
                    Lamaran Saya
                </a>
                <span class="block cursor-not-allowed rounded-xl px-4 py-3 text-gray-400" aria-disabled="true">
                    Chat (Belum tersedia)
                </span>
                <a href="{{ route('profile.edit') }}"
                   aria-current="page"
                   class="flex items-center rounded-xl bg-brand-100 px-4 py-3 font-semibold text-brand-800">
                    Profil
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="min-w-0 flex-1 p-5 sm:p-8 lg:p-10">
            <div class="mx-auto max-w-4xl space-y-6">

                <div>
                    <h1 class="text-2xl font-bold text-brand-800 sm:text-3xl">Profil</h1>
                    <p class="mt-1.5 text-sm text-[#718477]">
                        Kelola informasi diri dan kelayakan untuk melamar pekerjaan.
                    </p>
                </div>

                @if (session('success'))
                    <div class="flex items-start gap-3 rounded-2xl border border-brand-600/30 bg-brand-100/70 p-4 text-brand-800 shadow-sm" role="status">
                        <svg class="mt-0.5 h-5 w-5 shrink-0 text-brand-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-sm font-semibold">{{ session('success') }}</p>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-red-800 shadow-sm" role="alert">
                        <p class="mb-2 text-sm font-bold">Mohon periksa kembali isian formulir:</p>
                        <ul class="list-inside list-disc space-y-1 text-xs text-red-700">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('profile.update') }}"
                      method="POST"
                      enctype="multipart/form-data"
                      class="space-y-6">

                    @csrf
                    @method('PUT')

                    <!-- Foto Profil -->
                    <section class="rounded-2xl border border-[#e0ebe2] bg-white p-6 shadow-sm">
                        <div class="flex flex-col gap-5 sm:flex-row sm:items-center">
                            <div class="flex h-20 w-20 shrink-0 items-center justify-center overflow-hidden rounded-full border-2 border-[#c8e6d2] bg-brand-50">
                                @if($user->foto_profil)
                                    <img id="photo-preview"
                                         src="{{ asset('storage/'.$user->foto_profil) }}"
                                         class="h-full w-full object-cover"
                                         alt="Foto Profil">
                                @else
                                    <img id="photo-preview"
                                         src=""
                                         class="hidden h-full w-full object-cover"
                                         alt="Pratinjau Foto Profil">
                                    <svg id="photo-placeholder" class="h-9 w-9 text-brand-700/60" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                @endif
                            </div>

                            <div class="flex-1">
                                <label for="foto_profil" class="block text-sm font-semibold text-brand-800">
                                    Foto Profil
                                    <span class="font-normal text-[#718477]">(Opsional)</span>
                                </label>

                                <p class="mt-1 text-xs text-[#718477]">
                                    Format JPG atau PNG. Maksimal 2MB.
                                </p>

                                <div class="mt-3 flex flex-wrap items-center gap-3">
                                    <label for="foto_profil"
                                           class="inline-flex cursor-pointer items-center gap-2 rounded-xl border border-brand-700 bg-white px-4 py-2 text-xs font-semibold text-brand-700 transition hover:bg-brand-50">
                                        Pilih Foto
                                    </label>
                                    <span id="photo-file-name" class="text-xs text-[#718477]">
                                        {{ $user->foto_profil ? 'Foto profil tersimpan' : 'Belum ada file dipilih' }}
                                    </span>
                                    <input type="file"
                                           name="foto_profil"
                                           id="foto_profil"
                                           accept=".jpg,.jpeg,.png"
                                           class="sr-only">
                                </div>

                                @error('foto_profil')
                                    <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </section>

                    <!-- Informasi Pribadi -->
                    <section class="rounded-2xl border border-[#e0ebe2] bg-white p-6 shadow-sm">
                        <h2 class="mb-5 border-b border-[#e8eee9] pb-4 text-lg font-bold text-brand-800">
                            Informasi Pribadi &amp; Kontak
                        </h2>

                        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                            <div>
                                <label for="nama" class="mb-2 block text-sm font-semibold text-brand-800">
                                    Nama Lengkap <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       name="nama"
                                       id="nama"
                                       value="{{ old('nama', $user->nama) }}"
                                       placeholder="Masukkan nama lengkap sesuai KTP"
                                       maxlength="100"
                                       required
                                       class="w-full rounded-xl border border-[#dce8df] bg-white px-4 py-3 text-sm text-gray-800 placeholder-gray-400 transition focus:border-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-600/20">
                                @error('nama')
                                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="nomor_telepon" class="mb-2 block text-sm font-semibold text-brand-800">
                                    No. Handphone / WhatsApp <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       name="nomor_telepon"
                                       id="nomor_telepon"
                                       value="{{ old('nomor_telepon', $user->nomor_telepon) }}"
                                       placeholder="Contoh: 081234567890"
                                       maxlength="20"
                                       required
                                       class="w-full rounded-xl border border-[#dce8df] bg-white px-4 py-3 text-sm text-gray-800 placeholder-gray-400 transition focus:border-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-600/20">
                                @error('nomor_telepon')
                                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="alamat" class="mb-2 block text-sm font-semibold text-brand-800">
                                    Lokasi <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       name="alamat"
                                       id="alamat"
                                       value="{{ old('alamat', $user->alamat) }}"
                                       placeholder="Contoh: Kel. Walang Baru, Kec. Koja"
                                       maxlength="1000"
                                       required
                                       class="w-full rounded-xl border border-[#dce8df] bg-white px-4 py-3 text-sm text-gray-800 placeholder-gray-400 transition focus:border-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-600/20">
                                @error('alamat')
                                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="jenis_kelamin" class="mb-2 block text-sm font-semibold text-brand-800">
                                    Jenis Kelamin
                                </label>
                                <select name="jenis_kelamin"
                                        id="jenis_kelamin"
                                        class="w-full rounded-xl border border-[#dce8df] bg-white px-4 py-3 text-sm text-gray-800 transition focus:border-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-600/20">
                                    <option value="">Pilih jenis kelamin</option>
                                    <option value="laki-laki" @selected(old('jenis_kelamin', $user->jenis_kelamin) == 'laki-laki')>
                                        Laki-laki
                                    </option>
                                    <option value="perempuan" @selected(old('jenis_kelamin', $user->jenis_kelamin) == 'perempuan')>
                                        Perempuan
                                    </option>
                                </select>
                                @error('jenis_kelamin')
                                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-5">
                            <label for="deskripsi_diri" class="mb-2 block text-sm font-semibold text-brand-800">
                                Deskripsi Diri
                            </label>
                            <textarea name="deskripsi_diri"
                                      id="deskripsi_diri"
                                      rows="4"
                                      maxlength="2000"
                                      placeholder="Tuliskan deskripsi singkat mengenai diri Anda..."
                                      class="w-full rounded-xl border border-[#dce8df] bg-white px-4 py-3 text-sm text-gray-800 placeholder-gray-400 transition focus:border-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-600/20">{{ old('deskripsi_diri', $user->deskripsi_diri) }}</textarea>
                            @error('deskripsi_diri')
                                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </section>

                    <!-- Keahlian dan Pengalaman -->
                    <section class="rounded-2xl border border-[#e0ebe2] bg-white p-6 shadow-sm">
                        <h2 class="mb-5 border-b border-[#e8eee9] pb-4 text-lg font-bold text-brand-800">
                            Keahlian &amp; Pengalaman
                        </h2>

                        <div class="space-y-5">
                            <div>
                                <label for="keahlian" class="mb-2 block text-sm font-semibold text-brand-800">
                                    Keahlian <span class="text-red-500">*</span>
                                </label>
                                <textarea name="keahlian"
                                          id="keahlian"
                                          rows="2"
                                          maxlength="2000"
                                          required
                                          placeholder="Contoh: Bersih-bersih, Berkebun, Pertukangan ringan"
                                          class="w-full rounded-xl border border-[#dce8df] bg-white px-4 py-3 text-sm text-gray-800 placeholder-gray-400 transition focus:border-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-600/20">{{ old('keahlian', $user->keahlian) }}</textarea>
                                @error('keahlian')
                                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="pengalaman_kerja" class="mb-2 block text-sm font-semibold text-brand-800">
                                    Pengalaman Kerja <span class="text-red-500">*</span>
                                </label>
                                <textarea name="pengalaman_kerja"
                                          id="pengalaman_kerja"
                                          rows="4"
                                          maxlength="2000"
                                          required
                                          placeholder="Jelaskan pengalaman kerja atau tugas yang pernah diselesaikan..."
                                          class="w-full rounded-xl border border-[#dce8df] bg-white px-4 py-3 text-sm text-gray-800 placeholder-gray-400 transition focus:border-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-600/20">{{ old('pengalaman_kerja', $user->pengalaman_kerja) }}</textarea>
                                @error('pengalaman_kerja')
                                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="cv" class="mb-2 block text-sm font-semibold text-brand-800">
                                    Unggah CV / Surat Keterangan
                                    <span class="font-normal text-[#718477]">(Opsional)</span>
                                </label>

                                <div class="rounded-2xl border-2 border-dashed border-[#c8e6d2] bg-brand-50/50 p-6 text-center transition hover:border-brand-600">
                                    <label for="cv"
                                           class="inline-flex cursor-pointer items-center gap-2 rounded-xl border border-brand-700 bg-white px-4 py-2.5 text-xs font-semibold text-brand-700 transition hover:bg-brand-50">
                                        Pilih File CV (PDF)
                                    </label>
                                    <input type="file"
                                           name="cv"
                                           id="cv"
                                           accept=".pdf"
                                           class="sr-only">

                                    <p id="cv-file-name" class="mt-3 text-xs font-medium text-gray-700">
                                        Belum ada file baru dipilih
                                    </p>
                                    <p class="mt-1 text-xs text-[#718477]">
                                        Format PDF, ukuran maksimal 5MB.
                                    </p>

                                    @if($user->cv)
                                        <p class="mt-3 inline-flex items-center gap-1.5 rounded-full bg-brand-100 px-3 py-1 text-xs font-semibold text-brand-800">
                                            CV sudah tersimpan.
                                        </p>
                                    @endif

                                    @error('cv')
                                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Catatan -->
                    <div class="rounded-2xl border border-[#e0ebe2] bg-white p-4 text-sm text-[#61796a] shadow-sm">
                        <strong class="text-brand-800">Catatan:</strong>
                        Bidang bertanda bintang (<span class="text-red-500">*</span>) wajib diisi agar profil dapat digunakan untuk melamar pekerjaan.
                    </div>

                    <!-- Tombol -->
                    <div class="flex flex-wrap items-center gap-3">
                        <button type="submit"
                                class="cursor-pointer rounded-xl bg-brand-700 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-800">
                            Simpan Perubahan
                        </button>

                        <a href="{{ route('profile.edit') }}"
                           class="rounded-xl border border-[#dce8df] bg-white px-6 py-3 text-sm font-medium text-gray-700 transition hover:bg-brand-50">
                            Batal
                        </a>
                    </div>

                </form>

            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const photoInput = document.getElementById('foto_profil');
            const photoPreview = document.getElementById('photo-preview');
            const photoPlaceholder = document.getElementById('photo-placeholder');
            const photoFileName = document.getElementById('photo-file-name');

            if (photoInput) {
                photoInput.addEventListener('change', () => {
                    const file = photoInput.files?.[0];
                    if (!file) {
                        return;
                    }
                    if (photoFileName) {
                        photoFileName.textContent = file.name;
                    }
                    if (photoPreview && file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = (event) => {
                            photoPreview.src = event.target?.result || '';
                            photoPreview.classList.remove('hidden');
                            photoPlaceholder?.classList.add('hidden');
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }

            const cvInput = document.getElementById('cv');
            const cvFileName = document.getElementById('cv-file-name');

            if (cvInput && cvFileName) {
                cvInput.addEventListener('change', () => {
                    const file = cvInput.files?.[0];
                    if (file) {
                        cvFileName.textContent = `File dipilih: ${file.name}`;
                    }
                });
            }
        });
    </script>

</body>
</html>
