<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $this->jobSeeker($request),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $this->jobSeeker($request);

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'nomor_telepon' => ['required', 'string', 'max:20'],
            'alamat' => ['required', 'string', 'max:1000'],
            'jenis_kelamin' => ['nullable', 'in:laki-laki,perempuan'],
            'deskripsi_diri' => ['nullable', 'string', 'max:2000'],
            'keahlian' => ['required', 'string', 'max:2000'],
            'pengalaman_kerja' => ['required', 'string', 'max:2000'],
            'foto_profil' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'cv' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
        ]);

        $oldPhotoPath = $user->foto_profil;
        $oldCvPath = $user->cv;
        $newPhotoPath = null;
        $newCvPath = null;

        if ($request->hasFile('foto_profil')) {
            $newPhotoPath = $request->file('foto_profil')->store('foto-profil', 'public');

            if ($newPhotoPath === false) {
                throw new RuntimeException('Foto profil gagal disimpan. Silakan coba kembali.');
            }

            $validated['foto_profil'] = $newPhotoPath;
        }

        if ($request->hasFile('cv')) {
            $newCvPath = $request->file('cv')->store('cv', 'local');

            if ($newCvPath === false) {
                $this->deleteStoredFile('public', $newPhotoPath);

                throw new RuntimeException('CV gagal disimpan. Silakan coba kembali.');
            }

            $validated['cv'] = $newCvPath;
        }

        try {
            $user->update($validated);
        } catch (Throwable $exception) {
            $this->deleteStoredFile('public', $newPhotoPath);
            $this->deleteStoredFile('local', $newCvPath);

            throw $exception;
        }

        if ($newPhotoPath !== null && $oldPhotoPath !== null && str_starts_with($oldPhotoPath, 'foto-profil/')) {
            $this->deleteStoredFile('public', $oldPhotoPath);
        }

        if ($newCvPath !== null && $oldCvPath !== null && str_starts_with($oldCvPath, 'cv/')) {
            $this->deleteStoredFile('local', $oldCvPath);
            $this->deleteStoredFile('public', $oldCvPath);
        }

        return redirect()
            ->route('profile.edit')
            ->with('success', 'Profil berhasil diperbarui.');
    }

    private function jobSeeker(Request $request): User
    {
        $user = $request->user();

        abort_unless($user instanceof User && $user->role === 'pencari_kerja', 403);

        return $user;
    }

    private function deleteStoredFile(string $disk, ?string $path): void
    {
        if ($path !== null && ! Storage::disk($disk)->delete($path)) {
            Log::warning('Gagal menghapus berkas profil.', [
                'disk' => $disk,
                'path' => $path,
            ]);
        }
    }
}
