<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProviderController extends Controller
{
    public function create()
    {
        return view('auth.provider-biodata');
    }

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
            'no_hp' => 'required|string|max:20',
            'lokasi' => 'required|string|max:255',
            'jenis_kelamin' => 'nullable|in:Laki-laki,Perempuan',
            'deskripsi_diri' => 'nullable|string|max:1000',

            'foto_profil' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'jenis_penyedia' => 'required|in:Individu,Rumah Tangga,Usaha/UKM',
            'nama_usaha' => 'nullable|string|max:255',
            'dokumen_verifikasi' => 'nullable|mimes:pdf,jpg,jpeg,png|max:5120',
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

        return redirect()
            ->route('provider.biodata')
            ->with('success', 'Pendaftaran berhasil!');
    }
}