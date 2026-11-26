<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda - bisaKerja</title>
    <link rel="stylesheet" href="{{ asset('css/seeker-applications.css') }}">
    <link rel="stylesheet" href="{{ asset('css/seeker-jobs.css') }}">
</head>
<body class="seeker-applications">

    @php($role = auth()->user()?->role)
    @php($roleLabel = match ($role) {
        'pencari_kerja' => 'Pencari Kerja',
        'penyedia_pekerjaan' => 'Penyedia Pekerjaan',
        'admin' => 'Admin',
        default => 'Lowongan Kerja',
    })

    <header class="seeker-header">
        <a href="{{ $role ? route('jobs.index') : route('welcome') }}" class="seeker-logo">bisaKerja</a>
        <span>{{ $roleLabel }}</span>
        <div class="seeker-header-actions">
            @if (auth()->check())
                @include('components.logout-form', ['buttonClass' => 'seeker-button'])
            @else
                <div class="seeker-header-links">
                    <a href="{{ route('login') }}" class="seeker-button seeker-button-link">Masuk</a>
                    <a href="{{ route('register.role') }}" class="seeker-button seeker-button-primary seeker-button-link">Daftar</a>
                </div>
            @endif
        </div>
    </header>

    <div class="seeker-layout">
        <aside class="seeker-sidebar">
            <nav aria-label="Navigasi pencari kerja">
                @if ($role === 'pencari_kerja')
                    <a href="{{ route('jobs.index') }}" aria-current="page">Beranda</a>
                    <a href="{{ route('seeker.applications') }}">Lamaran Saya</a>
                    <span class="seeker-disabled-menu" aria-disabled="true">Chat (Belum tersedia)</span>
                    <a href="{{ route('profile.edit') }}">Profil</a>
                @elseif ($role === 'penyedia_pekerjaan')
                    <a href="{{ route('home') }}">Beranda</a>
                    <a href="{{ route('employer.vacancies.index') }}">Lowongan Saya</a>
                    <a href="{{ route('home', ['view' => 'pelamar-saya']) }}">Pelamar Saya</a>
                    <a href="{{ route('provider.biodata') }}">Profil</a>
                    <span class="seeker-disabled-menu" aria-disabled="true">Chat (Belum tersedia)</span>
                @elseif ($role === 'admin')
                    <a href="{{ route('admin.dashboard') }}">Dashboard Admin</a>
                    <a href="{{ route('admin.verifikasi') }}">Verifikasi Akun</a>
                    <a href="{{ route('admin.pengguna') }}">Manajemen Pengguna</a>
                    <span class="seeker-disabled-menu" aria-disabled="true">Kategori (Belum tersedia)</span>
                    <span class="seeker-disabled-menu" aria-disabled="true">Laporan (Belum tersedia)</span>
                @else
                    <a href="{{ route('welcome') }}">Beranda</a>
                    <a href="{{ route('jobs.index') }}" aria-current="page">Cari Lowongan</a>
                    <a href="{{ route('login') }}">Masuk</a>
                    <a href="{{ route('register.role') }}">Daftar</a>
                @endif
            </nav>
        </aside>

        <main class="seeker-content">
            <section class="seeker-hero" aria-label="Pencarian lowongan pekerjaan">
                <div class="seeker-hero-inner">
                    <h1>Cari pekerjaan di sekitar rumah</h1>
                    <p>Temukan pekerjaan harian atau freelance sesuai lokasi tempat tinggalmu.</p>

                    <form action="{{ route('jobs.index') }}" method="GET">
                        <div class="seeker-search-bar">
                            <div class="seeker-search-field">
                                <input
                                    type="text"
                                    name="keyword"
                                    placeholder="Pekerjaan / Keahlian..."
                                    value="{{ request('keyword') }}"
                                    class="seeker-search-input"
                                >
                            </div>

                            <div class="seeker-search-field">
                                <input
                                    type="text"
                                    id="location-input"
                                    name="location"
                                    placeholder="Masukkan kota atau lokasi..."
                                    value="{{ request('location') }}"
                                    class="seeker-search-input"
                                    autocomplete="off"
                                >
                                <div id="location-dropdown" class="seeker-location-dropdown" hidden>
                                    <ul id="location-list" class="seeker-location-list"></ul>
                                </div>
                            </div>

                            <div class="seeker-search-field">
                                <select name="id_kategori" class="seeker-search-select">
                                    <option value="">Semua kategori</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id_kategori }}"
                                            @selected((string) request('id_kategori') === (string) $category->id_kategori)>
                                            {{ $category->nama_kategori }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <button type="submit" class="seeker-search-submit">Cari</button>
                        </div>
                    </form>

                    @if (request('keyword') || request('location') || request('id_kategori'))
                        <div class="seeker-reset-wrap">
                            <a href="{{ route('jobs.index') }}" class="seeker-reset-link">
                                ↻ Reset Filter Pencarian
                            </a>
                        </div>
                    @endif
                </div>
            </section>

            <section class="seeker-jobs-section" aria-labelledby="seeker-jobs-heading">
                <h2 id="seeker-jobs-heading">Lowongan di sekitarmu</h2>
                <p class="seeker-jobs-subtitle">Pilih pekerjaan yang sesuai dengan keahlian dan lokasimu.</p>

                @if ($jobs->isEmpty())
                    <div class="seeker-empty-card">
                        <p class="seeker-empty-title">Lowongan tidak ditemukan</p>
                        <p>Coba gunakan kata kunci, lokasi, atau kategori lain.</p>
                    </div>
                @else
                    <div class="seeker-jobs-grid">
                        @foreach ($jobs as $job)
                            <article class="seeker-job-card">
                                <div class="seeker-job-card-body">
                                    <h3>{{ $job->judul }}</h3>

                                    <p class="seeker-job-meta">
                                        📍 {{ $job->lokasi }}
                                        @if ($job->employer)
                                            | {{ $job->employer->nama }}
                                        @endif
                                    </p>

                                    @if ($job->kategori)
                                        <span class="seeker-job-category">{{ $job->kategori->nama_kategori }}</span>
                                    @endif

                                    <p class="seeker-job-desc">{{ $job->deskripsi }}</p>
                                </div>

                                <div class="seeker-job-card-footer">
                                    <div class="seeker-job-wage">
                                        Rp{{ number_format((float) $job->upah, 0, ',', '.') }}
                                    </div>

                                    <a href="{{ route('jobs.show', $job->id_lowongan) }}" class="seeker-job-action">
                                        Lihat Detail
                                    </a>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    @if ($jobs->hasPages())
                        <nav class="seeker-jobs-pagination" aria-label="Halaman lowongan">
                            <p class="seeker-jobs-pagination-info">
                                Halaman {{ $jobs->currentPage() }} dari {{ $jobs->lastPage() }} ({{ $jobs->total() }} lowongan)
                            </p>
                            <div class="seeker-jobs-pagination-buttons">
                                <a href="{{ $jobs->previousPageUrl() ?: '#' }}"
                                   class="seeker-button {{ $jobs->onFirstPage() ? 'is-disabled' : '' }}"
                                   @if ($jobs->onFirstPage()) aria-disabled="true" tabindex="-1" @endif>
                                    Sebelumnya
                                </a>
                                <a href="{{ $jobs->nextPageUrl() ?: '#' }}"
                                   class="seeker-button {{ ! $jobs->hasMorePages() ? 'is-disabled' : '' }}"
                                   @if (! $jobs->hasMorePages()) aria-disabled="true" tabindex="-1" @endif>
                                    Berikutnya
                                </a>
                            </div>
                        </nav>
                    @endif
                @endif
            </section>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const locations = [
                'Jakarta Selatan', 'Jakarta Timur', 'Jakarta Barat', 'Jakarta Utara', 'Jakarta Pusat',
                'Bekasi', 'Depok', 'Tangerang', 'Tangerang Selatan', 'Bogor',
                'Bandung', 'Cimahi', 'Cirebon', 'Sukabumi', 'Tasikmalaya',
                'Semarang', 'Surakarta (Solo)', 'Yogyakarta', 'Magelang', 'Purwokerto',
                'Surabaya', 'Malang', 'Sidoarjo', 'Gresik', 'Kediri',
                'Medan', 'Palembang', 'Pekanbaru', 'Bandar Lampung', 'Denpasar (Bali)',
                'Makassar', 'Manado', 'Banjarmasin', 'Balikpapan', 'Pontianak'
            ];

            const input = document.getElementById('location-input');
            const dropdown = document.getElementById('location-dropdown');
            const list = document.getElementById('location-list');

            if (!input || !dropdown || !list) {
                return;
            }

            input.addEventListener('input', function () {
                const query = this.value.trim().toLowerCase();
                list.innerHTML = '';

                if (query.length === 0) {
                    dropdown.hidden = true;
                    return;
                }

                const matches = locations.filter((loc) => loc.toLowerCase().includes(query));

                if (matches.length > 0) {
                    matches.forEach((loc) => {
                        const li = document.createElement('li');
                        li.className = 'seeker-location-item';

                        const pin = document.createElement('span');
                        pin.textContent = '📍';
                        const label = document.createElement('span');
                        label.textContent = loc;

                        li.append(pin, label);
                        li.addEventListener('click', function () {
                            input.value = loc;
                            dropdown.hidden = true;
                        });

                        list.appendChild(li);
                    });
                    dropdown.hidden = false;
                } else {
                    dropdown.hidden = true;
                }
            });

            document.addEventListener('click', function (e) {
                if (!input.contains(e.target) && !dropdown.contains(e.target)) {
                    dropdown.hidden = true;
                }
            });
        });
    </script>
</body>
</html>
