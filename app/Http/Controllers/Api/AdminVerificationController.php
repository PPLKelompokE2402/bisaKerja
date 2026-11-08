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
