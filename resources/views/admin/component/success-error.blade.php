@if(session('success'))
    <div id="modalSuccess" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50 backdrop-blur-sm transition-opacity duration-300 opacity-0">
        <div class="bg-white p-8 rounded-xl shadow-2xl text-center max-w-md w-full mx-4 transform transition-all duration-300 scale-95 animate-bounce-in">
            <div class="h-1.5 bg-gray-200 rounded-full mb-6 overflow-hidden">
                <div id="successProgress" class="h-full bg-green-500 rounded-full" style="width: 100%; transition: width 8s linear;"></div>
            </div>

            <div class="flex justify-center mb-4">
                <svg class="w-16 h-16 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h2 class="text-3xl font-bold mb-4 text-green-600">Berhasil!</h2>
            <p class="text-gray-700 mb-6 text-lg">{{ session('success') }}</p>
            <button onclick="fadeOutModal('modalSuccess')"
                class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition-colors duration-300 font-medium text-lg shadow-md hover:shadow-lg">
                Close
            </button>
        </div>
    </div>
@endif

@if(session('error'))
    <div id="modalError" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50 backdrop-blur-sm transition-opacity duration-300 opacity-0">
        <div class="bg-white p-8 rounded-xl shadow-2xl text-center max-w-md w-full mx-4 transform transition-all duration-300 scale-95 animate-bounce-in">
            <div class="h-1.5 bg-gray-200 rounded-full mb-6 overflow-hidden">
                <div id="errorProgress" class="h-full bg-red-500 rounded-full" style="width: 100%; transition: width 8s linear;"></div>
            </div>

            <div class="flex justify-center mb-4">
                <svg class="w-16 h-16 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h2 class="text-3xl font-bold mb-4 text-red-600">Gagal!</h2>
            <p class="text-gray-700 mb-6 text-lg">{{ session('error') }}</p>
            <button onclick="fadeOutModal('modalError')"
                class="bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700 transition-colors duration-300 font-medium text-lg shadow-md hover:shadow-lg">
                Close
            </button>
        </div>
    </div>
@endif

<script>
    window.addEventListener('DOMContentLoaded', () => {
        // Modal Success
        const modalSuccess = document.getElementById('modalSuccess');
        if (modalSuccess) {
            // Tampilkan modal
            setTimeout(() => modalSuccess.classList.remove('opacity-0'), 10);

            const progress = document.getElementById('successProgress');
            // Trigger transition dengan delay kecil supaya animasi jalan
            setTimeout(() => {
                progress.style.width = '0%';
            }, 50);

            // Tutup modal setelah 8 detik (sama dengan durasi progress)
            setTimeout(() => fadeOutModal('modalSuccess'), 8050);
        }

        // Modal Error
        const modalError = document.getElementById('modalError');
        if (modalError) {
            setTimeout(() => modalError.classList.remove('opacity-0'), 10);

            const progress = document.getElementById('errorProgress');
            setTimeout(() => {
                progress.style.width = '0%';
            }, 50);

            setTimeout(() => fadeOutModal('modalError'), 8050);
        }
    });

    function fadeOutModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('opacity-0');
            setTimeout(() => modal.remove(), 300);
        }
    }
</script>

<style>
    @keyframes bounce-in {
        0% {
            opacity: 0;
            transform: scale(0.8);
        }
        50% {
            opacity: 1;
            transform: scale(1.05);
        }
        100% {
            transform: scale(1);
        }
    }

    .animate-bounce-in {
        animation: bounce-in 0.3s ease-out forwards;
    }
</style>
