<?php

namespace App\Http\Controllers;

use App\Models\Zona;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

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
        try {
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
                    ['folder' => 'datafoto', 'timeout' => 120]
                );
                $validated['fotozona'] = $result['secure_url'];
            }

            Zona::create($validated);

            return redirect()->back()->with('success', 'Zona berhasil ditambahkan.');

        } catch (ValidationException $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', implode(' ', $e->validator->errors()->all()));

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $zona = Zona::findOrFail($id);

        try {
            $validated = $request->validate([
                'keterangan' => 'required|string',
                'fotozona'   => 'nullable|image|max:5000',
            ], [
                'keterangan.required' => 'Keterangan Zona wajib diisi.',
                'fotozona.image'      => 'File yang diunggah harus berupa gambar.',
                'fotozona.max'        => 'Ukuran gambar tidak boleh lebih dari 5MB.',
            ]);

            if ($request->hasFile('fotozona')) {
                // Hapus foto lama dari Cloudinary
                if ($zona->fotozona) {
                    $urlPath  = parse_url($zona->fotozona, PHP_URL_PATH);
                    $publicId = preg_replace('/\.[^.]+$/', '',
                        implode('/', array_slice(explode('/', $urlPath), 5))
                    );
                    cloudinary()->uploadApi()->destroy($publicId);
                }

                // Upload foto baru
                $result = cloudinary()->uploadApi()->upload(
                    $request->file('fotozona')->getRealPath(),
                    ['folder' => 'datafoto', 'timeout' => 120]
                );
                $validated['fotozona'] = $result['secure_url'];
            }

            $zona->update($validated);

            return redirect()->back()->with('success', 'Keterangan zona berhasil diupdate.');

        } catch (ValidationException $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', implode(' ', $e->validator->errors()->all()));

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $zona = Zona::findOrFail($id);

            if ($zona->fotozona) {
                $urlPath  = parse_url($zona->fotozona, PHP_URL_PATH);
                $publicId = preg_replace('/\.[^.]+$/', '',
                    implode('/', array_slice(explode('/', $urlPath), 5))
                );
                cloudinary()->uploadApi()->destroy($publicId);
            }

            $zona->delete();

            return redirect()->back()->with('success', 'Zona berhasil dihapus.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus zona: ' . $e->getMessage());
        }
    }
}
