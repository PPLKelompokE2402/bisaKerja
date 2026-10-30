@props(['vacancy' => null, 'categories', 'durations'])

@php
    $inputClasses = 'w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm placeholder:text-gray-400 focus:border-brand-600 focus:ring-2 focus:ring-brand-600/20 focus:outline-none';
    $labelClasses = 'mb-1.5 block text-sm font-semibold text-brand-700';
@endphp

<div class="flex flex-col gap-4">
    <div>
        <label for="title" class="{{ $labelClasses }}">Judul Pekerjaan <span>*</span></label>
        <input type="text" id="title" name="title" value="{{ old('title', $vacancy?->title) }}"
            placeholder="Contoh: Bersihkan Halaman Rumah" class="{{ $inputClasses }}" required maxlength="255">
        @error('title')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="category" class="{{ $labelClasses }}">Kategori <span>*</span></label>
        <select id="category" name="category" class="{{ $inputClasses }}" required>
            <option value="" disabled @selected(old('category', $vacancy?->category?->value) === null)>Pilih kategori tugas...</option>
            @foreach ($categories as $category)
                <option value="{{ $category->value }}" @selected(old('category', $vacancy?->category?->value) === $category->value)>
                    {{ $category->label() }}
                </option>
            @endforeach
        </select>
        @error('category')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="location" class="{{ $labelClasses }}">Lokasi &amp; Patokan <span>*</span></label>
        <input type="text" id="location" name="location" value="{{ old('location', $vacancy?->location) }}"
            placeholder="Contoh: Kel. Sukajadi, Jl. Melati No. 12 (Patokan depan masjid)" class="{{ $inputClasses }}" required maxlength="255">
        @error('location')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label for="work_date" class="{{ $labelClasses }}">Tanggal Pelaksanaan <span>*</span></label>
            <input type="date" id="work_date" name="work_date" value="{{ old('work_date', $vacancy?->work_date?->format('Y-m-d')) }}"
                min="{{ now()->format('Y-m-d') }}" class="{{ $inputClasses }}" required>
            @error('work_date')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="duration" class="{{ $labelClasses }}">Estimasi Durasi <span>*</span></label>
            <select id="duration" name="duration" class="{{ $inputClasses }}" required>
                <option value="" disabled @selected(old('duration', $vacancy?->duration?->value) === null)>Pilih durasi tugas...</option>
                @foreach ($durations as $duration)
                    <option value="{{ $duration->value }}" @selected(old('duration', $vacancy?->duration?->value) === $duration->value)>
                        {{ $duration->label() }}
                    </option>
                @endforeach
            </select>
            @error('duration')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div>
        <div class="mb-1.5 flex items-center justify-between">
            <label for="notes" class="text-sm font-semibold text-brand-700">Catatan Tambahan</label>
            <span class="text-xs text-gray-500">Opsional</span>
        </div>
        <textarea id="notes" name="notes" rows="3" maxlength="1000"
            placeholder="Jelaskan peralatan yang disediakan atau instruksi khusus..." class="{{ $inputClasses }}">{{ old('notes', $vacancy?->notes) }}</textarea>
        @error('notes')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>

