<!-- Password Reset Link Modal -->
<div id="passwordResetModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl p-6 max-w-md w-full mx-4 transform transition-all duration-300 scale-95 opacity-0">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-800">Mengirim Link Ubah Password</h3>
            <button onclick="closePasswordResetModal()" class="text-gray-500 hover:text-gray-700">
            </button>
        </div>
        <p class="text-gray-600 mb-6">Kami akan mengirimkan link untuk mengubah password ke email yang terdaftar. Silakan cek inbox email Anda setelah menekan tombol di bawah.</p>
        <div class="flex justify-end space-x-3">
            <button onclick="closePasswordResetModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">Batal</button>
            <button onclick="sendPasswordResetLink()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">Kirim Link</button>
        </div>
    </div>
</div>

<!-- Password Reset Success Modal -->
<div id="passwordResetSuccessModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl p-6 max-w-md w-full mx-4 transform transition-all duration-300 scale-95 opacity-0">
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">Link Terkirim!</h3>
            <p class="text-gray-600 mb-6">Link untuk mengubah password telah dikirim ke email Anda. Silakan cek inbox email Anda.</p>
            <button onclick="closePasswordResetSuccessModal()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">Mengerti</button>
        </div>
    </div>
</div>
