
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - bisaKerja</title>

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
            min-height: 100vh;
        }

        header {
            background: white;
            padding: 20px 6%;
            border-bottom: 1px solid #e8eee9;
        }

        .logo {
            font-size: 21px;
            font-weight: bold;
            color: #347650;
        }

        main {
            min-height: 80vh;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 30px;
        }

        .content h1 {
            font-size: 30px;
            margin-bottom: 15px;
        }

        .content p {
            color: #718477;
            font-size: 14px;
            line-height: 1.7;
        }

        .status {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 18px;
            background: #fff4d6;
            color: #936b16;
            border-radius: 20px;
            font-size: 13px;
        }
    </style>
</head>

<body>

<header>
    <div class="logo">bisaKerja</div>
</header>

<main>
    <div class="content">
        <h1>Selamat Datang di bisaKerja!</h1>
        <p>Anda berhasil masuk ke dalam aplikasi.</p>

        <div class="status" id="accountStatus">
            Memeriksa status akun...
        </div>
    </div>
</main>

<script>
    const user = JSON.parse(sessionStorage.getItem('user'));

    if (user) {
        const statusElement = document.getElementById('accountStatus');

        if (user.status_akun === 'menunggu_verifikasi') {
            statusElement.textContent = 'Akun Anda sedang menunggu verifikasi Admin.';
        } else if (user.status_akun === 'aktif') {
            statusElement.textContent = 'Akun Anda sudah terverifikasi.';
        }
    }
</script>

</body>
</html>
