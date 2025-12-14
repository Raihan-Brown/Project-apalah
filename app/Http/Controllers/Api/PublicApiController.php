<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\Berita;
use App\Models\Guru;
use App\Models\Ekskul;
use App\Models\Galeri;
use App\Models\Kegiatan;
use App\Models\Prestasi;
use App\Models\Lokasi;

class PublicApiController extends Controller
{
    // ==========================================
    // 1. MODULE BERITA
    // ==========================================
    
    public function getBerita()
    {
        $data = Berita::latest()->paginate(10);
        
        $data->getCollection()->transform(function ($item) {
            if (!empty($item->image)) {
                if (str_contains($item->image, 'http')) {
                    $item->image_url = $item->image;
                } else {
                    $item->image_url = asset('storage/' . $item->image);
                }
            } else {
                $item->image_url = null;
            }
            return $item;
        });

        return response()->json([
            'success' => true,
            'message' => 'List Berita',
            'data'    => $data
        ]);
    }

    public function detailBerita($slug)
    {
        $item = Berita::where('slug', $slug)->first();
        if(!$item) return response()->json(['success'=>false, 'message'=>'Not Found'], 404);

        if (!empty($item->image)) {
            $item->image_url = str_contains($item->image, 'http') ? $item->image : asset('storage/' . $item->image);
        } else {
            $item->image_url = null;
        }

        return response()->json(['success' => true, 'data' => $item]);
    }

    public function storeBerita(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'judul' => 'required',
            'konten' => 'required',
            'image' => 'nullable|image|max:5120',
        ]);

        if ($validator->fails()) return response()->json($validator->errors(), 400);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('berita_images', 'public');
            $imagePath = asset('storage/' . $path);
        }

        $berita = DB::table('beritas')->insert([
            'user_id' => \Auth::id() ?? 1,
            'judul' => $request->judul,
            'slug' => Str::slug($request->judul . '-' . time()),
            'konten' => $request->konten,
            'image' => $imagePath, 
            'tanggal' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Berita Berhasil Disimpan']);
    }

    public function updateBerita(Request $request, $id) {
        $berita = Berita::find($id);
        if(!$berita) return response()->json(['success'=>false], 404);

        $berita->update([
            'judul' => $request->judul,
            'slug' => Str::slug($request->judul),
            'konten' => $request->konten,
        ]);

        return response()->json(['success' => true, 'message' => 'Berita Diupdate']);
    }

    public function deleteBerita($id) {
        $berita = Berita::find($id);
        if($berita) {
            $berita->delete();
            return response()->json(['success' => true, 'message' => 'Berita Dihapus']);
        }
        return response()->json(['success' => false], 404);
    }

    // ==========================================
    // 2. MODULE GALERI
    // ==========================================

    public function getGaleri()
    {
        $data = DB::table('galeries')->orderBy('created_at', 'desc')->get();
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function storeGaleri(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|max:5120', 
        ]);

        if ($validator->fails()) return response()->json($validator->errors(), 400);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('galeri_images', 'public');
            $imagePath = asset('storage/' . $path);
        }

        DB::table('galeries')->insert([
            'judul' => $request->judul ?? '', 
            'image' => $imagePath,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Foto Berhasil Diupload']);
    }

    public function deleteGaleri($id)
    {
        DB::table('galeries')->where('id', $id)->delete();
        return response()->json(['success' => true, 'message' => 'Foto Dihapus']);
    }

    // ==========================================
    // 3. MODULE GURU (YANG TADI DOUBLE)
    // ==========================================

    public function getGuru()
    {
        // Kita pakai yang urut Abjad (A-Z) biar rapi
        $guru = DB::table('gurus')->orderBy('nama', 'asc')->get();
        
        $guru->transform(function($item){
            $item->foto_url = $item->foto ? asset('storage/' . $item->foto) : null;
            return $item;
        });

        return response()->json(['success' => true, 'data' => $guru]);
    }

    public function storeGuru(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required',
            'mata_pelajaran' => 'required',
            'foto' => 'nullable|image|max:2048',
        ]);

        if ($validator->fails()) return response()->json($validator->errors(), 400);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('guru_images', 'public');
            $fotoPath = asset('storage/' . $path);
        }

        DB::table('gurus')->insert([
            'nama' => $request->nama,
            'nip' => $request->nip ?? '-',
            'mata_pelajaran' => $request->mata_pelajaran,
            'no_hp' => $request->no_hp ?? '-', 
            'foto' => $fotoPath,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Data Guru Disimpan']);
    }

    public function deleteGuru($id)
    {
        DB::table('gurus')->where('id', $id)->delete();
        return response()->json(['success' => true, 'message' => 'Data Guru Dihapus']);
    }

    // ==========================================
    // 4. MODULE LAINNYA (EKSKUL, DLL)
    // ==========================================

    public function getEkskul()
    {
        $ekskul = Ekskul::latest()->get()->map(function($item){
            $item->gambar_url = $item->gambar ? asset('storage/' . $item->gambar) : null;
            return $item;
        });
        return response()->json(['success' => true, 'data' => $ekskul]);
    }

    public function getKegiatan()
    {
        $data = Kegiatan::with('galeri')->latest()->paginate(10);
        $data->getCollection()->transform(function ($item) {
            $gambarPath = $item->galeri ? $item->galeri->gambar : null;
            $item->image_url = $gambarPath ? asset('storage/' . $gambarPath) : null;
            return $item;
        });
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function getPrestasi()
    {
        $data = Prestasi::latest()->paginate(10);
        $data->getCollection()->transform(function ($item) {
            $item->gambar_url = isset($item->gambar) ? asset('storage/' . $item->gambar) : null;
            return $item;
        });
        return response()->json(['success' => true, 'data' => $data]);
    }
    
    public function getLokasi()
    {
        $lokasi = Lokasi::first();
        return response()->json(['success' => true, 'data' => $lokasi]);
    }
}