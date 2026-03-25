<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\BrevoMailService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Password;

class SettingsController extends Controller
{
    protected BrevoMailService $brevo;

    public function __construct(BrevoMailService $brevo)
    {
        $this->brevo = $brevo;
    }

    public function index()
    {
        $user = Auth::user();
        return view('user.settings', [
            'title' => 'pengaturan',
            'user' => $user
        ]);
    }

    public function updateFotoKendaraan(Request $request)
    {
        $request->validate([
            'foto_kendaraan' => 'required|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        $user = auth()->user();

        // Hapus foto lama di Cloudinary jika ada
        if ($user->foto_kendaraan && str_starts_with($user->foto_kendaraan, 'http')) {
            $urlPath = parse_url($user->foto_kendaraan, PHP_URL_PATH);
            $publicId = preg_replace('/\.[^.]+$/', '',
                implode('/', array_slice(explode('/', $urlPath), 5))
            );
            cloudinary()->uploadApi()->destroy($publicId);
        }

        // Upload foto baru ke Cloudinary
        $result = cloudinary()->uploadApi()->upload(
            $request->file('foto_kendaraan')->getRealPath(),
            ['folder' => 'foto_kendaraan']
        );

        $user->foto_kendaraan = $result['secure_url'];
        $user->save();

        return response()->json([
            'message' => 'Foto kendaraan berhasil diperbarui.',
            'path' => $result['secure_url']
        ]);
    }

    public function changePassword(Request $request)
    {
        $request->validate(
            [
                'old_password' => 'required',
                'new_password' => 'required|min:8|different:old_password',
                'confirm_password' => 'required|same:new_password'
            ],
            [
                'old_password.required' => 'Password lama harus diisi.',
                'new_password.required' => 'Password baru harus diisi.',
                'new_password.min' => 'Password baru minimal 8 karakter.',
                'new_password.different' => 'Password baru harus berbeda dengan password lama.',
                'confirm_password.required' => 'Konfirmasi password harus diisi.',
                'confirm_password.same' => 'Konfirmasi password tidak sesuai.'
            ]
        );

        $user = Auth::user();

        if (!Hash::check($request->old_password, $user->password)) {
            return redirect()->back()->with('error', 'Password lama tidak sesuai.');
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->back()->with('success', 'password berhasil di ganti');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        $token    = Password::createToken($user);
        $resetUrl = URL::temporarySignedRoute(
            'password.reset',
            now()->addMinutes(30),
            ['token' => $token, 'id' => $user->id]
        );

        $berhasil = $this->brevo->sendPasswordResetEmail(
            $user->email,
            $user->nama,
            $resetUrl
        );

        if (!$berhasil) {
            return response()->json(['error' => 'Gagal mengirim email.'], 500);
        }

        return response()->json(['message' => 'Link reset telah dikirim ke email.']);
    }

    public function showResetForm($token, $id)
    {
        $user = User::findOrFail($id);

        Auth::login($user);

        return view('user.component.pengaturan.form_ubah_kata_sandi', [
            'token' => $token,
            'email' => $user->email,
            'title' => 'Ubah Kata Sandi'
        ]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed',
        ], [
            'password.confirmed' => 'Konfirmasi kata sandi tidak sesuai.',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'password' => 'Kata sandi baru tidak boleh sama seperti kata sandi lama.',
            ]);
        }

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect('/settings')->with('success', 'Kata sandi berhasil diubah.')
            : back()->withErrors(['email' => [__($status)]]);
    }
}
