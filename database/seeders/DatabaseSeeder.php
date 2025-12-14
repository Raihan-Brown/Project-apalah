<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Berita;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // 1. Bikin User Admin Dulu (Penting buat user_id)
        $user = User::create([
            'name' => 'Admin Sekolah',
            'email' => 'admin@sekolah.com',
            'password' => Hash::make('password123'),
        ]);

        // 2. Bikin 3 Contoh Berita
        Berita::create([
            'user_id' => $user->id,
            'judul' => 'Selamat Datang di Aplikasi Baru',
            'slug' => 'selamat-datang-di-aplikasi-baru',
            'konten' => 'Ini adalah konten berita percobaan untuk tes Flutter.',
            'tanggal' => date('Y-m-d'),
            // 'galeri_id' => null, // Biarin null dulu kalau belum ada gambar
        ]);

        Berita::create([
            'user_id' => $user->id,
            'judul' => 'Prestasi Siswa Juara Coding',
            'slug' => 'prestasi-siswa-juara-coding',
            'konten' => 'Siswa kita berhasil juara 1 lomba coding tingkat nasional.',
            'tanggal' => date('Y-m-d'),
        ]);

        Berita::create([
            'user_id' => $user->id,
            'judul' => 'Jadwal Libur Semester',
            'slug' => 'jadwal-libur-semester',
            'konten' => 'Libur semester akan dimulai minggu depan.',
            'tanggal' => date('Y-m-d'),
        ]);
    }
}