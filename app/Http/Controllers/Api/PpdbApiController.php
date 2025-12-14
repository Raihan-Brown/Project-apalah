<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ppdb;
use Illuminate\Support\Facades\Validator;

class PpdbApiController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validasi Manual (Biar bisa return JSON kalau error)
        $validator = Validator::make($request->all(), [
            // DATA DIRI
            'nama_lengkap' => 'required|string|max:255',
            'nama_panggilan' => 'required|string|max:255',
            'tempat_tanggal_lahir' => 'required|string|max:255',
            'jenis_kelamin' => 'required|string|max:20',
            'agama' => 'required|string|max:50',
            'no_hp' => 'required|string|max:20',
            'alamat_rumah' => 'required|string',
        
            // DATA SEKOLAH
            'asal_sekolah' => 'required|string|max:255',
            'alamat_sekolah' => 'required|string|max:255',
        
            // AYAH
            'nama_ayah' => 'required|string|max:255',
            'telepon_ayah' => 'required|string|max:20',
            'pekerjaan_ayah' => 'required|string|max:255',
            'alamat_ayah' => 'required|string',
        
            // IBU
            'nama_ibu' => 'required|string|max:255',
            'telepon_ibu' => 'required|string|max:20',
            'pekerjaan_ibu' => 'required|string|max:255',
            'alamat_ibu' => 'required|string',
        
            // WALI (TIDAK WAJIB)
            'nama_wali' => 'nullable|string|max:255',
            'telepon_wali' => 'nullable|string|max:20',
            'alamat_wali' => 'nullable|string',
        ]);

        // 2. Kalau Validasi Gagal
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi Gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        // 3. Simpan Data
        try {
            // Kita set user_id = 1 (Admin/System) kalau user di HP belum login
            // Atau lu bisa hapus 'user_id' kalau di database boleh NULL
            $data = $validator->validated();
            $data['user_id'] = $request->user() ? $request->user()->id : 1; 

            $ppdb = Ppdb::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Formulir PPDB berhasil dikirim!',
                'data'    => $ppdb
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data server error',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}