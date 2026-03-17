<?php

namespace App\Http\Controllers;

use App\Models\Zona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminZonaController extends Controller
{
    public function index()
    {
        $zonas = Zona::all();

        return view('admin.manageZona', compact('zonas'), [
            'title' => 'ManageZona',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_zona'  => 'required|unique:zona,nama_zona',
            'keterangan' => 'required|string',
            'fotozona'   => 'required|image|max:5000',
        ], [
            'nama_zona.required'  => 'Nama Zona wajib diisi.',
            'nama_zona.unique'    => 'Nama Zona sudah terdaftar.',
            'keterangan.required' => 'Keterangan Zona wajib diisi.',
            'fotozona.required'   => 'Foto area zona wajib diisi.',
            'fotozona.image'      => 'File yang diunggah harus berupa gambar.',
            'fotozona.max'        => 'Ukuran gambar tidak boleh lebih dari 5MB.',
        ]);

        if ($request->hasFile('fotozona')) {
            $validated['fotozona'] = $request->file('fotozona')->store('datafoto', 'public');
        }

        Zona::create($validated);

        return redirect()->back()->with('success', 'Zona berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $zona = Zona::findOrFail($id);

        $validated = $request->validate([
            'keterangan' => 'required|string',
            'fotozona'   => 'nullable|image|max:5000',
        ], [
            'keterangan.required' => 'Keterangan Zona wajib diisi.',
            'fotozona.image'      => 'File yang diunggah harus berupa gambar.',
            'fotozona.max'        => 'Ukuran gambar tidak boleh lebih dari 5MB.',
        ]);

        if ($request->hasFile('fotozona')) {
            // Hapus foto lama jika ada
            if ($zona->fotozona) {
                Storage::disk('public')->delete($zona->fotozona);
            }
            $validated['fotozona'] = $request->file('fotozona')->store('datafoto', 'public');
        }

        $zona->update($validated);

        return redirect()->back()->with('success', 'Keterangan zona berhasil diupdate.');
    }

    public function destroy($id)
    {
        $zona = Zona::findOrFail($id);

        // Hapus foto via Storage (konsisten dengan cara penyimpanan)
        if ($zona->fotozona) {
            Storage::disk('public')->delete($zona->fotozona);
        }

        $zona->delete();

        return redirect()->back()->with('success', 'Zona berhasil dihapus.');
    }
}