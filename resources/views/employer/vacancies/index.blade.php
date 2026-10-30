<x-layouts.employer title="Lowongan Saya">
    <div class="mx-auto max-w-5xl">
        <h1 class="text-2xl font-semibold text-brand-700">Lowongan</h1>
        <p class="mt-1 text-sm text-gray-600">Kelola lowongan dan pantau jumlah pelamar.</p>

        <div class="mt-8 mb-4 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-brand-700">Lowongan Saya</h2>
            <button type="button" data-dialog-open="create-vacancy-dialog"
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
                                <p class="font-semibold text-gray-900">{{ $vacancy->title }}</p>
                                <p class="mt-1 text-xs text-gray-500">{{ $vacancy->location }}</p>
                            </td>
                            <td class="px-5 py-4 text-gray-800">{{ $vacancy->applicants_count }} pelamar</td>
                            <td class="px-5 py-4">
                                <span @class([
                                    'inline-block w-20 rounded border px-2 py-1 text-center text-xs',
                                    'border-gray-300 bg-gray-100 text-gray-800' => $vacancy->isOpen(),
                                    'border-gray-200 bg-gray-50 text-gray-400' => ! $vacancy->isOpen(),
                                ])>
                                    {{ $vacancy->status->label() }}
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
                                        <input type="hidden" name="status" value="{{ \App\Enums\VacancyStatus::Open->value }}">
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
                                Belum ada lowongan. Klik "Tambah Lowongan" untuk memasang lowongan pertamamu.
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
                <x-vacancy.form-fields :categories="$categories" :durations="$durations" />
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
</x-layouts.employer>

