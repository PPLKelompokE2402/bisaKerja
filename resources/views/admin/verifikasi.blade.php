
@extends('admin.layouts.app')

@section('title', 'Verifikasi Akun')

@push('styles')
<style>
    .verification-summary {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 15px;
        margin-bottom: 25px;
    }

    .summary-card {
        background: white;
        padding: 18px;
        border: 1px solid #e8eee9;
        border-radius: 9px;
    }

    .summary-card p {
        color: #87958b;
        font-size: 11px;
        margin-bottom: 12px;
    }

    .summary-card h2 {
        color: #347650;
        font-size: 23px;
    }

    .table-container {
        background: white;
        border: 1px solid #e8eee9;
        border-radius: 10px;
        overflow: hidden;
    }

    .table-header {
        padding: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
    }

    .table-header h3 {
        font-size: 15px;
        color: #285d43;
    }

    .table-header p {
        font-size: 11px;
        color: #87958b;
        margin-top: 5px;
    }

    .refresh-button {
        padding: 9px 13px;
        background: #edf5ef;
        color: #347650;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 11px;
    }

    .table-scroll {
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        min-width: 750px;
    }

    thead {
        background: #347650;
        color: white;
    }

    th {
        padding: 13px 15px;
        font-size: 11px;
        text-align: left;
        font-weight: 600;
    }

    td {
        padding: 14px 15px;
        border-bottom: 1px solid #edf1ee;
        font-size: 11px;
        color: #526257;
    }

    tbody tr:hover {
        background: #f8fbf8;
    }

    .role-badge {
        background: #edf5ef;
        color: #347650;
        padding: 5px 8px;
        border-radius: 5px;
        font-size: 10px;
    }

    .action-buttons {
        display: flex;
        gap: 6px;
    }

    .btn-approve,
    .btn-reject {
        border: none;
        border-radius: 5px;
        padding: 7px 10px;
        font-size: 10px;
        cursor: pointer;
        color: white;
    }

    .btn-approve {
        background: #347650;
    }

    .btn-reject {
        background: #d9534f;
    }

    .btn-approve:hover {
        background: #285d43;
    }

    .btn-reject:hover {
        background: #c43d39;
    }

    .empty-message,
    .loading-message {
        padding: 35px;
        text-align: center;
        color: #87958b;
        font-size: 12px;
    }

    .error-message {
        padding: 20px;
        color: #c43d39;
        font-size: 12px;
    }
    .btn-detail {
    background: #e8f0f9;
    color: #35618b;
    border: none;
    border-radius: 5px;
    padding: 7px 10px;
    font-size: 10px;
    cursor: pointer;
}
.modal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.45);
    z-index: 1000;
    justify-content: center;
    align-items: center;
    padding: 20px;
}

.modal-content {
    background: white;
    width: 100%;
    max-width: 650px;
    max-height: 85vh;
    overflow-y: auto;
    border-radius: 12px;
    padding: 25px;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 20px;
}

.close-button {
    border: none;
    background: none;
    font-size: 25px;
    cursor: pointer;
    color: #718477;
}

.detail-profile {
    display: flex;
    gap: 18px;
    align-items: center;
    margin-bottom: 20px;
}

.detail-profile img {
    width: 85px;
    height: 85px;
    object-fit: cover;
    border-radius: 50%;
}

.detail-row {
    padding: 12px 0;
    border-bottom: 1px solid #edf1ee;
    font-size: 12px;
}

.detail-row strong {
    display: block;
    color: #347650;
    margin-bottom: 5px;
}

.detail-row p {
    color: #59675e;
    overflow-wrap: anywhere;
}

.detail-document {
    display: inline-block;
    margin-top: 10px;
    color: #347650;
}

    @media (max-width: 900px) {
        .verification-summary {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 500px) {
        .verification-summary {
            grid-template-columns: 1fr 1fr;
        }

        .table-header {
            align-items: flex-start;
            flex-direction: column;
        }
    }
</style>
@endpush

@section('content')

<div class="page-heading">
    <h1>Verifikasi Akun Pengguna</h1>
    <p>Periksa dan verifikasi akun pengguna yang telah mendaftar di bisaKerja.</p>
</div>

<div class="verification-summary">

    <div class="summary-card">
        <p>Total Menunggu Verifikasi</p>
        <h2 id="totalPending">0</h2>
    </div>

    <div class="summary-card">
        <p>Pencari Kerja</p>
        <h2 id="totalJobSeeker">0</h2>
    </div>

    <div class="summary-card">
        <p>Penyedia Pekerjaan</p>
        <h2 id="totalEmployer">0</h2>
    </div>

    <div class="summary-card">
        <p>Status</p>
        <h2 style="font-size: 16px;">Menunggu</h2>
    </div>

</div>

<div class="table-container">

    <div class="table-header">
        <div>
            <h3>Daftar Akun Menunggu Verifikasi</h3>
            <p>Berikut adalah pengguna yang belum diverifikasi Admin.</p>
        </div>

        <button class="refresh-button" onclick="loadPendingUsers()">
            ↻ Muat Ulang
        </button>
    </div>

    <div class="table-scroll">
        <table>
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Nama Pengguna</th>
                    <th>Email</th>
                    <th>Jenis Pengguna</th>
                    <th>Tanggal Daftar</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody id="pendingUsersTable">
                <tr>
                    <td colspan="6" class="loading-message">
                        Memuat data pengguna...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

</div>
<div id="detailModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Detail Pengguna</h3>
            <button onclick="closeDetail()" class="close-button">&times;</button>
        </div>

        <div id="detailContent">
            Memuat data pengguna...
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const API_URL = '/api/admin';

    function escapeHTML(value) {
        return String(value ?? '').replace(/[&<>"']/g, function (char) {
            return {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            }[char];
        });
    }

    function getToken() {
        return sessionStorage.getItem('auth_token');
    }

    function formatDate(date) {
        if (!date) return '-';

        return new Date(date).toLocaleDateString('id-ID', {
            day: '2-digit',
            month: 'short',
            year: 'numeric'
        });
    }

    async function loadPendingUsers() {
       const table = document.getElementById('pendingUsersTable');
    const token = getToken();

    if (!token) {
        window.location.replace('/login');
        return;
    }

    table.innerHTML = `
        <tr>
            <td colspan="6" class="loading-message">
                Memuat data pengguna...
            </td>
        </tr>
    `;

    try {
        const response = await fetch(`${API_URL}/pending-users`, {
            headers: {
                'Accept': 'application/json',
                'Authorization': `Bearer ${token}`
            }
        });

        const result = await response.json();

        if (response.status === 401) {
            sessionStorage.removeItem('auth_token');
            sessionStorage.removeItem('user');
            window.location.replace('/login');
            return;
        }

        if (!response.ok) {
            throw new Error(result.message || 'Gagal mengambil data.');
        }

        const users = result.data || [];

        document.getElementById('totalPending').textContent = users.length;

        document.getElementById('totalJobSeeker').textContent =
            users.filter(user => user.role === 'pencari_kerja').length;

        document.getElementById('totalEmployer').textContent =
            users.filter(user => user.role === 'penyedia_pekerjaan').length;

        if (users.length === 0) {
            table.innerHTML = `
                <tr>
                    <td colspan="6" class="empty-message">
                        Tidak ada akun yang menunggu verifikasi.
                    </td>
                </tr>
            `;
            return;
        }

        table.innerHTML = users.map((user, index) => `
            <tr>
                <td>${index + 1}</td>
                <td>${escapeHTML(user.nama)}</td>
                <td>${escapeHTML(user.email)}</td>

                <td>
                    <span class="role-badge">
                        ${user.role === 'pencari_kerja'
                            ? 'Pencari Kerja'
                            : 'Penyedia Pekerjaan'}
                    </span>
                </td>

                <td>${formatDate(user.dibuat_pada)}</td>

                <td>
                    <div class="action-buttons">

                        <button class="btn-detail"
                            onclick="showUserDetail(${Number(user.id_pengguna)})">
                            Detail
                        </button>

                        <button class="btn-approve"
                            onclick="verifyUser(${Number(user.id_pengguna)}, 'aktif')">
                            Setujui
                        </button>

                        <button class="btn-reject"
                            onclick="verifyUser(${Number(user.id_pengguna)}, 'ditolak')">
                            Tolak
                        </button>

                    </div>
                </td>
            </tr>
        `).join('');

    } catch (error) {
        console.error(error);

        table.innerHTML = `
            <tr>
                <td colspan="6" class="error-message">
                    ${escapeHTML(error.message)}
                </td>
            </tr>
        `;
    }
    }

    async function verifyUser(id, decision) {
        const action = decision === 'aktif' ? 'menyetujui' : 'menolak';

        if (!confirm(`Apakah kamu yakin ingin ${action} akun ini?`)) {
            return;
        }

        let note = '';

        if (decision === 'ditolak') {
            note = prompt('Masukkan alasan penolakan akun:');

            if (note === null) return;

            if (!note.trim()) {
                alert('Alasan penolakan harus diisi.');
                return;
            }
        }

        try {
            const response = await fetch(`${API_URL}/users/${id}/verify`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${getToken()}`
                },
                body: JSON.stringify({
                    keputusan: decision,
                    catatan_verifikasi: note
                })
            });

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || 'Verifikasi gagal.');
            }

            alert(result.message);

            await loadPendingUsers();

        } catch (error) {
            console.error(error);
            alert(error.message);
        }
    }

    async function showUserDetail(id) {
    const modal = document.getElementById('detailModal');
    const content = document.getElementById('detailContent');

    modal.style.display = 'flex';
    content.innerHTML = 'Memuat data pengguna...';

    try {
        const response = await fetch(`${API_URL}/users/${id}`, {
            headers: {
                'Accept': 'application/json',
                'Authorization': `Bearer ${getToken()}`
            }
        });

        const result = await response.json();

        if (!response.ok) {
            throw new Error(result.message || 'Gagal mengambil detail.');
        }

        const user = result.data;

        const photo = user.foto_profil
            ? `/storage/${user.foto_profil}`
            : null;

        const cv = user.cv
            ? `/storage/${user.cv}`
            : null;

        content.innerHTML = `
            <div class="detail-profile">
                ${photo
                    ? `<img src="${photo}" alt="Foto profil">`
                    : '<div>Tidak ada foto</div>'}

                <div>
                    <h3>${escapeHTML(user.nama)}</h3>
                    <p>${escapeHTML(user.email)}</p>
                    <p>${user.role === 'pencari_kerja'
                        ? 'Pencari Kerja'
                        : 'Penyedia Pekerjaan'}</p>
                </div>
            </div>

            <div class="detail-row">
                <strong>Nomor Telepon</strong>
                <p>${escapeHTML(user.nomor_telepon || '-')}</p>
            </div>

            <div class="detail-row">
                <strong>Alamat</strong>
                <p>${escapeHTML(user.alamat || '-')}</p>
            </div>

            <div class="detail-row">
                <strong>Jenis Kelamin</strong>
                <p>${escapeHTML(user.jenis_kelamin || '-')}</p>
            </div>

            <div class="detail-row">
                <strong>Deskripsi Diri</strong>
                <p>${escapeHTML(user.deskripsi_diri || '-')}</p>
            </div>

            <div class="detail-row">
                <strong>Keahlian</strong>
                <p>${escapeHTML(user.keahlian || '-')}</p>
            </div>

            <div class="detail-row">
                <strong>Pengalaman Kerja</strong>
                <p>${escapeHTML(user.pengalaman_kerja || '-')}</p>
            </div>

            <div class="detail-row">
                <strong>Dokumen CV</strong>
                ${cv
                    ? `<a class="detail-document"
                         href="${cv}" target="_blank" rel="noopener">
                         Lihat CV Pengguna (PDF)
                       </a>`
                    : '<p>CV belum tersedia.</p>'}
            </div>
        `;

    } catch (error) {
        content.innerHTML = `
            <p class="error-message">${escapeHTML(error.message)}</p>
        `;
    }
}

function closeDetail() {
    document.getElementById('detailModal').style.display = 'none';
}

    loadPendingUsers();
</script>
@endpush
