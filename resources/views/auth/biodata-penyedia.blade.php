<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BisaKerja - {{ $editing ? 'Edit Profil Penyedia' : 'Biodata Penyedia Pekerjaan' }}</title>
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
        <h1>{{ $editing ? 'Edit Profil Penyedia' : 'Biodata Penyedia Pekerjaan' }}</h1>
        <p>{{ $editing ? 'Perbarui informasi profil Anda.' : 'Lengkapi biodata untuk menyelesaikan pendaftaran.' }}</p>
    </div>

      @if ($errors->any())
        <div role="alert" style="margin-bottom: 20px; color: #a12622;">
          <ul>
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

   <form id="providerForm" action="{{ route('provider.profile.update') }}" method="POST" enctype="multipart/form-data">
      @csrf
      @method('PUT')

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
                    value="{{ old('nama', $user->nama) }}"
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
                    value="{{ old('nomor_telepon', $user->nomor_telepon) }}"
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
                    value="{{ old('alamat', $user->alamat) }}"
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
                    <option value="Laki-laki" @selected(strtolower((string) old('jenis_kelamin', $user->jenis_kelamin)) === 'laki-laki')>Laki-laki</option>
                    <option value="Perempuan" @selected(strtolower((string) old('jenis_kelamin', $user->jenis_kelamin)) === 'perempuan')>Perempuan</option>
                </select>
            </div>

            <div class="form-group">
                <label for="deskripsi">Deskripsi Diri</label>
                <textarea
                    id="deskripsi"
                    name="deskripsi_diri"
                    class="form-control"
                    placeholder="Ceritakan singkat tentang dirimu"
                >{{ old('deskripsi_diri', $user->deskripsi_diri) }}</textarea>
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
                        {{ $user->foto_profil ? 'Foto saat ini tersimpan' : 'Belum ada file dipilih' }}
                    </span>
                </div>
                @if ($user->foto_profil)
                    <img src="{{ asset('storage/'.$user->foto_profil) }}" alt="Foto profil saat ini" style="width: 72px; height: 72px; margin-top: 10px; border-radius: 50%; object-fit: cover;">
                @endif

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
                            @checked(old('jenis_pemberi', $user->jenis_pemberi) === 'Individu')
                            required
                        >
                        Individu
                    </label>

                    <label class="radio-label">
                        <input
                            type="radio"
                            name="jenis_pemberi"
                            value="Rumah tangga"
                            @checked(old('jenis_pemberi', $user->jenis_pemberi) === 'Rumah tangga')
                        >
                        Rumah Tangga
                    </label>

                    <label class="radio-label">
                        <input
                            type="radio"
                            name="jenis_pemberi"
                            value="Usaha/UMKM"
                            @checked(old('jenis_pemberi', $user->jenis_pemberi) === 'Usaha/UMKM')
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
                    value="{{ old('nama_usaha', $user->nama_usaha) }}"
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
                        @required(blank($user->dokumen_verifikasi))
                    >
                    <span id="dokumen-label" class="file-label">
                        {{ $user->dokumen_verifikasi ? 'Dokumen verifikasi saat ini tersimpan' : 'Belum ada file dipilih' }}
                    </span>
                </div>
                @if ($user->dokumen_verifikasi)
                    <a href="{{ asset('storage/'.$user->dokumen_verifikasi) }}" target="_blank" rel="noopener noreferrer" class="file-note">Lihat dokumen verifikasi saat ini</a>
                @endif
                <div class="file-note">
                    JPG, PNG, atau PDF, maksimal 5 MB.
                </div>
            </div>
        </div>
    </div>
    <div class="submit-section">
        <button type="submit" class="btn-submit">
            {{ $editing ? 'Simpan Perubahan' : 'Selesaikan Pendaftaran' }}
        </button>
    </div>
</form>

  </div>

  <script>
    const fotoInput = document.getElementById('foto-upload');
    const fotoLabel = document.getElementById('foto-label');
    const dokumenInput = document.getElementById('dokumen-upload');
    const dokumenLabel = document.getElementById('dokumen-label');

    fotoInput.addEventListener('change', function () {
        const file = this.files[0];

        if (!file) {
              fotoLabel.textContent = '{{ $user->foto_profil ? "Foto saat ini tersimpan" : "Belum ada file dipilih" }}';
            return;
        }

        if (file.size > 2 * 1024 * 1024) {
            alert('Ukuran foto maksimal 2 MB.');
            this.value = '';
            fotoLabel.textContent = 'Belum ada file dipilih';
            return;
        }

        fotoLabel.textContent = file.name;
    });

    dokumenInput.addEventListener('change', function () {
        const file = this.files[0];

        if (!file) {
            dokumenLabel.textContent = '{{ $user->dokumen_verifikasi ? "Dokumen verifikasi saat ini tersimpan" : "Belum ada file dipilih" }}';
            return;
        }

        if (file.size > 5 * 1024 * 1024) {
            alert('Ukuran dokumen maksimal 5 MB.');
            this.value = '';
            dokumenLabel.textContent = 'Belum ada file dipilih';
            return;
        }

        dokumenLabel.textContent = file.name;
    });
</script>


</body>
</html>