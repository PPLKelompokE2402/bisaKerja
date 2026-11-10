
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - bisaKerja</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f3faf5;
            margin: 0;
            padding: 40px;
            color: #285d43;
        }

        .card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            max-width: 700px;
            margin: auto;
            box-shadow: 0 5px 25px rgba(0,0,0,.05);
        }

        h1 {
            color: #347650;
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>Dashboard Admin bisaKerja</h1>
        <p id="welcome">Memeriksa akun Admin...</p>
        <p>Selamat datang di halaman pengelolaan bisaKerja.</p>
    </div>

    <script>
        async function checkAdmin() {
            const token = sessionStorage.getItem('auth_token');

            if (!token) {
                window.location.replace('/login');
                return;
            }

            try {
                const response = await fetch('/api/user', {
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': `Bearer ${token}`
                    }
                });

                if (!response.ok) {
                    window.location.replace('/login');
                    return;
                }

                const result = await response.json();
                const user = result.user;

                if (!user || user.role !== 'admin') {
                    window.location.replace('/home');
                    return;
                }

                document.getElementById('welcome').textContent =
                    `Halo, ${user.nama}! Anda login sebagai Admin.`;

            } catch (error) {
                console.error(error);
                document.getElementById('welcome').textContent =
                    'Gagal memverifikasi sesi Admin.';
            }
        }

        checkAdmin();
    </script>
</body>
</html>
