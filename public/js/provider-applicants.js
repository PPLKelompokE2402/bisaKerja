(() => {
    const root = document.querySelector('[data-applicants]');
    const endpoint = root.dataset.endpoint;
    const token = sessionStorage.getItem('auth_token');
    if (!token) {
        window.location.assign(root.dataset.login);
        return;
    }

    const element = (id) => document.getElementById(`applicants-${id}`);
    const form = element('filters');
    const dialog = element('detail');
    const headers = { Authorization: `Bearer ${token}`, Accept: 'application/json' };
    const params = new URLSearchParams(window.location.search);
    const validStatuses = ['menunggu', 'diterima', 'ditolak'];
    element('search').value = params.get('search') || '';
    element('status').value = validStatuses.includes(params.get('status')) ? params.get('status') : '';
    let search = element('search').value;
    let status = element('status').value;
    let page = Math.max(1, Number.parseInt(params.get('page'), 10) || 1);
    let listRequest;
    let detailRequest;
    let selectedId;
    let cvExtension = 'pdf';
    let photoUrl;

    const dateLabel = (value) => value
        ? new Intl.DateTimeFormat('id-ID', { day: 'numeric', month: 'long', year: 'numeric', timeZone: 'Asia/Jakarta' }).format(new Date(value))
        : 'Tidak tersedia';
    const badge = (application) => {
        const span = document.createElement('span');
        span.classList.add('applicants-badge');
        if ([...validStatuses, 'selesai'].includes(application.status)) span.classList.add(`applicants-badge-${application.status}`);
        span.textContent = application.status_label;
        return span;
    };
    const api = async (url, signal, file = false) => {
        let response;
        try {
            response = await fetch(url, { headers, signal, cache: 'no-store' });
        } catch (error) {
            if (error.name === 'AbortError') throw error;
            throw new Error('Tidak dapat terhubung ke server. Silakan coba lagi.');
        }
        if (response.status === 401) {
            window.location.assign(root.dataset.login);
            throw new Error('Sesi berakhir. Silakan login kembali.');
        }
        if (!response.ok) {
            if (response.status === 403) throw new Error('Anda tidak memiliki izin untuk mengakses data ini.');
            if (response.status === 404) throw new Error('Data atau berkas pelamar tidak ditemukan.');
            if (response.status === 422) throw new Error('Pencarian atau filter tidak valid. Periksa isian Anda.');
            throw new Error('Data gagal dimuat. Silakan coba lagi.');
        }
        return file ? response.blob() : response.json();
    };
    const cell = (row, content) => {
        const td = document.createElement('td');
        if (content instanceof Node) td.append(content);
        else td.textContent = content;
        row.append(td);
    };
    const updateAddress = () => {
        const address = new URL(window.location.href);
        address.searchParams.set('view', 'pelamar-saya');
        for (const [key, value] of Object.entries({ search, status, page: page > 1 ? page : '' })) {
            if (value) address.searchParams.set(key, value);
            else address.searchParams.delete(key);
        }
        window.history.replaceState(null, '', address);
    };
    const load = async () => {
        listRequest?.abort();
        listRequest = new AbortController();
        const request = listRequest;
        element('loading').hidden = false;
        element('results').hidden = true;
        element('results').setAttribute('aria-busy', 'true');
        element('error').hidden = true;
        updateAddress();
        try {
            const query = new URLSearchParams({ search, status, page });
            const result = await api(`${endpoint}?${query}`, request.signal);
            for (const count of document.querySelectorAll('[data-count]')) {
                count.textContent = new Intl.NumberFormat('id-ID').format(result.summary[count.dataset.count]);
            }
            element('rows').replaceChildren();
            for (const application of result.data) {
                const row = document.createElement('tr');
                cell(row, application.nama_pelamar);
                cell(row, application.pekerjaan);
                cell(row, dateLabel(application.tanggal_melamar));
                cell(row, badge(application));
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'applicants-button applicants-button-secondary';
                button.textContent = 'Detail';
                button.setAttribute('aria-label', `Detail ${application.nama_pelamar} untuk ${application.pekerjaan}`);
                button.addEventListener('click', () => showDetail(application.id));
                cell(row, button);
                element('rows').append(row);
            }
            const empty = element('empty');
            empty.hidden = result.data.length !== 0;
            empty.querySelector('h2').textContent = search || status ? 'Tidak ada pelamar yang sesuai' : 'Belum ada pelamar';
            empty.querySelector('p').textContent = search || status
                ? 'Coba kata kunci lain atau reset filter pencarian.'
                : 'Lamaran yang masuk ke lowongan Anda akan tampil di sini.';
            element('page-info').textContent = result.meta.total
                ? `Menampilkan ${result.meta.from ?? 0}–${result.meta.to ?? 0} dari ${result.meta.total} lamaran`
                : '0 lamaran';
            document.querySelector('.applicants-pagination').hidden = result.meta.total === 0;
            element('previous').disabled = page <= 1;
            element('next').disabled = page >= result.meta.last_page;
            element('results').hidden = false;
        } catch (error) {
            if (error.name === 'AbortError') return;
            element('error').querySelector('p').textContent = error.message || 'Koneksi ke server gagal. Silakan coba lagi.';
            element('error').hidden = false;
            for (const count of document.querySelectorAll('[data-count]')) count.textContent = '—';
        } finally {
            if (request === listRequest) {
                element('loading').hidden = true;
                element('results').setAttribute('aria-busy', 'false');
            }
        }
    };

    const detailField = (label, value) => {
        if (value === null || value === undefined || value === '') return;
        const dt = document.createElement('dt');
        const dd = document.createElement('dd');
        dt.textContent = label;
        if (value instanceof Node) dd.append(value);
        else dd.textContent = value;
        element('detail-fields').append(dt, dd);
    };
    const clearPhoto = () => {
        if (photoUrl) URL.revokeObjectURL(photoUrl);
        photoUrl = null;
        element('photo').hidden = true;
        element('photo').removeAttribute('src');
    };
    const showDetail = async (id) => {
        detailRequest?.abort();
        detailRequest = new AbortController();
        const request = detailRequest;
        selectedId = id;
        cvExtension = 'pdf';
        clearPhoto();
        element('detail-content').hidden = true;
        element('detail-loading').hidden = false;
        element('detail-error').hidden = true;
        element('cv').hidden = true;
        if (!dialog.open) dialog.showModal();
        try {
            const { data } = await api(`${endpoint}/${id}`, request.signal);
            if (request.signal.aborted) return;
            element('detail-fields').replaceChildren();
            const applicant = data.pelamar;
            cvExtension = applicant.cv_extension || 'pdf';
            detailField('Nama lengkap', applicant.nama);
            detailField('Tanggal lahir', applicant.tanggal_lahir);
            detailField('Umur', applicant.umur === null ? null : `${applicant.umur} tahun`);
            detailField('Tempat tinggal', applicant.alamat);
            detailField('Email', applicant.email);
            detailField('Nomor telepon', applicant.nomor_telepon);
            detailField('Pekerjaan', data.pekerjaan);
            detailField('Tanggal melamar', dateLabel(data.tanggal_melamar));
            detailField('Status', badge(data));
            detailField('Pesan lamaran', data.pesan_lamaran);
            detailField('Status pekerjaan', data.status_pekerjaan === 'selesai'
                ? badge({ status: 'selesai', status_label: 'Selesai' })
                : data.status_pekerjaan);
            detailField('Tentang pelamar', applicant.deskripsi_diri);
            detailField('Keahlian', applicant.keahlian);
            detailField('Pengalaman kerja', applicant.pengalaman_kerja);
            element('cv').hidden = !applicant.has_cv;
            element('detail-content').hidden = false;
            element('detail-loading').hidden = true;
            if (applicant.has_photo) {
                const photo = await api(`${endpoint}/${id}/berkas/foto`, request.signal, true);
                if (!request.signal.aborted) {
                    photoUrl = URL.createObjectURL(photo);
                    element('photo').src = photoUrl;
                    element('photo').hidden = false;
                }
            }
        } catch (error) {
            if (error.name === 'AbortError') return;
            element('detail-error').textContent = error.message || 'Detail pelamar gagal dimuat. Silakan tutup dan coba lagi.';
            element('detail-error').hidden = false;
        } finally {
            if (request === detailRequest) element('detail-loading').hidden = true;
        }
    };
    element('cv').addEventListener('click', async () => {
        const button = element('cv');
        button.disabled = true;
        button.textContent = 'Mengunduh CV...';
        element('detail-error').hidden = true;
        try {
            const blob = await api(`${endpoint}/${selectedId}/berkas/cv`, detailRequest.signal, true);
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = `CV-pelamar.${cvExtension}`;
            document.body.append(link);
            link.click();
            link.remove();
            setTimeout(() => URL.revokeObjectURL(url), 30000);
        } catch (error) {
            if (error.name !== 'AbortError') {
                element('detail-error').textContent = error.message || 'CV gagal diunduh. Silakan coba lagi.';
                element('detail-error').hidden = false;
            }
        } finally {
            button.disabled = false;
            button.textContent = 'Unduh CV';
        }
    });
    element('close').addEventListener('click', () => dialog.close());
    dialog.addEventListener('close', () => { detailRequest?.abort(); clearPhoto(); });
    form.addEventListener('submit', (event) => {
        event.preventDefault();
        search = element('search').value.trim();
        status = element('status').value;
        page = 1;
        load();
    });
    form.addEventListener('reset', () => {
        search = '';
        status = '';
        page = 1;
        load();
    });
    element('retry').addEventListener('click', load);
    element('previous').addEventListener('click', () => { if (page > 1) { page--; load(); } });
    element('next').addEventListener('click', () => { page++; load(); });
    load();
})();
