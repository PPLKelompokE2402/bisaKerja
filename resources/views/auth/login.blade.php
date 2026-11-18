
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - bisaKerja</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
            background: white;
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

        .container {
            min-height: calc(100vh - 65px);
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 30px 20px;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 5px 25px rgba(0,0,0,.05);
        }

        .heading {
            text-align: center;
            margin-bottom: 30px;
        }

        .heading h1 {
            color: #347650;
            font-size: 30px;
            margin-bottom: 10px;
        }

        .heading p {
            color: #718477;
            font-size: 13px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #dce8df;
            border-radius: 7px;
            font-size: 14px;
            outline: none;
        }

        input:focus {
            border-color: #347650;
        }

        .btn-login {
            width: 100%;
            padding: 13px;
            background: #347650;
            color: white;
            border: none;
            border-radius: 7px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
        }

        .btn-login:hover {
            background: #285d43;
        }

        .register-link {
            text-align: center;
            margin-top: 25px;
            font-size: 13px;
            color: #718477;
        }

        .register-link a {
            color: #347650;
            text-decoration: none;
            font-weight: bold;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #718477;
            text-decoration: none;
        }

        @media(max-width: 480px) {
            .login-card {
                padding: 28px 22px;
            }
        }
    </style>
</head>

<body>

<header class="navbar">
    <div class="logo">bisaKerja</div>
</header>

<main class="container">

    <div class="login-card">

        <div class="heading">
            <h1>bisaKerja</h1>
            <p>Selamat datang kembali! Silakan masuk ke akun Anda.</p>
        </div>

        <form id="loginForm">

            <div class="form-group">
                <label for="login">Email atau Username</label>
                <input
                    type="text"
                    id="login"
                    name="login"
                    placeholder="Masukkan email atau username"
                    required >
            </div>

            <div class="form-group">
                <label for="kata_sandi">Password</label>
                <input
                    type="password"
                    id="kata_sandi"
                    name="kata_sandi"
                    placeholder="Masukkan password"
                    required
                >
            </div>

            <button type="submit" class="btn-login">
                Masuk
            </button>

        </form>

        <div class="register-link">
            Belum punya akun?
            <a href="{{ route('register.role') }}">Daftar sekarang</a>
        </div>

        <a href="{{ url('/') }}" class="back-link">
            ← Kembali ke halaman utama
        </a>

    </div>

</main>
<script>
   const loginForm = document.getElementById('loginForm');

    loginForm.addEventListener('submit', async function (event) {
        event.preventDefault();

        const submitButton = loginForm.querySelector('button[type="submit"]');

        const formData = {
            login: document.getElementById('login').value,
            kata_sandi: document.getElementById('kata_sandi').value
        };

        submitButton.disabled = true;
        submitButton.textContent = 'Memproses...';

        try {
            // 1. Login menggunakan API
            const response = await fetch('/api/login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(formData)
            });

            // Ambil hasil respons login
            const result = await response.json();

            // Periksa apakah login berhasil
            if (!response.ok) {
                if (result.errors) {
                    const messages = Object.values(result.errors)
                        .flat()
                        .join('\n');

                    alert(messages);
                } else {
                    alert(result.message || 'Login gagal.');
                }

                return;
            }

            // 2. Hubungkan token Sanctum dengan session Laravel
            const sessionResponse = await fetch('/auth/session', {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector(
                        'meta[name="csrf-token"]'
                    ).content
                },
                body: JSON.stringify({
                    token: result.token
                })
            });

            const sessionResult = await sessionResponse.json();

            if (!sessionResponse.ok) {
                alert(
                    sessionResult.message ||
                    'Gagal membuat session login.'
                );

                return;
            }

            // 3. Simpan token dan informasi pengguna
            sessionStorage.setItem('auth_token', result.token);
            sessionStorage.setItem('user', JSON.stringify(result.user));

            alert(result.message);

            // 4. Redirect berdasarkan role
            if (result.user.role === 'admin') {
                window.location.href = '/admin/dashboard';

            } else if (result.user.role === 'pencari_kerja') {
                window.location.href = '/lowongan';

            } else if (result.user.role === 'penyedia_pekerjaan') {
                window.location.href = '/pemberi-kerja/lowongan';

            } else {
                alert('Role pengguna tidak dikenali.');
            }

        } catch (error) {
            console.error('Detail error login:', error);
            alert('Terjadi kesalahan: ' + error.message);

        } finally {
            submitButton.disabled = false;
            submitButton.textContent = 'Masuk';
        }
    });
</script>
</body>
</html>
