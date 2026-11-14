
@extends('admin.layouts.app')

@section('title', 'Manajemen Pengguna')

@push('styles')
<style>
    .user-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 15px;
        margin-bottom: 25px;
    }

    .stat-card {
        background: white;
        border: 1px solid #e8eee9;
        border-radius: 9px;
        padding: 18px;
    }

    .stat-card p {
        font-size: 11px;
        color: #87958b;
        margin-bottom: 10px;
    }

    .stat-card h2 {
        font-size: 23px;
        color: #347650;
    }

    .filter-container {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        padding: 18px 20px;
        border-bottom: 1px solid #edf1ee;
    }

    .filter-container input,
    .filter-container select {
        padding: 10px;
        border: 1px solid #dce8df;
        border-radius: 6px;
        font-size: 12px;
        outline: none;
        background: white;
    }

    .filter-container input {
        flex: 1;
        min-width: 180px;
    }

    .filter-container select {
        min-width: 160px;
    }

    .user-table-container {
        background: white;
        border: 1px solid #e8eee9;
        border-radius: 10px;
        overflow: hidden;
    }

    .table-title {
        padding: 20px;
    }

    .table-title h3 {
        font-size: 15px;
        color: #285d43;
    }

    .table-title p {
        font-size: 11px;
        color: #87958b;
        margin-top: 5px;
    }

    .table-scroll {
        overflow-x: auto;
    }

    .user-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 850px;
    }

    .user-table thead {
        background: #347650;
        color: white;
    }

    .user-table th {
        padding: 13px;
        font-size: 11px;
        text-align: left;
    }

    .user-table td {
        padding: 13px;
        border-bottom: 1px solid #edf1ee;
        font-size: 11px;
        color: #526257;
    }

    .status-badge {
        display: inline-block;
        padding: 5px 8px;
        border-radius: 5px;
        font-size: 10px;
    }

    .status-aktif {
        background: #e5f5e9;
        color: #287a42;
    }

    .status-menunggu_verifikasi {
        background: #fff4d9;
        color: #a87512;
    }

    .status-ditolak {
        background: #fde9e8;
        color: #c43d39;
    }

    .status-ditangguhkan {
        background: #ececec;
        color: #666;
    }

    .btn-detail,
    .btn-suspend,
    .btn-activate {
        border: none;
        padding: 7px 9px;
        border-radius: 5px;
        font-size: 10px;
        cursor: pointer;
        margin: 2px;
    }

    .btn-detail {
        background: #e8f0f9;
        color: #35618b;
    }

    .btn-suspend {
        background: #fde9e8;
        color: #c43d39;
    }

    .btn-activate {
        background: #e5f5e9;
        color: #287a42;
    }

    .pagination {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 17px 20px;
        font-size: 11px;
        color: #718477;
    }

    .pagination button {
        border: 1px solid #dce8df;
        background: white;
        padding: 7px 12px;
        border-radius: 5px;
        cursor: pointer;
        margin-left: 5px;
    }

    .pagination button:disabled {
        opacity: .4;
        cursor: not-allowed;
    }


.user-modal {
    position: fixed;
    inset: 0;
    z-index: 9999;
    background: rgba(0, 0, 0, 0.55);
    display: none;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.user-modal-content {
    background: white;
    width: 100%;
    max-width: 750px;
    max-height: 90vh;
    overflow-y: auto;
    border-radius: 14px;
    padding: 25px;
}

.user-modal-header,
.user-modal-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.user-modal-header {
    border-bottom: 1px solid #eee;
    padding-bottom: 15px;
}

.close-modal {
    border: none;
    background: none;
    font-size: 28px;
    cursor: pointer;
}

.user-profile-header {
    display: flex;
    align-items: center;
    gap: 18px;
    margin: 22px 0;
}

.user-detail-photo {
    width: 75px;
    height: 75px;
    border-radius: 50%;
    object-fit: cover;
    background: #eee;
}

.user-detail-section {
    border-top: 1px solid #eee;
    padding: 18px 0;
}

.user-detail-section h3 {
    margin-bottom: 15px;
}

.user-detail-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 18px;
}

.user-detail-section label {
    color: #777;
    font-size: 13px;
}

.user-detail-section p {
    margin-top: 5px;
    color: #222;
    overflow-wrap: anywhere;
}

.user-status-badge {
    display: inline-block;
    margin-top: 5px;
    padding: 5px 12px;
    border-radius: 20px;
    background: #e8f5e9;
    color: #26733b;
    font-size: 12px;
}

.user-document-links a {
    color: #16834a;
    text-decoration: underline;
}

.user-modal-footer {
    border-top: 1px solid #eee;
    padding-top: 15px;
    justify-content: flex-end;
}

.user-modal-footer button {
    background: #16834a;
    color: white;
    border: none;
    padding: 9px 20px;
    border-radius: 7px;
    cursor: pointer;
}

@media (max-width: 600px) {
    .user-detail-grid {
        grid-template-columns: 1fr;
    }
}


    @media(max-width: 700px) {
        .user-stats {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')

<div class="page-heading">
    <h1>Manajemen Pengguna</h1>
    <p>Kelola seluruh akun pencari kerja dan penyedia pekerjaan.</p>
</div>

<div class="user-stats">
    <div class="stat-card">
        <p>Total Pengguna</p>
        <h2 id="totalUsers">0</h2>
    </div>

    <div class="stat-card">
        <p>Akun Aktif</p>
        <h2 id="activeUsers">0</h2>
    </div>

    <div class="stat-card">
        <p>Akun Ditangguhkan</p>
        <h2 id="suspendedUsers">0</h2>
    </div>
</div>

<div class="user-table-container">

    <div class="table-title">
        <h3>Daftar Seluruh Pengguna</h3>
        <p>Informasi seluruh akun yang terdaftar di bisaKerja.</p>
    </div>

    <div class="filter-container">
        <input
            type="text"
            id="searchUser"
            placeholder="Cari nama atau email..."
        >

        <select id="filterRole">
            <option value="">Semua Role</option>
            <option value="pencari_kerja">Pencari Kerja</option>
            <option value="penyedia_pekerjaan">Penyedia Pekerjaan</option>
        </select>

        <select id="filterStatus">
            <option value="">Semua Status</option>
            <option value="aktif">Aktif</option>
            <option value="menunggu_verifikasi">Menunggu Verifikasi</option>
            <option value="ditolak">Ditolak</option>
            <option value="ditangguhkan">Ditangguhkan</option>
        </select>

        <button class="btn-detail" onclick="loadUsers(1)">
            Terapkan
        </button>
    </div>

    <div class="table-scroll">
        <table class="user-table">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status Akun</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody id="usersTable">
                <tr>
                    <td colspan="6" style="text-align:center;padding:30px">
                        Memuat data...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="pagination">
        <span id="paginationInfo">Memuat halaman...</span>

        <div>
            <button id="prevPage" onclick="loadUsers(currentPage - 1)">
                Sebelumnya
            </button>

            <button id="nextPage" onclick="loadUsers(currentPage + 1)">
                Berikutnya
            </button>
        </div>
    </div>

</div>

<!-- Modal Detail Pengguna -->
<div id="userDetailModal" class="user-modal" style="display: none;">
    <div class="user-modal-content">

        <div class="user-modal-header">
            <h2>Detail Pengguna</h2>
            <button type="button" onclick="closeUserDetail()" class="close-modal">
                &times;
            </button>
        </div>

        <div class="user-detail-body">

            <div class="user-profile-header">
                <img id="detailFoto" src="" alt="Foto Profil"
                     class="user-detail-photo">

                <div>
                    <h3 id="detailNama">-</h3>
                    <p id="detailEmail">-</p>
                    <span id="detailStatus" class="user-status-badge">-</span>
                </div>
            </div>

            <div class="user-detail-section">
                <h3>Informasi Dasar</h3>

                <div class="user-detail-grid">
                    <div>
                        <label>Nomor Telepon</label>
                        <p id="detailTelepon">-</p>
                    </div>

                    <div>
                        <label>Role</label>
                        <p id="detailRole">-</p>
                    </div>

                    <div>
                        <label>Alamat</label>
                        <p id="detailAlamat">-</p>
                    </div>

                    <div>
                        <label>Tanggal Lahir</label>
                        <p id="detailTanggalLahir">-</p>
                    </div>

                    <div>
                        <label>Jenis Kelamin</label>
                        <p id="detailJenisKelamin">-</p>
                    </div>

                    <div>
                        <label>Jenis Pemberi Kerja</label>
                        <p id="detailJenisPemberi">-</p>
                    </div>
                </div>
            </div>

            <div class="user-detail-section">
                <h3>Informasi Tambahan</h3>

                <div>
                    <label>Deskripsi Diri</label>
                    <p id="detailDeskripsi">-</p>
                </div>

                <div>
                    <label>Keahlian</label>
                    <p id="detailKeahlian">-</p>
                </div>

                <div>
                    <label>Pengalaman Kerja</label>
                    <p id="detailPengalaman">-</p>
                </div>
            </div>

            <div class="user-detail-section">
                <h3>Dokumen</h3>

                <div class="user-document-links">
                    <a id="detailCV" href="#" target="_blank"
                       style="display: none;">
                        Lihat CV
                    </a>
                </div>
            </div>

        </div>

        <div class="user-modal-footer">
            <button type="button" onclick="closeUserDetail()">
                Tutup
            </button>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
    const API_URL = '/api/admin';
    let currentPage = 1;
    let lastPage = 1;

    function escapeHTML(value) {
        return String(value ?? '').replace(/[&<>"']/g, char => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        })[char]);
    }

    async function loadUsers(page = 1) {
        const table = document.getElementById('usersTable');
        const token = sessionStorage.getItem('auth_token');

        if (!token) {
            window.location.replace('/login');
            return;
        }

        table.innerHTML = `
            <tr><td colspan="6" style="text-align:center;padding:30px">
                Memuat data pengguna...
            </td></tr>
        `;

        const params = new URLSearchParams({
            page,
            search: document.getElementById('searchUser').value,
            role: document.getElementById('filterRole').value,
            status: document.getElementById('filterStatus').value
        });

        try {
            const response = await fetch(`${API_URL}/users?${params}`, {
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                }
            });

            const result = await response.json();

            if (response.status === 401) {
                sessionStorage.clear();
                window.location.replace('/login');
                return;
            }

            if (!response.ok) {
                throw new Error(result.message || 'Gagal mengambil data.');
            }

            const users = result.data.data || [];
            currentPage = result.data.current_page;
            lastPage = result.data.last_page;

            document.getElementById('totalUsers').textContent =
                result.data.total;

            document.getElementById('paginationInfo').textContent =
                `Halaman ${currentPage} dari ${lastPage} (${result.data.total} pengguna)`;

            document.getElementById('prevPage').disabled =
                currentPage <= 1;

            document.getElementById('nextPage').disabled =
                currentPage >= lastPage;

            if (users.length === 0) {
                table.innerHTML = `
                    <tr><td colspan="6" style="text-align:center;padding:30px">
                        Pengguna tidak ditemukan.
                    </td></tr>
                `;
                return;
            }

            table.innerHTML = users.map((user, index) => `
                <tr>
                    <td>${(currentPage - 1) * 10 + index + 1}</td>
                    <td>${escapeHTML(user.nama)}</td>
                    <td>${escapeHTML(user.email)}</td>
                    <td>${user.role === 'pencari_kerja'
                        ? 'Pencari Kerja'
                        : 'Penyedia Pekerjaan'}</td>
                    <td>
                        <span class="status-badge status-${escapeHTML(user.status_akun)}">
                            ${escapeHTML(user.status_akun.replaceAll('_', ' '))}
                        </span>
                    </td>
                    <td>
                        <button class="btn-detail"
                            onclick="showUserDetail(${Number(user.id_pengguna)})">
                            Detail
                        </button>

                        ${user.status_akun === 'aktif'
                            ? `<button class="btn-suspend"
                                onclick="changeStatus(${Number(user.id_pengguna)}, 'ditangguhkan')">
                                Tangguhkan
                               </button>`
                            : user.status_akun === 'ditangguhkan'
                                ? `<button class="btn-activate"
                                    onclick="changeStatus(${Number(user.id_pengguna)}, 'aktif')">
                                    Aktifkan
                                   </button>`
                                : '-'}
                    </td>
                </tr>
            `).join('');

            // Ambil statistik keseluruhan, bukan hanya halaman aktif
            await loadUserStats(token);

        } catch (error) {
            console.error(error);
            table.innerHTML = `
                <tr><td colspan="6" style="color:red;padding:20px">
                    ${escapeHTML(error.message)}
                </td></tr>
            `;
        }
    }

    async function loadUserStats(token) {
        try {
        const response = await fetch('/api/admin/users', {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        const result = await response.json();

        if (!response.ok) {
            throw new Error(result.message || 'Gagal mengambil statistik');
        }

        document.getElementById('totalUsers').textContent =
            result.statistics.total_pengguna;

        document.getElementById('activeUsers').textContent =
            result.statistics.akun_aktif;

        document.getElementById('suspendedUsers').textContent =
            result.statistics.akun_ditangguhkan;

    } catch (error) {
        console.error('Gagal memuat statistik:', error);
    }
    }

    async function changeStatus(id, status) {
        const action = status === 'aktif'
            ? 'mengaktifkan kembali'
            : 'menangguhkan';

        if (!confirm(`Yakin ingin ${action} akun ini?`)) return;

        try {
            const response = await fetch(`${API_URL}/users/${id}/status`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${sessionStorage.getItem('auth_token')}`
                },
                body: JSON.stringify({ status_akun: status })
            });

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || 'Gagal mengubah status.');
            }

            alert(result.message);
            loadUsers(currentPage);

        } catch (error) {
            alert(error.message);
        }
    }

    // Memakai modal detail yang sudah dibuat di halaman verifikasi.
    // Halaman ini belum memiliki modal sendiri, jadi detail akan
    // ditampilkan menggunakan endpoint dan dialog sederhana.
    async function showUserDetail(id) {
          try {
        const response = await fetch(`${API_URL}/users/${id}`, {
            headers: {
                'Accept': 'application/json',
                'Authorization': `Bearer ${sessionStorage.getItem('auth_token')}`
            }
        });

        const result = await response.json();

        if (!response.ok) {
            throw new Error(result.message || 'Gagal mengambil detail.');
        }

        const user = result.data;

        // Mengisi informasi profil
        document.getElementById('detailNama').textContent = user.nama || '-';
        document.getElementById('detailEmail').textContent = user.email || '-';
        document.getElementById('detailTelepon').textContent = user.nomor_telepon || '-';
        document.getElementById('detailRole').textContent = user.role || '-';
        document.getElementById('detailAlamat').textContent = user.alamat || '-';
        document.getElementById('detailTanggalLahir').textContent = user.tanggal_lahir || '-';
        document.getElementById('detailJenisKelamin').textContent = user.jenis_kelamin || '-';
        document.getElementById('detailJenisPemberi').textContent = user.jenis_pemberi || '-';
        document.getElementById('detailDeskripsi').textContent = user.deskripsi_diri || '-';
        document.getElementById('detailKeahlian').textContent = user.keahlian || '-';
        document.getElementById('detailPengalaman').textContent = user.pengalaman_kerja || '-';

        // Status akun
        const status = user.status_akun || user.status || '-';
        document.getElementById('detailStatus').textContent = status;

        // Foto profil
        const foto = document.getElementById('detailFoto');

        if (user.foto_profil) {
            foto.src = user.foto_profil.startsWith('http')
                ? user.foto_profil
                : `/storage/${user.foto_profil}`;
        } else {
            foto.src = 'https://placehold.co/100x100?text=User';
        }

        // CV
        const cv = document.getElementById('detailCV');

        if (user.cv) {
            cv.href = user.cv.startsWith('http')
                ? user.cv
                : `/storage/${user.cv}`;

            cv.style.display = 'inline-block';
        } else {
            cv.style.display = 'none';
        }

        // Tampilkan modal
        document.getElementById('userDetailModal').style.display = 'flex';

    } catch (error) {
        console.error(error);
        alert(error.message);
    }
}

// Menutup modal
function closeUserDetail() {
    document.getElementById('userDetailModal').style.display = 'none';
}
    

    document.getElementById('userDetailModal').addEventListener('click', function(event) {
    if (event.target === this) {
        closeUserDetail();
    }
});

    loadUsers();
</script>
@endpush
