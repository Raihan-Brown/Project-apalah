<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Berita;
use App\Models\Galeri;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BeritaController extends Controller
{
    public function index()
    {
        $beritas = Berita::with(['user', 'galeri'])->latest()->paginate(10);

        return view('admin.berita.index', [
            'beritas' => $beritas,
        ]);
    }

    public function create()
    {
        $galeris = Galeri::all();

        return view('admin.berita.create', [
            'galeris' => $galeris,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'konten' => 'required',
            'galeri_id' => 'nullable|exists:galeris,id',
        ]);

        $validated['slug'] = Str::slug($validated['judul']);
        $validated['user_id'] = auth()->id();
        $validated['tanggal'] = now()->format('Y-m-d');

        Berita::create($validated);

        return redirect()->route('admin.berita.index')
            ->with('success', 'Berita berhasil ditambahkan.');
    }

    public function edit(Berita $beritum)
    {
        $galeris = Galeri::all();

        return view('admin.berita.edit', [
            'berita' => $beritum,
            'galeris' => $galeris,
        ]);
    }

    public function update(Request $request, Berita $beritum)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'konten' => 'required',
            'galeri_id' => 'nullable|exists:galeris,id',
        ]);

        $validated['slug'] = Str::slug($validated['judul']);
        $validated['tanggal'] = now()->format('Y-m-d');

        $beritum->update($validated);

        return redirect()->route('admin.berita.index')
            ->with('success', 'Berita berhasil diperbarui.');
    }

    public function destroy(Berita $beritum)
    {
        $beritum->delete();

        return redirect()->route('admin.berita.index')
            ->with('success', 'Berita berhasil dihapus.');
    }
}
