(() => {
    const root = document.body;
    const token = sessionStorage.getItem('auth_token');
    const element = (id) => document.getElementById(`seeker-${id}`);
    const params = new URLSearchParams(window.location.search);
    const positivePage = (value) => Math.max(1, Number.parseInt(value, 10) || 1);
    const pages = { applications: positivePage(params.get('applications_page')), history: positivePage(params.get('history_page')) };
    const lastPages = { applications: 1, history: 1 };
    const keys = Object.keys(pages);
    const statuses = ['menunggu', 'diterima', 'ditolak', 'selesai'];
    const numberFormat = new Intl.NumberFormat('id-ID', { maximumFractionDigits: 2 });
    const dateFormat = new Intl.DateTimeFormat('id-ID', { day: 'numeric', month: 'long', year: 'numeric', timeZone: 'Asia/Jakarta' });
    let request;
    let loadingTimer;
    let loaded = false;
    let disposed = false;

    const dateLabel = (value) => {
        if (!value) return 'Tanggal belum tersedia';
        const date = new Date(value);
        return Number.isNaN(date.getTime()) ? 'Tanggal belum tersedia' : dateFormat.format(date);
    };
    const wageLabel = (value) => {
        if (value === null || value === undefined || value === '' || !Number.isFinite(Number(value))) return 'Upah belum tersedia';
        return `Rp${numberFormat.format(Number(value))}`;
    };
    const cell = (row, text, className) => {
        const td = document.createElement('td');
        td.textContent = text;
        if (className) td.className = className;
        row.append(td);
        return td;
    };
    const renderTable = (key, result) => {
        const body = element(`${key}-rows`);
        body.replaceChildren();
        for (const application of result.data) {
            const row = document.createElement('tr');
            const job = cell(row, '');
            const name = document.createElement('div');
            name.className = 'seeker-job-name';
            name.textContent = application.pekerjaan || 'Informasi pekerjaan belum tersedia';
            const location = document.createElement('div');
            location.className = 'seeker-location';
            location.textContent = application.lokasi || 'Lokasi belum tersedia';
            job.append(name, location);
            cell(row, wageLabel(application.upah), 'seeker-wage');
            cell(row, dateLabel(application.tanggal_melamar), 'seeker-date');
            const status = document.createElement('span');
            status.className = 'seeker-badge';
            if (statuses.includes(application.status)) status.classList.add(`seeker-badge-${application.status}`);
            status.textContent = application.status_label || 'Status belum tersedia';
            cell(row, '').append(status);
            body.append(row);
        }
        element(`${key}-empty`).hidden = result.data.length !== 0;
        lastPages[key] = result.meta.last_page;
        pages[key] = result.meta.current_page;
        element(`${key}-pagination`).hidden = result.meta.last_page <= 1;
        element(`${key}-previous`).disabled = pages[key] <= 1;
        element(`${key}-next`).disabled = pages[key] >= lastPages[key];
        element(`${key}-page-info`).textContent = `Halaman ${pages[key]} dari ${lastPages[key]}`;
    };
    const updateAddress = () => {
        const address = new URL(window.location.href);
        for (const key of keys) {
            if (pages[key] > 1) address.searchParams.set(`${key}_page`, pages[key]);
            else address.searchParams.delete(`${key}_page`);
        }
        window.history.replaceState(null, '', address);
    };
    const load = async () => {
        if (disposed) return;
        request?.abort();
        const current = new AbortController();
        request = current;
        element('loading').textContent = loaded ? 'Memperbarui status lamaran...' : 'Memuat status lamaran...';
        window.clearTimeout(loadingTimer);
        loadingTimer = window.setTimeout(() => {
            if (request === current) element('loading').hidden = false;
        }, 300);
        element('error').hidden = true;
        for (const key of keys) element(key).setAttribute('aria-busy', 'true');
        try {
            const query = new URLSearchParams({ applications_page: pages.applications, history_page: pages.history });
            let response;
            try {
                const headers = {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                };
                if (token) {
                    headers.Authorization = `Bearer ${token}`;
                }
                response = await fetch(`${root.dataset.endpoint}?${query}`, {
                    headers,
                    credentials: 'same-origin',
                    signal: current.signal,
                    cache: 'no-store',
                });
            } catch (error) {
                if (error.name === 'AbortError') throw error;
                throw new Error('Tidak dapat terhubung ke server. Silakan coba lagi.');
            }
            if (response.status === 401) {
                window.location.assign(root.dataset.login);
                return;
            }
            if (!response.ok) {
                if (response.status === 403) throw new Error('Halaman ini hanya dapat diakses oleh Pencari Kerja.');
                if (response.status === 422) throw new Error('Nomor halaman tidak valid. Silakan buka kembali menu Lamaran Saya.');
                throw new Error('Status lamaran gagal dimuat. Silakan coba lagi.');
            }
            let result;
            try {
                result = await response.json();
            } catch {
                throw new Error('Respons data belum dapat dibaca. Silakan coba lagi.');
            }
            if (current.signal.aborted) return;
            for (const key of keys) renderTable(key, result[key]);
            loaded = true;
            updateAddress();
        } catch (error) {
            if (error.name === 'AbortError') return;
            element('error').querySelector('p').textContent = error.message || 'Data gagal dimuat. Silakan coba lagi.';
            element('error').hidden = false;
            if (!loaded) {
                for (const key of keys) {
                    const row = document.createElement('tr');
                    const td = cell(row, 'Data belum dapat dimuat.', 'seeker-state');
                    td.colSpan = 4;
                    element(`${key}-rows`).replaceChildren(row);
                }
            }
        } finally {
            if (request === current) {
                request = null;
                window.clearTimeout(loadingTimer);
                element('loading').hidden = true;
                for (const key of keys) element(key).setAttribute('aria-busy', 'false');
            }
        }
    };
    for (const key of keys) {
        element(`${key}-previous`).addEventListener('click', () => {
            if (pages[key] > 1) { pages[key]--; load(); }
        });
        element(`${key}-next`).addEventListener('click', () => {
            if (pages[key] < lastPages[key]) { pages[key]++; load(); }
        });
    }
    element('retry').addEventListener('click', load);
    const refreshIfVisible = () => {
        if (!disposed && document.visibilityState === 'visible' && !request) load();
    };
    document.addEventListener('visibilitychange', refreshIfVisible);
    window.addEventListener('focus', refreshIfVisible);
    let timer = window.setInterval(refreshIfVisible, 30000);
    window.addEventListener('pagehide', () => {
        disposed = true;
        request?.abort();
        window.clearInterval(timer);
    });
    window.addEventListener('pageshow', (event) => {
        if (event.persisted) {
            disposed = false;
            timer = window.setInterval(refreshIfVisible, 30000);
            load();
        }
    });
    load();
})();
