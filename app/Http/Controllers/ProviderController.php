<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProviderController extends Controller
{
    public function store(Request $request)
    {
        // Ambil pengguna yang login melalui Sanctum
        $user = $request->user();

        if (!$user || $user->role !== 'penyedia_pekerjaan') {
            return response()->json([
                'message' => 'Akses hanya untuk penyedia pekerjaan.'
            ], 403);
        }

        if ($user->status_akun === 'ditangguhkan') {
            return response()->json([
                'message' => 'Akun Anda sedang ditangguhkan.'
            ], 403);
        }

        // Validasi biodata dan file
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'nomor_telepon' => ['required', 'string', 'max:20'],
            'alamat' => ['required', 'string', 'max:255'],
            'jenis_kelamin' => ['nullable', 'in:Laki-laki,Perempuan'],
            'deskripsi_diri' => ['nullable', 'string', 'max:1000'],

            'foto_profil' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png',
                'max:2048',
            ],

            'jenis_pemberi' => [
                'required',
                'in:Individu,Rumah tangga,Usaha/UMKM',
            ],

            'nama_usaha' => [
                'required_if:jenis_pemberi,Usaha/UMKM',
                'nullable',
                'string',
                'max:255',
            ],

            // Dokumen wajib jika pengguna belum memiliki dokumen sebelumnya
            'dokumen_verifikasi' => [
                $user->dokumen_verifikasi ? 'nullable' : 'required',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:5120',
            ],
        ]);

        $oldFoto = $user->foto_profil;
        $oldDokumen = $user->dokumen_verifikasi;

        // Simpan foto baru jika ada
        if ($request->hasFile('foto_profil')) {
            $validated['foto_profil'] = $request
                ->file('foto_profil')
                ->store('foto-penyedia', 'public');
        }

        // Simpan dokumen baru jika ada
        if ($request->hasFile('dokumen_verifikasi')) {
            $validated['dokumen_verifikasi'] = $request
                ->file('dokumen_verifikasi')
                ->store('dokumen-penyedia', 'public');
        }

        // Simpan biodata
        $user->fill([
            'nama' => $validated['nama'],
            'nomor_telepon' => $validated['nomor_telepon'],
            'alamat' => $validated['alamat'],
            'jenis_kelamin' => $validated['jenis_kelamin'] ?? null,
            'deskripsi_diri' => $validated['deskripsi_diri'] ?? null,
            'jenis_pemberi' => $validated['jenis_pemberi'],
            'nama_usaha' => $validated['nama_usaha'] ?? null,

            'foto_profil' => $validated['foto_profil']
                ?? $oldFoto,

            'dokumen_verifikasi' => $validated['dokumen_verifikasi']
                ?? $oldDokumen,

            'status_akun' => 'menunggu_verifikasi',
        ]);

        try {
            $user->save();
        } catch (\Throwable $error) {
            // Hapus file baru jika penyimpanan database gagal
            if (isset($validated['foto_profil']) &&
                $validated['foto_profil'] !== $oldFoto) {
                Storage::disk('public')->delete($validated['foto_profil']);
            }

            if (isset($validated['dokumen_verifikasi']) &&
                $validated['dokumen_verifikasi'] !== $oldDokumen) {
                Storage::disk('public')->delete($validated['dokumen_verifikasi']);
            }

            throw $error;
        }

        // Hapus file lama setelah data baru berhasil disimpan
        if (isset($validated['foto_profil']) &&
            $oldFoto &&
            $oldFoto !== $validated['foto_profil']) {
            Storage::disk('public')->delete($oldFoto);
        }

        if (isset($validated['dokumen_verifikasi']) &&
            $oldDokumen &&
            $oldDokumen !== $validated['dokumen_verifikasi']) {
            Storage::disk('public')->delete($oldDokumen);
        }

        return response()->json([
            'message' => 'Biodata berhasil disimpan. Akun Anda sedang menunggu verifikasi Admin.',
            'user' => [
                'id_pengguna' => $user->id_pengguna,
                'nama' => $user->nama,
                'role' => $user->role,
                'status_akun' => $user->status_akun,
                'foto_profil' => $user->foto_profil,
                'dokumen_verifikasi' => $user->dokumen_verifikasi,
            ],
        ]);
    }
}