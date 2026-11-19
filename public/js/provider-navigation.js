(() => {
    const requested = new URLSearchParams(window.location.search).get('view') === 'pelamar-saya';
    const token = sessionStorage.getItem('auth_token');
    const notice = document.getElementById('provider-navigation-notice');
    const loginUrl = document.body.dataset.login;

    if (!requested) return;

    const showNotice = (message) => {
        if (!notice) return;
        notice.textContent = message;
        notice.hidden = false;
    };

    if (!token) {
        window.location.assign(loginUrl);
        return;
    }

    const loadPage = async () => {
        showNotice('Memuat halaman Pelamar Saya...');

        try {
            const response = await fetch('/api/penyedia/pelamar-saya/halaman', {
                headers: {
                    Authorization: `Bearer ${token}`,
                    Accept: 'application/json',
                },
                cache: 'no-store',
            });

            if (response.status === 401) {
                window.location.assign(loginUrl);
                return;
            }

            if (!response.ok) {
                showNotice(response.status === 403
                    ? 'Halaman Pelamar Saya hanya dapat diakses oleh Penyedia Pekerjaan.'
                    : 'Halaman Pelamar Saya gagal dimuat. Silakan muat ulang halaman.');
                return;
            }

            const html = await response.text();
            document.open();
            document.write(html);
            document.close();
        } catch {
            showNotice('Tidak dapat terhubung ke server. Silakan muat ulang halaman.');
        }
    };

    loadPage();
})();
