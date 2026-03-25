@extends('layout.mainUser')

@section('main')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center p-4 overflow-hidden">
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden w-full max-w-md transform transition-all duration-500 relative">
        <!-- Background Float Elements -->
        <div class="absolute -top-20 -left-20 w-40 h-40 bg-blue-100 rounded-full opacity-20 animate-float"></div>
        <div class="absolute -bottom-10 -right-10 w-32 h-32 bg-indigo-100 rounded-full opacity-20 animate-float-delay"></div>

        <!-- Confetti -->
        <div class="confetti-container">
            @for ($i = 0; $i < 250; $i++)
                <div class="confetti" style="
                    background-color: hsl({{ rand(0, 360) }}, 100%, 50%);
                    left: {{ rand(0, 100) }}%;
                    animation-delay: {{ rand(0, 100) / 100 }}s;
                    animation-duration: {{ rand(20, 40) / 10 }}s;
                    width: {{ rand(5, 10) }}px;
                    height: {{ rand(5, 10) }}px;
                "></div>
            @endfor
        </div>

        <div class="p-8 text-center relative z-10">
            <div class="w-40 h-40 mx-auto mb-6 animate-bounce">
                <img src="{{ asset('images/complate.png') }}" alt="Vehicle Plate" class="w-full h-full object-contain scale-125">
            </div>

            <h1 class="text-3xl font-bold text-gray-800 mb-3 animate-fade-in-down">Profil Lengkap!</h1>
            <p class="text-gray-600 mb-6 animate-fade-in-down delay-100">Selamat, Anda sekarang bisa menikmati semua fitur SPARKING</p>

            <!-- Dashboard Button -->
            <div class="animate-fade-in-down delay-300">
            <a href="{{ route('user-dashboard') }}"
            class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-base rounded-xl font-semibold hover:from-blue-700 hover:to-indigo-800 transition-all duration-300 transform hover:scale-[1.02] shadow-md">
                Mulai Menggunakan SPARKING
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2 -mr-1" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z"
                        clip-rule="evenodd" />
                </svg>
            </a>
            </div>
        </div>
    </div>
</div>

<style>
    /* Confetti */
    .confetti-container {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        overflow: hidden;
        z-index: 1;
    }

    .confetti {
        position: absolute;
        width: 10px;
        height: 10px;
        opacity: 0;
        animation: confetti-fall linear forwards;
    }

    @keyframes confetti-fall {
        0% {
            opacity: 1;
            transform: translateY(-100px) rotate(0deg) scale(1);
        }
        100% {
            opacity: 0;
            transform: translateY(100vh) rotate(720deg) scale(0.5);
        }
    }

    /* Floating background elements */
    .animate-float {
        animation: float 8s ease-in-out infinite;
    }

    .animate-float-delay {
        animation: float 8s ease-in-out infinite 2s;
    }

    @keyframes float {
        0%, 100% {
            transform: translate(0, 0) rotate(0deg);
        }
        25% {
            transform: translate(10px, 10px) rotate(5deg);
        }
        50% {
            transform: translate(0, 20px) rotate(0deg);
        }
        75% {
            transform: translate(-10px, 10px) rotate(-5deg);
        }
    }

    /* Wiggle Animation */
    .animate-wiggle {
        animation: wiggle 1.5s ease-in-out infinite;
    }

    @keyframes wiggle {
        0%, 100% {
            transform: rotate(0deg);
        }
        25% {
            transform: rotate(3deg);
        }
        50% {
            transform: rotate(-3deg);
        }
        75% {
            transform: rotate(2deg);
        }
    }

    /* Fade-in */
    .animate-fade-in-down {
        animation: fadeInDown 0.8s ease-out forwards;
        opacity: 0;
    }

    .delay-100 { animation-delay: 0.2s; }
    .delay-200 { animation-delay: 0.4s; }
    .delay-300 { animation-delay: 0.6s; }
    .delay-400 { animation-delay: 0.8s; }

    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-bounce {
        animation: bounce 2s ease-in-out infinite;
    }

    @keyframes bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }

    .animate-fade-in-down {
        animation: fadeInDown 0.6s ease-out forwards;
        opacity: 0;
    }

    .delay-100 { animation-delay: 0.1s; }
    .delay-200 { animation-delay: 0.2s; }
    .delay-300 { animation-delay: 0.3s; }

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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const audio = new Audio('{{ asset("sounds/success.mp3") }}');
        audio.volume = 0.3;
        audio.play().catch(err => console.warn("Sound playback blocked:", err));
    });
</script>
@endsection
