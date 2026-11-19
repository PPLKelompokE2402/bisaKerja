# Implementasi Status Lamaran — Pencari Kerja

Tanggal: 18 November 2026. Branch aktif: `Haidar_Branch`.

## Ringkasan dan akses

Halaman **Lamaran Saya** sudah tersedia dengan dua bagian sesuai referensi: **Lamaran** dan **Riwayat pekerjaan**. Keduanya menampilkan judul/lokasi pekerjaan, upah, tanggal melamar, serta badge status. Tidak ada statistik, grafik, atau kartu ringkasan tambahan.

Masuk melalui login yang sudah ada sebagai `pencari_kerja`, kemudian pilih tautan **Lamaran Saya** pada Beranda. Menu Lamaran Saya pada template sidebar lowongan juga sudah diarahkan ke halaman ini. Alamat yang diperiksa melalui Laravel Boost: `http://localhost:8000/lamaran-saya`. Jika server belum berjalan, jalankan `php artisan serve`.

Header putih, logo kiri, label role kanan, sidebar dengan penanda aktif, latar hijau sangat muda, tabel hijau/putih, dan empat warna badge mengikuti screenshot. Pada ponsel, navigasi menjadi baris dan tabel dapat digeser. Tersedia loading, pesan error/retry, empty state terpisah, serta fallback untuk data opsional yang tidak tersedia.

## File baru

| File | Fungsi |
| --- | --- |
| `app/Http/Controllers/Api/SeekerApplicationController.php` | HTML yang dilindungi, query milik akun login, pemisahan lamaran/riwayat, serialisasi minimal, pagination. |
| `resources/views/seeker/applications-entry.blade.php` | Halaman masuk netral yang memuat HTML melalui token login yang sudah ada. |
| `resources/views/seeker/applications.blade.php` | Header/sidebar dan dua tabel sesuai desain. |
| `public/css/seeker-applications.css` | Stylesheet khusus halaman baru; tidak mengganti stylesheet global. |
| `public/js/seeker-applications-entry.js` | Memuat HTML yang dilindungi, menangani akses ditolak, login ulang, dan error koneksi. |
| `public/js/seeker-applications.js` | Render data aman, format upah/tanggal, state UI, pagination, dan pembaruan status. |
| `public/js/seeker-applications-link.js` | Menampilkan tautan Beranda hanya setelah API mengonfirmasi role pencari kerja. |
| `tests/Feature/SeekerApplicationTest.php` | 22 kasus pengujian fitur, data, kepemilikan, role, pagination, dan pembacaan perubahan status. |
| `IMPLEMENTASI_LAMARAN_SAYA.md` | Laporan ini. |

## File lama yang dimodifikasi untuk tugas ini

| File | Perubahan |
| --- | --- |
| `routes/api.php` | Dua endpoint baru, dilindungi Sanctum, role pencari kerja, dan cache privat. |
| `routes/web.php` | Route masuk `/lamaran-saya`; route lama tetap dipertahankan. |
| `resources/views/jobs/index.blade.php` | Hanya tautan menu Lamaran Saya yang sebelumnya `#` menjadi named route fitur. Tautan lain tetap seperti sebelumnya. |
| `resources/views/home.blade.php` | Penambahan satu tautan masuk tersembunyi secara default dan pemuat JS khusus role. Isi Beranda, autentikasi, status akun, serta navigasi penyedia dipertahankan. |

Perubahan monitoring penyedia, model, factory, dan migration yang sudah ada pada working tree sebelum tugas ini tetap dipertahankan. Tugas pencari kerja ini tidak mengubah file-file tersebut, tidak menambah migration/tabel/relasi, dan tidak mengganti dependency.

## Integrasi database dan sumber data

Menggunakan Laravel 13.40.0, Sanctum 4.3.3, Blade, PHP CLI 8.4.13, serta database yang sudah dikonfigurasi aplikasi. Skema diperiksa melalui Laravel Boost sebelum implementasi.

| Informasi | Sumber |
| --- | --- |
| Pemilik lamaran | `lamaran.id_pencari`, dibatasi dengan `request()->user()->getKey()`. |
| Relasi pekerjaan | Relasi `Lamaran::lowongan()` yang sudah ada, melalui `id_lowongan`. |
| Nama pekerjaan | `lowongan.judul`. |
| Lokasi | `lowongan.lokasi`. |
| Upah | `lowongan.upah`, termasuk nilai nol dan pecahan. |
| Tanggal melamar | `lamaran.tanggal_lamaran`, bukan tanggal lowongan dibuat/dikerjakan. |
| Status lamaran | `lamaran.status_lamaran`, dengan label yang sudah ada pada `Lamaran::STATUS_LABELS`. |
| Penanda pekerjaan selesai | `lamaran.status_pekerjaan = 'selesai'`. |

Tabel **Lamaran** mengambil milik akun login dengan kondisi berikut:

```sql
WHERE id_pencari = ?
  AND (status_pekerjaan IS NULL OR status_pekerjaan <> 'selesai')
ORDER BY tanggal_lamaran DESC, id_lamaran DESC
```

Tabel **Riwayat pekerjaan** menggunakan:

```sql
WHERE id_pencari = ? AND status_pekerjaan = 'selesai'
ORDER BY tanggal_lamaran DESC, id_lamaran DESC
```

Query menggunakan Eloquent dengan parameter terikat dan eager loading judul/lokasi/upah. Tidak ada query per baris. Masing-masing tabel memiliki pagination 15 baris dengan parameter `applications_page` dan `history_page`. Jika perubahan status membuat halaman terakhir menyusut, pagination otomatis kembali ke halaman valid agar tidak menampilkan empty state yang keliru.

## Pembacaan status dan pembaruan data

Menunggu memakai badge kuning, Diterima hijau, Ditolak merah. Riwayat memakai badge biru **Selesai**, langsung berdasarkan `status_pekerjaan` yang tersimpan. Status lamaran asli tetap dikirim terpisah; misalnya pekerjaan selesai dengan lamaran Diterima tidak mengubah nilai `status_lamaran` menjadi Selesai.

Status lowongan selesai atau tanggal selesai saja tidak dianggap bukti bahwa pelamar tersebut sudah menyelesaikan pekerjaan. Dengan demikian, pelamar yang ditolak tidak otomatis masuk riwayat hanya karena lowongannya selesai. Status lain yang sudah tersimpan dipertahankan tanpa membuat definisi baru.

Data dibaca ulang saat halaman dibuka, tab kembali aktif/fokus, dan setiap 30 detik selama halaman terlihat. Tidak ada cache data lamaran atau mekanisme persetujuan baru. Perubahan dari proses lain yang menulis baris lamaran akan terlihat pada pengambilan data berikutnya.

## Endpoint dan otorisasi

| Metode | Route | Akses |
| --- | --- | --- |
| GET | `/lamaran-saya` | Pemuat netral tanpa data pribadi; browser yang belum login diarahkan ke login lama. |
| GET | `/api/pencari/lamaran-saya/halaman` | HTML fitur; `auth:sanctum` dan `role:pencari_kerja`. |
| GET | `/api/pencari/lamaran-saya` | Data kedua tabel; middleware yang sama. |

HTML dan data yang dilindungi mengembalikan 401 tanpa token serta 403 untuk penyedia/admin. Frontend memakai Bearer token dari `sessionStorage.auth_token` sesuai login aplikasi saat ini. Route masuk netral tidak menyediakan HTML tabel atau data pribadi sebelum HTML fitur berhasil diambil.

ID pencari kerja selalu diambil dari autentikasi, bukan dari query browser. Parameter seperti `id_pencari`/`user_id` tidak bisa mengganti pemilik query. Response tidak memuat email, telepon, password, pesan lamaran, CV, atau informasi penyedia. Data pekerjaan ditulis ke DOM dengan `textContent`, bukan HTML dari pengguna. Endpoint dilindungi `Cache-Control: no-store, private` dan tidak memiliki operasi perubahan status/pengajuan lamaran.

## Hasil pengujian yang dijalankan

- **46 test fitur lulus, 365 assertion** melalui `php artisan test --compact tests/Feature/SeekerApplicationTest.php tests/Feature/ProviderApplicantTest.php tests/Feature/ApplicantSchemaCompatibilityTest.php`. Terdiri dari 22 kasus pencari kerja serta 24 kasus monitoring penyedia/kompatibilitas yang sudah ada.
- Test pencari kerja meliputi akses token/role, data seluruh kolom, tiga status lamaran, pekerjaan selesai, pemisahan dua tabel, isolasi antaruser termasuk kondisi NULL/OR, penolakan manipulasi ID, status tidak dikenal, dua data kosong, field response minimal, pembaruan baris yang sama, tanggal kosong, upah nol/pecahan, pagination independen/penyusutan halaman, validasi parameter, serta tidak adanya endpoint tulis.
- **13 pemeriksaan Chrome headless lulus**, dengan database SQLite sementara terpisah: tautan Beranda, sidebar aktif, tepat dua tabel, nilai/warna badge, data user lain tidak tampil, viewport 1440/820/390px, refresh status Diterima lalu Selesai, loading, server error/retry, network error/retry, data tidak lengkap, format pecahan, escaping XSS, pagination, empty state, penolakan penyedia, serta redirect login tamu. Tidak terdeteksi exception JavaScript atau pemanggilan console.error.
- Seluruh suite: **68 test, 53 lulus, 11 failure dan 4 error**. Kelima belas test lama yang gagal memiliki nama yang sama dengan baseline sebelum implementasi; **tidak ada test gagal baru**. Test lama masih memuat ekspektasi redirect serta kolom/form lowongan versi sebelumnya dan tidak dihapus/dinonaktifkan oleh tugas ini.
- `node --check` pada tiga JS baru, `php -l` controller, Laravel Pint, dan `git diff --check` lulus.
- `npm.cmd run build` lulus di luar sandbox. Peringatan dependency font opsional `fontaine` berasal dari konfigurasi lama dan tidak menghalangi build.
- `php artisan route:list --path=lamaran-saya -v` mengonfirmasi ketiga route dan middleware-nya.

Screenshot lokal: `storage/app/private/seeker-applications-desktop.png` dan `seeker-applications-mobile.png`. Script pemeriksaan browser serta hasil suite berada di `storage/app/private/check-seeker-applications.cjs`, `seeker-baseline-tests.txt`, dan `seeker-final-tests.txt`; bukan seeder atau dependency produksi. Log browser terpusat Boost belum tersedia; pemeriksaan error browser dilakukan langsung lewat Chrome DevTools Protocol.

## Batasan dan fitur lama yang diperiksa

- Koneksi aktif tetap SQLite. Pemeriksaan akhir menunjukkan 0 pengguna, 0 lowongan, 0 lamaran pada database aplikasi. Tidak ada data contoh yang dimasukkan ke database tersebut; semua fixture berada pada database pengujian.
- Checkout ini belum mempunyai endpoint/alur pengajuan dan keputusan lamaran yang lengkap. Halaman menggunakan tabel/relasi aktual yang sudah tersedia dan tidak mengarang proses pengajuan, persetujuan, ataupun penyelesaian baru. Pengujian alur tersebut secara menyeluruh belum dapat diklaim berhasil.
- Chat dan Profil masih berupa placeholder `#` pada sidebar pencari kerja. Tautan itu dipertahankan; tidak ada klaim bahwa kedua modul sudah berfungsi. Template daftar/detail lowongan juga masih merujuk named route `jobs.index`/`jobs.show` yang belum terdaftar. Masalah lama itu tidak diperbaiki di luar cakupan fitur ini; akses halaman baru tersedia langsung dan melalui tautan Beranda.
- Regresi yang sudah diperiksa melalui test lama mencakup login/token, data pengguna, logout, registrasi pencari kerja beserta berkas, halaman Beranda, monitoring penyedia, hitungan pelamar, serta membuat/mengubah/menutup lowongan dengan skema saat ini. Browser juga memeriksa bahwa sidebar penyedia tetap tampil dan tautan pencari kerja tidak muncul untuk penyedia.
- Tidak ada perubahan autentikasi, role, tabel/database, proses pengajuan lamaran, keputusan penyedia, Chat, Profil, ataupun desain global. Beranda hanya menerima tautan masuk tambahan; isi dan alur lamanya tetap dipertahankan.
