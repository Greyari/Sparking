@extends('layout.mainUser')
@include('user.component.headerUser')

@section('main')

{{-- ============================================================
     HALAMAN PENGATURAN PROFIL PENGGUNA
     Berisi:
       - Profil card (foto, nama, info kendaraan)
       - Modal: Konfirmasi kirim link reset password
       - Modal: Sukses kirim link reset password
       - Modal: Form ubah kata sandi (dari link token email)
       - Modal: Ubah foto kendaraan
       - Modal: Sukses ubah foto kendaraan
       - Modal: Perbesar gambar kendaraan
       - JavaScript semua fungsi modal & fetch
     ============================================================ --}}

{{-- Hidden email untuk dikirim via fetch saat reset password --}}
<input type="hidden" id="userEmail" value="{{ $user->email }}">

{{-- Pass routes & CSRF ke JavaScript --}}
<script>
    const APP_ROUTES = {
        passwordEmail : "{{ route('password.email') }}",
        csrfToken     : "{{ csrf_token() }}"
    };
</script>


{{-- ============================================================
     HALAMAN UTAMA — PROFIL CARD
     ============================================================ --}}
<div class="min-h-[calc(100vh-80px)] bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center p-4">
    <div class="flex flex-col items-center gap-6 w-full max-w-4xl">

        {{-- Tombol Kembali --}}
        <div class="w-full flex justify-start">
            <a href="{{ route('user-dashboard') }}"
                class="inline-flex items-center bg-white hover:bg-gray-50 border border-gray-200 text-gray-700 px-4 py-2 rounded-lg shadow-sm hover:shadow-md transition-all duration-300 hover:-translate-x-0.5">
                <i class="mr-2 text-gray-500 fas fa-arrow-left"></i>Kembali
            </a>
        </div>

        <div class="bg-white shadow-xl rounded-2xl w-full max-w-4xl overflow-hidden transition-all duration-300 hover:shadow-2xl">

            {{-- ---- Cover Photo & Avatar ---- --}}
            <div class="px-6 pt-6">
                <div class="relative h-48 overflow-visible">
                    <img src="{{ asset('img/User/bg_pengaturan.jpg') }}" alt="Cover"
                        class="w-full h-full object-cover rounded-xl object-center">

                    {{-- Avatar melayang di bawah cover --}}
                    <div class="absolute left-1/2 transform -translate-x-1/2 -bottom-12 z-10">
                        <div class="relative group">
                            <img src="{{ $user->foto_user }}" alt="Profile"
                                class="h-24 w-24 rounded-full border-4 border-white object-cover shadow-lg transition-all duration-300 group-hover:ring-4 group-hover:ring-blue-200">
                            {{-- Overlay kamera saat hover --}}
                            <div class="absolute inset-0 bg-blue-500 bg-opacity-30 rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ---- Info Profil & Tombol Aksi ---- --}}
            <div class="p-6 pt-16">
                <div class="bg-gradient-to-r from-blue-50 to-cyan-50 rounded-xl p-6 shadow-inner">
                    <div class="flex flex-col lg:flex-row gap-8 items-center">

                        {{-- Foto Kendaraan (klik untuk perbesar) --}}
                        <div class="w-full lg:w-1/3 flex justify-center">
                            <div class="group w-full max-w-xs cursor-pointer" onclick="openImageModal('{{ $user->foto_kendaraan }}')">
                                <div class="relative w-full transition-transform duration-300 group-hover:scale-105">
                                    <img src="{{ $user->foto_kendaraan }}" alt="Car Photo"
                                        class="w-full h-auto max-h-48 object-contain rounded-lg shadow-md">
                                    <div class="absolute inset-0 bg-black bg-opacity-20 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                                        <span class="text-white font-medium bg-black bg-opacity-50 px-3 py-1 rounded-full">Lihat Ukuran Penuh</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Nama, Email, Jenis Kendaraan, No Plat --}}
                        <div class="flex-1 text-center lg:text-left space-y-4">
                            <div>
                                <h2 class="text-3xl font-bold text-gray-800 mb-4">{{ $user->nama }}</h2>
                                <div class="space-y-3">

                                    <div class="flex items-center justify-center lg:justify-start space-x-3 p-2 bg-white hover:bg-white/90 rounded-lg transition-all duration-300 shadow-sm hover:shadow-md">
                                        <span class="material-icons text-indigo-500 text-[24px]">mail</span>
                                        <span class="text-gray-700 break-all">{{ $user->email }}</span>
                                    </div>

                                    <div class="flex items-center justify-center lg:justify-start space-x-3 p-2 bg-white hover:bg-white/90 rounded-lg transition-all duration-300 shadow-sm hover:shadow-md">
                                        <span class="material-icons text-indigo-500 text-[24px]">person</span>
                                        <span class="text-gray-700 capitalize">{{ $user->jenis_user }}</span>
                                    </div>

                                    <div class="flex items-center justify-center lg:justify-start space-x-3 p-2 bg-white hover:bg-white/90 rounded-lg transition-all duration-300 shadow-sm hover:shadow-md">
                                        @if ($user->jenis_kendaraan === 'motor')
                                            <span class="material-icons text-indigo-500 text-[24px]">two_wheeler</span>
                                        @else
                                            <span class="material-icons text-indigo-500 text-[24px]">directions_car</span>
                                        @endif
                                        <span class="text-gray-700 capitalize">{{ $user->jenis_kendaraan }}</span>
                                    </div>

                                    <div class="flex items-center justify-center lg:justify-start space-x-3 p-2 bg-white hover:bg-white/90 rounded-lg transition-all duration-300 shadow-sm hover:shadow-md">
                                        <span class="material-icons text-indigo-500 text-[24px]">credit_card</span>
                                        <span class="text-gray-700 font-mono uppercase tracking-wider">{{ $user->no_plat }}</span>
                                    </div>

                                </div>
                            </div>

                            {{-- Tombol: Ubah Kata Sandi & Ubah Foto Kendaraan --}}
                            <div class="flex flex-col sm:flex-row justify-center lg:justify-start gap-3 pt-2">
                                <button onclick="openPasswordResetModal()"
                                    class="flex items-center justify-center space-x-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg shadow-md transition-all duration-300 transform hover:-translate-y-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                    <span>Ubah Kata Sandi</span>
                                </button>

                                <button onclick="openVehiclePhotoModal()"
                                    class="flex items-center justify-center space-x-2 bg-white hover:bg-gray-100 border border-gray-200 text-gray-800 px-5 py-2 rounded-lg shadow-md transition-all duration-300 transform hover:-translate-y-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span>Ubah Foto Kendaraan</span>
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@include('user.component.success-error')


{{-- ============================================================
     MODAL 1 — KONFIRMASI KIRIM LINK RESET PASSWORD
     Trigger: tombol "Ubah Kata Sandi" di profil card
     Flow: kirim email → tutup modal → buka Modal 2 (sukses)
     ============================================================ --}}
<div id="passwordResetModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl p-6 max-w-md w-full mx-4 transform transition-all duration-300 scale-95 opacity-0">

        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-800">Mengirim Link Ubah Password</h3>
            <button onclick="closePasswordResetModal()" class="text-gray-500 hover:text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <p class="text-gray-600 mb-6">
            Kami akan mengirimkan link untuk mengubah password ke email yang terdaftar.
            Silakan cek inbox email Anda setelah menekan tombol di bawah.
        </p>

        <div class="flex justify-end space-x-3">
            <button onclick="closePasswordResetModal()"
                class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                Batal
            </button>
            <button onclick="sendPasswordResetLink()"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                Kirim Link
            </button>
        </div>

    </div>
</div>


{{-- ============================================================
     MODAL 2 — SUKSES KIRIM LINK RESET PASSWORD
     Muncul otomatis setelah Modal 1 berhasil kirim email
     ============================================================ --}}
<div id="passwordResetSuccessModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl p-6 max-w-md w-full mx-4 transform transition-all duration-300 scale-95 opacity-0">
        <div class="text-center">

            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>

            <h3 class="text-xl font-bold text-gray-800 mb-2">Link Terkirim!</h3>
            <p class="text-gray-600 mb-6">
                Link untuk mengubah password telah dikirim ke email Anda. Silakan cek inbox email Anda.
            </p>

            <button onclick="closePasswordResetSuccessModal()"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                Mengerti
            </button>

        </div>
    </div>
</div>


{{-- ============================================================
     MODAL 3 — FORM UBAH KATA SANDI (dari link token email)
     Form dikirim ke route('password.update') dengan token & email
     dari query string yang sudah di-pass controller ke view
     ============================================================ --}}
<div id="changePasswordModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl p-8 w-full max-w-md mx-4 transform transition-all duration-300 scale-95 opacity-0">

        {{-- Header --}}
        <div class="flex justify-between items-center mb-6">
            <div class="text-center flex-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                <h2 class="text-2xl font-bold text-gray-800 mt-4">Ubah Kata Sandi</h2>
                <p class="text-gray-600 mt-2">Masukkan password baru Anda</p>
            </div>
            <button onclick="closeChangePasswordModal()" class="text-gray-500 hover:text-gray-700 self-start">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Tampilkan error validasi Laravel jika ada --}}
        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        @foreach ($errors->all() as $error)
                            <p class="text-sm text-red-700">{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        {{-- Form Reset Password --}}
        <form id="passwordChangeForm" method="POST" action="{{ route('password.update') }}" class="space-y-6">
            @csrf
            <input type="hidden" name="token" value="{{ $token ?? '' }}">
            <input type="hidden" name="email" value="{{ $email ?? $user->email }}">

            {{-- Input Password Baru --}}
            <div class="relative">
                <input type="password" id="password" name="password" required
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500 transition duration-200"
                    placeholder="Kata Sandi Baru">
                <button type="button" class="absolute right-3 top-3" onclick="togglePasswordVisibility('password')">
                    <svg class="w-6 h-6 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </button>
            </div>

            {{-- Input Konfirmasi Password --}}
            <div class="relative">
                <input type="password" id="password_confirmation" name="password_confirmation" required
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500 transition duration-200"
                    placeholder="Konfirmasi Kata Sandi Baru">
                <button type="button" class="absolute right-3 top-3" onclick="togglePasswordVisibility('password_confirmation')">
                    <svg class="w-6 h-6 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </button>
            </div>

            <button type="submit"
                class="w-full py-3 px-4 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white font-semibold shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-200">
                Reset Password
            </button>
        </form>

    </div>
</div>


{{-- ============================================================
     MODAL 4 — UBAH FOTO KENDARAAN
     Trigger: tombol "Ubah Foto Kendaraan" di profil card
     Flow: pilih file → preview → simpan via fetch → Modal 5
     ============================================================ --}}
<div id="changeVehiclePhotoModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl p-6 w-full sm:max-w-lg max-h-[90vh] overflow-y-auto mx-4 transform transition-all duration-300 scale-95 opacity-0">

        {{-- Header Modal --}}
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-800">Ubah Foto Kendaraan</h3>
            <button onclick="closeVehiclePhotoModal()" class="text-gray-500 hover:text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="space-y-6">

            {{-- Preview Foto Kendaraan Saat Ini --}}
            <div class="text-center">
                <h4 class="text-sm font-medium text-gray-500 mb-2">Foto Saat Ini</h4>
                <div class="relative mx-auto w-48 h-32 bg-gray-100 rounded-lg overflow-hidden">
                    <img id="currentVehiclePhoto" src="{{ $user->foto_kendaraan }}"
                        alt="Current Vehicle" class="w-full h-full object-contain">
                    <div class="absolute inset-0 bg-black bg-opacity-20 flex items-center justify-center">
                        <span class="text-white text-sm font-medium">Foto Kendaraan</span>
                    </div>
                </div>
            </div>

            {{-- Area Upload Foto Baru --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Upload Foto Baru</label>
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg">
                    <div class="space-y-1 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class="flex text-sm text-gray-600">
                            <label for="vehiclePhotoUpload" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none">
                                <span>Upload file</span>
                                <input id="vehiclePhotoUpload" name="vehiclePhotoUpload" type="file"
                                    class="sr-only" accept="image/*" onchange="previewNewVehiclePhoto(event)">
                            </label>
                            <p class="pl-1">atau drag and drop</p>
                        </div>
                        <p class="text-xs text-gray-500">PNG, JPG, JPEG (Maks. 5MB)</p>
                    </div>
                </div>

                {{-- Preview Foto Baru setelah dipilih --}}
                <div id="newPhotoPreviewContainer" class="mt-4 hidden">
                    <h4 class="text-sm font-medium text-gray-500 mb-2">Pratinjau Foto Baru</h4>
                    <div class="relative mx-auto w-48 h-32 bg-gray-100 rounded-lg overflow-hidden">
                        <img id="newVehiclePhotoPreview" src="#" alt="New Vehicle Photo Preview"
                            class="w-full h-full object-cover hidden">
                        <div id="newPhotoPlaceholder" class="absolute inset-0 flex items-center justify-center text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tombol Aksi Modal --}}
            <div class="flex justify-end space-x-3 pt-2">
                <button onclick="closeVehiclePhotoModal()"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <button id="saveVehiclePhotoBtn" onclick="saveVehiclePhoto()"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                    disabled>
                    Simpan Perubahan
                </button>
            </div>

        </div>
    </div>
</div>


{{-- ============================================================
     MODAL 5 — SUKSES UBAH FOTO KENDARAAN
     Muncul otomatis setelah fetch upload foto berhasil
     ============================================================ --}}
<div id="vehiclePhotoSuccessModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl p-6 max-w-md w-full mx-4 transform transition-all duration-300 scale-95 opacity-0">
        <div class="text-center">

            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>

            <h3 class="text-xl font-bold text-gray-800 mb-2">Foto Kendaraan Berhasil Diubah!</h3>

            <div class="mx-auto w-32 h-24 bg-gray-100 rounded-lg overflow-hidden my-4">
                <img id="updatedVehiclePhoto" src="#" alt="Updated Vehicle Photo" class="w-full h-full object-cover">
            </div>

            <button onclick="closeVehiclePhotoSuccessModal()"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                Tutup
            </button>

        </div>
    </div>
</div>


{{-- ============================================================
     MODAL 6 — PERBESAR GAMBAR KENDARAAN
     Trigger: klik foto kendaraan di profil card
     ============================================================ --}}
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-80 flex items-center justify-center z-50 hidden">
    <div class="relative max-w-3xl w-full">
        <img id="modalImage" src="" class="rounded-lg max-h-[90vh] mx-auto" alt="Full Size">
        <button onclick="closeImageModal()" class="absolute top-2 right-2 text-white hover:text-gray-300">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
</div>


{{-- ============================================================
     JAVASCRIPT — SEMUA FUNGSI MODAL & FETCH
     ============================================================

     DAFTAR FUNGSI:
       [1] openModal / closeModal      — helper animasi buka/tutup modal
       [2] openPasswordResetModal      — buka modal konfirmasi reset
       [3] closePasswordResetModal     — tutup modal konfirmasi reset
       [4] sendPasswordResetLink       — fetch kirim email reset password
       [5] openPasswordResetSuccessModal
       [6] closePasswordResetSuccessModal
       [7] openChangePasswordModal     — buka modal form ubah password
       [8] closeChangePasswordModal
       [9] togglePasswordVisibility    — toggle show/hide input password
      [10] openVehiclePhotoModal       — buka modal ubah foto kendaraan
      [11] closeVehiclePhotoModal
      [12] previewNewVehiclePhoto      — preview gambar sebelum diupload
      [13] saveVehiclePhoto            — fetch upload foto kendaraan ke server
      [14] openVehiclePhotoSuccessModal
      [15] closeVehiclePhotoSuccessModal
      [16] openImageModal              — buka modal perbesar gambar
      [17] closeImageModal
     ============================================================ --}}
<script>

    // -------------------------------------------------------
    // [HELPER] Buka & tutup modal dengan animasi scale+opacity
    // -------------------------------------------------------
    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        const content = modal.querySelector('div');
        modal.classList.remove('hidden');
        setTimeout(() => {
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeModal(modalId, callback = null) {
        const modal = document.getElementById(modalId);
        const content = modal.querySelector('div');
        content.classList.remove('scale-100', 'opacity-100');
        content.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
            if (callback) callback();
        }, 300);
    }


    // -------------------------------------------------------
    // [1] MODAL KONFIRMASI KIRIM LINK RESET PASSWORD
    // -------------------------------------------------------
    function openPasswordResetModal()  { openModal('passwordResetModal'); }
    function closePasswordResetModal() { closeModal('passwordResetModal'); }


    // -------------------------------------------------------
    // [2] FETCH — Kirim email link reset password
    //     Setelah sukses → tutup modal 1 → buka modal sukses
    // -------------------------------------------------------
    function sendPasswordResetLink() {
        const btn = document.querySelector('[onclick="sendPasswordResetLink()"]');
        btn.disabled    = true;
        btn.textContent = 'Mengirim...';

        fetch(APP_ROUTES.passwordEmail, {
            method  : 'POST',
            headers : {
                'Content-Type' : 'application/json',
                'Accept'       : 'application/json',
                'X-CSRF-TOKEN' : APP_ROUTES.csrfToken
            },
            body: JSON.stringify({ email: document.getElementById('userEmail').value })
        })
        .then(res => res.json())
        .then(data => {
            if (data.message) {
                closePasswordResetModal();
                setTimeout(() => openModal('passwordResetSuccessModal'), 350);
            } else {
                alert(data.error || 'Gagal mengirim email.');
            }
        })
        .catch(err => {
            console.error('Error kirim reset link:', err);
            alert('Terjadi kesalahan, silakan coba lagi.');
        })
        .finally(() => {
            btn.disabled    = false;
            btn.textContent = 'Kirim Link';
        });
    }


    // -------------------------------------------------------
    // [3] MODAL SUKSES KIRIM LINK RESET PASSWORD
    // -------------------------------------------------------
    function closePasswordResetSuccessModal() { closeModal('passwordResetSuccessModal'); }


    // -------------------------------------------------------
    // [4] MODAL FORM UBAH KATA SANDI
    // -------------------------------------------------------
    function openChangePasswordModal()  { openModal('changePasswordModal'); }
    function closeChangePasswordModal() { closeModal('changePasswordModal'); }


    // -------------------------------------------------------
    // [5] TOGGLE SHOW/HIDE INPUT PASSWORD
    //     inputId: 'password' atau 'password_confirmation'
    // -------------------------------------------------------
    function togglePasswordVisibility(inputId) {
        const input = document.getElementById(inputId);
        input.type = (input.type === 'password') ? 'text' : 'password';
    }


    // -------------------------------------------------------
    // [6] MODAL UBAH FOTO KENDARAAN
    //     Reset state input & preview saat dibuka ulang
    // -------------------------------------------------------
    function openVehiclePhotoModal() {
        document.getElementById('vehiclePhotoUpload').value = '';
        document.getElementById('newPhotoPreviewContainer').classList.add('hidden');
        document.getElementById('newVehiclePhotoPreview').classList.add('hidden');
        document.getElementById('saveVehiclePhotoBtn').disabled = true;
        openModal('changeVehiclePhotoModal');
    }

    function closeVehiclePhotoModal() { closeModal('changeVehiclePhotoModal'); }


    // -------------------------------------------------------
    // [7] PREVIEW FOTO BARU SEBELUM DIUPLOAD
    //     Validasi: tipe file gambar & max 5MB
    // -------------------------------------------------------
    function previewNewVehiclePhoto(event) {
        const input     = event.target;
        const container = document.getElementById('newPhotoPreviewContainer');
        const preview   = document.getElementById('newVehiclePhotoPreview');
        const placeholder = document.getElementById('newPhotoPlaceholder');
        const saveBtn   = document.getElementById('saveVehiclePhotoBtn');

        if (!input.files || !input.files[0]) return;

        const file = input.files[0];

        if (file.size > 5 * 1024 * 1024) {
            alert('Ukuran file terlalu besar. Maksimal 5MB.');
            input.value = '';
            return;
        }
        if (!file.type.match('image.*')) {
            alert('Hanya file gambar yang diperbolehkan.');
            input.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function (e) {
            preview.src = e.target.result;
            preview.classList.remove('hidden');
            placeholder.classList.add('hidden');
            container.classList.remove('hidden');
            saveBtn.disabled = false;
        };
        reader.readAsDataURL(file);
    }


    // -------------------------------------------------------
    // [8] FETCH — Upload foto kendaraan ke server
    //     POST /profil/update-foto-kendaraan (FormData)
    //     Setelah sukses → tutup modal 4 → buka modal 5
    // -------------------------------------------------------
    function saveVehiclePhoto() {
        const fileInput = document.getElementById('vehiclePhotoUpload');
        if (!fileInput.files || fileInput.files.length === 0) return;

        const saveBtn = document.getElementById('saveVehiclePhotoBtn');
        saveBtn.disabled    = true;
        saveBtn.textContent = 'Menyimpan...';

        const formData = new FormData();
        formData.append('foto_kendaraan', fileInput.files[0]);

        fetch('/profil/update-foto-kendaraan', {
            method  : 'POST',
            headers : {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        })
        .then(res => {
            if (!res.ok) throw new Error('Gagal menyimpan foto kendaraan. Silakan coba lagi.');
            return res.json();
        })
        .then(data => {
            document.getElementById('updatedVehiclePhoto').src = data.path;
            closeVehiclePhotoModal();
            setTimeout(() => openModal('vehiclePhotoSuccessModal'), 350);
        })
        .catch(err => {
            console.error('Error upload foto kendaraan:', err);
            alert(err.message);
        })
        .finally(() => {
            saveBtn.disabled    = false;
            saveBtn.textContent = 'Simpan Perubahan';
        });
    }


    // -------------------------------------------------------
    // [9] MODAL SUKSES UBAH FOTO KENDARAAN
    //     Tutup → reload halaman agar foto terupdate
    // -------------------------------------------------------
    function closeVehiclePhotoSuccessModal() {
        closeModal('vehiclePhotoSuccessModal', () => window.location.reload());
    }


    // -------------------------------------------------------
    // [10] MODAL PERBESAR GAMBAR KENDARAAN
    // -------------------------------------------------------
    function openImageModal(imageUrl) {
        document.getElementById('modalImage').src = imageUrl;
        document.getElementById('imageModal').classList.remove('hidden');
    }

    function closeImageModal() {
        document.getElementById('imageModal').classList.add('hidden');
    }


    // -------------------------------------------------------
    // [INIT] Buka modal ubah password jika ada token di URL
    //        (user datang dari link email reset password)
    // -------------------------------------------------------
    @if(isset($token))
        openChangePasswordModal();
    @endif

</script>

@endsection
