<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lamaran Saya - bisaKerja</title>
    <link rel="stylesheet" href="{{ asset('css/seeker-applications.css') }}">
    <script src="{{ asset('js/seeker-applications-entry.js') }}" defer></script>
</head>
<body class="seeker-applications" data-page="{{ route('seeker.applications.page') }}" data-login="{{ route('login') }}">
    <header class="seeker-header">
        <a class="seeker-logo" href="{{ route('home') }}">bisaKerja</a>
        @include('components.logout-form', ['buttonClass' => 'seeker-button'])
    </header>
    <main class="seeker-entry">
        <p id="seeker-entry-message" role="status">Memuat halaman Lamaran Saya...</p>
        <button id="seeker-entry-retry" class="seeker-button" type="button" hidden>Coba lagi</button>
        <p><a href="{{ route('home') }}">Kembali ke Beranda</a></p>
    </main>
</body>
</html>
