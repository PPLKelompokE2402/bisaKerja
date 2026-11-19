<x-layouts.employer title="Lowongan Saya">
    <div class="mx-auto max-w-5xl">
        <h1 class="text-2xl font-semibold text-brand-700">Lowongan</h1>
        <p class="mt-1 text-sm text-gray-600">Kelola lowongan dan pantau jumlah pelamar.</p>

        <div class="mt-8 mb-4 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-brand-700">Lowongan Saya</h2>
            <button type="button" id="btn-create-vacancy"
                class="rounded-lg border border-brand-700 bg-white px-4 py-2 text-sm text-brand-700 transition hover:bg-brand-100">
                + Tambah Lowongan
            </button>
        </div>

        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
            <table class="w-full text-left text-sm">
                <thead class="bg-brand-700 text-white">
                    <tr>
                        <th scope="col" class="px-5 py-3.5 font-medium">Pekerjaan</th>
                        <th scope="col" class="px-5 py-3.5 font-medium">Jumlah pelamar</th>
                        <th scope="col" class="px-5 py-3.5 font-medium">Status</th>
                        <th scope="col" class="px-5 py-3.5 font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($vacancies as $vacancy)
                        <tr>
                            <td class="px-5 py-4">
                                <p class="font-semibold text-gray-900">{{ $vacancy->judul }}</p>
                                <p class="mt-1 text-xs text-gray-500">{{ $vacancy->lokasi }}</p>
                            </td>
                            <td class="px-5 py-4 text-gray-800">{{ $vacancy->lamaran_count }} pelamar</td>
                            <td class="px-5 py-4">
                                <span @class([
                                    'inline-block w-20 rounded border px-2 py-1 text-center text-xs',
                                    'border-gray-300 bg-gray-100 text-gray-800' => $vacancy->isOpen(),
                                    'border-gray-200 bg-gray-50 text-gray-400' => ! $vacancy->isOpen(),
                                ])>
                                    {{ $vacancy->status_label }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                @if ($vacancy->isOpen())
                                    <a href="{{ route('employer.vacancies.edit', $vacancy) }}"
                                        class="inline-block w-24 rounded border border-gray-300 bg-gray-100 px-2 py-1 text-center text-xs text-gray-800 transition hover:bg-gray-200">
                                        Kelola
                                    </a>
                                @else
                                    <form method="POST" action="{{ route('employer.vacancies.status.update', $vacancy) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="aktif">
                                        <button type="submit"
                                            class="w-24 rounded border border-gray-300 bg-gray-100 px-2 py-1 text-xs text-gray-800 transition hover:bg-gray-200">
                                            Buka kembali
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-10 text-center text-gray-500">
                                Belum ada lowongan. Klik "+ Tambah Lowongan" untuk memasang lowongan pertamamu.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <dialog id="create-vacancy-dialog" @if ($errors->any()) data-open-on-load @endif
        class="m-auto w-full max-w-lg rounded-xl bg-white p-0 shadow-xl backdrop:bg-black/40">
        <form method="POST" action="{{ route('employer.vacancies.store') }}" class="flex flex-col">
            @csrf

            <div class="flex items-start justify-between border-b border-gray-200 px-6 pt-6 pb-4">
                <div>
                    <h2 class="text-lg font-semibold text-brand-700">Tambah Lowongan Pekerjaan</h2>
                    <p class="text-sm text-gray-500">Pasang lowongan pekerjaan harian untuk warga di sekitarmu.</p>
                </div>
                <button type="button" data-dialog-close aria-label="Tutup" class="text-xl leading-none text-gray-400 hover:text-gray-600">&times;</button>
            </div>

            <div class="max-h-[60vh] overflow-y-auto px-6 py-5">
                <x-vacancy.form-fields :categories="$categories" />
            </div>

            <div class="flex items-center justify-between border-t border-gray-200 px-6 py-4">
                <p class="text-xs text-gray-500">Bidang bertanda (*) wajib diisi.</p>
                <div class="flex gap-2">
                    <button type="button" data-dialog-close
                        class="rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-700 transition hover:bg-gray-100">Batal</button>
                    <button type="submit"
                        class="rounded-lg bg-brand-700 px-4 py-2 text-sm font-medium text-white transition hover:bg-brand-800">Pasang Lowongan</button>
                </div>
            </div>
        </form>
    </dialog>
    <dialog id="verification-warning-dialog"
    class="m-auto w-full max-w-md rounded-2xl bg-white p-0 shadow-xl backdrop:bg-black/40">

    <div class="p-6 text-center">

        <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-amber-100">
            <svg xmlns="http://www.w3.org/2000/svg"
                class="h-8 w-8 text-amber-600"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 9v4m0 4h.01M10.3 3.9l-8 14A2 2 0 004 21h16a2 2 0 001.7-3.1l-8-14a2 2 0 00-3.4 0z"/>
            </svg>
        </div>

        <h2 class="text-xl font-semibold text-brand-700">
            Akun Belum Terverifikasi
        </h2>

        <p id="verification-warning-message"
            class="mt-3 text-sm leading-relaxed text-gray-600">
            Akun Anda belum dapat membuat lowongan pekerjaan karena masih menunggu verifikasi Admin.
        </p>

        <div class="mt-5 rounded-lg bg-amber-50 p-4 text-left">
            <p class="text-sm font-semibold text-amber-800">
                Apa yang harus dilakukan?
            </p>
            <p class="mt-1 text-xs leading-relaxed text-amber-700">
                Silakan tunggu Admin menyelesaikan proses verifikasi akun Anda.
                Setelah akun disetujui, fitur pembuatan lowongan akan terbuka secara otomatis.
            </p>
        </div>

        <button type="button"
            data-verification-close
            class="mt-6 w-full rounded-lg bg-brand-700 px-4 py-3 text-sm font-medium text-white transition hover:bg-brand-800">
            Mengerti
        </button>

    </div>
</dialog>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const createButton = document.getElementById('btn-create-vacancy');
    const createDialog = document.getElementById('create-vacancy-dialog');
    const warningDialog = document.getElementById('verification-warning-dialog');
    const warningMessage = document.getElementById('verification-warning-message');

    const user = JSON.parse(sessionStorage.getItem('user') || '{}');

    const status = user.status_akun;

    createButton.addEventListener('click', function () {

        if (status === 'aktif') {
            createDialog.showModal();
            return;
        }

        if (status === 'ditangguhkan') {
            warningMessage.textContent =
                'Akun Anda sedang ditangguhkan oleh Admin. Anda tidak dapat membuat lowongan pekerjaan. Silakan hubungi Admin untuk informasi lebih lanjut.';
        } else if (status === 'ditolak') {
            warningMessage.textContent =
                'Pengajuan verifikasi akun Anda ditolak. Silakan perbaiki biodata dan ajukan verifikasi ulang sebelum membuat lowongan pekerjaan.';
        } else if (status === 'belum_lengkap') {
            warningMessage.textContent =
                'Biodata Anda belum lengkap. Silakan lengkapi biodata terlebih dahulu agar akun dapat diverifikasi Admin.';
        } else {
            warningMessage.textContent =
                'Akun Anda masih menunggu verifikasi Admin. Anda belum dapat membuat lowongan pekerjaan.';
        }

        warningDialog.showModal();
    });

    document.querySelectorAll('[data-verification-close]').forEach(button => {
        button.addEventListener('click', function () {
            warningDialog.close();
        });
    });

    warningDialog.addEventListener('click', function (event) {
        if (event.target === warningDialog) {
            warningDialog.close();
        }
    });

});
</script>

</x-layouts.employer>


