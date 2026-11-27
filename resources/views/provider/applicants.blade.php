@pushOnce('styles')
    <link rel="stylesheet" href="{{ asset('css/provider-applicants.css') }}">
@endPushOnce
@pushOnce('scripts')
    <script src="{{ asset('js/provider-applicants.js') }}" defer></script>
@endPushOnce
<x-layouts.employer title="Pelamar Saya">
    <div class="applicants-page mx-auto max-w-5xl" data-applicants data-endpoint="{{ route('provider.applicants.index') }}" data-login="{{ route('login') }}">
        <nav class="mb-6 flex gap-4 text-sm md:hidden" aria-label="Navigasi penyedia pekerjaan">
            <a href="{{ route('home') }}" class="text-brand-700">Beranda</a>
            <a href="{{ route('employer.vacancies.index') }}" class="text-brand-700">Lowongan saya</a>
            <a href="{{ route('home', ['view' => 'pelamar-saya']) }}" class="font-semibold text-brand-800" aria-current="page">Pelamar Saya</a>
        </nav>
        <div class="applicants-content">
            <h1>Pelamar Saya</h1>
            <p class="applicants-subtitle">Pantau pelamar dan perkembangan lamaran pada lowongan Anda.</p>
            <section class="applicants-summary" aria-label="Ringkasan seluruh lamaran">
                @foreach (['total' => 'Total Pelamar', 'menunggu' => 'Lamaran Menunggu', 'diterima' => 'Lamaran Diterima', 'ditolak' => 'Lamaran Ditolak'] as $key => $label)
                    <article class="applicants-stat">
                        <h2>{{ $label }}</h2>
                        <p data-count="{{ $key }}">—</p>
                    </article>
                @endforeach
            </section>
            <form id="applicants-filters" class="applicants-filters" role="search">
                <div class="applicants-search">
                    <label for="applicants-search">Cari pelamar atau pekerjaan</label>
                    <input id="applicants-search" name="search" type="search" maxlength="100" placeholder="Nama pelamar atau pekerjaan...">
                </div>
                <div>
                    <label for="applicants-status">Status lamaran</label>
                    <select id="applicants-status" name="status">
                        <option value="">Semua Status</option>
                        @foreach (\App\Models\Lamaran::STATUS_LABELS as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="applicants-button">Cari</button>
                <button type="reset" class="applicants-button applicants-button-secondary">Reset</button>
            </form>
            <div id="applicants-error" class="applicants-error" role="alert" hidden>
                <p></p>
                <button type="button" id="applicants-retry" class="applicants-button applicants-button-secondary">Coba lagi</button>
            </div>
            <p id="applicants-loading" class="applicants-loading" role="status" hidden>Memuat data pelamar...</p>
            <section id="applicants-results" aria-label="Daftar pelamar" aria-busy="true">
                <p class="applicants-scroll-hint">Geser tabel ke samping untuk melihat semua kolom.</p>
                <div class="applicants-table-container">
                    <table>
                        <caption class="applicants-sr-only">Lamaran pada lowongan milik Anda</caption>
                        <thead><tr><th scope="col">Nama Pelamar</th><th scope="col">Pekerjaan</th><th scope="col">Tanggal Melamar</th><th scope="col">Status</th><th scope="col">Detail</th></tr></thead>
                        <tbody id="applicants-rows"></tbody>
                    </table>
                    <div id="applicants-empty" class="applicants-empty" hidden>
                        <h2>Belum ada pelamar</h2>
                        <p>Lamaran yang masuk ke lowongan Anda akan tampil di sini.</p>
                    </div>
                </div>
                <nav class="applicants-pagination" aria-label="Halaman pelamar" hidden>
                    <p id="applicants-page-info" aria-live="polite"></p>
                    <div>
                        <button id="applicants-previous" type="button" class="applicants-button applicants-button-secondary">Sebelumnya</button>
                        <button id="applicants-next" type="button" class="applicants-button applicants-button-secondary">Berikutnya</button>
                    </div>
                </nav>
            </section>
        </div>
    <dialog id="applicants-detail" class="applicants-detail" aria-labelledby="applicants-detail-title">
        <div class="applicants-detail-header">
            <h2 id="applicants-detail-title">Detail Pelamar</h2>
            <button type="button" id="applicants-close" class="applicants-button applicants-button-secondary" aria-label="Tutup detail pelamar">Tutup</button>
        </div>
        <p id="applicants-detail-loading" role="status">Memuat detail pelamar...</p>
        <p id="applicants-detail-error" class="applicants-error" role="alert" hidden></p>
        <div id="applicants-detail-content" hidden>
            <img id="applicants-photo" alt="Foto profil pelamar" width="80" height="80" hidden>
            <dl id="applicants-detail-fields"></dl>
            <button id="applicants-cv" type="button" class="applicants-button" hidden>Unduh CV</button>
        </div>
    </dialog>
    </div>
</x-layouts.employer>
