@extends('layout.mainUser')

@section('main')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden w-full max-w-md transform transition-all duration-500 hover:scale-[1.02]">
        <!-- Header -->
        <div class="px-8 pt-8 text-center">
            <div class="w-40 h-40 mx-auto mb-6 animate-wiggle">
                <img src="{{ asset('images/step4.png') }}" alt="Select User Type" class="w-full h-full object-contain scale-125">
            </div>
            <h2 class="text-3xl font-bold text-gray-800 mb-2 animate-fade-in-down">Foto Kendaraan</h2>
            <p class="text-gray-600 animate-fade-in-down delay-100">Silakan unggah foto Kendaraan Anda dengan jelas</p>
        </div>

        <!-- Form -->
        <form action="{{ route('onboarding.update') }}" method="POST" enctype="multipart/form-data" class="px-8 pb-8 space-y-6">
            @csrf
            <input type="hidden" name="step" value="4">
            <!-- Foto User -->
            <div class="relative group animate-fade-in-down delay-300">
                <input type="file" name="foto_kendaraan" id="foto_kendaraan" class="hidden" accept="image/*" required>
                <label for="foto_kendaraan" class="block cursor-pointer">
                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center transition-all duration-300 hover:border-blue-400 hover:bg-blue-50 group-hover:shadow-md">
                        <div class="mx-auto h-16 w-16 mb-3 text-gray-400 group-hover:text-blue-500 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <p class="text-sm text-gray-500 group-hover:text-gray-700 transition-colors">Klik untuk upload foto Kendaraan</p>
                        <p class="text-xs text-gray-400 mt-1">Format JPG/PNG, maks. 5MB</p>
                    </div>
                </label>
                <div id="user-preview" class="mt-4 hidden animate-fade-in">
                    <div class="relative">
                        <img id="user-preview-image" class="h-48 w-full object-cover rounded-xl border-2 border-gray-200 shadow-sm">
                        <button type="button" onclick="clearImage()"
                            class="absolute top-3 right-3 bg-white/30 backdrop-blur-md text-white rounded-full p-2 shadow-lg ring-1 ring-white/40 transition-all duration-300 hover:scale-110 hover:bg-white/50">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full bg-gradient-to-r from-blue-500 to-indigo-600 text-white py-3 rounded-lg font-semibold hover:from-blue-700 hover:to-indigo-800 transition-all duration-300 transform hover:scale-[1.02] shadow-md">
                Lanjutkan
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline ml-1" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>
        </form>
    </div>
</div>

<script>
    // Image preview functionality
    document.getElementById('foto_kendaraan').addEventListener('change', function(e) {
        const preview = document.getElementById('user-preview');
        const previewImage = document.getElementById('user-preview-image');

        if (this.files && this.files[0]) {
            // Validate file size (5MB max)
            if (this.files[0].size > 5 * 1024 * 1024) {
                alert('Ukuran file terlalu besar. Maksimal 5MB.');
                this.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                previewImage.src = e.target.result;
                preview.classList.remove('hidden');
                preview.classList.add('block');
            }
            reader.readAsDataURL(this.files[0]);
        }
    });

    // Clear image function
    function clearImage() {
        const fileInput = document.getElementById('foto_kendaraan');
        const preview = document.getElementById('user-preview');

        fileInput.value = '';
        preview.classList.add('hidden');
        preview.classList.remove('block');
    }
</script>

<style>
    .animate-bounce {
        animation: bounce 2s infinite;
    }

    @keyframes bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }

    .animate-fade-in-down {
        animation: fadeInDown 0.6s ease-out forwards;
        opacity: 0;
    }

    .animate-fade-in {
        animation: fadeIn 0.4s ease-out forwards;
    }

    .delay-100 { animation-delay: 0.1s; }
    .delay-200 { animation-delay: 0.2s; }
    .delay-300 { animation-delay: 0.3s; }
    .delay-400 { animation-delay: 0.4s; }

    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
</style>
<style>
    .animate-wiggle {
        animation: wiggle 2s ease-in-out infinite;
    }

    @keyframes wiggle {
        0%, 100% { transform: rotate(-3deg); }
        50% { transform: rotate(3deg); }
    }

    .animate-fade-in-down {
        animation: fadeInDown 0.6s ease-out forwards;
        opacity: 0;
    }

    .delay-100 { animation-delay: 0.1s; }
    .delay-200 { animation-delay: 0.2s; }
    .delay-300 { animation-delay: 0.3s; }
    .delay-400 { animation-delay: 0.4s; }
    .delay-500 { animation-delay: 0.5s; }
    .delay-600 { animation-delay: 0.6s; }

    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
@endsection
