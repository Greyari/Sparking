<?php

namespace App\Http\Controllers;

use App\Models\SubZona;
use App\Models\Zona;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AdminSubZonaController extends Controller
{
    public function index(Request $request)
    {
        $zonas   = Zona::all();
        $zonaId  = $request->zona_id;

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
        try {
            $validated = $request->validate([
                'zona_id'      => 'required|exists:zona,id',
                'nama_subzona' => 'required|unique:subzona,nama_subzona',
                'camera_id'    => 'required|integer',
            ], [
                'zona_id.required'      => 'Zona wajib dipilih.',
                'zona_id.exists'        => 'Zona yang dipilih tidak valid.',
                'nama_subzona.required' => 'Nama Subzona wajib diisi.',
                'nama_subzona.unique'   => 'Nama Subzona sudah terdaftar.',
                'camera_id.required'    => 'Kamera ID wajib diisi.',
                'camera_id.integer'     => 'Kamera ID harus berupa angka.',
            ]);

            SubZona::create($validated);

            return redirect()->back()->with('success', 'Subzona berhasil ditambahkan.');

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
        $subzona = SubZona::findOrFail($id);

        try {
            $validated = $request->validate([
                'camera_id' => 'required|integer',
            ], [
                'camera_id.required' => 'Kamera ID wajib diisi.',
                'camera_id.integer'  => 'Kamera ID harus berupa angka.',
            ]);

            $subzona->update($validated);

            return redirect()->back()->with('success', 'Subzona berhasil diupdate.');

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
            $subzona = SubZona::findOrFail($id);
            $subzona->delete();
            return redirect()->back()->with('success', 'Subzona berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus subzona: ' . $e->getMessage());
        }
    }
}
