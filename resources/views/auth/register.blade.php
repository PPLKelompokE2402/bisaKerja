
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - bisaKerja</title>

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
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .register-card {
            width: 100%;
            max-width: 430px;
            background: white;
            padding: 35px;
            border-radius: 12px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.06);
        }

        .logo {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            color: #347650;
            margin-bottom: 25px;
        }

        h1 {
            font-size: 23px;
            text-align: center;
            margin-bottom: 10px;
        }

        .description {
            text-align: center;
            font-size: 13px;
            color: #718477;
            margin-bottom: 25px;
            line-height: 1.5;
        }

        .role-label {
            background: #f0f8f2;
            padding: 12px;
            border-radius: 7px;
            font-size: 13px;
            text-align: center;
            margin-bottom: 22px;
        }

        .form-group {
            margin-bottom: 18px;
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
            font-size: 13px;
            outline: none;
        }

        input:focus {
            border-color: #347650;
        }

        .btn-submit {
            width: 100%;
            background: #347650;
            color: white;
            border: none;
            padding: 13px;
            border-radius: 7px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 8px;
        }

        .btn-submit:hover {
            background: #285d43;
        }

        .btn-submit:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .footer {
            text-align: center;
            font-size: 12px;
            color: #718477;
            margin-top: 22px;
        }

        .footer a, .back-link {
            color: #347650;
            text-decoration: none;
            font-weight: bold;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 18px;
            font-size: 12px;
        }
    </style>
</head>

<body>

<div class="register-card">

    <div class="logo">bisaKerja</div>

    <h1>Buat Akun</h1>
    <p class="description">
        Daftarkan akunmu terlebih dahulu untuk mulai menggunakan bisaKerja.
    </p>

    <div class="role-label">
        Mendaftar sebagai:
        <strong id="roleLabel">Memuat...</strong>
    </div>

    <form id="registerForm">

        <input type="hidden" id="role" name="role" value="{{ request('role') }}">

        <div class="form-group">
            <label for="email">Email</label>
            <input
                type="email"
                id="email"
                name="email"
                placeholder="Contoh: email@gmail.com"
                required
            >
        </div>

        <div class="form-group">
            <label for="username">Username</label>
            <input
                type="text"
                id="username"
                name="username"
                placeholder="Buat username"
                maxlength="50"
                required
            >
        </div>

        <div class="form-group">
            <label for="kata_sandi">Password</label>
            <input
                type="password"
                id="kata_sandi"
                name="kata_sandi"
                placeholder="Minimal 8 karakter"
                minlength="8"
                required
            >
        </div>

        <button type="submit" class="btn-submit">
            Daftar Sekarang
        </button>

    </form>

    <div class="footer">
        Sudah memiliki akun?
        <a href="{{ route('login') }}">Masuk</a>
    </div>

    <a href="{{ route('register.role') }}" class="back-link">
        ← Kembali memilih role
    </a>

</div>

<script>
    const registerForm = document.getElementById('registerForm');
    const roleInput = document.getElementById('role');
    const roleLabel = document.getElementById('roleLabel');

    const roleNames = {
        pencari_kerja: 'Pencari Kerja',
        penyedia_pekerjaan: 'Penyedia Pekerjaan'
    };

    const selectedRole = roleInput.value;

    if (!roleNames[selectedRole]) {
        window.location.href = "{{ route('register.role') }}";
    } else {
        roleLabel.textContent = roleNames[selectedRole];
    }

    registerForm.addEventListener('submit', async function(event) {
        event.preventDefault();

        const submitButton = registerForm.querySelector('button[type="submit"]');

        if (!roleNames[roleInput.value]) {
            alert('Silakan pilih role terlebih dahulu.');
            return;
        }

        const formData = new FormData(registerForm);

        submitButton.disabled = true;
        submitButton.textContent = 'Memproses...';

        try {
            const response = await fetch('/api/register/job-seeker', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json'
                },
                body: formData
            });

            const result = await response.json();

            if (!response.ok) {
                if (result.errors) {
                    const messages = Object.values(result.errors)
                        .flat()
                        .join('\n');

                    alert(messages);
                } else {
                    alert(result.message || 'Pendaftaran gagal.');
                }

                return;
            }

            alert(result.message || 'Akun berhasil dibuat. Silakan masuk.');

            window.location.href = "{{ route('login') }}";

        } catch (error) {
            alert('Terjadi kesalahan koneksi ke server.');
            console.error(error);

        } finally {
            submitButton.disabled = false;
            submitButton.textContent = 'Daftar Sekarang';
        }
    });
</script>

</body>
</html>
