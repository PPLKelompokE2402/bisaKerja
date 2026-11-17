
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Role - bisaKerja</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f3faf5;
            min-height: 100vh;
            color: #285d43;
        }

        .navbar {
            height: 65px;
            background: #fff;
            display: flex;
            align-items: center;
            padding: 0 6%;
            border-bottom: 1px solid #e8eee9;
        }

        .logo {
            font-size: 21px;
            font-weight: bold;
            color: #347650;
        }

        .progress {
            display: flex;
            justify-content: center;
            gap: 8px;
            padding: 20px 0;
        }

        .progress span {
            width: 35px;
            height: 5px;
            border-radius: 10px;
            background: #dce9df;
        }

        .progress span.active {
            background: #347650;
        }

        .container {
            max-width: 850px;
            margin: 100px auto;
            padding: 20px;
            text-align: center;
        }

        h1 {
            font-size: 28px;
            margin-bottom: 12px;
        }

        .subtitle {
            color: #718477;
            font-size: 14px;
            margin-bottom: 40px;
        }

        .role-container {
            display: flex;
            justify-content: center;
            gap: 25px;
        }

        .role-card {
            width: 280px;
            padding: 30px 25px;
            background: white;
            border: 1px solid #e3eee5;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,.04);
        }

        .role-card h3 {
            margin-bottom: 12px;
            color: #347650;
        }

        .role-card p {
            font-size: 13px;
            color: #718477;
            line-height: 1.6;
            min-height: 60px;
            margin-bottom: 20px;
        }

        .btn {
            display: block;
            padding: 11px;
            border-radius: 7px;
            text-decoration: none;
            font-size: 13px;
            font-weight: bold;
            transition: .2s;
        }

        .btn-primary {
            background: #347650;
            color: white;
        }

        .btn-outline {
            border: 1px solid #347650;
            color: #347650;
        }

        .btn:hover {
            opacity: .85;
            transform: translateY(-2px);
        }

        .back-link {
            display: inline-block;
            margin-top: 35px;
            color: #718477;
            font-size: 13px;
            text-decoration: none;
        }

        @media(max-width: 650px) {
            .container {
                margin: 50px auto;
            }

            .role-container {
                flex-direction: column;
                align-items: center;
            }

            .role-card {
                width: 100%;
                max-width: 350px;
            }
        }
    </style>
</head>

<body>

    <header class="navbar">
        <div class="logo">bisaKerja</div>
    </header>

    <div class="progress">
        <span></span>
        <span class="active"></span>
        <span></span>
    </div>

    <main class="container">

        <h1>Daftar sebagai apa?</h1>

        <p class="subtitle">
            Pilih peran yang sesuai untuk melanjutkan pendaftaran.
        </p>

        <div class="role-container">

            <div class="role-card">
                <h3>Pencari Kerja</h3>

                <p>
                    Saya ingin mencari dan melamar pekerjaan
                    harian di sekitar saya.
                </p>

                <a href="{{ route('register') }}" class="btn btn-primary">
                    Daftar sebagai Pencari Kerja
                </a>
            </div>

            <div class="role-card">
                <h3>Pemberi Kerja</h3>

                <p>
                    Saya ingin memasang lowongan dan mencari
                    orang untuk membantu pekerjaan.
                </p>

                <a href="#" class="btn btn-outline">
                    Daftar sebagai Pemberi Kerja
                </a>
            </div>

        </div>

        <a href="{{ url('/') }}" class="back-link">
            ← Kembali ke halaman utama
        </a>

    </main>

</body>
</html>
