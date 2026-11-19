(() => {
    const container = document.getElementById('seeker-applications-link');
    const token = sessionStorage.getItem('auth_token');
    if (!container || !token) return;
    fetch('/api/user', { headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' }, cache: 'no-store' })
        .then((response) => response.ok ? response.json() : null)
        .then((result) => { container.hidden = result?.user?.role !== 'pencari_kerja'; })
        .catch(() => { container.hidden = true; });
})();
