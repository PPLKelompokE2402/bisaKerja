(() => {
    const root = document.body;
    const token = sessionStorage.getItem('auth_token');
    const message = document.getElementById('seeker-entry-message');
    const retry = document.getElementById('seeker-entry-retry');
    if (!token) {
        window.location.assign(root.dataset.login);
        return;
    }
    const load = async () => {
        retry.hidden = true;
        message.textContent = 'Memuat halaman Lamaran Saya...';
        try {
            const response = await fetch(root.dataset.page, {
                headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' }, cache: 'no-store',
            });
            if (response.status === 401) {
                window.location.assign(root.dataset.login);
                return;
            }
            if (!response.ok) {
                message.textContent = response.status === 403
                    ? 'Halaman Lamaran Saya hanya dapat diakses oleh Pencari Kerja.'
                    : 'Halaman Lamaran Saya gagal dimuat. Silakan coba lagi.';
                retry.hidden = response.status === 403;
                return;
            }
            const html = await response.text();
            document.open();
            document.write(html);
            document.close();
        } catch {
            message.textContent = 'Tidak dapat terhubung ke server. Silakan coba lagi.';
            retry.hidden = false;
        }
    };
    retry.addEventListener('click', load);
    load();
})();
