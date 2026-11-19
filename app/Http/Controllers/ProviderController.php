<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProviderController extends Controller
{
    public function profile(Request $request): View
    {
        $user = $this->authenticatedProvider($request);

        if (! $this->hasCompleteBiodata($user)) {
            return view('auth.biodata-penyedia', [
                'user' => $user,
                'editing' => false,
            ]);
        }

        $statusVerifikasi = match ($user->status_akun) {
            'aktif' => 'Aktif / terverifikasi',
            'menunggu_verifikasi' => 'Menunggu verifikasi',
            'belum_lengkap' => 'Biodata belum lengkap',
            'ditangguhkan' => 'Ditangguhkan',
            'ditolak' => 'Ditolak',
            default => 'Status tidak diketahui',
        };

        return view('provider.profile', compact('user', 'statusVerifikasi'));
    }

    public function edit(Request $request): View
    {
        return view('auth.biodata-penyedia', [
            'user' => $this->authenticatedProvider($request),
            'editing' => true,
        ]);
    }

    public function create(): View
    {
        return view('auth.provider-biodata');
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        // Gunakan pengguna dari guard request saat ini.
        $user = $request->user();

        if (! $user || $user->role !== 'penyedia_pekerjaan') {
            return response()->json([
                'message' => 'Akses hanya untuk penyedia pekerjaan.',
            ], 403);
        }

        if ($user->status_akun === 'ditangguhkan') {
            return response()->json([
                'message' => 'Akun Anda sedang ditangguhkan.',
            ], 403);
        }

        // Validasi biodata dan file
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
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
        $statusAkun = $user->status_akun;

        if ($statusAkun !== 'aktif' || $request->hasFile('dokumen_verifikasi')) {
            $statusAkun = 'menunggu_verifikasi';
        }

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

            'status_akun' => $statusAkun,
        ]);

        try {
            $user->save();
        } catch (\Throwable $error) {
            // Hapus file baru jika penyimpanan database gagal
            if (
                isset($validated['foto_profil']) &&
                $validated['foto_profil'] !== $oldFoto
            ) {
                Storage::disk('public')->delete($validated['foto_profil']);
            }

            if (
                isset($validated['dokumen_verifikasi']) &&
                $validated['dokumen_verifikasi'] !== $oldDokumen
            ) {
                Storage::disk('public')->delete($validated['dokumen_verifikasi']);
            }

            throw $error;
        }

        // Hapus file lama setelah data baru berhasil disimpan
        if (
            isset($validated['foto_profil']) &&
            $oldFoto &&
            $oldFoto !== $validated['foto_profil']
        ) {
            Storage::disk('public')->delete($oldFoto);
        }

        if (
            isset($validated['dokumen_verifikasi']) &&
            $oldDokumen &&
            $oldDokumen !== $validated['dokumen_verifikasi']
        ) {
            Storage::disk('public')->delete($oldDokumen);
        }

        $message = $statusAkun === 'aktif'
            ? 'Biodata berhasil diperbarui.'
            : 'Biodata berhasil disimpan. Akun Anda sedang menunggu verifikasi Admin.';

        if ($request->routeIs('provider.profile.update')) {
            return redirect()->route('provider.biodata')->with('status', $message);
        }

        return response()->json([
            'message' => $message,
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

    private function authenticatedProvider(Request $request): User
    {
        $user = $request->user();

        abort_unless($user instanceof User, 403);

        return $user;
    }

    private function hasCompleteBiodata(User $user): bool
    {
        if ($user->status_akun === 'belum_lengkap') {
            return false;
        }

        foreach ([
            $user->nama,
            $user->nomor_telepon,
            $user->alamat,
            $user->jenis_pemberi,
            $user->dokumen_verifikasi,
        ] as $value) {
            if (blank($value)) {
                return false;
            }
        }

        return $user->jenis_pemberi !== 'Usaha/UMKM' || filled($user->nama_usaha);
    }
}
