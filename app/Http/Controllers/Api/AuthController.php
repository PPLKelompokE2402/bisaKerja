<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:pengguna,email'],
            'kata_sandi' => ['required', 'string', 'min:8'],
            'nomor_telepon' => ['nullable', 'string', 'max:20'],
            'role' => [
                'required',
                Rule::in(['pencari_kerja', 'penyedia_pekerjaan']),
            ],
        ]);

        $user = User::create([
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'kata_sandi' => Hash::make($validated['kata_sandi']),
            'nomor_telepon' => $validated['nomor_telepon'] ?? null,
            'role' => $validated['role'],
            'status_akun' => 'menunggu_verifikasi',
        ]);

        return response()->json([
            'message' => 'Registrasi berhasil. Akun Anda menunggu verifikasi Admin.',
            'user' => [
                'id_pengguna' => $user->id_pengguna,
                'nama' => $user->nama,
                'email' => $user->email,
                'role' => $user->role,
                'status_akun' => $user->status_akun,
            ],
        ], 201);
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'kata_sandi' => ['required', 'string'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user || ! Hash::check(
            $validated['kata_sandi'],
            $user->kata_sandi
        )) {
            return response()->json([
                'message' => 'Email atau kata sandi salah.',
            ], 401);
        }

        if ($user->status_akun === 'ditangguhkan'
            || $user->status_akun === 'ditolak') {
            return response()->json([
                'message' => 'Akun Anda tidak dapat digunakan. Silakan hubungi Admin.',
                'status_akun' => $user->status_akun,
            ], 403);
        }

        $token = $user->createToken('bisaKerja-token')->plainTextToken;

        return response()->json([
            'message' => $user->status_akun === 'menunggu_verifikasi'
                ? 'Login berhasil. Akun Anda masih menunggu verifikasi Admin.'
                : 'Login berhasil.',
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id_pengguna' => $user->id_pengguna,
                'nama' => $user->nama,
                'email' => $user->email,
                'role' => $user->role,
                'status_akun' => $user->status_akun,
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout berhasil.',
        ]);
    }

    public function me(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'user' => [
                'id_pengguna' => $user->id_pengguna,
                'nama' => $user->nama,
                'email' => $user->email,
                'nomor_telepon' => $user->nomor_telepon,
                'role' => $user->role,
                'status_akun' => $user->status_akun,
            ],
        ]);
    }
}
