@extends('layout.mainUser')

@section('main')

@include('landingPage.component.success-error')
{{-- ============================================================
     LANDING PAGE — SPARKING
     Berisi (urutan dari atas ke bawah):
       1. Navbar (sticky)
       2. Hero Section (slider)
       3. Section Tentang Kami
       4. Section Keunggulan
       5. Footer
       6. Modal Login & Registrasi
       7. JavaScript (navbar toggle, modal, form toggle)
     ============================================================ --}}


{{-- ============================================================
     [1] NAVBAR — Sticky, responsive (desktop + mobile)
     ============================================================ --}}
<nav class="sticky top-0 z-50 shadow-lg bg-blue-900 font-poppins">
    <div class="flex items-center justify-between px-4 py-4 mx-auto max-w-7xl">

        {{-- Logo --}}
        <img src="img/icon.png" class="object-cover w-12 h-12" alt="Logo Sparking" />

        {{-- Tombol Hamburger (mobile) --}}
        <div class="md:hidden">
            <button id="menu-button" class="text-white focus:outline-none" onclick="toggleMenu()">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>

        {{-- Menu Links (desktop) --}}
        <div class="hidden md:flex md:items-center md:space-x-6">
            <a href="#tentang"    class="text-white transition hover:text-blue-400">Tentang</a>
            <a href="#keunggulan" class="text-white transition hover:text-blue-400">Keunggulan</a>
            <a href="javascript:void(0);" onclick="openModal()"
                class="px-6 py-2 font-semibold text-white transition duration-300 rounded-full shadow bg-biru_muda hover:bg-white hover:text-biru_muda">
                Login
            </a>
        </div>

    </div>

    {{-- Menu Mobile (hidden by default) --}}
    <div id="mobile-menu" class="hidden px-4 pb-4 md:hidden">
        <a href="#tentang"    class="block py-2 text-white hover:text-blue-400">Tentang</a>
        <a href="#keunggulan" class="block py-2 text-white hover:text-blue-400">Keunggulan</a>
        <a href="javascript:void(0);" onclick="openModal()"
            class="block px-6 py-2 mt-2 font-semibold text-center text-white transition duration-300 rounded-full shadow bg-biru_muda hover:bg-white hover:text-biru_muda">
            Login
        </a>
    </div>
</nav>


{{-- ============================================================
     [2] HERO SECTION — Slider gambar dengan overlay teks
     ============================================================ --}}
<section class="relative h-[calc(100vh-4rem)]">

    {{-- Overlay teks di atas slider --}}
    <div class="absolute inset-0 z-10 flex flex-col items-center justify-center px-4 text-center text-white bg-black/40">
        <h5 class="mb-2 text-3xl font-bold md:text-5xl font-poppins">
            SELAMAT DATANG DI SPARKING
        </h5>
        <h1 class="text-6xl text-blue-800 font-londrina drop-shadow-lg">SPARKING</h1>
        <p class="mt-2 text-base md:text-lg font-poppins">
            Layanan online parkir yang mempermudah hari anda
        </p>
    </div>

    {{-- Slider Gambar --}}
    <div id="slider" class="flex h-full transition-transform duration-700 ease-in-out">
        <img src="img/Landing Page/Gedung.jpg" class="flex-shrink-0 object-cover w-full" alt="Gedung" />
    </div>

</section>


{{-- ============================================================
     [3] SECTION TENTANG KAMI
     Berisi: deskripsi proyek, fitur utama, galeri foto
     ============================================================ --}}
<section id="tentang" class="relative px-6 py-16 overflow-hidden bg-white font-poppins">
    <div class="flex flex-col items-center max-w-6xl gap-8 mx-auto lg:flex-row">

        {{-- Kiri: Gambar dengan Triangle Overlay Effect --}}
        <div class="relative w-full h-[200px] sm:h-[300px] md:h-[400px] lg:w-1/2 lg:h-[500px]">
            <img src="{{ asset('img/Landing Page/mobil1.jpeg') }}" alt="Smart Parking System"
                class="object-cover w-full h-full shadow-lg rounded-xl" />

            {{-- Gradient overlay --}}
            <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-r from-white via-white/80 to-transparent mix-blend-overlay"></div>
            {{-- Triangle putih di sisi kiri --}}
            <div class="absolute top-0 left-0 w-1/2 h-full"
                style="clip-path: polygon(0 0, 80% 0, 30% 100%, 0% 100%); background: white; opacity: 0.9;"></div>
        </div>

        {{-- Kanan: Konten deskripsi & fitur --}}
        <div class="w-full lg:w-1/2">
            <div class="p-6 lg:p-8">
                <h2 class="mb-6 text-4xl font-bold text-gray-900 md:text-5xl">
                    Tentang <span class="text-blue-600">SPARKING</span>
                </h2>
                <p class="mb-4 text-lg leading-relaxed text-gray-600">
                    Smart Parking System (SPARKING) adalah inovasi mahasiswa Teknik Informatika Polibatam yang
                    mengintegrasikan teknologi IoT untuk manajemen parkir kampus Politeknik Negeri Batam.
                </p>
                <p class="mb-6 leading-relaxed text-gray-600">
                    Sejak <span class="font-semibold text-blue-600">Januari 2024</span>, kami mengembangkan solusi
                    parkir cerdas dengan fitur real-time monitoring dan analisis data parkir.
                </p>

                {{-- Daftar fitur utama --}}
                <ul class="space-y-3 text-gray-700">
                    @foreach ([
                        'Sensor pintar untuk deteksi kendaraan real-time',
                        'Dashboard monitoring kapasitas parkir',
                        'Proyek bimbingan Ibu Mirathul Khusna Mufida, PhD.',
                        'Durasi pengembangan: 6 bulan',
                    ] as $fitur)
                        <li class="flex items-start">
                            <svg class="w-5 h-5 mt-1 mr-2 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>{{ $fitur }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

    </div>

    {{-- Galeri Foto Parkir --}}
    <div class="max-w-6xl px-4 mx-auto mt-16">
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-5">
            @foreach ([
                ['gambar3.png', 'Parking Sensor'],
                ['gambar2.png', 'IoT Technology'],
                ['gambar1.png', 'Parking Lot'],
                ['gambar4.jpeg', 'Data Dashboard'],
                ['gambar5.jpeg', 'Coding Session'],
            ] as [$file, $alt])
                <div class="overflow-hidden transition-shadow rounded-lg shadow-md hover:shadow-xl">
                    <img src="{{ asset('img/Landing Page/' . $file) }}" alt="{{ $alt }}"
                        class="object-cover w-full h-32 transition-transform hover:scale-110">
                </div>
            @endforeach
        </div>
    </div>
</section>


{{-- ============================================================
     [4] SECTION KEUNGGULAN
     Berisi: list keunggulan + 4 kartu manfaat
     ============================================================ --}}
<section id="keunggulan" class="py-12 bg-gray-50 font-poppins sm:py-16 lg:py-20">
    <div class="container px-6 mx-auto max-w-7xl">

        {{-- Bagian atas: Teks kiri + Gambar kanan --}}
        <div class="flex flex-col-reverse items-center gap-8 lg:flex-row">

            {{-- Kiri: List keunggulan --}}
            <div class="w-full lg:w-1/2">
                <div class="max-w-lg p-6 mx-auto lg:ml-0 lg:mr-auto lg:p-8">
                    <h2 class="mb-6 text-3xl font-bold text-gray-900 sm:text-4xl">
                        Keunggulan <span class="text-blue-600">SPARKING</span>
                    </h2>

                    <ul class="space-y-4 text-gray-700">
                        @foreach ([
                            'Integrasi real-time dengan sensor parkir IoT',
                            'Antarmuka responsif untuk semua perangkat',
                            'Proses otomatis dengan sistem pintar',
                            'Laporan parkir lengkap dan terstruktur',
                            'Desain antarmuka modern dan intuitif',
                        ] as $keunggulan)
                            <li class="flex items-start">
                                <svg class="flex-shrink-0 w-5 h-5 mt-1 mr-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-base font-medium sm:text-lg">{{ $keunggulan }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            {{-- Kanan: Gambar --}}
            <div class="w-full lg:w-1/2">
                <div class="relative h-64 overflow-hidden rounded-lg shadow-lg sm:h-80 lg:h-[500px]">
                    <img src="{{ asset('img/Landing Page/aboutme.jpg') }}" alt="Sistem Parkir Polibatam"
                        class="object-cover w-full h-full transition duration-500 hover:scale-105">
                    <div class="absolute inset-0 bg-gradient-to-l from-white/30 to-transparent"></div>
                </div>
            </div>

        </div>

        {{-- Kartu Manfaat --}}
        <div class="grid grid-cols-1 gap-6 mt-16 sm:grid-cols-2 lg:grid-cols-4 sm:gap-8">

            {{-- Kartu 1: Efisiensi Waktu --}}
            <div class="p-6 transition-all bg-white rounded-xl hover:shadow-md">
                <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-blue-100 rounded-full sm:w-16 sm:h-16">
                    <svg class="w-6 h-6 text-blue-600 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <h3 class="mb-2 text-lg font-semibold text-center text-gray-800 sm:text-xl">Efisiensi Waktu</h3>
                <p class="text-sm text-center text-gray-600 sm:text-base">Mempercepat proses pencarian slot parkir hingga 70% lebih cepat</p>
            </div>

            {{-- Kartu 2: Keamanan --}}
            <div class="p-6 transition-all bg-white rounded-xl hover:shadow-md">
                <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-blue-100 rounded-full sm:w-16 sm:h-16">
                    <svg class="w-6 h-6 text-blue-600 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <h3 class="mb-2 text-lg font-semibold text-center text-gray-800 sm:text-xl">Keamanan</h3>
                <p class="text-sm text-center text-gray-600 sm:text-base">Sistem pemantauan terintegrasi meningkatkan keamanan kendaraan</p>
            </div>

            {{-- Kartu 3: Penghematan --}}
            <div class="p-6 transition-all bg-white rounded-xl hover:shadow-md">
                <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-blue-100 rounded-full sm:w-16 sm:h-16">
                    <svg class="w-6 h-6 text-blue-600 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="mb-2 text-lg font-semibold text-center text-gray-800 sm:text-xl">Penghematan</h3>
                <p class="text-sm text-center text-gray-600 sm:text-base">Mengurangi waktu idle kendaraan yang berdampak pada penghematan BBM</p>
            </div>

            {{-- Kartu 4: Data Akurat --}}
            <div class="p-6 transition-all bg-white rounded-xl hover:shadow-md">
                <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-blue-100 rounded-full sm:w-16 sm:h-16">
                    <svg class="w-6 h-6 text-blue-600 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="mb-2 text-lg font-semibold text-center text-gray-800 sm:text-xl">Data Akurat</h3>
                <p class="text-sm text-center text-gray-600 sm:text-base">Laporan parkir digital yang akurat untuk manajemen kampus</p>
            </div>

        </div>
    </div>
</section>


{{-- ============================================================
     [5] FOOTER
     ============================================================ --}}
<footer id="footer" class="py-6 text-white bg-blue-900">
    <div class="grid grid-cols-1 px-4 mx-auto text-center sm:text-left max-w-7xl sm:grid-cols-2 md:grid-cols-3 gap-y-8 gap-x-6">

        {{-- Deskripsi --}}
        <div>
            <h2 class="mb-2 text-lg font-bold sm:text-xl">SPARKING</h2>
            <p class="text-sm leading-relaxed">
                Sistem Informasi Pemantauan Parkir Polibatam secara real-time dan efisien.
            </p>
        </div>

        {{-- Navigasi --}}
        <div class="flex justify-center sm:justify-start">
            <div>
                <h3 class="mb-2 text-lg font-bold">Navigasi</h3>
                <ul class="space-y-1 text-sm">
                    <li><a href="#tentang"    class="hover:underline">Tentang</a></li>
                    <li><a href="#keunggulan" class="hover:underline">Keunggulan</a></li>
                    <li><a href="#footer"     class="hover:underline">Kontak</a></li>
                </ul>
            </div>
        </div>

        {{-- Kontak --}}
        <div>
            <h3 class="mb-2 text-lg font-bold">Kontak Kami</h3>
            <p class="text-sm">Email: sipp@gmail.com</p>
            <p class="text-sm">Telepon: 0852-6423-5208</p>
            <p class="mb-2 text-sm">Alamat: Jl. Ahmad Yani, Tlk. Tering, Batam</p>
            <div class="flex justify-center mt-2 space-x-3 text-lg sm:justify-start">
                <a href="#"><i class="fab fa-instagram hover:text-gray-300"></i></a>
                <a href="#"><i class="fab fa-facebook-f hover:text-gray-300"></i></a>
                <a href="#"><i class="fab fa-youtube hover:text-gray-300"></i></a>
            </div>
        </div>

    </div>
</footer>


{{-- ============================================================
     [6] MODAL LOGIN & REGISTRASI
     - Bagian kiri  : panel toggle (biru) dengan tombol switch form
     - Bagian kanan : form login / form registrasi (toggle hidden)
     ============================================================ --}}
<div id="loginModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-20 top-[4rem] hidden">
    <div class="bg-white flex flex-col md:flex-row rounded-3xl w-[95%] md:w-[80%] lg:w-[70%] xl:w-[60%] max-w-4xl relative h-[90%] overflow-hidden">

        {{-- Tombol Close --}}
        <button onclick="closeModal()" class="absolute z-30 text-xl text-white top-3 left-3 hover:text-red-600">
            <i class="fas fa-times"></i>
        </button>

        {{-- Panel Toggle — Kiri (hanya desktop) --}}
        <div class="relative items-center justify-center hidden w-full md:w-1/2 md:flex bg-gradient-to-br from-blue-500 to-purple-600 rounded-t-3xl md:rounded-l-2xl md:rounded-tr-none">
            <div class="z-20 px-8 text-center">
                <h2 class="mb-6 text-2xl font-bold tracking-wider text-white md:text-3xl" id="toggleTitle">
                    Selamat Datang Kembali
                </h2>
                <p class="mb-8 text-sm text-white/90">
                    Masuk untuk mengakses akun Anda dan menjelajahi lebih banyak fitur
                </p>
                <button type="button" onclick="toggleForm()" id="toggleButton"
                    class="px-4 py-2 text-sm text-white transition-all duration-500 transform border-2 rounded-full shadow-lg md:px-6 md:py-3 bg-white/20 backdrop-blur-sm border-white/30 hover:bg-white/30 hover:border-white/50 hover:scale-105 md:text-base">
                    Belum memiliki akun?
                </button>
            </div>
            {{-- Dot indikator aktif form --}}
            <div class="absolute bottom-0 left-0 right-0 z-20 flex justify-center pb-8">
                <div class="flex space-x-2">
                    <div class="w-2 h-2 rounded-full md:w-3 md:h-3 bg-white/80 toggle-dot active"></div>
                    <div class="w-2 h-2 rounded-full md:w-3 md:h-3 bg-white/30 toggle-dot"></div>
                </div>
            </div>
        </div>

        {{-- Form Container — Kanan --}}
        <div class="relative w-full md:w-1/2 h-full overflow-hidden">

            {{-- Background gambar --}}
            <img src="img/Landing Page/login.jpg" alt="Login Image"
                class="absolute inset-0 object-cover w-full h-full" />

            <div class="absolute inset-0" id="formSlider">

                {{-- Form Login (default tampil) --}}
                <div class="flex items-center justify-center w-full h-full p-10" id="loginForm">
                    <div class="z-20 w-full max-w-md p-4 border border-white shadow-lg bg-white/30 backdrop-blur-md backdrop-saturate-150 md:p-6 rounded-xl">
                        <h2 class="mb-3 text-xl font-extrabold tracking-widest text-center text-white md:text-2xl font-poppins">Login</h2>
                        <form action="{{ route('login_proses') }}" method="POST">
                            @csrf

                            {{-- Email --}}
                            <div class="relative py-2">
                                <span class="absolute inset-y-0 left-0 z-10 flex items-center pl-3 text-white">
                                    <i class="fas fa-envelope"></i>
                                </span>
                                @error('email')
                                    <div class="mt-1 text-red-500">{{ $message }}</div>
                                @enderror
                                <input type="email" placeholder="Email" name="email" value="{{ old('email') }}" required
                                    class="w-full px-3 py-2 pl-10 text-white border shadow-md bg-white/20 backdrop-blur-md border-white/30 placeholder-white/70 rounded-2xl focus:outline-none focus:ring-1 focus:ring-white focus:border-white/50" />
                            </div>

                            {{-- Password --}}
                            <div class="relative py-2">
                                <span class="absolute inset-y-0 left-0 z-10 flex items-center pl-3 text-white">
                                    <i class="fas fa-lock"></i>
                                </span>
                                @error('password')
                                    <div class="mt-1 text-red-500">{{ $message }}</div>
                                @enderror
                                <input type="password" placeholder="Password" name="password" required
                                    class="w-full px-3 py-2 pl-10 text-white border shadow-md bg-white/20 backdrop-blur-md border-white/30 placeholder-white/70 rounded-2xl focus:outline-none focus:ring-1 focus:ring-white focus:border-white/50" />
                            </div>

                            <div class="flex flex-col items-center mt-6 mb-2 space-y-2">
                                <button name="login_path" type="submit"
                                    class="w-4/5 px-4 py-2 font-semibold tracking-wide text-black transition-all duration-300 bg-white border border-white shadow-md rounded-2xl hover:bg-opacity-90 hover:shadow-lg">
                                    Login
                                </button>
                                <p class="mt-2 text-sm text-white md:hidden">
                                    Belum punya akun?
                                    <a href="javascript:void(0)" onclick="toggleForm()" class="font-semibold text-blue-200 hover:text-white">
                                        Daftar disini
                                    </a>
                                </p>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Form Registrasi (hidden by default) --}}
                <div class="flex items-center justify-center w-full h-full p-10 hidden" id="registerForm">
                    <div class="z-20 w-full max-w-md p-4 border border-white shadow-lg bg-white/30 backdrop-blur-md backdrop-saturate-150 md:p-6 rounded-xl">
                        <h2 class="mb-3 text-xl font-extrabold tracking-widest text-center text-white md:text-2xl font-poppins">Daftar</h2>
                        <form action="{{ route('registrasi_proses') }}" method="POST">
                            @csrf

                            {{-- Nama --}}
                            <div class="relative py-2">
                                <span class="absolute inset-y-0 left-0 z-10 flex items-center pl-3 text-white">
                                    <i class="fas fa-user"></i>
                                </span>
                                <input type="text" placeholder="Nama Lengkap" name="nama" value="{{ old('nama') }}"
                                    class="w-full px-3 py-2 pl-10 text-white border shadow-md bg-white/20 backdrop-blur-md border-white/30 placeholder-white/70 rounded-2xl focus:outline-none focus:ring-1 focus:ring-white focus:border-white/50" />
                            </div>

                            {{-- Email --}}
                            <div class="relative py-2">
                                <span class="absolute inset-y-0 left-0 z-10 flex items-center pl-3 text-white">
                                    <i class="fas fa-envelope"></i>
                                </span>
                                <input type="email" placeholder="Email" name="email" value="{{ old('email') }}"
                                    class="w-full px-3 py-2 pl-10 text-white border shadow-md bg-white/20 backdrop-blur-md border-white/30 placeholder-white/70 rounded-2xl focus:outline-none focus:ring-1 focus:ring-white focus:border-white/50" />
                            </div>

                            {{-- Password --}}
                            <div class="relative py-2">
                                <span class="absolute inset-y-0 left-0 z-10 flex items-center pl-3 text-white">
                                    <i class="fas fa-lock"></i>
                                </span>
                                <input type="password" placeholder="Password" name="password"
                                    class="w-full px-3 py-2 pl-10 text-white border shadow-md bg-white/20 backdrop-blur-md border-white/30 placeholder-white/70 rounded-2xl focus:outline-none focus:ring-1 focus:ring-white focus:border-white/50" />
                            </div>

                            {{-- Konfirmasi Password --}}
                            <div class="relative py-2">
                                <span class="absolute inset-y-0 left-0 z-10 flex items-center pl-3 text-white">
                                    <i class="fas fa-lock"></i>
                                </span>
                                <input type="password" placeholder="Konfirmasi Password" name="password_confirmation"
                                    class="w-full px-3 py-2 pl-10 text-white border shadow-md bg-white/20 backdrop-blur-md border-white/30 placeholder-white/70 rounded-2xl focus:outline-none focus:ring-1 focus:ring-white focus:border-white/50" />
                            </div>

                            <div class="flex flex-col items-center mt-6 mb-2 space-y-2">
                                <button type="submit"
                                    class="w-4/5 px-4 py-2 font-semibold tracking-wide text-black transition-all duration-300 bg-white border border-white shadow-md rounded-2xl hover:bg-opacity-90 hover:shadow-lg">
                                    Daftar
                                </button>
                                <p class="mt-2 text-sm text-white md:hidden">
                                    Sudah punya akun?
                                    <a href="javascript:void(0)" onclick="toggleForm()" class="font-semibold text-blue-200 hover:text-white">
                                        Masuk disini
                                    </a>
                                </p>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>


{{-- ============================================================
     [7] JAVASCRIPT
     ============================================================

     DAFTAR FUNGSI:
       [1] toggleMenu      — buka/tutup mobile nav
       [2] openModal       — tampilkan modal login
       [3] closeModal      — sembunyikan modal login
       [4] toggleForm      — switch antara form login & registrasi
       [5] DOMContentLoaded — auto-buka modal jika session showLogin
     ============================================================ --}}
<script>

    // -------------------------------------------------------
    // [1] Toggle mobile navigation menu
    // -------------------------------------------------------
    function toggleMenu() {
        document.getElementById('mobile-menu').classList.toggle('hidden');
    }


    // -------------------------------------------------------
    // [2] & [3] Buka / tutup modal login
    // -------------------------------------------------------
    function openModal() {
        const modal = document.getElementById('loginModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeModal() {
        const modal = document.getElementById('loginModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }


    // -------------------------------------------------------
    // [4] Toggle antara form Login dan Registrasi
    //     Mengubah teks panel kiri & dot indikator aktif
    // -------------------------------------------------------
    function toggleForm() {
        const loginForm    = document.getElementById('loginForm');
        const registerForm = document.getElementById('registerForm');
        const toggleTitle  = document.getElementById('toggleTitle');
        const toggleButton = document.getElementById('toggleButton');
        const toggleDots   = document.querySelectorAll('.toggle-dot');

        if (!loginForm || !registerForm) return;

        const isLoginVisible = !loginForm.classList.contains('hidden');

        if (isLoginVisible) {
            // Pindah ke form Registrasi
            loginForm.classList.add('hidden');
            registerForm.classList.remove('hidden');
            if (toggleTitle)  toggleTitle.textContent  = 'Bergabunglah Dengan Kami';
            if (toggleButton) toggleButton.textContent = 'Sudah memiliki akun?';
            toggleDots.forEach((dot, i) => dot.classList.toggle('active', i === 1));
        } else {
            // Pindah ke form Login
            registerForm.classList.add('hidden');
            loginForm.classList.remove('hidden');
            if (toggleTitle)  toggleTitle.textContent  = 'Selamat Datang Kembali';
            if (toggleButton) toggleButton.textContent = 'Belum memiliki akun?';
            toggleDots.forEach((dot, i) => dot.classList.toggle('active', i === 0));
        }
    }


    // -------------------------------------------------------
    // [5] Auto-buka modal login jika session 'showLogin' aktif
    //     (misal: setelah logout atau redirect dari halaman lain)
    // -------------------------------------------------------
    document.addEventListener('DOMContentLoaded', function () {
        @if(session('showLogin'))
            const modal = document.getElementById('loginModal');
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.getElementById('loginForm')?.classList.remove('hidden');
                document.getElementById('registerForm')?.classList.add('hidden');
                const toggleTitle  = document.getElementById('toggleTitle');
                const toggleButton = document.getElementById('toggleButton');
                if (toggleTitle)  toggleTitle.innerText  = 'Selamat Datang Kembali';
                if (toggleButton) toggleButton.innerText = 'Belum memiliki akun?';
            }
        @endif
    });

</script>

@endsection
