<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProviderController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'no_hp' => 'required|string|max:20',
            'lokasi' => 'required|string|max:255',
            'jenis_kelamin' => 'nullable|in:Laki-laki,Perempuan',
            'deskripsi' => 'nullable|string|max:1000',
            'foto_profil' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'jenis_penyedia' => 'required|in:Individu,Rumah Tangga,Usaha/UKM',
            'nama_usaha' => 'nullable|string|max:255',
            'dokumen_verifikasi' => 'nullable|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        if ($request->hasFile('foto_profil')) {
            $validated['foto_profil'] = $request
                ->file('foto_profil')
                ->store('foto-penyedia', 'public');
        }

        if ($request->hasFile('dokumen_verifikasi')) {
            $validated['dokumen_verifikasi'] = $request
                ->file('dokumen_verifikasi')
                ->store('dokumen-penyedia', 'public');
        }

        return redirect()
            ->route('provider.biodata')
            ->with('success', 'Pendaftaran berhasil!');
    }
}