<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Validation\Rule;

class AdminVerificationController extends Controller
{
        public function pendingUsers()
    {
        $users = User::where(
            'status_akun',
            'menunggu_verifikasi'
        )
        ->whereIn('role', [
            'pencari_kerja',
            'penyedia_pekerjaan',
        ])
        ->select(
            'id_pengguna',
            'nama',
            'email',
            'role',
            'status_akun',
            'dibuat_pada'
        )
        ->orderBy('dibuat_pada', 'asc')
        ->get();

        return response()->json([
            'message' => 'Daftar akun yang menunggu verifikasi.',
            'data' => $users,
        ]);
    }

   public function allUsers(Request $request)
{
    // Query dasar untuk seluruh pengguna (tanpa admin)
    $baseQuery = User::query()
        ->whereIn('role', [
            'pencari_kerja',
            'penyedia_pekerjaan',
        ]);

    // Menghitung statistik seluruh pengguna
    $statistics = [
        'total_pengguna' => (clone $baseQuery)->count(),

        'akun_aktif' => (clone $baseQuery)
            ->where('status_akun', 'aktif')
            ->count(),

        'akun_ditangguhkan' => (clone $baseQuery)
            ->where('status_akun', 'ditangguhkan')
            ->count(),
    ];

    // Query untuk daftar pengguna
    $query = clone $baseQuery;

    // Pencarian nama atau email
    if ($request->filled('search')) {
        $search = $request->input('search');

        $query->where(function ($q) use ($search) {
            $q->where('nama', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%");
        });
    }

    // Filter berdasarkan role
    if ($request->filled('role')) {
        $request->validate([
            'role' => [
                Rule::in([
                    'pencari_kerja',
                    'penyedia_pekerjaan',
                ]),
            ],
        ]);

        $query->where('role', $request->input('role'));
    }

    // Filter berdasarkan status akun
    if ($request->filled('status')) {
        $request->validate([
            'status' => [
                Rule::in([
                    'aktif',
                    'menunggu_verifikasi',
                    'ditolak',
                    'ditangguhkan',
                ]),
            ],
        ]);

        $query->where('status_akun', $request->input('status'));
    }

    $users = $query
        ->select(
            'id_pengguna',
            'nama',
            'email',
            'role',
            'status_akun',
            'dibuat_pada'
        )
        ->orderBy('dibuat_pada', 'desc')
        ->paginate(10);

    return response()->json([
        'message' => 'Daftar pengguna berhasil diambil.',
        'statistics' => $statistics,
        'data' => $users,
    ]);
}

public function updateUserStatus(Request $request, int $id)
{
    $validated = $request->validate([
        'status_akun' => [
            'required',
            Rule::in(['aktif', 'ditangguhkan']),
        ],
    ]);

    $user = User::where('id_pengguna', $id)
        ->whereIn('role', [
            'pencari_kerja',
            'penyedia_pekerjaan',
        ])
        ->first();

    if (! $user) {
        return response()->json([
            'message' => 'Pengguna tidak ditemukan.',
        ], 404);
    }

    // Hanya akun aktif yang dapat ditangguhkan,
    // dan hanya akun ditangguhkan yang dapat diaktifkan kembali.
    $allowed = (
        $user->status_akun === 'aktif'
        && $validated['status_akun'] === 'ditangguhkan'
    ) || (
        $user->status_akun === 'ditangguhkan'
        && $validated['status_akun'] === 'aktif'
    );

    if (! $allowed) {
        return response()->json([
            'message' => 'Perubahan status akun tidak diperbolehkan.',
        ], 422);
    }

    $user->status_akun = $validated['status_akun'];
    $user->save();

    // Cabut token supaya akun yang ditangguhkan
    // tidak bisa terus menggunakan sesi sebelumnya.
    if ($user->status_akun === 'ditangguhkan') {
        $user->tokens()->delete();
    }

    return response()->json([
        'message' => $user->status_akun === 'ditangguhkan'
            ? 'Akun berhasil ditangguhkan.'
            : 'Akun berhasil diaktifkan kembali.',
        'data' => [
            'id_pengguna' => $user->id_pengguna,
            'nama' => $user->nama,
            'status_akun' => $user->status_akun,
        ],
    ]);
}

    public function showUser(int $id)
{
    $user = User::where('id_pengguna', $id)
        ->whereIn('role', [
            'pencari_kerja',
            'penyedia_pekerjaan',
        ])
        ->select(
            'id_pengguna',
            'nama',
            'email',
            'nomor_telepon',
            'role',
            'status_akun',
            'alamat',
            'tanggal_lahir',
            'jenis_kelamin',
            'deskripsi_diri',
            'cv',
            'foto_profil',
            'keahlian',
            'pengalaman_kerja',
            'jenis_pemberi',
            'tanggal_verifikasi',
            'id_admin_verifikator',
            'catatan_verifikasi',
            'dibuat_pada'
        )
        ->first();

    if (! $user) {
        return response()->json([
            'message' => 'Data pengguna tidak ditemukan.',
        ], 404);
    }

    return response()->json([
        'message' => 'Detail pengguna berhasil diambil.',
        'data' => $user,
    ]);
}

    public function verifyUser(Request $request, int $id)
    {
        $validated = $request->validate([
            'keputusan' => [
                'required',
                Rule::in(['aktif', 'ditolak']),
            ],
            'catatan_verifikasi' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        $user = User::where('id_pengguna', $id)
            ->whereIn('role', [
                'pencari_kerja',
                'penyedia_pekerjaan',
            ])
            ->where('status_akun', 'menunggu_verifikasi')
            ->first();

        if (! $user) {
            return response()->json([
                'message' => 'Akun tidak ditemukan atau sudah diverifikasi.',
            ], 404);
        }

        $user->status_akun = $validated['keputusan'];
        $user->tanggal_verifikasi = now();
        $user->id_admin_verifikator = $request->user()->id_pengguna;
        $user->catatan_verifikasi =
            $validated['catatan_verifikasi'] ?? null;

        $user->save();

        return response()->json([
            'message' => $validated['keputusan'] === 'aktif'
                ? 'Akun berhasil disetujui.'
                : 'Akun berhasil ditolak.',
            'data' => [
                'id_pengguna' => $user->id_pengguna,
                'nama' => $user->nama,
                'role' => $user->role,
                'status_akun' => $user->status_akun,
                'tanggal_verifikasi' => $user->tanggal_verifikasi,
            ],
        ]);
    }
}
