<?php

namespace App\Http\Controllers;

use Dompdf\Dompdf;
use App\Models\User;
use App\Models\Datakendaraan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QrCodeController extends Controller
{
    public function index()
    {
        $vehicles = Datakendaraan::with('user')
            ->where('status1', 'aktif')
            ->where('id_user', auth()->id())
            ->get();

        return view('qrCode', [
            'title' => 'qr-code',
            'vehicles' => $vehicles
        ]);
    }

    public function fotoQrcode($qr_code)
    {
        $path = storage_path('app/public/' . $qr_code);
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        return 'data:image/' . $type . ';base64,' . base64_encode($data);
    }

    public function generatePDF()
    {
        $user = Auth::user();
        $gambar = $user->qr_code;
        $id_user = $user->id;
        $nama_lengkap = $user->nama;
        $jenis_kendaraan = $user->jenis_kendaraan;
        $no_plat = $user->no_plat;

        // Step 3: Generate the HTML content
        $html = view('printPDF', [
            'gambar' => $this->fotoQrcode($gambar),
            'id_user' => $id_user,
            'nama_lengkap' => $nama_lengkap,
            'jenis_kendaraan' => $jenis_kendaraan,
            'no_plat' => $no_plat
        ])->render();

        // Step 4: Create a new Dompdf instance
        $dompdf = new Dompdf();

        // Step 5: Load the HTML content
        $dompdf->loadHtml($html);

        // Step 6: Render the PDF
        $dompdf->render();

        // Step 7: Output the PDF
        return $dompdf->stream('informasi_qr_code_' . $id_user . '.pdf', ['Attachment' => 0]);
    }

    public function storeVehicle(Request $request)
    {
        // Validate input
        $validatedData = $request->validate([
            'jenis_kendaraan1' => 'required|in:mobil,motor',
            'no_plat1' => 'required|unique:datakendaraan,no_plat1',
            'foto_kendaraan1' => 'required|image|max:2048' // max 2MB
        ]);

        // Check if user already has a vehicle
        $existingVehicle = Datakendaraan::where('id_user', Auth::id())->first();
        if ($existingVehicle) {
            return back()->with('error', 'Anda sudah memiliki kendaraan terdaftar.');
        }

        // Store the vehicle image
        $imagePath = $request->file('foto_kendaraan1')->store('vehicle_photos', 'public');

        // Create new vehicle entry
        $vehicle = new Datakendaraan();
        $vehicle->id_user = Auth::id();
        $vehicle->jenis_kendaraan1 = $validatedData['jenis_kendaraan1'];
        $vehicle->no_plat1 = $validatedData['no_plat1'];
        $vehicle->foto_kendaraan1 = $imagePath;
        $vehicle->status1 = 'nonAktif';
        $vehicle->save();

        return back()->with('success', 'Kendaraan berhasil ditambahkan dan menunggu persetujuan.');
    }
}
