@extends('layout.mainUser')

@include('landingPage.component.headerLanding')

@section('main')
    @include('landingPage.component.modal_login_regis')

    <section class="relative h-[calc(100vh-4rem)]">
        <!-- Hero Section (hanya satu) -->
        <div class="absolute inset-0 z-10 flex flex-col items-center justify-center px-4 text-center text-white bg-black/40">
            <h5 class="mb-2 text-3xl font-bold md:text-5xl font-poppins">
                SELAMAT DATANG DI SPARKING
            </h5>
            <h1 class="text-6xl text-blue-800 font-londrina drop-shadow-lg">SPARKING</h1>
            <p class="mt-2 text-base md:text-lg font-poppins">
                Layanan online parkir yang mempermudah hari anda
            </p>
        </div>

        <!-- Slider Images -->
        <div id="slider" class="flex h-full transition-transform duration-700 ease-in-out">
            <img src="img/Gedung.jpg" class="flex-shrink-0 object-cover w-full" alt="Gedung" />
        </div>
    </section>

    <!-- Konten lainnya tetap sama -->
    @include('landingPage.component.success-error')
    @include('landingPage.component.tentang_kami')
    @include('landingPage.component.keunggulan')
    @include('landingPage.component.footerLanding')
@endsection
