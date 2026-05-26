@extends('layout.mainUser')

@include('user.component.headerUser')

@section('main')
<div class="min-h-[calc(100vh-80px)] bg-gradient-to-br from-blue-50 to-indigo-100 p-4 md:p-8 flex items-center justify-center transition-colors duration-300">
    <div class="w-full max-w-4xl mx-auto">

        <div class="mb-12 rounded-2xl overflow-hidden shadow-2xl">
            <div class="swiper h-48 md:h-72">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <div class="relative h-full w-full">
                            <img src="{{ asset('img/User/carousel1.png') }}" alt="Gambar Dashboard 1"
                                class="object-cover w-full h-full transition-all duration-500 hover:scale-105">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent flex items-end p-6">
                                <h3 class="text-white text-xl font-bold">Area Parkir Kampus</h3>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="relative h-full w-full">
                            <img src="{{ asset('img/User/carousel2.webp') }}" alt="Gambar Dashboard 2"
                                class="object-cover w-full h-full transition-all duration-500 hover:scale-105">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent flex items-end p-6">
                                <h3 class="text-white text-xl font-bold">Zona Parkir 5</h3>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="relative h-full w-full">
                            <img src="{{ asset('img/User/carousel3.png') }}" alt="Gambar Dashboard 3"
                                class="object-cover w-full h-full transition-all duration-500 hover:scale-105">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent flex items-end p-6">
                                <h3 class="text-white text-xl font-bold">Denah Parkir</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mb-10 animate-fade-in">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-2">Selamat Datang di SPARKING</h1>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">Sistem Informasi Parkir Pintar Politeknik Negeri Batam</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-20 max-w-4xl md:max-w-2xl mx-auto mb-12">
            <a href="{{ route('real-time') }}"
               class="group bg-white rounded-xl shadow-lg overflow-hidden transition-all duration-300 hover:shadow-xl hover:-translate-y-1 transform translate-y-10 opacity-0 transition-all duration-500 ease-out"
               id="realtime-card">
                <div class="p-6 flex flex-col items-center text-center">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-4 group-hover:bg-blue-200 transition-colors">
                        <i class="fas fa-tachometer-alt text-blue-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Real-Time Monitoring</h3>
                    <p class="text-gray-500 text-sm">Pantau ketersediaan slot parkir secara real-time</p>
                </div>
            </a>

            <a href="{{ route('statistik') }}"
               class="group bg-white rounded-xl shadow-lg overflow-hidden transition-all duration-300 hover:shadow-xl hover:-translate-y-1 transform translate-y-10 opacity-0 transition-all duration-500 ease-out delay-150"
               id="analysis-card">
                <div class="p-6 flex flex-col items-center text-center">
                    <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mb-4 group-hover:bg-indigo-200 transition-colors">
                        <i class="fas fa-chart-bar text-indigo-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Analisis Data</h3>
                    <p class="text-gray-500 text-sm">Statistik dan analisis useran parkir</p>
                </div>
            </a>
        </div>

    </div>
</div>

<script>
    function animateValue(id, target, duration = 2000) {
        const element = document.getElementById(id);
        const start = 0;
        const increment = target / (duration / 16);
        let current = start;

        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                clearInterval(timer);
                current = target;
            }
            element.textContent = Math.floor(current);
        }, 16);
    }

    function animateOnScroll() {
        const cards = [
            document.getElementById('realtime-card'),
            document.getElementById('analysis-card')
        ];

        cards.forEach(card => {
            if (card) {
                const cardPosition = card.getBoundingClientRect().top;
                const screenPosition = window.innerHeight / 1.3;

                if (cardPosition < screenPosition) {
                    card.classList.remove('translate-y-10', 'opacity-0');
                    card.classList.add('translate-y-0', 'opacity-100');
                }
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        animateValue('total-counter', 350);
        animateValue('available-counter', 142);
        animateValue('filled-counter', 208);
        animateValue('users-counter', 84);

        const swiper = new Swiper('.swiper', {
            loop: true,
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
                renderBullet: function (index, className) {
                    return `<span class="${className} w-2 h-2 md:w-3 md:h-3 bg-white/50 rounded-full cursor-pointer inline-block mx-1 transition-all"></span>`;
                },
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            effect: 'fade',
            fadeEffect: {
                crossFade: true
            },
        });

        setTimeout(animateOnScroll, 100);
    });

    window.addEventListener('scroll', animateOnScroll);
</script>

@endsection
