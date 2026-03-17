<?php

namespace App\Http\Controllers;

use App\Models\Zona;
use Illuminate\Http\Request;

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
            $result = cloudinary()->uploadApi()->upload(
                $request->file('fotozona')->getRealPath(),
                ['folder' => 'datafoto']
            );
            $validated['fotozona'] = $result['secure_url'];
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
            if ($zona->fotozona) {
                $urlPath = parse_url($zona->fotozona, PHP_URL_PATH);
                $publicId = preg_replace('/\.[^.]+$/', '',
                    implode('/', array_slice(explode('/', $urlPath), 5))
                );
                cloudinary()->uploadApi()->destroy($publicId);
            }

            $result = cloudinary()->uploadApi()->upload(
                $request->file('fotozona')->getRealPath(),
                ['folder' => 'datafoto']
            );
            $validated['fotozona'] = $result['secure_url'];
        }

        $zona->update($validated);

        return redirect()->back()->with('success', 'Keterangan zona berhasil diupdate.');
    }

    public function destroy($id)
    {
        $zona = Zona::findOrFail($id);

        if ($zona->fotozona) {
            $urlPath = parse_url($zona->fotozona, PHP_URL_PATH);
            $publicId = preg_replace('/\.[^.]+$/', '',
                implode('/', array_slice(explode('/', $urlPath), 5))
            );
            cloudinary()->uploadApi()->destroy($publicId);
        }

        $zona->delete();

        return redirect()->back()->with('success', 'Zona berhasil dihapus.');
    }
}
