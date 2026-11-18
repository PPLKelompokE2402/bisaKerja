<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProviderController extends Controller
{
    public function store(Request $request)
    {
        // Ambil pengguna yang sedang login melalui Sanctum
        $user = $request->user();

        // Pastikan hanya penyedia pekerjaan yang dapat mengakses
        if ($user->role !== 'penyedia_pekerjaan') {
            return response()->json([
                'message' => 'Akses hanya untuk penyedia pekerjaan.'
            ], 403);
        }

        if ($user->status_akun === 'ditangguhkan') {
            return response()->json([
                'message' => 'Akun Anda sedang ditangguhkan.'
            ], 403);
        }

        // Validasi biodata
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nomor_telepon' => 'required|string|max:20',
            'alamat' => 'required|string|max:255',
            'jenis_kelamin' => 'nullable|in:Laki-laki,Perempuan',
            'deskripsi_diri' => 'nullable|string|max:1000',

            'foto_profil' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',

            'jenis_pemberi' => 'required|in:Individu,Rumah tangga,Usaha/UMKM',

            'nama_usaha' => 'required_if:jenis_pemberi,Usaha/UMKM|nullable|string|max:255',

            'dokumen_verifikasi' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        // Simpan foto profil
        if ($request->hasFile('foto_profil')) {

            if ($user->foto_profil) {
                Storage::disk('public')->delete($user->foto_profil);
            }

            $validated['foto_profil'] = $request
                ->file('foto_profil')
                ->store('foto-penyedia', 'public');
        }

        // Simpan dokumen verifikasi
        if ($request->hasFile('dokumen_verifikasi')) {

            if ($user->dokumen_verifikasi) {
                Storage::disk('public')->delete($user->dokumen_verifikasi);
            }

            $validated['dokumen_verifikasi'] = $request
                ->file('dokumen_verifikasi')
                ->store('dokumen-penyedia', 'public');
        }

        // Simpan biodata ke akun pengguna yang sedang login
        $user->fill([
            'nama' => $validated['nama'],
            'nomor_telepon' => $validated['nomor_telepon'],
            'alamat' => $validated['alamat'],
            'jenis_kelamin' => $validated['jenis_kelamin'] ?? null,
            'deskripsi_diri' => $validated['deskripsi_diri'] ?? null,
            'jenis_pemberi' => $validated['jenis_pemberi'],
            'nama_usaha' => $validated['nama_usaha'] ?? null,
            'foto_profil' => $validated['foto_profil'] ?? $user->foto_profil,
            'dokumen_verifikasi' => $validated['dokumen_verifikasi'],
            'status_akun' => 'menunggu_verifikasi',
        ]);

        $user->save();

        return response()->json([
            'message' => 'Biodata berhasil disimpan. Akun Anda sedang menunggu verifikasi Admin.',
            'user' => [
                'id_pengguna' => $user->id_pengguna,
                'nama' => $user->nama,
                'role' => $user->role,
                'status_akun' => $user->status_akun,
            ],
        ], 200);
    }
}