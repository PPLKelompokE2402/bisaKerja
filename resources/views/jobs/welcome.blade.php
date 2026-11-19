
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>bisaKerja - Pekerjaan Harian di Sekitarmu</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f3faf5;
            color: #285d43;
        }

        a {
            text-decoration: none;
        }

        .navbar {
            height: 72px;
            background: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 6%;
            border-bottom: 1px solid #e5eee7;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #347650;
        }

        .nav-menu {
            display: flex;
            align-items: center;
            gap: 30px;
        }

        .nav-menu a {
            color: #61796a;
            font-size: 14px;
            transition: .2s;
        }

        .nav-menu a:hover,
        .nav-menu a.active {
            color: #347650;
            font-weight: bold;
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn {
            display: inline-block;
            padding: 11px 20px;
            border-radius: 9px;
            font-size: 13px;
            font-weight: bold;
            transition: .2s;
            cursor: pointer;
        }

        .btn:hover {
            transform: translateY(-2px);
            opacity: .9;
        }

        .btn-outline {
            border: 1px solid #347650;
            color: #347650;
            background: white;
        }

        .btn-primary {
            background: #347650;
            color: white;
            border: 1px solid #347650;
        }

        /* HERO */

        .hero {
            min-height: 300px;
            padding: 55px 6%;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            background:
                linear-gradient(
                    120deg,
                    rgba(225, 244, 231, .94),
                    rgba(240, 249, 242, .90)
                );
        }

        .hero-content {
            width: 100%;
            max-width: 1100px;
        }

        .hero h1 {
            font-size: 34px;
            color: #347650;
            margin-bottom: 12px;
        }

        .hero p {
            font-size: 15px;
            color: #718477;
            margin-bottom: 35px;
        }

        .search-form {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr)) auto;
            gap: 12px;
            align-items: end;
            text-align: left;
        }

        .search-group label {
            display: block;
            font-size: 12px;
            color: #347650;
            margin-bottom: 9px;
        }

        .search-group input {
            width: 100%;
            height: 44px;
            padding: 0 14px;
            border: 1px solid #dce9df;
            border-radius: 12px;
            outline: none;
            font-size: 13px;
        }

        .search-group select {
            width: 100%;
            height: 44px;
            padding: 0 14px;
            border: 1px solid #dce9df;
            border-radius: 12px;
            outline: none;
            background: white;
            font-size: 13px;
        }

        .search-group input:focus {
            border-color: #347650;
        }

        .search-button {
            height: 44px;
            min-width: 105px;
            border: none;
            border-radius: 12px;
            background: #347650;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }

        /* LOWONGAN */

        .jobs-section {
            padding: 40px 6%;
            max-width: 1400px;
            margin: auto;
        }

        .section-title {
            font-size: 21px;
            color: #347650;
            margin-bottom: 20px;
        }

        .jobs-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 18px;
        }

        .job-card {
            background: white;
            border: 1px solid #e0ebe2;
            border-radius: 18px;
            padding: 22px 18px 16px;
            display: flex;
            flex-direction: column;
            min-height: 195px;
            transition: .2s;
        }

        .job-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(52, 118, 80, .08);
        }

        .job-card h3 {
            color: #347650;
            font-size: 16px;
            margin-bottom: 10px;
        }

        .job-description {
            color: #718477;
            font-size: 13px;
            line-height: 1.6;
            min-height: 42px;
            margin-bottom: 15px;
        }

        .job-location {
            color: #87978b;
            font-size: 11px;
            margin-top: auto;
            margin-bottom: 7px;
        }

        .job-salary {
            color: #347650;
            font-size: 15px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .job-button {
            display: block;
            background: #347650;
            color: white;
            padding: 10px;
            border-radius: 10px;
            text-align: center;
            font-size: 12px;
            font-weight: bold;
        }

        .empty-jobs {
            grid-column: 1 / -1;
            background: white;
            border: 1px solid #e0ebe2;
            padding: 35px;
            border-radius: 16px;
            text-align: center;
            color: #718477;
        }

        /* CARA KERJA */

        .how-section {
            max-width: 1400px;
            margin: 0 auto 45px;
            padding: 45px 6%;
            background: white;
            border: 1px solid #e8eee9;
            border-radius: 20px;
        }

        .how-heading {
            text-align: center;
            margin-bottom: 35px;
        }

        .how-heading h2 {
            color: #347650;
            font-size: 21px;
            margin-bottom: 9px;
        }

        .how-heading p {
            color: #87978b;
            font-size: 13px;
        }

        .steps-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 18px;
        }

        .step-card {
            border: 1px solid #e0ebe2;
            border-radius: 16px;
            padding: 20px;
        }

        .step-number {
            display: inline-block;
            background: #dcefe0;
            color: #347650;
            border-radius: 20px;
            padding: 5px 11px;
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 13px;
        }

        .step-card h3 {
            font-size: 14px;
            color: #347650;
            margin-bottom: 8px;
        }

        .step-card p {
            color: #718477;
            font-size: 12px;
            line-height: 1.6;
        }

        /* FOOTER */

        .footer {
            background: white;
            border-top: 1px solid #e5eee7;
            padding: 25px 6%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
            color: #87978b;
            font-size: 11px;
        }

        .footer strong {
            color: #347650;
        }

        .footer-links {
            display: flex;
            gap: 20px;
        }

        .footer-links a {
            color: #718477;
        }

        @media (max-width: 768px) {
            .navbar {
                height: auto;
                min-height: 65px;
                padding: 16px 5%;
                flex-wrap: wrap;
                gap: 15px;
            }

            .nav-menu {
                order: 3;
                width: 100%;
                justify-content: center;
                gap: 20px;
                flex-wrap: wrap;
            }

            .nav-menu a {
                font-size: 12px;
            }

            .nav-actions {
                margin-left: auto;
            }

            .hero {
                padding: 45px 5%;
            }

            .hero h1 {
                font-size: 27px;
            }

            .search-form {
                grid-template-columns: 1fr;
            }

            .search-button {
                width: 100%;
            }

            .jobs-section {
                padding: 30px 5%;
            }

            .jobs-grid,
            .steps-grid {
                grid-template-columns: 1fr;
            }

            .how-section {
                margin: 0 5% 30px;
                padding: 30px 20px;
            }

            .footer {
                justify-content: center;
                text-align: center;
            }
        }
    </style>
</head>

<body>

    <!-- NAVBAR -->
    <header class="navbar">

        <a href="{{ route('welcome') }}" class="logo">
            bisaKerja
        </a>

        <nav class="nav-menu">
            <a href="{{ route('welcome') }}" class="active">Beranda</a>
            <!-- <a href="{{ route('jobs.index') }}">Cari Lowongan</a> -->
            <a href="{{ route('login') }}">Pasang Pekerjaan</a>
        </nav>

        <div class="nav-actions">
            <a href="{{ route('login') }}" class="btn btn-outline">
                Masuk
            </a>

            <a href="{{ route('register.role') }}" class="btn btn-primary">
                Daftar Sekarang
            </a>
        </div>

    </header>


    <!-- HERO SECTION -->
    <section class="hero">

        <div class="hero-content">

            <h1>Cari pekerjaan di sekitar rumah</h1>

            <p>
                Temukan pekerjaan harian yang sesuai dengan kebutuhan dan keahlianmu.
            </p>

            <form action="{{ route('jobs.index') }}" method="GET" class="search-form">

                <div class="search-group">
                    <label>Nama pekerjaan</label>
                    <input
                        type="text"
                        name="keyword"
                        placeholder="Cari nama pekerjaan..."
                    >
                </div>

                <div class="search-group">
                    <label>Lokasi</label>
                    <input
                        type="text"
                        name="location"
                        placeholder="Masukkan kota atau kecamatan"
                    >
                </div>

                <div class="search-group">
                    <label>Kategori pekerjaan</label>
                    <select name="id_kategori">
                        <option value="">Semua kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id_kategori }}">{{ $category->nama_kategori }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="search-button">
                    Cari Lowongan
                </button>

            </form>

        </div>

    </section>


    <!-- LOWONGAN TERBARU -->
    <section class="jobs-section">

        <h2 class="section-title">
            Lowongan di sekitarmu
        </h2>

        <div class="jobs-grid">

            @forelse($jobs as $job)

                <article class="job-card">

                    <h3>{{ $job->judul }}</h3>

                    <p class="job-description">
                        {{ \Illuminate\Support\Str::limit($job->deskripsi, 90) }}
                    </p>

                    <p class="job-location">
                        {{ $job->lokasi }}
                        @if($job->employer)
                            · {{ $job->employer->nama }}
                        @endif
                    </p>

                    <p class="job-salary">
                        Rp{{ number_format((float) $job->upah, 0, ',', '.') }}
                    </p>

                    <a
                        href="{{ route('jobs.show', $job->id_lowongan) }}"
                        class="job-button"
                    >
                        Lihat Detail
                    </a>

                </article>

            @empty

                <div class="empty-jobs">
                    <h3>Belum ada lowongan tersedia</h3>
                    <p>Silakan kunjungi kembali nanti untuk melihat pekerjaan terbaru.</p>
                </div>

            @endforelse

        </div>

    </section>


    <!-- CARA KERJA -->
    <section class="how-section">

        <div class="how-heading">
            <h2>Cara Mudah Cari Kerja Harian</h2>
            <p>
                Langkah singkat bekerja dan mendapatkan penghasilan tambahan.
            </p>
        </div>

        <div class="steps-grid">

            <div class="step-card">
                <span class="step-number">01</span>
                <h3>Cari Lowongan Dekatmu</h3>
                <p>
                    Pilih pekerjaan fleksibel di lingkungan sekitar
                    sesuai dengan kemampuanmu.
                </p>
            </div>

            <div class="step-card">
                <span class="step-number">02</span>
                <h3>Ajukan Lamaran</h3>
                <p>
                    Hubungi penyedia pekerjaan dan ajukan lamaran
                    sesuai dengan kebutuhan pekerjaan.
                </p>
            </div>

            <div class="step-card">
                <span class="step-number">03</span>
                <h3>Kerja dan Terima Upah</h3>
                <p>
                    Selesaikan pekerjaan yang disepakati dan
                    terima upah sesuai kesepakatan.
                </p>
            </div>

        </div>

    </section>


    <!-- FOOTER -->
    <footer class="footer">

        <div>
            <strong>bisaKerja</strong> —
            Portal Pekerjaan Harian Warga
        </div>

        <div>
            © {{ date('Y') }} bisaKerja. Hak cipta dilindungi undang-undang.
        </div>

        <div class="footer-links">
            <a href="{{ route('login') }}">Bantuan</a>
            <a href="{{ route('welcome') }}">Syarat & Ketentuan</a>
            <a href="{{ route('welcome') }}">Privasi</a>
        </div>

    </footer>

</body>
</html>
