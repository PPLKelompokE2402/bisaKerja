<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pilih Role - bisaKerja</title>
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    body {
      background-color: #f3f9f4;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
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
    .main-content {
      flex: 1;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 40px 20px;
    }
    /* Indikator Step Bar */
    .steps-indicator {
      display: flex;
      justify-content: center;
      gap: 10px;
      margin-bottom: 25px;
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
    .title-section {
      text-align: center;
      margin-bottom: 40px;
    }
    .title-section h1 {
      color: #2b3a30;
      font-size: 26px;
      font-weight: 700;
      margin-bottom: 8px;
    }
    .title-section p {
      color: #777;
      font-size: 14px;
    }
    .role-container {
      display: flex;
      gap: 30px;
      max-width: 800px;
      width: 100%;
      justify-content: center;
    }
    .role-card {
      background: #ffffff;
      border-radius: 16px;
      padding: 40px 30px;
      flex: 1;
      max-width: 360px;
      text-align: center;
      box-shadow: 0 4px 15px rgba(0,0,0,0.03);
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      border: 1px solid #eef3ef;
    }
    .role-card h3 {
      color: #2b3a30;
      font-size: 18px;
      margin-bottom: 12px;
      font-weight: 700;
    }
    .role-card p {
      color: #666;
      font-size: 13px;
      line-height: 1.5;
      margin-bottom: 30px;
      min-height: 40px;
    }
    .btn-role {
      background-color: #1b7348;
      color: #ffffff;
      padding: 12px 24px;
      border-radius: 25px;
      text-decoration: none;
      font-weight: 600;
      font-size: 13px;
      display: inline-block;
      transition: background-color 0.2s;
    }
    .btn-role:hover {
      background-color: #145937;
    }
    .back-home {
      margin-top: 40px;
      color: #777;
      text-decoration: none;
      font-size: 13px;
    }
    .back-home:hover {
      color: #1b7348;
    }
    @media (max-width: 650px) {
      .role-container {
        flex-direction: column;
        align-items: center;
      }
      .role-card {
        width: 100%;
      }
    }
  </style>
</head>
<body>

  <header>
    <div class="brand">bisaKerja</div>
  </header>

  <div class="main-content">

    <div class="steps-indicator">
      <div class="step-bar"></div>
      <div class="step-bar active"></div>
      <div class="step-bar"></div>
    </div>

    <div class="title-section">
      <h1>Daftar sebagai apa?</h1>
      <p>Pilih peran yang sesuai untuk melanjutkan pendaftaran.</p>
    </div>

    <div class="role-container">

      <div class="role-card">
        <div>
          <h3>Pencari Kerja</h3>
          <p>Saya ingin mencari dan melamar pekerjaan harian di sekitar saya.</p>
        </div>
        <a href="#" class="btn-role">Daftar sebagai Pencari Kerja</a>
      </div>

      <div class="role-card">
        <div>
          <h3>Penyedia Kerja</h3>
          <p>Saya ingin memasang lowongan dan mencari orang untuk membantu pekerjaan.</p>
        </div>
        <a href="{{ route('provider.create') }}" class="btn-role">Daftar sebagai Penyedia Kerja</a>
      </div>
    </div>

    <a href="/" class="back-home">← Kembali ke halaman utama</a>
  </div>

</body>
</html>