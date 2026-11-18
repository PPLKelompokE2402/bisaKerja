<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:150', 'unique:pengguna,email'],
            'username' => ['required', 'string', 'max:50', 'unique:pengguna,username', 'alpha_dash'],
            'kata_sandi' => ['required', 'string', 'min:8'],
            'role' => ['required', 'in:pencari_kerja,penyedia_pekerjaan'],
        ]);

        $user = User::create([
            'email' => $validated['email'],
            'username' => $validated['username'],
            'kata_sandi' => Hash::make($validated['kata_sandi']),
            'role' => $validated['role'],
            'status_akun' => 'belum_lengkap',
        ]);

        return response()->json([
            'message' => 'Registrasi berhasil. Silakan login dan lengkapi profil Anda.',
            'user' => [
                'id_pengguna' => $user->id_pengguna,
                'username' => $user->username,
                'email' => $user->email,
                'role' => $user->role,
                'status_akun' => $user->status_akun,
            ],
        ], 201);
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'login' => ['required', 'string'],
            'kata_sandi' => ['required', 'string'],
        ]);

        $identifier = $validated['login'];

        $user = User::where('email', $identifier)
            ->orWhere('username', $identifier)
            ->first();

        if (! $user || ! Hash::check(
            $validated['kata_sandi'],
            $user->kata_sandi
        )) {
            return response()->json([
                'message' => 'Email/username atau kata sandi salah.',
            ], 401);
        }

        if (
            $user->status_akun === 'ditangguhkan' ||
            $user->status_akun === 'ditolak'
        ) {
            return response()->json([
                'message' => 'Akun Anda tidak dapat digunakan. Silakan hubungi Admin.',
                'status_akun' => $user->status_akun,
            ], 403);
        }

        $token = $user->createToken('bisaKerja-token')->plainTextToken;

        return response()->json([
            'message' => $user->status_akun === 'belum_lengkap'
                ? 'Login berhasil. Silakan lengkapi profil Anda.'
                : ($user->status_akun === 'menunggu_verifikasi'
                    ? 'Login berhasil. Akun Anda menunggu verifikasi Admin.'
                    : 'Login berhasil.'),
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id_pengguna' => $user->id_pengguna,
                'nama' => $user->nama,
                'username' => $user->username,
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
                'username' => $user->username,
                'email' => $user->email,
                'nomor_telepon' => $user->nomor_telepon,
                'role' => $user->role,
                'status_akun' => $user->status_akun,
            ],
        ]);
    }
}