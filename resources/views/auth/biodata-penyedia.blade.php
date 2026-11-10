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
      background-color: #f3f9f4;
      color: #333;
      padding-bottom: 60px;
    }

    header {
      background-color: #ffffff;
      padding: 20px 40px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }

    .brand {
      color: #1b7348;
      font-size: 24px;
      font-weight: 700;
    }

    .container {
      max-width: 900px;
      margin: 20px auto;
      padding: 0 20px;
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
      .form-grid {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body>

  <header>
    <div class="brand">bisaKerja</div>
  </header>

  <div class="container">

    <div class="steps-indicator">
      <div class="step-bar"></div>
      <div class="step-bar"></div>
      <div class="step-bar active"></div>
    </div>

    <div class="top-meta">
      <a href="{{ route('register.role') }}" class="back-link">Kembali ke pilihan peran</a>
      <div>* Wajib diisi; lainnya opsional</div>
    </div>

    <div class="title-section">
      <h1>Biodata Penyedia Pekerjaan</h1>
      <p>Lengkapi biodata untuk melanjutkan pendaftaran.</p>
    </div>

    <form action="#" method="POST">
      <div class="form-grid">

        <div class="left-col" style="display: flex; flex-direction: column; gap: 20px;">
          
          <div class="form-group">
            <label for="nama">Nama*</label>
            <input type="text" id="nama" class="form-control" placeholder="Masukkan nama lengkap" required>
          </div>

          <div class="form-group">
            <label for="nohp">No. HP*</label>
            <input type="tel" id="nohp" class="form-control" placeholder="Contoh: 081234567890" required>
          </div>

          <div class="form-group">
            <label for="lokasi">Lokasi pekerjaan*</label>
            <input type="text" id="lokasi" class="form-control" placeholder="Kelurahan/Kecamatan" required>
          </div>

          <div class="form-group">
            <label for="jk">Jenis kelamin</label>
            <select id="jk" class="form-control">
              <option value="" disabled selected>Pilih jenis kelamin</option>
              <option value="Laki-laki">Laki-laki</option>
              <option value="Perempuan">Perempuan</option>
            </select>
          </div>

          <div class="form-group">
            <label for="deskripsi">Deskripsi diri</label>
            <textarea id="deskripsi" class="form-control" placeholder="Ceritakan singkat tentang dirimu"></textarea>
          </div>

        </div>

        <div class="right-col" style="display: flex; flex-direction: column; gap: 20px;">

          <div class="form-group">
            <label>Foto profil</label>
            <div class="file-upload-wrapper">
              <label for="foto-upload" class="file-btn">Unggah foto</label>
              <input type="file" id="foto-upload" accept="image/png, image/jpeg">
              <span id="foto-label" class="file-label">Belum ada file dipilih</span>
            </div>
            <div class="file-note">JPG atau PNG, maks. 2 MB</div>
          </div>

          <div class="form-group">
            <label>Jenis penyedia*</label>
            <div class="radio-group">
              <label class="radio-label">
                <input type="radio" name="jenis_penyedia" value="Individu" required>
                Individu
              </label>
              <label class="radio-label">
                <input type="radio" name="jenis_penyedia" value="Rumah tangga">
                Rumah tangga
              </label>
              <label class="radio-label">
                <input type="radio" name="jenis_penyedia" value="Usaha/UMKM">
                Usaha/UMKM
              </label>
            </div>
          </div>

          <div class="form-group">
            <label for="nama-usaha">Nama usaha</label>
            <input type="text" id="nama-usaha" class="form-control" placeholder="Masukkan nama usaha, jika ada">
          </div>

          <div class="form-group">
            <label>Dokumen verifikasi*</label>
            <div class="file-upload-wrapper">
              <label for="dokumen-upload" class="file-btn">Unggah dokumen</label>
              <input type="file" id="dokumen-upload" accept="image/png, image/jpeg, application/pdf" required>
              <span id="dokumen-label" class="file-label">Belum ada file dipilih</span>
            </div>
            <div class="file-note">JPG, PNG, atau PDF, maks. 5 MB</div>
          </div>

        </div>

      </div>

      <div class="submit-section">
        <button type="submit" class="btn-submit">Selesaikan pendaftaran</button>
      </div>
    </form>

  </div>

  <script>
    const fotoUpload = document.getElementById('foto-upload');
    const fotoLabel = document.getElementById('foto-label');

    fotoUpload.addEventListener('change', function() {
      if (this.files && this.files[0]) {
        fotoLabel.textContent = this.files[0].name;
        fotoLabel.style.color = '#1b7348';
      } else {
        fotoLabel.textContent = 'Belum ada file dipilih';
        fotoLabel.style.color = '#777';
      }
    });

    const dokumenUpload = document.getElementById('dokumen-upload');
    const dokumenLabel = document.getElementById('dokumen-label');

    dokumenUpload.addEventListener('change', function() {
      if (this.files && this.files[0]) {
        dokumenLabel.textContent = this.files[0].name;
        dokumenLabel.style.color = '#1b7348';
      } else {
        dokumenLabel.textContent = 'Belum ada file dipilih';
        dokumenLabel.style.color = '#777';
      }
    });
  </script>

</body>
</html>