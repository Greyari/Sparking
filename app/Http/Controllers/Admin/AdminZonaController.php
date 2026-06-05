<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
            ], [
                'nama_zona.required'  => 'Nama Zona wajib diisi.',
                'nama_zona.unique'    => 'Nama Zona sudah terdaftar.',
                'keterangan.required' => 'Keterangan Zona wajib diisi.',
            ]);


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
            ], [
                'keterangan.required' => 'Keterangan Zona wajib diisi.',
            ]);

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

            $zona->delete();

            return redirect()->back()->with('success', 'Zona berhasil dihapus.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus zona: ' . $e->getMessage());
        }
    }
}
