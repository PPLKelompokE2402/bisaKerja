
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - bisaKerja</title>

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
            background: #ffffff;
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

        .main {
            max-width: 1200px;
            min-height: calc(100vh - 65px);
            margin: auto;
            padding: 55px 6%;
            display: flex;
            align-items: center;
            gap: 70px;
        }

        .image-section {
            flex: 1;
        }

        .image-section img {
            /* width: 100%; */
            height: 230px;
            object-fit: cover;
            border-radius: 16px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
        }

        .content-section {
            flex: 1;
            text-align: center;
        }

        .brand-title {
            font-size: 42px;
            font-weight: bold;
            color: #347650;
            margin-bottom: 20px;
        }

        .content-section h2 {
            font-size: 25px;
            margin-bottom: 12px;
        }

        .description {
            color: #718477;
            line-height: 1.7;
            font-size: 14px;
            margin-bottom: 30px;
        }

        .button-group {
            max-width: 350px;
            margin: auto;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .btn {
            padding: 13px;
            border-radius: 7px;
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
            transition: 0.2s;
        }

        .btn-login {
            background: white;
            border: 1px solid #347650;
            color: #347650;
        }

        .btn-register {
            background: #347650;
            color: white;
        }

        .btn:hover {
            transform: translateY(-2px);
            opacity: 0.9;
        }

        @media (max-width: 768px) {
            .main {
                flex-direction: column;
                gap: 35px;
                padding: 35px 6%;
            }

            .image-section,
            .content-section {
                width: 100%;
            }

            .image-section img {
                height: 260px;
            }

            .brand-title {
                font-size: 34px;
            }
        }
    </style>
</head>

<body>

    <header class="navbar">
        <div class="logo">bisaKerja</div>
    </header>

    <main class="main">

        <section class="image-section">
            <img
                src="{{ asset('images/Logo.png') }}"
                alt="Pekerja bisaKerja"
            >
        </section>

        <section class="content-section">

            <div class="brand-title">bisaKerja</div>

            <h2>Selamat datang!</h2>

            <p class="description">
                Temukan pekerjaan harian atau cari tenaga kerja
                untuk membantu pekerjaan di sekitarmu.
            </p>

            <div class="button-group">

                <a href="/login" class="btn btn-login">
                    Masuk
                </a>

                <a href="/register/role" class="btn btn-register">
                    Daftar
                </a>

            </div>

        </section>

    </main>

</body>
</html>
