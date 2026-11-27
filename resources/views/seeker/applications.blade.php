<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lamaran Saya - bisaKerja</title>
    <link rel="stylesheet" href="{{ asset('css/seeker-applications.css') }}">
    <script src="{{ asset('js/seeker-applications.js') }}" defer></script>
</head>
<body class="seeker-applications seeker-page" data-endpoint="{{ route('seeker.applications.index') }}" data-login="{{ route('login') }}">
    <x-app-header />
    <div class="seeker-layout">
        <aside class="seeker-sidebar">
            <x-seeker.sidebar />
        </aside>
        <main class="seeker-content">
            <p id="seeker-loading" class="seeker-loading" role="status" hidden>Memuat status lamaran...</p>
            <div id="seeker-error" class="seeker-error" role="alert" hidden>
                <p></p>
                <button id="seeker-retry" type="button" class="seeker-button">Coba lagi</button>
            </div>
            @foreach (['applications' => ['Lamaran', 'Pantau perkembangan lamaran pekerjaanmu.', 'Belum ada lamaran pekerjaan. Yuk, temukan pekerjaan yang sesuai denganmu!'], 'history' => ['Riwayat pekerjaan', 'Daftar pekerjaan yang sudah kamu kerjakan.', 'Belum ada riwayat pekerjaan yang selesai.']] as $key => [$title, $description, $empty])
                <section class="seeker-section" aria-labelledby="seeker-{{ $key }}-title" id="seeker-{{ $key }}" aria-busy="true">
                    @if ($key === 'applications')
                        <h1 id="seeker-{{ $key }}-title">{{ $title }}</h1>
                    @else
                        <h2 id="seeker-{{ $key }}-title">{{ $title }}</h2>
                    @endif
                    <p class="seeker-description">{{ $description }}</p>
                    <div class="seeker-table-wrap" tabindex="0" role="region" aria-label="Tabel {{ strtolower($title) }}; geser untuk melihat seluruh kolom">
                        <table>
                            <caption class="seeker-sr-only">{{ $title }} milik akun Anda</caption>
                            <thead><tr><th scope="col">Pekerjaan</th><th scope="col">Upah</th><th scope="col">Tanggal melamar</th><th scope="col">Status</th></tr></thead>
                            <tbody id="seeker-{{ $key }}-rows"></tbody>
                        </table>
                        <p id="seeker-{{ $key }}-empty" class="seeker-state" hidden>{{ $empty }}</p>
                    </div>
                    <nav class="seeker-pagination" id="seeker-{{ $key }}-pagination" aria-label="Halaman {{ strtolower($title) }}" hidden>
                        <p id="seeker-{{ $key }}-page-info" aria-live="polite"></p>
                        <div>
                            <button type="button" class="seeker-button" id="seeker-{{ $key }}-previous">Sebelumnya</button>
                            <button type="button" class="seeker-button" id="seeker-{{ $key }}-next">Berikutnya</button>
                        </div>
                    </nav>
                </section>
            @endforeach
        </main>
    </div>
</body>
</html>
