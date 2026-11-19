<x-layouts.employer title="Kelola Lowongan">
    <div class="mx-auto max-w-3xl">
        <a href="{{ route('employer.vacancies.index') }}" class="text-sm text-brand-700 hover:underline">&larr; Kembali ke Lowongan Saya</a>

        <h1 class="mt-4 text-2xl font-semibold text-brand-700">Kelola Lowongan</h1>
        <p class="mt-1 text-sm text-gray-600">Ubah informasi, tutup, atau hapus lowongan "{{ $vacancy->judul }}".</p>

        <form method="POST" action="{{ route('employer.vacancies.update', $vacancy) }}"
            class="mt-6 rounded-xl border border-gray-200 bg-white">
            @csrf
            @method('PUT')

            <div class="px-6 py-5">
                <x-vacancy.form-fields :vacancy="$vacancy" :categories="$categories" />
            </div>

            <div class="flex items-center justify-between border-t border-gray-200 px-6 py-4">
                <p class="text-xs text-gray-500">Bidang bertanda (*) wajib diisi.</p>
                <div class="flex gap-2">
                    <a href="{{ route('employer.vacancies.index') }}"
                        class="rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-700 transition hover:bg-gray-100">Batal</a>
                    <button type="submit"
                        class="rounded-lg bg-brand-700 px-4 py-2 text-sm font-medium text-white transition hover:bg-brand-800">Simpan Perubahan</button>
                </div>
            </div>
        </form>

        <div class="mt-6 flex flex-col gap-4 rounded-xl border border-gray-200 bg-white px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-semibold text-gray-900">Tutup lowongan</h2>
                <p class="text-sm text-gray-500">Lowongan yang ditutup tidak lagi menerima pelamar baru. Kamu bisa membukanya kembali kapan saja.</p>
            </div>
            <form method="POST" action="{{ route('employer.vacancies.status.update', $vacancy) }}">
                @csrf
                @method('PATCH')
                <input type="hidden" name="status" value="ditutup">
                <button type="submit"
                    class="rounded-lg border border-gray-300 px-4 py-2 text-sm whitespace-nowrap text-gray-700 transition hover:bg-gray-100">Tutup Lowongan</button>
            </form>
        </div>

        <div class="mt-4 flex flex-col gap-4 rounded-xl border border-amber-200 bg-white px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-semibold text-amber-800">Tutup lowongan</h2>
                <p class="text-sm text-gray-500">Permintaan ini akan menutup lowongan dan mempertahankan data serta riwayat lamaran.</p>
            </div>
            <form method="POST" action="{{ route('employer.vacancies.destroy', $vacancy) }}"
                data-confirm="Yakin ingin menutup lowongan ini dan mempertahankan seluruh riwayatnya?">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="rounded-lg bg-amber-700 px-4 py-2 text-sm font-medium whitespace-nowrap text-white transition hover:bg-amber-800">Tutup Lowongan</button>
            </form>
        </div>
    </div>
</x-layouts.employer>
