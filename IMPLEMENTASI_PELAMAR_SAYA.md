# Integrasi Pelamar Saya dengan devlop

Tanggal: 14 November 2026. Branch: `Haidar_Branch`, setelah merge `2047bfb`.

## Hasil dan cara akses

Fitur monitoring sudah diterapkan kembali pada working tree aktif. Login sebagai `penyedia_pekerjaan`, lalu pilih **Pelamar Saya** pada sidebar Beranda atau layout penyedia. Alamat sesuai konfigurasi aplikasi: `http://localhost:8000/home?view=pelamar-saya`. Jika server belum berjalan, gunakan `php artisan serve`.

Fitur mencakup ringkasan database, tabel, pencarian nama pelamar/judul lowongan, filter status, pagination 15 baris, detail profil, foto/CV yang benar-benar tersedia, pesan lamaran, loading, error/retry, dan empty state. Tampilan memakai komponen layout penyedia dari devlop beserta warna, font, dan sidebar yang sudah ada. Layar sempit memiliki navigasi dan tabel yang dapat digeser.

Database aktif tetap SQLite; koneksi MySQL Laragon tidak diubah. Saat verifikasi akhir terdapat **0 pengguna, 0 lowongan, dan 0 lamaran** pada database aplikasi. Untuk mencoba secara manual diperlukan akun penyedia aktual dan data yang sesuai. Registrasi penyedia lama belum menyimpan akun; tugas ini tidak mengganti autentikasi atau membuat akun contoh. Data pada screenshot hanya fixture database pengujian sementara.

## Penyesuaian terhadap devlop

Model terbaru sudah memakai relasi berikut, sehingga model tersebut dipertahankan:

```text
pengguna.id_pengguna <- lowongan.id_penyedia
lowongan.id_lowongan <- lamaran.id_lowongan
pengguna.id_pengguna <- lamaran.id_pencari
```

Monitoring kini membaca `lowongan.judul`, `lamaran.status_lamaran`, dan `lamaran.tanggal_lamaran`, bukan struktur `job_listings`/`job_id` dari implementasi sebelumnya. Relasi `lowongan()` dan `pencari()` pada model Lamaran tidak diganti. Daftar lowongan dan monitoring menggunakan relasi lamaran yang sama, termasuk penghitungan jumlah pelamar.

Status lamaran tetap dipisahkan dari status pekerjaan. Menunggu berwarna kuning, Diterima hijau, Ditolak merah. Jika data `status_pekerjaan` berisi `selesai`, detail menampilkan badge biru Selesai tanpa mengganti nilai atau label `status_lamaran` yang tersimpan.

## File baru

| File | Fungsi |
| --- | --- |
| `app/Http/Controllers/Api/ProviderApplicantController.php` | HTML halaman, daftar/ringkasan, detail, dan akses berkas dengan scope pemilik. |
| `database/migrations/2026_10_28_000000_create_pengguna_table.php` | Migration dasar pengguna yang hilang dari devlop, dipulihkan dari implementasi sebelumnya agar urutan migration biodata valid. |
| `database/migrations/2026_11_14_091113_create_missing_vacancy_monitoring_tables.php` | Melengkapi tabel kategori/lowongan/lamaran sesuai model terbaru jika belum ada; menangani skema lokal lama secara aman. |
| `database/factories/KategoriPekerjaanFactory.php` | Fixture kategori sesuai skema terbaru. |
| `database/factories/LamaranFactory.php` | Fixture lamaran dengan foreign key dan status yang sesuai model devlop. |
| `resources/views/provider/applicants.blade.php` | Halaman dan modal dengan komponen `x-layouts.employer`. |
| `public/css/provider-applicants.css` | Gaya khusus isi halaman, status, dan modal. |
| `public/css/provider-navigation.css` | Sidebar tambahan pada Beranda, aktif hanya setelah role diverifikasi server. |
| `public/js/provider-applicants.js` | Pengambilan data bertoken, state UI, filter, pagination, detail, dan berkas. |
| `public/js/provider-navigation.js` | Pemeriksaan role dan pemuatan HTML yang dilindungi memakai token login yang sudah ada. |
| `tests/Feature/ProviderApplicantTest.php` | 20 test monitoring, keamanan, autentikasi, unggahan profil, dan regresi lowongan saat ini. |
| `tests/Feature/ApplicantSchemaCompatibilityTest.php` | 4 test perlindungan data dan kompatibilitas skema. |
| `IMPLEMENTASI_PELAMAR_SAYA.md` | Laporan integrasi ini, menggantikan laporan versi sebelum pull. |

## File lama yang dimodifikasi

| File | Perubahan |
| --- | --- |
| `app/Models/Lamaran.php` | Tambahan label tampilan, cast tanggal, dan scope `forProvider`; identitas tabel/key/relasi devlop dipertahankan. |
| `database/factories/UserFactory.php` | Kolom fixture disesuaikan dengan model pengguna dan role penyedia yang dipakai backend. |
| `database/factories/JobVacancyFactory.php` | Fixture disesuaikan dengan kolom lowongan yang dipakai model/controller saat ini. |
| `resources/views/components/layouts/employer.blade.php` | Placeholder Pelamar menjadi tautan Pelamar Saya khusus penyedia, stack aset halaman, dan batas lebar isi agar tabel responsif. Menu Chat/Profil tetap seperti sebelumnya. |
| `resources/views/home.blade.php` | Sidebar penyedia serta pemuat monitoring; konten dan status Beranda lama dipertahankan. |
| `routes/api.php` | Empat endpoint baru dengan middleware Sanctum, role, dan cache privat. Perilaku endpoint lama tidak diubah. |

Tidak ada perubahan dependency, model User, sistem login, definisi role, controller pengajuan lamaran, atau controller kelola lowongan. Perubahan lain dalam stash sebelumnya tidak diterapkan secara massal. `stash@{0}` tetap dipertahankan sebagai cadangan.

## Route dan keamanan

| Metode | Endpoint | Fungsi |
| --- | --- | --- |
| GET | `/api/penyedia/pelamar-saya/halaman` | HTML Blade yang dilindungi backend. |
| GET | `/api/penyedia/pelamar-saya` | Ringkasan dan daftar; query opsional `search`, `status`, `page`. |
| GET | `/api/penyedia/pelamar-saya/{lamaran}` | Detail satu lamaran milik penyedia. |
| GET | `/api/penyedia/pelamar-saya/{lamaran}/berkas/{type}` | CV/foto, dengan `type=cv` atau `foto`. |

Semua endpoint memakai `auth:sanctum`, `role:penyedia_pekerjaan`, dan `Cache-Control: no-store, private`. HTML dimuat melalui header Bearer karena login aplikasi menyimpan token pada `sessionStorage`; mekanisme login tidak diganti. Tanpa token menghasilkan 401, role lain 403, ID penyedia lain maupun ID yang tidak ditemukan menghasilkan 404. Monitoring tidak memakai middleware login otomatis `UseDemoEmployer`.

Password, alamat, verifikasi akun, dan path berkas tidak dimasukkan ke response detail. Berkas diperiksa setelah kepemilikan lamaran terverifikasi; path dibatasi pada direktori/ekstensi unggahan yang sudah dipakai aplikasi. Nama dan data profil diisi melalui Blade escaping atau DOM `textContent`. Tidak tersedia endpoint untuk mengubah status.

## Query database

Semua daftar, ringkasan, detail, dan berkas menggunakan constraint yang sama:

```sql
WHERE EXISTS (
    SELECT 1 FROM lowongan
    WHERE lowongan.id_lowongan = lamaran.id_lowongan
      AND lowongan.id_penyedia = ?
)
```

Ringkasan memakai `SELECT status_lamaran, COUNT(*) AS total ... GROUP BY status_lamaran`. Pencarian menambahkan grup kondisi `AND (EXISTS pencari WHERE nama LIKE ? OR EXISTS lowongan WHERE judul LIKE ?)`, dan filter memakai `status_lamaran = ?`. Nilai query terikat melalui Eloquent. Daftar diurutkan dengan `tanggal_lamaran DESC, id_lamaran DESC`; relasi nama/judul di-eager-load dan kolom daftar dibatasi sesuai kebutuhan.

Ringkasan mencakup seluruh lamaran milik penyedia, sedangkan filter berlaku pada tabel. Total menghitung lamaran: pengguna yang melamar dua lowongan dihitung dua kali. Lowongan tanpa pemilik tidak termasuk hasil penyedia mana pun.

## Migration dan perlindungan data

Migration dasar pengguna dibutuhkan sebelum migration biodata lama. Pada database lokal migration itu sudah pernah diterapkan, sehingga tidak dijalankan ulang. Migration integrasi hanya membuat tabel canonical yang belum ada; tabel canonical yang sudah ada beserta statusnya tidak ditulis ulang.

Tabel lamaran lokal dari implementasi lama telah diperiksa dan kosong. Tabel itu dipertahankan sebagai `lamaran_monitoring_legacy`, kemudian tabel `lamaran` dibuat sesuai model devlop. Migration akan berhenti sebelum perubahan skema jika menemukan lamaran lama yang berisi data, agar pemetaan dapat dilakukan secara khusus tanpa menebak relasi atau membuang data. Rollback otomatis migration integrasi sengaja ditolak untuk melindungi tabel yang mungkin sudah ada; gunakan migration perbaikan maju.

Migration sudah diterapkan pada SQLite aktif. Cadangan sebelum integrasi tersedia di `storage/app/private/before-applicant-integration.sqlite`. Tidak ada seeder produksi yang dijalankan dan tidak ada dummy data yang dimasukkan ke database aplikasi.

## Pengujian

- Baseline sesudah merge, sebelum integrasi: 22 test; 1 lulus, 1 gagal, 20 error. Error awal terutama karena migration pengguna hilang. Test landing juga mengharapkan redirect, sementara route yang ada mengembalikan welcome dengan status 200.
- Test khusus integrasi: **24 lulus, 216 assertion** melalui `php artisan test --compact tests/Feature/ProviderApplicantTest.php tests/Feature/ApplicantSchemaCompatibilityTest.php`.
- Seluruh suite sesudah integrasi: **46 test, 31 lulus, 11 gagal dan 4 error**. Lima belas test lama yang masih gagal berasal dari suite devlop yang sudah gagal pada baseline. Test tersebut belum disesuaikan karena masih merujuk kolom/form lama (`title`, `user_id`, `work_date`, `duration`, `job_vacancies`) serta ekspektasi lama mengenai redirect/status. Tidak ada test lama yang dihapus atau dinonaktifkan.
- Regresi dengan skema saat ini: login/token, `/api/user`, logout, registrasi pencari beserta berkas, halaman web yang tersedia, halaman lowongan dan hitungan pelamar, membuat/mengubah/menutup lowongan, serta pemisahan status lamaran dari status pekerjaan lulus dalam suite integrasi.
- Chrome headless dengan database SQLite sementara: **14 pemeriksaan lulus**, tidak ada exception JavaScript atau pemanggilan console.error yang terdeteksi. Meliputi sidebar role, tabel/ringkasan, pencarian/filter, pagination, detail, status pekerjaan selesai, mobile 390px, desktop 1280px, loading, error server/koneksi dan retry, empty state, serta penolakan pencari kerja.
- Dua file JavaScript baru lulus `node --check`; Laravel Pint dijalankan dan `git diff --check` lulus.
- `npm.cmd run build` lulus. Peringatan dependency font opsional `fontaine` berasal dari konfigurasi sebelumnya dan tidak menghalangi build.
- Artefak pemeriksaan lokal: `storage/app/private/applicants-desktop.png`, `applicants-mobile.png`, `check-applicants.cjs`, dan `integration-suite-result.txt`. Semuanya berada di area storage privat yang tidak dilacak Git.

## Batasan yang masih ada

Registrasi penyedia belum menyimpan akun, dan alur pengajuan lamaran belum tersedia lengkap pada checkout ini. Data monitoring akan muncul setelah akun/lowongan/lamaran aktual tersedia dengan relasi yang sesuai. Chat dan Profil masih berupa placeholder. Penyimpanan berkas lama tetap memakai disk public; kebijakan penyimpanan global tidak diganti. Middleware demo pada halaman lowongan lama juga dipertahankan, sedangkan monitoring selalu memerlukan autentikasi nyata. Pengujian MySQL belum dilakukan. Seluruh keterbatasan ini tidak diklaim selesai oleh integrasi monitoring.
