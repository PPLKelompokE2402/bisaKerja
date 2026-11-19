
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - bisaKerja</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-[#f3faf6] text-gray-800">

    <!-- Header -->
    <header class="bg-white border-b px-8 py-5 flex justify-between items-center">
        <h1 class="text-2xl font-bold">bisaKerja</h1>
        <span class="text-sm text-gray-600">Pencari Kerja</span>
    </header>

    <div class="flex min-h-screen">

        <!-- Sidebar -->
        <aside class="w-56 bg-white border-r p-5 hidden md:block">
            <nav class="space-y-3">
                <a href="#" class="block p-3 rounded-lg hover:bg-green-50">Beranda</a>
                <a href="#" class="block p-3 rounded-lg hover:bg-green-50">Lamaran Saya</a>
                <a href="#" class="block p-3 rounded-lg hover:bg-green-50">Chat</a>
                <a href="{{ route('profile.edit') }}"
                   class="block p-3 rounded-lg bg-[#e8f4ee] text-green-800 font-semibold">
                    Profil
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-6 md:p-10">

            <div class="max-w-6xl mx-auto">

                <h2 class="text-3xl font-bold text-green-800">Profil</h2>
                <p class="text-gray-500 mt-2 mb-8">
                    Kelola informasi diri dan kelayakan untuk melamar pekerjaan
                </p>

                <form action="{{ route('profile.update') }}"
                      method="POST"
                      enctype="multipart/form-data">

                    @csrf
                    @if(session('success'))
    <div class="bg-green-100 text-green-700 p-4 rounded-lg mb-5">
        {{ session('success') }}
    </div>
@endif
                    @method('PUT')

                    <!-- Foto Profil -->
                    <section class="bg-white rounded-2xl border p-6 mb-6">
                        <div class="flex items-center gap-5">
                            <div class="w-20 h-20 rounded-full border-2 border-dashed flex items-center justify-center overflow-hidden bg-gray-50">
                                @if($user->foto_profil)
                                    <img src="{{ asset('storage/'.$user->foto_profil) }}"
                                         class="w-full h-full object-cover"
                                         alt="Foto Profil">
                                @else
                                    <span class="text-3xl text-gray-400">👤</span>
                                @endif
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-green-700">
                                    Foto Profil
                                    <span class="text-gray-400 font-normal">(Opsional)</span>
                                </label>

                                <p class="text-xs text-gray-400 mt-1">
                                    Format JPG atau PNG. Maksimal 2MB.
                                </p>

                                <input type="file"
                                       name="foto_profil"
                                       accept=".jpg,.jpeg,.png"
                                       class="mt-3 text-sm">

                                @error('foto_profil')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </section>

                    <!-- Informasi Pribadi -->
                    <section class="bg-white rounded-2xl border p-6 mb-6">

                        <h3 class="text-lg font-semibold text-green-800 border-b pb-4 mb-5">
                            Informasi Pribadi & Kontak
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                            <div>
                                <label class="block text-sm text-green-800 mb-2">
                                    Nama Lengkap *
                                </label>
                                <input type="text"
                                       name="nama"
                                       value="{{ old('nama', $user->nama) }}"
                                       placeholder="Masukkan nama lengkap sesuai KTP"
                                       required
                                       class="w-full border border-green-200 rounded-xl px-3 py-3 focus:outline-none focus:ring-2 focus:ring-green-500">
                                @error('nama')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm text-green-800 mb-2">
                                    No. Handphone / WhatsApp *
                                </label>
                                <input type="text"
                                       name="nomor_telepon"
                                       value="{{ old('nomor_telepon', $user->nomor_telepon) }}"
                                       placeholder="Contoh: 081234567890"
                                       required
                                       class="w-full border border-green-200 rounded-xl px-3 py-3 focus:outline-none focus:ring-2 focus:ring-green-500">
                                @error('nomor_telepon')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm text-green-800 mb-2">
                                    Lokasi *
                                </label>
                                <input type="text"
                                       name="alamat"
                                       value="{{ old('alamat', $user->alamat) }}"
                                       placeholder="Contoh: Kel. Walang Baru, Kec. Koja"
                                       required
                                       class="w-full border border-green-200 rounded-xl px-3 py-3 focus:outline-none focus:ring-2 focus:ring-green-500">
                                @error('alamat')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm text-green-800 mb-2">
                                    Jenis Kelamin
                                </label>
                                <select name="jenis_kelamin"
                                        class="w-full border border-green-200 rounded-xl px-3 py-3 bg-white">
                                    <option value="">Pilih jenis kelamin</option>
                                    <option value="laki-laki" @selected(old('jenis_kelamin', $user->jenis_kelamin) == 'laki-laki')>
                                        Laki-laki
                                    </option>
                                    <option value="perempuan" @selected(old('jenis_kelamin', $user->jenis_kelamin) == 'perempuan')>
                                        Perempuan
                                    </option>
                                </select>
                            </div>

                        </div>

                        <div class="mt-5">
                            <label class="block text-sm text-green-800 mb-2">
                                Deskripsi Diri
                            </label>
                            <textarea name="deskripsi_diri"
                                      rows="4"
                                      placeholder="Tuliskan deskripsi singkat mengenai diri Anda..."
                                      class="w-full border border-green-200 rounded-xl px-3 py-3 focus:outline-none focus:ring-2 focus:ring-green-500">{{ old('deskripsi_diri', $user->deskripsi_diri) }}</textarea>
                        </div>

                    </section>

                    <!-- Keahlian dan Pengalaman -->
                    <section class="bg-white rounded-2xl border p-6 mb-6">

                        <h3 class="text-lg font-semibold text-green-800 border-b pb-4 mb-5">
                            Keahlian & Pengalaman
                        </h3>

                        <div class="mb-5">
                            <label class="block text-sm text-green-800 mb-2">
                                Keahlian *
                            </label>
                            <textarea name="keahlian"
                                      rows="2"
                                      required
                                      placeholder="Contoh: Bersih-bersih, Berkebun, Pertukangan ringan"
                                      class="w-full border border-green-200 rounded-xl px-3 py-3 focus:outline-none focus:ring-2 focus:ring-green-500">{{ old('keahlian', $user->keahlian) }}</textarea>
                            @error('keahlian')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-5">
                            <label class="block text-sm text-green-800 mb-2">
                                Pengalaman Kerja *
                            </label>
                            <textarea name="pengalaman_kerja"
                                      rows="4"
                                      required
                                      placeholder="Jelaskan pengalaman kerja atau tugas yang pernah diselesaikan..."
                                      class="w-full border border-green-200 rounded-xl px-3 py-3 focus:outline-none focus:ring-2 focus:ring-green-500">{{ old('pengalaman_kerja', $user->pengalaman_kerja) }}</textarea>
                            @error('pengalaman_kerja')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm text-green-800 mb-2">
                                Unggah CV / Surat Keterangan (Opsional)
                            </label>

                            <div class="border-2 border-dashed border-green-200 rounded-xl p-8 text-center">
                                <input type="file"
                                       name="cv"
                                       accept=".pdf"
                                       class="w-full text-sm">

                                <p class="text-xs text-gray-400 mt-2">
                                    Format PDF, ukuran maksimal 5MB.
                                </p>

                                @if($user->cv)
                                    <p class="text-sm text-green-700 mt-2">
                                        CV sudah tersimpan.
                                    </p>
                                @endif

                                @error('cv')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                    </section>

                    <!-- Catatan -->
                    <div class="bg-white border rounded-xl p-4 text-sm text-gray-600 mb-6">
                        <strong>Catatan:</strong>
                        Bidang bertanda bintang (*) wajib diisi agar profil dapat digunakan untuk melamar pekerjaan.
                    </div>

                    <!-- Tombol -->
                    <div class="flex gap-3">
                        <button type="submit"
                                class="bg-green-700 hover:bg-green-800 text-white px-6 py-3 rounded-lg font-medium">
                            Simpan Perubahan
                        </button>

                        <a href="{{ route('profile.edit') }}"
                           class="bg-white border px-6 py-3 rounded-lg hover:bg-gray-50">
                            Batal
                        </a>
                    </div>

                </form>

            </div>
        </main>
    </div>

</body>
</html>
