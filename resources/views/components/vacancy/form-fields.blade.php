@props(['vacancy' => null, 'categories'])

@php
    $inputClasses = 'w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm placeholder:text-gray-400 focus:border-brand-600 focus:ring-2 focus:ring-brand-600/20 focus:outline-none';
    $labelClasses = 'mb-1.5 block text-sm font-semibold text-brand-700';
@endphp

<div class="flex flex-col gap-4">
    <div>
        <label for="judul" class="{{ $labelClasses }}">Judul Pekerjaan <span>*</span></label>
        <input type="text" id="judul" name="judul" value="{{ old('judul', $vacancy?->judul) }}"
            placeholder="Contoh: Bersihkan Halaman Rumah" class="{{ $inputClasses }}" required maxlength="150">
        @error('judul')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="id_kategori" class="{{ $labelClasses }}">Kategori <span>*</span></label>
        <select id="id_kategori" name="id_kategori" class="{{ $inputClasses }}" required>
            <option value="" disabled @selected(old('id_kategori', $vacancy?->id_kategori) === null)>Pilih kategori tugas...</option>
            @foreach ($categories as $cat)
                <option value="{{ $cat->id_kategori }}" @selected(old('id_kategori', $vacancy?->id_kategori) == $cat->id_kategori)>
                    {{ $cat->nama_kategori }}
                </option>
            @endforeach
        </select>
        @error('id_kategori')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="lokasi" class="{{ $labelClasses }}">Lokasi &amp; Patokan <span>*</span></label>
        <input type="text" id="lokasi" name="lokasi" value="{{ old('lokasi', $vacancy?->lokasi) }}"
            placeholder="Contoh: Kel. Sukajadi, Jl. Melati No. 12 (Patokan depan masjid)" class="{{ $inputClasses }}" required maxlength="255">
        @error('lokasi')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="waktu_pekerjaan" class="{{ $labelClasses }}">Tanggal Pelaksanaan</label>
        <input type="date" id="waktu_pekerjaan" name="waktu_pekerjaan"
            value="{{ old('waktu_pekerjaan', $vacancy?->waktu_pekerjaan?->format('Y-m-d')) }}"
            min="{{ now()->format('Y-m-d') }}" class="{{ $inputClasses }}">
        @error('waktu_pekerjaan')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <div class="mb-1.5 flex items-center justify-between">
            <label for="deskripsi" class="text-sm font-semibold text-brand-700">Catatan &amp; Instruksi Khusus <span>*</span></label>
        </div>
        <textarea id="deskripsi" name="deskripsi" rows="3" maxlength="1000" required
            placeholder="Jelaskan peralatan yang disediakan atau instruksi khusus..." class="{{ $inputClasses }}">{{ old('deskripsi', $vacancy?->deskripsi) }}</textarea>
        @error('deskripsi')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>
