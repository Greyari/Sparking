<!-- Change Vehicle Photo Modal -->
<div id="changeVehiclePhotoModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl p-6 w-full sm:max-w-lg max-h-[90vh] overflow-y-auto mx-4 transform transition-all duration-300 scale-95 opacity-0">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-800">Ubah Foto Kendaraan</h3>
            <button onclick="closeVehiclePhotoModal()" class="text-gray-500 hover:text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="space-y-6">
            <!-- Current Photo Preview -->
            <div class="text-center">
                <h4 class="text-sm font-medium text-gray-500 mb-2">Foto Saat Ini</h4>
                <div class="relative mx-auto w-48 h-32 bg-gray-100 rounded-lg overflow-hidden">
                    <img id="currentVehiclePhoto" src="{{$user->foto_kendaraan}}"
                         alt="Current Vehicle" class="w-full h-full object-contain">
                    <div class="absolute inset-0 bg-black bg-opacity-20 flex items-center justify-center">
                        <span class="text-white text-sm font-medium">Foto Kendaraan</span>
                    </div>
                </div>
            </div>

            <!-- Upload New Photo -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Upload Foto Baru</label>
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg">
                    <div class="space-y-1 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class="flex text-sm text-gray-600">
                            <label for="vehiclePhotoUpload" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none">
                                <span>Upload file</span>
                                <input id="vehiclePhotoUpload" name="vehiclePhotoUpload" type="file" class="sr-only" accept="image/*" onchange="previewNewVehiclePhoto(event)">
                            </label>
                            <p class="pl-1">atau drag and drop</p>
                        </div>
                        <p class="text-xs text-gray-500">
                            PNG, JPG, JPEG (Maks. 5MB)
                        </p>
                    </div>
                </div>

                <!-- New Photo Preview -->
                <div id="newPhotoPreviewContainer" class="mt-4 hidden">
                    <h4 class="text-sm font-medium text-gray-500 mb-2">Pratinjau Foto Baru</h4>
                    <div class="relative mx-auto w-48 h-32 bg-gray-100 rounded-lg overflow-hidden">
                        <img id="newVehiclePhotoPreview" src="#" alt="New Vehicle Photo Preview" class="w-full h-full object-cover hidden">
                        <div id="newPhotoPlaceholder" class="absolute inset-0 flex items-center justify-center text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-3 pt-2">
                <button onclick="closeVehiclePhotoModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <button id="saveVehiclePhotoBtn" onclick="saveVehiclePhoto()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                    Simpan Perubahan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Vehicle Photo Update Success Modal -->
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
            <button onclick="closeVehiclePhotoSuccessModal()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                Tutup
            </button>
        </div>
    </div>
</div>
