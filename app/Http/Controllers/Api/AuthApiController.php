<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthApiController extends Controller
{
    public function login(Request $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau Password salah'
            ], 401);
        }

        $user = User::where('email', $request['email'])->firstOrFail();
        
        // Hapus token lama biar gak numpuk (opsional)
        $user->tokens()->delete();
        
        // Buat token baru
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login Berhasil',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logout berhasil']);
    }
    
    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    public function register(Request $request)
    {
        // 1. Cek Validasi Input
        $validator = \Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users', // Pastikan email belum dipake
            'password' => 'required|string|min:6', // Password minimal 6 angka/huruf
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi Gagal',
                'errors' => $validator->errors() // Ini bakal ngasih tau salahnya dimana
            ], 400);
        }

        // 2. Logic Penentuan Role (User vs Admin)
        $role = 'user'; // Default jadi user biasa (Siswa)
        
        // Cek kalau dia masukin kode rahasia yang bener
        if ($request->has('kode_rahasia') && $request->kode_rahasia === 'GURU_SATAP_2025') {
            $role = 'admin';
        }

        // 3. Simpan ke Database
        try {
            $user = \App\Models\User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => \Hash::make($request->password),
                'role' => $role, // Masukin role hasil logic di atas
            ]);

            // 4. Bikin Token biar langsung login
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Register Berhasil! Welcome ' . $role,
                'data' => $user,
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal Simpan Database: ' . $e->getMessage()
            ], 500);
        }
    }
}