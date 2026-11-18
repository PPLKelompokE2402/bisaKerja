<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BisaKerja - Biodata Penyedia Pekerjaan</title>
  <style>

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
    background-color: #e9f0eb;
    color: #333;
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 25px;
}

  header {
    background-color: #ffffff;
    padding: 0 0 20px;
    margin-bottom: 25px;
    border-bottom: 1px solid #e8eee9;
}

    .brand {
      color: #1b7348;
      font-size: 24px;
      font-weight: 700;
    }

   .container {
    width: 100%;
    max-width: 1000px;
    max-height: 92vh;
    overflow-y: auto;
    margin: auto;
    padding: 30px 40px;
    background: #ffffff;
    border-radius: 18px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.15);
}

    .steps-indicator {
      display: flex;
      justify-content: center;
      gap: 10px;
      margin-bottom: 30px;
    }

    .step-bar {
      height: 8px;
      border-radius: 10px;
      background-color: #c8e6d2;
      width: 50px;
    }

    .step-bar.active {
      background-color: #1b7348;
      width: 60px;
    }

    .top-meta {
      display: flex;
      justify-content: space-between;
      font-size: 13px;
      color: #666;
      margin-bottom: 25px;
    }

    .back-link {
      color: #666;
      text-decoration: none;
      cursor: pointer;
    }

    .title-section {
      text-align: center;
      margin-bottom: 35px;
    }

    .title-section h1 {
      color: #1b7348;
      font-size: 28px;
      margin-bottom: 8px;
    }

    .title-section p {
      color: #666;
      font-size: 14px;
    }

    .form-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 25px 40px;
    }

    .form-group {
      display: flex;
      flex-direction: column;
    }

    .form-group label {
      font-size: 13px;
      font-weight: 600;
      color: #1b7348;
      margin-bottom: 8px;
    }

    .form-control {
      width: 100%;
      padding: 12px 16px;
      border: 1px solid #dcdcdc;
      border-radius: 12px;
      font-size: 14px;
      outline: none;
      background-color: #ffffff;
      transition: border-color 0.2s;
    }

    .form-control:focus {
      border-color: #1b7348;
    }

    select.form-control {
      appearance: none;
      background-image: url('data:image/svg+xml;utf8,<svg fill="%23888" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M7 10l5 5 5-5z"/></svg>');
      background-repeat: no-repeat;
      background-position: right 12px center;
      color: #777;
    }

    textarea.form-control {
      resize: vertical;
      height: 90px;
    }

    .file-upload-wrapper {
      display: flex;
      align-items: center;
      gap: 15px;
    }

    .file-btn {
      display: inline-block;
      padding: 10px 20px;
      border: 1.5px solid #1b7348;
      background-color: #ffffff;
      color: #1b7348;
      border-radius: 10px;
      font-size: 13px;
      font-weight: 600;
      cursor: pointer;
      transition: background-color 0.2s;
    }

    .file-btn:hover {
      background-color: #eaf5ed;
    }

    .file-upload-wrapper input[type="file"] {
      display: none;
    }

    .file-label {
      font-size: 13px;
      color: #777;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      max-width: 200px;
    }

    .file-note {
      font-size: 11px;
      color: #888;
      margin-top: 6px;
    }

    .radio-group {
      display: flex;
      align-items: center;
      gap: 20px;
      margin-top: 6px;
    }

    .radio-label {
      display: flex;
      align-items: center;
      gap: 6px;
      font-size: 13px;
      color: #444;
      cursor: pointer;
    }

    .radio-label input {
      accent-color: #1b7348;
      width: 16px;
      height: 16px;
    }

    .submit-section {
      margin-top: 40px;
      display: flex;
      justify-content: center;
    }

    .btn-submit {
      background-color: #1b7348;
      color: white;
      border: none;
      padding: 14px 40px;
      font-size: 14px;
      font-weight: 600;
      border-radius: 25px;
      cursor: pointer;
      transition: background-color 0.2s;
    }

    .btn-submit:hover {
      background-color: #145937;
    }

    @media (max-width: 768px) {
    body {
        padding: 10px;
        align-items: flex-start;
    }

    .container {
        max-height: 96vh;
        padding: 22px 18px;
    }

    .form-grid {
        grid-template-columns: 1fr;
    }
}

.btn-back {
    display: inline-block;
    margin-bottom: 20px;
    padding: 10px 16px;
    background-color: #e9f0eb;
    color: #347650;
    text-decoration: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    transition: 0.2s;
}

.btn-back:hover {
    background-color: #d8e8dc;
}
  </style>
</head>
<body>



  <div class="container">
  <a href="{{ route('home') }}" class="btn-back">
    ← Kembali ke Beranda
  </a>
  <header>
    <div class="brand">bisaKerja</div>
  </header>

    <div class="steps-indicator">
      <div class="step-bar"></div>
      <div class="step-bar"></div>
      <div class="step-bar active"></div>
    </div>

    <div class="top-meta">
      <!-- <a href="#" class="back-link">Kembali ke pilihan peran</a> -->
      <!-- <div>* Wajib diisi; lainnya opsional</div> -->
    </div>

    <div class="title-section">
      <h1>Biodata Penyedia Pekerjaan</h1>
      <p>Lengkapi biodata untuk melanjutkan pendaftaran.</p>
    </div>

   <form id="providerForm" enctype="multipart/form-data">

    <div class="form-grid">

        <!-- Kolom kiri: Informasi pribadi -->
        <div class="left-col" style="display: flex; flex-direction: column; gap: 20px;">

            <div class="form-group">
                <label for="nama">Nama Lengkap *</label>
                <input
                    type="text"
                    id="nama"
                    name="nama"
                    class="form-control"
                    placeholder="Masukkan nama lengkap"
                    required
                >
            </div>

            <div class="form-group">
                <label for="nohp">No. HP *</label>
                <input
                    type="tel"
                    id="nohp"
                    name="nomor_telepon"
                    class="form-control"
                    placeholder="Contoh: 081234567890"
                    required
                >
            </div>

            <div class="form-group">
                <label for="lokasi">Lokasi Pekerjaan *</label>
                <input
                    type="text"
                    id="lokasi"
                    name="alamat"
                    class="form-control"
                    placeholder="Kelurahan/Kecamatan"
                    required
                >
            </div>

            <div class="form-group">
                <label for="jk">Jenis Kelamin</label>
                <select
                    id="jk"
                    name="jenis_kelamin"
                    class="form-control"
                >
                    <option value="">Pilih jenis kelamin</option>
                    <option value="Laki-laki">Laki-laki</option>
                    <option value="Perempuan">Perempuan</option>
                </select>
            </div>

            <div class="form-group">
                <label for="deskripsi">Deskripsi Diri</label>
                <textarea
                    id="deskripsi"
                    name="deskripsi_diri"
                    class="form-control"
                    placeholder="Ceritakan singkat tentang dirimu"
                ></textarea>
            </div>

        </div>

        <!-- Kolom kanan: Informasi penyedia -->
        <div class="right-col" style="display: flex; flex-direction: column; gap: 20px;">

            <div class="form-group">
                <label for="foto-upload">Foto Profil</label>

                <div class="file-upload-wrapper">
                    <label for="foto-upload" class="file-btn">
                        Unggah Foto
                    </label>

                    <input
                        type="file"
                        id="foto-upload"
                        name="foto_profil"
                        accept=".jpg,.jpeg,.png"
                    >

                    <span id="foto-label" class="file-label">
                        Belum ada file dipilih
                    </span>
                </div>

                <div class="file-note">
                    JPG atau PNG, maksimal 2 MB.
                </div>
            </div>
            <div class="form-group">
                <label>Jenis Penyedia *</label>

                <div class="radio-group">

                    <label class="radio-label">
                        <input
                            type="radio"
                            name="jenis_pemberi"
                            value="Individu"
                            required
                        >
                        Individu
                    </label>

                    <label class="radio-label">
                        <input
                            type="radio"
                            name="jenis_pemberi"
                            value="Rumah tangga"
                        >
                        Rumah Tangga
                    </label>

                    <label class="radio-label">
                        <input
                            type="radio"
                            name="jenis_pemberi"
                            value="Usaha/UMKM"
                        >
                        Usaha/UMKM
                    </label>

                </div>
            </div>
            <div class="form-group">
                <label for="nama-usaha">Nama Usaha</label>

                <input
                    type="text"
                    id="nama-usaha"
                    name="nama_usaha"
                    class="form-control"
                    placeholder="Masukkan nama usaha, jika ada"
                >

                <div class="file-note">
                    Wajib diisi jika memilih jenis penyedia Usaha/UMKM.
                </div>
            </div>
            <div class="form-group">
                <label for="dokumen-upload">Dokumen Verifikasi *</label>
                <div class="file-upload-wrapper">
                    <label for="dokumen-upload" class="file-btn">
                        Unggah Dokumen
                    </label>
                    <input
                        type="file"
                        id="dokumen-upload"
                        name="dokumen_verifikasi"
                        accept=".jpg,.jpeg,.png,.pdf"
                        required
                    >
                    <span id="dokumen-label" class="file-label">
                        Belum ada file dipilih
                    </span>
                </div>
                <div class="file-note">
                    JPG, PNG, atau PDF, maksimal 5 MB.
                </div>
            </div>
        </div>
    </div>
    <div class="submit-section">
        <button type="submit" class="btn-submit">
            Selesaikan Pendaftaran
        </button>
    </div>
</form>

  </div>

  <script>
 
const fotoUpload = document.getElementById('foto-upload');
const fotoLabel = document.getElementById('foto-label');

const dokumenUpload = document.getElementById('dokumen-upload');
const dokumenLabel = document.getElementById('dokumen-label');

const providerForm = document.getElementById('providerForm');

// Menampilkan nama file foto
fotoUpload.addEventListener('change', function () {
    fotoLabel.textContent = this.files[0]
        ? this.files[0].name
        : 'Belum ada file dipilih';

    fotoLabel.style.color = '#1b7348';
});

// Menampilkan nama file dokumen
dokumenUpload.addEventListener('change', function () {
    dokumenLabel.textContent = this.files[0]
        ? this.files[0].name
        : 'Belum ada file dipilih';

    dokumenLabel.style.color = '#1b7348';
});

// Mengirim biodata ke backend
providerForm.addEventListener('submit', async function (event) {
    event.preventDefault();

    const token = sessionStorage.getItem('auth_token');

    if (!token) {
        alert('Sesi login tidak ditemukan. Silakan login kembali.');
        window.location.href = '/login';
        return;
    }

    const submitButton = providerForm.querySelector(
        'button[type="submit"]'
    );

    const formData = new FormData(providerForm);

    submitButton.disabled = true;
    submitButton.textContent = 'Menyimpan...';

    try {
        const response = await fetch('/api/provider/profile', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Authorization': `Bearer ${token}`
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
                alert(result.message || 'Gagal menyimpan biodata.');
            }

            return;
        }

        // Perbarui status pengguna di sessionStorage
        const user = JSON.parse(
            sessionStorage.getItem('user') || '{}'
        );

        user.nama = result.user.nama;
        user.status_akun = result.user.status_akun;

        sessionStorage.setItem('user', JSON.stringify(user));

        alert(result.message);

        window.location.href = '/home';

    } catch (error) {
        console.error(error);
        alert('Terjadi kesalahan koneksi ke server.');

    } finally {
        submitButton.disabled = false;
        submitButton.textContent = 'Selesaikan pendaftaran';
    }
});

  </script>

</body>
</html>