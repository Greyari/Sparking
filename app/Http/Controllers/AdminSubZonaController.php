<?php

namespace App\Http\Controllers;

use App\Models\SubZona;
use App\Models\Zona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminSubZonaController extends Controller
{
    public function index(Request $request)
    {
        $zonas   = Zona::all();
        $zonaId  = $request->zona_id; // diperlukan view untuk pre-select dropdown

        $query = SubZona::with('zona');

        if ($zonaId) {
            $query->where('zona_id', $zonaId);
            $subzonas = $query->get();
        } else {
            $subzonas = null;
        }

        return view('admin.manageSubzona', compact('subzonas', 'zonas', 'zonaId'), [
            'title' => 'ManageSubZona',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'zona_id'      => 'required|exists:zona,id',
            'nama_subzona' => 'required|unique:subzona,nama_subzona',
            'camera_id'    => 'required|integer',
            'foto'         => 'required|image|max:5000',
        ], [
            'zona_id.required'      => 'Zona wajib dipilih.',
            'zona_id.exists'        => 'Zona yang dipilih tidak valid.',
            'nama_subzona.required' => 'Nama Subzona wajib diisi.',
            'nama_subzona.unique'   => 'Nama Subzona sudah terdaftar.',
            'camera_id.required'    => 'Kamera ID wajib diisi.',
            'camera_id.integer'     => 'Kamera ID harus berupa angka.',
            'foto.required'         => 'Foto area Sub-Zona wajib diisi.',
            'foto.image'            => 'File yang diunggah harus berupa gambar.',
            'foto.max'              => 'Ukuran gambar tidak boleh lebih dari 5MB.',
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('datafoto', 'public');
        }

        SubZona::create($validated);

        return redirect()->back()->with('success', 'Subzona berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $subzona = SubZona::findOrFail($id);

        $validated = $request->validate([
            'camera_id' => 'required|integer',
            'foto'      => 'nullable|image|max:5000',
        ], [
            'camera_id.required' => 'Kamera ID wajib diisi.',
            'camera_id.integer'  => 'Kamera ID harus berupa angka.',
            'foto.image'         => 'File yang diunggah harus berupa gambar.',
            'foto.max'           => 'Ukuran gambar tidak boleh lebih dari 5MB.',
        ]);

        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($subzona->foto) {
                Storage::disk('public')->delete($subzona->foto);
            }
            $validated['foto'] = $request->file('foto')->store('datafoto', 'public');
        }

        $subzona->update($validated);

        return redirect()->back()->with('success', 'Subzona berhasil diupdate.');
    }

    public function destroy($id)
    {
        $subzona = SubZona::findOrFail($id); // Fix: huruf kapital konsisten

        // Hapus foto via Storage (konsisten dengan cara penyimpanan)
        if ($subzona->foto) {
            Storage::disk('public')->delete($subzona->foto);
        }

        $subzona->delete();

        return redirect()->back()->with('success', 'Subzona berhasil dihapus.');
    }
}