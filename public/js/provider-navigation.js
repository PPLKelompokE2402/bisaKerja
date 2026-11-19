(() => {
    const requested = new URLSearchParams(window.location.search).get('view') === 'pelamar-saya';
    const token = sessionStorage.getItem('auth_token');
    const notice = document.getElementById('provider-navigation-notice');
    const showNotice = (message) => {
         if (!notice) return;
    notice.textContent = message;
    notice.hidden = false;
    };

    if (!token) {
        if (requested) window.location.assign('/login');
        return;
    }

    const headers = { Authorization: `Bearer ${token}`, Accept: 'application/json' };
    const loadPage = async () => {
        showNotice('Memuat halaman Pelamar Saya...');
        try {
            const response = await fetch('/api/penyedia/pelamar-saya/halaman', { headers, cache: 'no-store' });
            if (response.status === 401) {
                window.location.assign('/login');
                return;
            }
            if (!response.ok) {
                showNotice(response.status === 403
                    ? 'Halaman Pelamar Saya hanya dapat diakses oleh Penyedia Pekerjaan.'
                    : 'Halaman Pelamar Saya gagal dimuat. Silakan muat ulang halaman.');
                return;
            }
            // HTML berasal dari route Blade yang dilindungi Sanctum dan middleware role.
            const html = await response.text();
            document.open();
            document.write(html);
            document.close();
        } catch {
            showNotice('Tidak dapat terhubung ke server. Silakan muat ulang halaman.');
        }
    };

    if (requested) {
        loadPage();
        return;
    }

    fetch('/api/user', { headers, cache: 'no-store' })
        .then((response) => response.ok ? response.json() : null)
        .then((result) => {
            if (result?.user?.role !== 'penyedia_pekerjaan') return;
            document.body.dataset.providerNavigation = '';
            document.getElementById('provider-navigation').hidden = false;
        })
        .catch(() => showNotice('Navigasi penyedia belum dapat dimuat. Silakan muat ulang halaman.'));
})();
