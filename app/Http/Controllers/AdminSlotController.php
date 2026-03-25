<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use App\Models\Zona;
use App\Models\SubZona;
use App\Models\Slot;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AdminSlotController extends Controller
{
    public function index($zonaId = null)
    {
        // Ambil semua zona
        $zonas = Zona::all();

        // Zona yang dipilih, default ke zona pertama jika null
        $selectedZona = $zonaId ? Zona::findOrFail($zonaId) : $zonas->first();

        // Subzona berdasarkan zona yang dipilih
        $subzonas = $selectedZona ? SubZona::where('zona_id', $selectedZona->id)->get() : collect();

        // Subzona pertama sebagai default
        $selectedSubzona = $subzonas->first();

        // Slot berdasarkan subzona yang dipilih
        $slots = $selectedSubzona ? Slot::where('subzona_id', $selectedSubzona->id)->get() : collect();

        return view('admin.manageSlot', [
            'zonas' => $zonas,
            'subzonas' => $subzonas,
            'slots' => $slots,
            'selectedZona' => $selectedZona,
            'selectedSubzona' => $selectedSubzona,
            'title' => 'ManageSlot'
        ]);
    }

    public function getSlotsBySubzona($subzonaId)
    {
        // Ambil subzona yang dipilih
        $subzona = SubZona::findOrFail($subzonaId);

        // Ambil zona dari subzona
        $zona = $subzona->zona;

        // Ambil semua zona untuk dropdown
        $zonas = Zona::all();

        // Ambil semua subzona untuk zona yang dipilih
        $subzonas = SubZona::where('zona_id', $zona->id)->get();

        // Ambil slot untuk subzona yang dipilih
        $slots = Slot::where('subzona_id', $subzonaId)->get();

        // Kembalikan view dengan data yang dibutuhkan
        return view('admin.manageSlot', [
            'zonas' => $zonas,
            'subzonas' => $subzonas,
            'slots' => $slots,
            'selectedZona' => $zona,
            'selectedSubzona' => $subzona,
            'title' => 'ManageSlot'
        ]);
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'subzona_id' => 'required|exists:subzona,id',
                'nomor_slot' => [
                    'required',
                    'integer',
                    'min:1',
                    Rule::unique('slot', 'nomor_slot')->where(function ($query) use ($request) {
                        return $query->where('subzona_id', $request->subzona_id);
                    })
                ],
                'keterangan' => 'required|in:Tersedia,Terisi,Perbaikan',
            ]);

            Slot::create($validatedData);

            return redirect()->back()->with('success', 'Slot berhasil ditambahkan');

        } catch (ValidationException $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', implode(' ', $e->validator->errors()->all()));

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $slot = Slot::findOrFail($id);

        try {
            $validatedData = $request->validate([
                'nomor_slot' => [
                    'required',
                    'integer',
                    'min:1',
                    Rule::unique('slot', 'nomor_slot')->where(function ($query) use ($request) {
                        return $query->where('subzona_id', $request->subzona_id);
                    })->ignore($slot->id)
                ],
                'keterangan' => 'required|in:Tersedia,Terisi,Perbaikan',
                'x1' => 'required|integer|min:0',
                'y1' => 'required|integer|min:0',
                'x2' => 'required|integer|min:0',
                'y2' => 'required|integer|min:0',
                'x3' => 'required|integer|min:0',
                'y3' => 'required|integer|min:0',
                'x4' => 'required|integer|min:0',
                'y4' => 'required|integer|min:0',
            ]);

            $slot->update($validatedData);

            return redirect()->route('admin-slot')->with('success', 'Slot berhasil diupdate');

        } catch (ValidationException $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', implode(' ', $e->validator->errors()->all()));

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $slot = Slot::findOrFail($id);
        $slot->delete();

        return redirect()->route('admin-slot')->with('success', 'Slot berhasil dihapus');
    }
}
