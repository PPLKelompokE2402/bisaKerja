<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class ProfileController extends Controller
{
    // Menampilkan halaman profil pengguna
    public function edit(Request $request)
    {
        $user = User::firstOrFail();

        return view('profile.edit', compact('user'));
    }

    // Memperbarui biodata dan CV pengguna
    public function update(Request $request)
    {
        $user = User::firstOrFail();

        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'nomor_telepon' => 'required|string|max:20',
            'alamat' => 'required|string',
            'jenis_kelamin' => 'nullable|in:laki-laki,perempuan',
            'deskripsi_diri' => 'nullable|string',
            'keahlian' => 'required|string',
            'pengalaman_kerja' => 'required|string',

            'foto_profil' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'cv' => 'nullable|file|mimes:pdf|max:5120',
        ]);

        // Upload foto profil
        if ($request->hasFile('foto_profil')) {

            if ($user->foto_profil) {
                Storage::disk('public')->delete($user->foto_profil);
            }

            $validated['foto_profil'] = $request
                ->file('foto_profil')
                ->store('foto-profil', 'public');
        }

        // Upload CV
        if ($request->hasFile('cv')) {

            if ($user->cv) {
                Storage::disk('public')->delete($user->cv);
            }

            $validated['cv'] = $request
                ->file('cv')
                ->store('cv', 'public');
        }

        // Simpan perubahan biodata
        $user->update($validated);

        return redirect()
            ->route('profile.edit')
            ->with('success', 'Profil berhasil diperbarui!');
    }
}
