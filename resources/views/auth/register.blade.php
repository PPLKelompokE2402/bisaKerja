
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biodata Pencari Kerja - bisaKerja</title>

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
            max-width: 1000px;
            margin: 35px auto;
            padding: 20px;
        }

        .heading {
            text-align: center;
            margin-bottom: 35px;
        }

        .heading h1 {
            font-size: 25px;
            margin-bottom: 10px;
        }

        .heading p {
            font-size: 13px;
            color: #718477;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 35px;
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

        input, select, textarea {
            width: 100%;
            padding: 11px 12px;
            border: 1px solid #dce8df;
            border-radius: 7px;
            background: white;
            font-size: 13px;
            font-family: inherit;
            outline: none;
        }

        input:focus, select:focus, textarea:focus {
            border-color: #347650;
        }

        textarea {
            resize: vertical;
            min-height: 75px;
        }

        input[type="file"] {
            padding: 9px;
            font-size: 12px;
        }

        .file-info {
            font-size: 11px;
            color: #87968b;
            margin-top: 5px;
        }

        .submit-wrapper {
            text-align: center;
            margin-top: 20px;
        }

        .btn-submit {
            background: #347650;
            color: white;
            border: none;
            padding: 13px 30px;
            border-radius: 7px;
            font-size: 13px;
            font-weight: bold;
            cursor: pointer;
        }

        .btn-submit:hover {
            background: #285d43;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #718477;
            font-size: 12px;
            text-decoration: none;
        }

        @media (max-width: 700px) {
            .form-grid {
                grid-template-columns: 1fr;
                gap: 0;
            }

            .container {
                margin-top: 15px;
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
    <span></span>
    <span class="active"></span>
</div>

<main class="container">

    <div class="heading">
        <h1>Biodata Pencari Kerja</h1>
        <p>Lengkapi biodata untuk melanjutkan pendaftaran.</p>
    </div>

    <form id="registerForm" enctype="multipart/form-data">

        <div class="form-grid">

            <!-- Kolom kiri: Informasi pribadi -->
            <div>
                <div class="form-group">
                    <label for="nama">Nama *</label>
                    <input type="text" id="nama" name="nama"
                           placeholder="Masukkan nama lengkap" required>
                </div>

                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email"
                           placeholder="Contoh: email@gmail.com" required>
                </div>

                <div class="form-group">
                    <label for="kata_sandi">Password *</label>
                    <input type="password" id="kata_sandi" name="kata_sandi"
                           minlength="8" placeholder="Minimal 8 karakter" required>
                </div>

                <div class="form-group">
                    <label for="nomor_telepon">No. HP *</label>
                    <input type="tel" id="nomor_telepon" name="nomor_telepon"
                           placeholder="Contoh: 081234567890">
                </div>

                <div class="form-group">
                    <label for="alamat">Lokasi *</label>
                    <input type="text" id="alamat" name="alamat"
                           placeholder="Masukkan alamat/lokasi" required>
                </div>

                <div class="form-group">
                    <label for="jenis_kelamin">Jenis Kelamin *</label>
                    <select id="jenis_kelamin" name="jenis_kelamin" required>
                        <option value="">Pilih jenis kelamin</option>
                        <option value="laki-laki">Laki-laki</option>
                        <option value="perempuan">Perempuan</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="deskripsi_diri">Deskripsi Diri *</label>
                    <textarea id="deskripsi_diri" name="deskripsi_diri"
                              placeholder="Ceritakan singkat tentang dirimu"
                              required></textarea>
                </div>
            </div>

            <!-- Kolom kanan: Profil dan pengalaman -->
            <div>
                <div class="form-group">
                    <label for="foto_profil">Foto Profil *</label>
                    <input type="file" id="foto_profil" name="foto_profil"
                           accept=".jpg,.jpeg,.png" required>
                    <p class="file-info">JPG/PNG, maksimal 2 MB.</p>
                </div>

                <div class="form-group">
                    <label for="keahlian">Keahlian *</label>
                    <textarea id="keahlian" name="keahlian"
                              placeholder="Contoh: menyapu, mengepel, membersihkan rumah"
                              required></textarea>
                </div>

                <div class="form-group">
                    <label for="pengalaman_kerja">Pengalaman Kerja *</label>
                    <textarea id="pengalaman_kerja" name="pengalaman_kerja"
                              placeholder="Tuliskan pengalaman kerja jika ada"
                              required></textarea>
                </div>

                <div class="form-group">
                    <label for="cv">CV *</label>
                    <input type="file" id="cv" name="cv"
                           accept=".pdf" required>
                    <p class="file-info">Format PDF, maksimal 5 MB.</p>
                </div>
            </div>

        </div>

        <div class="submit-wrapper">
            <button type="submit" class="btn-submit">
                Selesaikan Pendaftaran
            </button>
        </div>

    </form>

    <a href="{{ route('register.role') }}" class="back-link">
        ← Kembali memilih role
    </a>

</main>

<script>
    const registerForm = document.getElementById('registerForm');

    registerForm.addEventListener('submit', async function (event) {
        event.preventDefault();

        const submitButton = registerForm.querySelector('button[type="submit"]');
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
                    alert(result.message || 'Registrasi gagal.');
                }

                return;
            }

            alert(result.message);

            window.location.href = '/login';

        } catch (error) {
            alert('Terjadi kesalahan koneksi ke server.');
            console.error(error);

        } finally {
            submitButton.disabled = false;
            submitButton.textContent = 'Selesaikan Pendaftaran';
        }
    });
</script>
</body>
</html>
