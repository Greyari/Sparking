@extends('layout.mainUser')

@section('main')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden w-full max-w-md transform transition-all duration-500 hover:scale-[1.02]">
        <div class="p-8 text-center">
            <!-- Animated Illustration -->
            <div class="w-40 h-40 mx-auto mb-6 animate-wiggle">
                <img src="{{ asset('images/step3.png') }}" alt="Select User Type" class="w-full h-full object-cover">
            </div>

            <h1 class="text-3xl font-bold text-gray-800 mb-3 animate-fade-in-down">Jenis Kendaraan</h1>
            <p class="text-gray-600 mb-6 animate-fade-in-down delay-100">Silakan pilih jenis kendaraan Anda.</p>

            <form action="{{ route('onboarding.update') }}" method="POST" class="space-y-6">
                @csrf
                <input type="hidden" name="step" value="3">

                <div class="space-y-4">
                    <label class="flex items-center p-4 border border-gray-200 rounded-xl hover:border-blue-400 hover:bg-blue-50 transition-all cursor-pointer">
                        <input type="radio" name="jenis_kendaraan" value="mobil" class="h-5 w-5 text-blue-600 focus:ring-blue-500">
                        <div class="ml-4 flex flex-col text-left">
                            <span class="text-lg text-gray-700 font-medium">Mobil</span>
                            <span class="mt-1 text-sm text-gray-500">Kendaraan roda empat</span>
                        </div>
                    </label>

                    <label class="flex items-center p-4 border border-gray-200 rounded-xl hover:border-blue-400 hover:bg-blue-50 transition-all cursor-pointer">
                        <input type="radio" name="jenis_kendaraan" value="motor" class="h-5 w-5 text-blue-600 focus:ring-blue-500">
                        <div class="ml-4 flex flex-col text-left">
                            <span class="text-lg text-gray-700 font-medium">Motor</span>
                            <span class="mt-1 text-sm text-gray-500">kendaraan roda dua</span>
                        </div>
                    </label>
                </div>

                <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-indigo-700 text-white py-3 rounded-lg font-semibold hover:from-blue-700 hover:to-indigo-800 transition-all duration-300 transform hover:scale-[1.02] shadow-md">
                    Lanjutkan
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline ml-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </form>
        </div>
    </div>
</div>

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
