{{-- ============================================================
     FILE   : resources/views/user/real-time.blade.php
     TUJUAN : Halaman utama real-time parkir — menampilkan denah,
              informasi ringkas per zona, dan informasi detail
              per sub-zona termasuk video stream kamera.
     ============================================================ --}}

@extends('layout.mainUser')

@include('user.component.headerUser')

@section('main')

{{-- ──────────────────────────────────────────────────────────────
     MODAL ALERT (success / error)
     Di-include dari komponen terpisah, tidak diubah.
────────────────────────────────────────────────────────────────── --}}
@include('user.component.success-error')


{{-- ──────────────────────────────────────────────────────────────
     WRAPPER UTAMA
────────────────────────────────────────────────────────────────── --}}
<div class="min-h-[calc(100vh-80px)] bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center p-4">
    <div class="w-full max-w-6xl">
        <div class="flex flex-col gap-6 p-6">


            {{-- ────────────────────────────────────────────────────
                 TOMBOL KEMBALI
            ──────────────────────────────────────────────────────── --}}
            <div class="mb-3 w-max">
                <a href="{{ route('user-dashboard') }}"
                   class="inline-flex items-center bg-white hover:bg-gray-50 border border-gray-200 text-gray-700 px-4 py-2 rounded-lg shadow-sm hover:shadow-md transition-all duration-300 hover:-translate-x-0.5">
                    <i class="mr-2 text-gray-500 fas fa-arrow-left"></i>
                    Kembali
                </a>
            </div>


            {{-- ────────────────────────────────────────────────────
                 DENAH LOKASI — klik untuk perbesar (image modal)
            ──────────────────────────────────────────────────────── --}}
            <div id="peta"
                 class="relative w-full h-64 overflow-hidden shadow-lg cursor-pointer rounded-2xl group"
                 onclick="openImageModal(this)">

                <img src="{{ asset('images/peta.png') }}"
                     alt="Parking Area"
                     class="object-cover w-full h-full transition-transform duration-300 group-hover:scale-105">

                {{-- Overlay teks di atas gambar --}}
                <div class="absolute inset-0 flex items-end p-6 bg-gradient-to-t from-blue-900/70 to-blue-500/30">
                    <div>
                        <h1 class="text-3xl font-bold text-white">Denah Lokasi Zona Parkiran</h1>
                        <p class="mt-2 text-blue-100">Klik gambar untuk memperbesar</p>
                    </div>
                </div>

                {{-- Hover overlay --}}
                <div class="absolute inset-0 flex items-center justify-center transition-opacity duration-300 bg-black rounded-lg opacity-0 bg-opacity-20 group-hover:opacity-100">
                    <span class="px-3 py-1 font-medium text-white bg-black bg-opacity-50 rounded-full">Lihat Ukuran Penuh</span>
                </div>
            </div>


            {{-- ════════════════════════════════════════════════════
                 BAGIAN 1 — INFORMASI RINGKAS ZONA
                 Menampilkan kartu per zona dengan slot tersedia,
                 total slot, persentase, dan tombol notifikasi.
            ════════════════════════════════════════════════════════ --}}
            <div id="zona-slot-wrapper" class="p-6 bg-white shadow-2xl rounded-2xl border border-gray-100 transition-all duration-300 hover:shadow-3xl">

                <div class="flex items-center justify-between mb-6">
                    <h2 class="flex items-center text-2xl font-bold text-gray-800">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mr-2 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z" />
                        </svg>
                        Informasi Ringkas Zona
                    </h2>
                </div>

                @php
                    $zoneColors = [
                        'from-blue-500 to-blue-600',
                        'from-green-500 to-green-600',
                        'from-purple-500 to-purple-600',
                        'from-amber-500 to-amber-600',
                        'from-rose-500 to-rose-600',
                        'from-emerald-500 to-emerald-600',
                        'from-indigo-500 to-indigo-600',
                        'from-cyan-500 to-cyan-600',
                    ];

                    $zones     = $zonas ?? [];
                    $zoneCount = count($zones);
                @endphp

                @if ($zoneCount > 0)
                    {{-- Grid kartu zona --}}
                    <div id="zona-container" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
                        @foreach ($zones as $index => $zone)
                            @php
                                $available   = $zone->available ?? 0;
                                $total       = $zone->total ?? 0;
                                $percentage  = $total > 0 ? round(($available / $total) * 100) : 0;
                                $colorIndex  = $index % count($zoneColors);
                                $bgClass     = $zoneColors[$colorIndex];
                            @endphp

                            <div id="zona-{{ $zone->id }}"
                                 class="relative overflow-hidden bg-gradient-to-br {{ $bgClass }} p-5 rounded-xl shadow-md text-white transform transition-all duration-300 hover:scale-[1.03] hover:shadow-xl group">

                                {{-- Dekorasi lingkaran background --}}
                                <div class="absolute top-0 right-0 w-16 h-16 -mr-5 -mt-5 rounded-full opacity-20 group-hover:opacity-30 transition-opacity duration-300"></div>

                                <div class="relative z-10">
                                    {{-- Header: nama zona + persentase --}}
                                    <div class="flex items-start justify-between">
                                        <h3 class="text-xl font-bold truncate">{{ $zone->nama_zona ?? 'Zona ' . ($index + 1) }}</h3>
                                        <span id="percentage-zona-{{ $zone->id }}"
                                              class="px-2 py-1 text-xs font-bold bg-white bg-opacity-20 rounded-full">{{ $percentage }}%</span>
                                    </div>

                                    {{-- Info slot tersedia --}}
                                    <div class="mt-4">
                                        <p class="text-sm opacity-90">Slot Tersedia</p>
                                        <div class="flex items-end justify-between mt-1">
                                            <span id="available-zona-{{ $zone->id }}" class="text-3xl font-bold">{{ $available }}</span>
                                            <span id="total-zona-{{ $zone->id }}" class="text-sm opacity-80">/{{ $total }} total</span>
                                        </div>
                                    </div>

                                    {{-- Tombol notifikasi (hanya untuk user yang login & slot penuh) --}}
                                    @auth
                                    <div class="mt-4 pt-3 border-t border-white border-opacity-30">
                                        <button
                                            id="btn-notif-{{ $zone->id }}"
                                            data-terdaftar="false"
                                            onclick="toggleNotifikasi({{ $zone->id }}, this)"
                                            class="w-full py-2 px-3 rounded-lg text-xs font-semibold transition-all duration-300
                                                   bg-white bg-opacity-20 hover:bg-opacity-30 text-white border border-white border-opacity-40
                                                   flex items-center justify-center gap-2 {{ $available > 0 ? 'hidden' : '' }}">
                                            <span id="btn-notif-text-{{ $zone->id }}">
                                                <i class="fas fa-bell mr-1"></i> Beritahu Saya
                                            </span>
                                        </button>
                                    </div>
                                    @endauth
                                </div>
                            </div>
                        @endforeach
                    </div>

                @else
                    {{-- Tampilan jika belum ada zona --}}
                    <div class="py-12 text-center bg-gray-50 rounded-xl">
                        <div class="relative inline-block mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-20 h-20 mx-auto text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-medium text-gray-600">Tidak ada data zona tersedia</h3>
                        <p class="max-w-md mx-auto mt-2 text-gray-500">Admin belum menambahkan zona, silahkan tambahkan zona terlebih dahulu</p>
                    </div>
                @endif

            </div>{{-- /Informasi Ringkas --}}


            {{-- ════════════════════════════════════════════════════
                 BAGIAN 2 — INFORMASI DETAIL ZONA & SUB-ZONA
                 Dropdown pilih zona → pilih sub-zona → tampilkan
                 statistik slot real-time + video stream kamera.
            ════════════════════════════════════════════════════════ --}}
            <div class="p-6 bg-white shadow-xl rounded-2xl">

                <h2 class="flex items-center mb-6 text-2xl font-bold text-gray-800">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mr-2 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    Informasi Detail Zona
                </h2>

                <div class="flex flex-col gap-6 lg:flex-row">

                    {{-- ── Panel kiri: Dropdown + status ringkas ── --}}
                    <div class="p-6 space-y-4 border border-gray-100 shadow-inner lg:w-1/3 bg-gray-50 rounded-xl">

                        {{-- Dropdown pilih zona --}}
                        <div class="relative mb-6">
                            <select id="zona-select"
                                    class="w-full px-4 py-3 bg-white border border-gray-300 shadow-sm appearance-none rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                                <option value="" disabled {{ is_null($selectedZonaId) ? 'selected' : '' }}>Pilih Zona</option>
                                @foreach ($zonas as $zona)
                                    <option value="{{ $zona->id }}" {{ $selectedZonaId == $zona->id ? 'selected' : '' }}>
                                        {{ $zona->nama_zona }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Dropdown pilih sub-zona (diisi dinamis via JS) --}}
                        <div class="relative mb-6">
                            <select id="subzona-select"
                                    class="w-full px-4 py-3 bg-white border border-gray-300 shadow-sm appearance-none rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                                <option value="" disabled {{ is_null($selectedSubzonaId) ? 'selected' : '' }}>Pilih Sub Zona</option>
                                @foreach ($subzonas as $subzona)
                                    <option value="{{ $subzona->id }}" {{ $selectedSubzonaId == $subzona->id ? 'selected' : '' }}>
                                        {{ $subzona->nama_subzona }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Ringkasan status (total zona, sub-zona, slot) --}}
                        <div class="p-4 bg-white border border-gray-200 rounded-xl">
                            <h4 class="mb-2 font-semibold text-blue-800">Status Real Time</h4>
                            <ul class="space-y-2">
                                <li class="flex justify-between">
                                    <span class="text-gray-600">Total Zona:</span>
                                    <span class="font-medium">{{ $totalZona }} Zona</span>
                                </li>
                                <li class="flex justify-between">
                                    <span class="text-gray-600">Total Sub Zona:</span>
                                    <span class="font-medium">{{ $totalSubzona }} Sub Zona</span>
                                </li>
                                <li class="flex justify-between">
                                    <span class="text-gray-600">Total Slot:</span>
                                    <span class="font-medium text-green-600">{{ $totalSlot }} Slot</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    {{-- ── Panel kanan: Detail statistik sub-zona ── --}}
                    <div class="p-6 bg-white border border-gray-100 shadow-lg lg:w-2/3 rounded-xl">
                        <h3 class="mb-4 text-xl font-semibold text-gray-800">Detail Sub Zona</h3>

                        {{-- Grid 4 kartu statistik slot --}}
                        <div class="grid grid-cols-1 gap-6 mb-6 md:grid-cols-2">

                            {{-- Total Slot --}}
                            <div class="p-4 border border-gray-200 rounded-lg shadow-sm bg-gray-50">
                                <div class="flex items-center mb-2">
                                    <div class="p-2 mr-3 bg-gray-100 rounded-full">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                        </svg>
                                    </div>
                                    <h4 class="font-medium text-gray-700">Total Slot Subzona</h4>
                                </div>
                                <p id="total" class="text-3xl font-bold text-gray-900 ml-11">
                                    {{ $selectedSubzonaId ? $slotStats['total'] : '-' }}
                                </p>
                            </div>

                            {{-- Slot Tersedia --}}
                            <div class="p-4 border border-gray-200 rounded-lg shadow-sm bg-gray-50">
                                <div class="flex items-center mb-2">
                                    <div class="p-2 mr-3 bg-blue-100 rounded-full">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <h4 class="font-medium text-gray-700">Slot parkiran yang tersedia</h4>
                                </div>
                                <p id="tersedia" class="text-3xl font-bold text-blue-600 ml-11">
                                    {{ $selectedSubzonaId ? $slotStats['tersedia'] : '-' }}
                                </p>
                            </div>

                            {{-- Slot Terisi --}}
                            <div class="p-4 border border-gray-200 rounded-lg shadow-sm bg-gray-50">
                                <div class="flex items-center mb-2">
                                    <div class="p-2 mr-3 bg-red-100 rounded-full">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </div>
                                    <h4 class="font-medium text-gray-700">Slot parkiran yang terisi</h4>
                                </div>
                                <p id="terisi" class="text-3xl font-bold text-red-600 ml-11">
                                    {{ $selectedSubzonaId ? $slotStats['terisi'] : '-' }}
                                </p>
                            </div>

                            {{-- Slot Diperbaiki --}}
                            <div class="p-4 border border-gray-200 rounded-lg shadow-sm bg-gray-50">
                                <div class="flex items-center mb-2">
                                    <div class="p-2 mr-3 bg-yellow-100 rounded-full">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <h4 class="font-medium text-gray-700">Slot parkiran yang sedang diperbaiki</h4>
                                </div>
                                <p id="perbaikan" class="text-3xl font-bold text-yellow-600 ml-11">
                                    {{ $selectedSubzonaId ? $slotStats['diperbaiki'] : '-' }}
                                </p>
                            </div>

                        </div>{{-- /Grid statistik --}}


                        {{-- ── Tombol buka modal sub-zona (detail + video) ── --}}
                        <button onclick="showSubzoneModal()"
                                class="w-full py-3 px-6 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-medium rounded-xl shadow-md transition duration-300 ease-in-out transform hover:scale-[1.02] flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            Tampilkan Sub Zona
                        </button>

                    </div>{{-- /Panel kanan --}}

                </div>{{-- /flex row --}}

            </div>{{-- /Informasi Detail --}}


            {{-- ════════════════════════════════════════════════════
                 MODAL — DETAIL SUB-ZONA
                 Menampilkan video stream kamera + denah slot.
                 Dibuka oleh showSubzoneModal(), ditutup oleh
                 tombol closeModalBtn atau klik di luar.
            ════════════════════════════════════════════════════════ --}}
            <div id="subZoneModal" class="fixed inset-0 z-50 hidden overflow-hidden">
                {{-- Backdrop --}}
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"></div>

                <div class="fixed inset-0 flex items-center justify-center p-4">
                    <div class="relative w-full max-w-4xl max-h-[90vh] overflow-y-auto bg-white rounded-lg shadow-xl">

                        {{-- Tombol tutup modal --}}
                        <button id="closeModalBtn"
                                class="absolute z-50 p-2 text-white rounded-full top-6 right-6 hover:text-gray-300 bg-black/40 backdrop-blur-md">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>

                        <div class="p-6">
                            {{-- Judul sub-zona (diisi JS) --}}
                            <h3 class="mb-4 text-2xl font-bold" id="subzoneName">Sub Zona: Memuat...</h3>

                            {{-- Video stream kamera --}}
                            <div class="mb-6 overflow-hidden rounded-lg shadow-md">
                                <img id="subzoneStream"
                                     class="object-cover w-full h-auto max-h-[85vh] rounded-lg shadow-md"
                                     src=""
                                     alt="Stream Kamera Subzona">
                            </div>

                            {{-- Denah slot --}}
                            <div>
                                <h4 class="mb-3 text-lg font-semibold">Denah Slot</h4>

                                {{-- Grid slot (diisi JS) --}}
                                <div class="grid grid-cols-10 gap-2" id="slotGrid"></div>

                                {{-- Legenda warna --}}
                                <div class="flex flex-wrap gap-4 mt-4">
                                    <div class="flex items-center">
                                        <div class="w-4 h-4 mr-2 bg-blue-500 rounded"></div>
                                        <span class="text-sm">Tersedia</span>
                                    </div>
                                    <div class="flex items-center">
                                        <div class="w-4 h-4 mr-2 bg-red-500 rounded"></div>
                                        <span class="text-sm">Terisi</span>
                                    </div>
                                    <div class="flex items-center">
                                        <div class="w-4 h-4 mr-2 bg-yellow-300 rounded"></div>
                                        <span class="text-sm">Perbaikan</span>
                                    </div>
                                </div>

                                {{-- Ringkasan statistik di dalam modal --}}
                                <div class="grid grid-cols-1 gap-4 mt-6 md:grid-cols-4" id="slotStats">
                                    <div class="p-3 rounded-lg bg-blue-50">
                                        <h5 class="font-medium text-blue-800">Tersedia</h5>
                                        <p class="text-2xl font-bold text-blue-600" id="tersediaCount">0</p>
                                    </div>
                                    <div class="p-3 rounded-lg bg-red-50">
                                        <h5 class="font-medium text-red-800">Terisi</h5>
                                        <p class="text-2xl font-bold text-red-600" id="terisiCount">0</p>
                                    </div>
                                    <div class="p-3 rounded-lg bg-yellow-50">
                                        <h5 class="font-medium text-yellow-500">Perbaikan</h5>
                                        <p class="text-2xl font-bold text-yellow-300" id="diperbaikiCount">0</p>
                                    </div>
                                </div>
                            </div>{{-- /Denah slot --}}
                        </div>
                    </div>
                </div>
            </div>{{-- /subZoneModal --}}


            {{-- ════════════════════════════════════════════════════
                 MODAL — ZOOM GAMBAR DENAH
                 Dibuka saat user klik gambar peta di atas.
            ════════════════════════════════════════════════════════ --}}
            <div id="imageModal"
                 class="fixed inset-0 z-50 items-center justify-center hidden p-4 bg-black bg-opacity-90 animate-fadeIn">
                <div class="relative max-w-6xl w-full max-h-[30vh] mx-auto my-auto">

                    {{-- Tombol tutup --}}
                    <button id="close"
                            onclick="closeImageModal()"
                            class="absolute text-3xl text-white transition-transform duration-200 top-2 right-2 hover:text-gray-300 focus:outline-none hover:scale-110"
                            aria-label="Tutup modal">
                        <div class="flex items-center justify-center w-10 h-10 transition-all bg-black bg-opacity-50 rounded-full hover:bg-opacity-75">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                    </button>

                    <div class="flex items-center justify-center h-full">
                        <img id="modalImage" src="" alt="Zoomed Image"
                             class="max-w-full max-h-[90vh] object-contain animate-zoomIn">
                    </div>
                </div>
            </div>{{-- /imageModal --}}


        </div>
    </div>
</div>{{-- /wrapper utama --}}


{{-- ================================================================
     JAVASCRIPT
     Semua logika real-time ada di sini, dibagi per bagian:
       1. Image Modal        — buka/tutup zoom gambar denah
       2. VideoStream class  — kelola stream kamera subzona
       3. DOM Ready          — inisialisasi event & polling
       4. Subzona loader     — fetch data + update UI subzona
       5. Sub-zone modal     — buka/update modal denah slot
       6. Notifikasi zona    — cek status, toggle, update tombol
       7. Zona ringkas poll  — polling tiap 5 detik kartu zona
================================================================ --}}
<script>

/* ──────────────────────────────────────────────────────────────────
   1. IMAGE MODAL — zoom gambar peta
────────────────────────────────────────────────────────────────── */

/** Buka modal zoom dengan src gambar dari element yang diklik */
function openImageModal(element) {
    const imgSrc   = element.querySelector('img').src;
    const modal    = document.getElementById('imageModal');
    const modalImg = document.getElementById('modalImage');

    modalImg.src = imgSrc;
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';

    // Fokus ke tombol tutup untuk aksesibilitas
    setTimeout(() => modal.querySelector('button').focus(), 100);
}

/** Tutup modal zoom */
function closeImageModal() {
    document.getElementById('imageModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Tutup jika klik di luar gambar
document.getElementById('imageModal').addEventListener('click', function (e) {
    if (e.target === this) closeImageModal();
});

// Tutup dengan tombol Escape
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && !document.getElementById('imageModal').classList.contains('hidden')) {
        closeImageModal();
    }
});


/* ──────────────────────────────────────────────────────────────────
   2. VideoStream — kelola MJPEG stream dari kamera subzona
────────────────────────────────────────────────────────────────── */
class VideoStream {
    /**
     * @param {number} cameraId   - ID kamera dari database
     * @param {string} elementId  - ID elemen <img> tujuan
     * @param {number} subzonaId  - ID sub-zona aktif
     */
    constructor(cameraId, elementId, subzonaId) {
        if (!subzonaId) throw new Error('subzonaId diperlukan');
        this.cameraId  = cameraId;
        this.elementId = elementId;
        this.subzonaId = subzonaId;
        this.init();
    }

    /** Pasang URL stream ke elemen <img> */
    init() {
        this.streamUrl = `{{ config('services.cloudflare.url') }}/clean_video_feed?camera_id=${this.cameraId}&subzona_id=${this.subzonaId}`;
        console.log('Starting stream:', this.streamUrl);

        const el  = document.getElementById(this.elementId);
        el.src    = this.streamUrl;
        el.onerror = () => {
            console.error('Stream error, retry dalam 3 detik...');
            this.retry();
        };
    }

    /** Coba ulang setelah error */
    retry() { setTimeout(() => this.init(), 3000); }

    /** Hentikan stream */
    stop() {
        document.getElementById(this.elementId).src = '';
        console.log('Stream dihentikan');
    }
}


/* ──────────────────────────────────────────────────────────────────
   3. DOM READY — inisialisasi event listener & polling
────────────────────────────────────────────────────────────────── */
let pollingInterval    = null;   // interval polling untuk modal sub-zona
let currentSubzonaId   = "{{ $selectedSubzonaId ?? '' }}";
let videoStreamInstance = null;  // instance VideoStream aktif

document.addEventListener('DOMContentLoaded', () => {
    const zonaSelect    = document.getElementById('zona-select');
    const subzonaSelect = document.getElementById('subzona-select');
    const closeBtn      = document.getElementById('closeModalBtn');

    // ── Tutup modal sub-zona ──────────────────────────────────────
    closeBtn?.addEventListener('click', () => {
        document.getElementById('subZoneModal').classList.add('hidden');
        if (pollingInterval)    clearInterval(pollingInterval);
        if (videoStreamInstance) { videoStreamInstance.stop(); videoStreamInstance = null; }
    });

    // ── Zona berubah: reset sub-zona & slot stats ─────────────────
    zonaSelect?.addEventListener('change', function () {
        const zonaId = this.value;
        if (!zonaId) return;

        // Reset dropdown sub-zona
        subzonaSelect.innerHTML = '<option value="" disabled selected>Pilih Sub Zona</option>';
        resetSlotStats();
        currentSubzonaId = '';

        // Hentikan stream jika ada
        if (videoStreamInstance) { videoStreamInstance.stop(); videoStreamInstance = null; }

        // Ambil daftar sub-zona untuk zona terpilih
        fetch(`/api/get-subzonas/${zonaId}`)
            .then(res => {
                if (!res.ok) throw new Error(`HTTP ${res.status}`);
                return res.json();
            })
            .then(data => {
                data.forEach(subzona => {
                    const opt       = document.createElement('option');
                    opt.value       = subzona.id;
                    opt.textContent = subzona.nama_subzona;
                    subzonaSelect.appendChild(opt);
                });
            })
            .catch(err => console.error('Gagal mengambil subzona:', err));
    });

    // ── Sub-zona berubah: muat data ───────────────────────────────
    subzonaSelect?.addEventListener('change', function () {
        currentSubzonaId = this.value;
        if (!currentSubzonaId) return;
        loadSubzonaData(currentSubzonaId);
    });

    // ── Jika ada sub-zona terpilih dari server (initial load) ─────
    if (currentSubzonaId) {
        subzonaSelect.value = currentSubzonaId;
        loadSubzonaData(currentSubzonaId);
    }

    // ── Polling statistik slot setiap 2 detik ────────────────────
    setInterval(() => {
        if (!currentSubzonaId) return;
        fetch(`/api/real-time/subzona/${currentSubzonaId}`)
            .then(res => res.json())
            .then(data => updateSlotStats(data.slotStats ?? {}))
            .catch(console.error);
    }, 2000);

    // ── Cek status notifikasi semua tombol zona saat load ─────────
    document.querySelectorAll('[id^="btn-notif-"]').forEach(btn => {
        const zonaId = btn.id.replace('btn-notif-', '');
        cekStatusNotifikasi(zonaId);
    });

    // ── Polling kartu ringkas zona setiap 5 detik ─────────────────
    realtimeUpdateZona();
    setInterval(realtimeUpdateZona, 5000);
});


/* ──────────────────────────────────────────────────────────────────
   4. SUBZONA LOADER — fetch data sub-zona dan perbarui UI
────────────────────────────────────────────────────────────────── */

/** Muat data sub-zona dari API lalu update seluruh UI terkait */
async function loadSubzonaData(subzonaId) {
    try {
        const res = await fetch(`/api/real-time/subzona/${subzonaId}`);
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        const data = await res.json();

        updateSlotStats(data.slotStats);
        document.getElementById('subzoneName').textContent = `Sub Zona: ${data.nama_subzona}`;

        // Mulai atau perbarui video stream jika camera_id tersedia
        if (Number.isInteger(data.camera_id)) {
            if (videoStreamInstance) videoStreamInstance.stop();
            videoStreamInstance = new VideoStream(data.camera_id, 'subzoneStream', subzonaId);
        } else {
            console.warn('Camera ID tidak tersedia untuk sub-zona ini');
            if (videoStreamInstance) { videoStreamInstance.stop(); videoStreamInstance = null; }
        }

        // Perbarui modal jika sedang terbuka
        if (!document.getElementById('subZoneModal').classList.contains('hidden')) {
            updateSubzoneModal(data);
        }

    } catch (err) {
        console.error('Gagal memuat data subzona:', err);
        resetSlotStats();
    }
}

/** Reset semua angka statistik ke '-' */
function resetSlotStats() {
    ['total', 'terisi', 'tersedia', 'perbaikan'].forEach(id => {
        document.getElementById(id).innerText = '-';
    });
}

/** Update angka statistik di panel detail */
function updateSlotStats(stats) {
    if (!stats) return resetSlotStats();
    document.getElementById('total').innerText    = stats.total    ?? '-';
    document.getElementById('terisi').innerText   = stats.terisi   ?? '-';
    document.getElementById('tersedia').innerText = stats.tersedia ?? '-';
    document.getElementById('perbaikan').innerText = stats.perbaikan ?? '-';
}


/* ──────────────────────────────────────────────────────────────────
   5. MODAL SUB-ZONA — buka modal & render denah slot
────────────────────────────────────────────────────────────────── */

/** Buka modal sub-zona: validasi, fetch data, mulai polling */
function showSubzoneModal() {
    const subzonaId = document.getElementById('subzona-select').value;
    if (!subzonaId) {
        alert('Silakan pilih Sub Zona terlebih dahulu.');
        return;
    }

    fetch(`/api/real-time/subzona/${subzonaId}`)
        .then(res => {
            if (!res.ok) throw new Error(`Gagal mengambil data: ${res.status}`);
            return res.json();
        })
        .then(data => {
            updateSubzoneModal(data);
            document.getElementById('subZoneModal').classList.remove('hidden');

            // Polling isi modal setiap 3 detik selama modal terbuka
            if (pollingInterval) clearInterval(pollingInterval);
            pollingInterval = setInterval(() => {
                fetch(`/api/real-time/subzona/${subzonaId}`)
                    .then(r => r.json())
                    .then(updateSubzoneModal)
                    .catch(console.error);
            }, 3000);
        })
        .catch(err => {
            console.error('Error saat mengambil data subzona:', err);
            alert('Gagal memuat data subzona. Pastikan ID valid atau coba lagi nanti.');
        });
}

/**
 * Render ulang konten modal sub-zona (judul, video, grid slot, statistik)
 * @param {Object} data - response dari /api/real-time/subzona/:id
 */
function updateSubzoneModal(data) {
    if (!data) return;

    document.getElementById('subzoneName').textContent = `Sub Zona: ${data.nama_subzona}`;

    // Perbarui stream kamera jika sudah ada instance
    if (data.camera_id && videoStreamInstance) {
        videoStreamInstance.cameraId = data.camera_id;
        videoStreamInstance.init();
    }

    // Render grid slot dengan warna sesuai status
    const slotGrid = document.getElementById('slotGrid');
    slotGrid.innerHTML = '';

    const colorMap = {
        'Tersedia'  : 'bg-blue-500 text-white',
        'Terisi'    : 'bg-red-500 text-white',
        'Perbaikan' : 'bg-yellow-300 text-black',
    };

    data.slots?.forEach(slot => {
        const colorClass = colorMap[slot.keterangan] ?? 'bg-gray-400 text-white';
        const div        = document.createElement('div');
        div.className    = `w-full aspect-square rounded flex items-center justify-center text-xs cursor-pointer hover:opacity-80 ${colorClass}`;
        div.title        = `Slot ${slot.nomor_slot} (${slot.keterangan})`;
        div.textContent  = slot.nomor_slot;
        slotGrid.appendChild(div);
    });

    // Update counter ringkasan di dalam modal
    if (data.slotStats) {
        document.getElementById('tersediaCount').textContent  = data.slotStats.tersedia;
        document.getElementById('terisiCount').textContent    = data.slotStats.terisi;
        document.getElementById('diperbaikiCount').textContent = data.slotStats.perbaikan;
    }
}


/* ──────────────────────────────────────────────────────────────────
   6. NOTIFIKASI ZONA — daftar/batal notifikasi slot penuh
────────────────────────────────────────────────────────────────── */

/** Cek apakah user sudah terdaftar notifikasi untuk zona ini */
function cekStatusNotifikasi(zonaId) {
    fetch(`/notifikasi-slot/status?zona_id=${zonaId}`)
        .then(res => res.json())
        .then(data => updateTombolNotifikasi(zonaId, data.terdaftar))
        .catch(console.error);
}

/**
 * Toggle daftar/batal notifikasi untuk zona
 * @param {number} zonaId
 * @param {HTMLElement} btn - tombol yang diklik
 */
function toggleNotifikasi(zonaId, btn) {
    const terdaftar = btn.dataset.terdaftar === 'true';
    const url       = terdaftar ? '/notifikasi-slot/batal' : '/notifikasi-slot/daftar';

    btn.disabled = true;

    fetch(url, {
        method  : 'POST',
        headers : {
            'Content-Type' : 'application/json',
            'X-CSRF-TOKEN' : document.querySelector('meta[name="csrf-token"]').content,
        },
        body : JSON.stringify({ zona_id: zonaId }),
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            updateTombolNotifikasi(zonaId, !terdaftar);
            showToast(data.message, 'success');
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(() => showToast('Terjadi kesalahan, coba lagi.', 'error'))
    .finally(() => { btn.disabled = false; });
}

/**
 * Perbarui tampilan tombol notifikasi berdasarkan status terdaftar
 * @param {number}  zonaId
 * @param {boolean} terdaftar
 */
function updateTombolNotifikasi(zonaId, terdaftar) {
    const btn  = document.getElementById(`btn-notif-${zonaId}`);
    const text = document.getElementById(`btn-notif-text-${zonaId}`);
    if (!btn || !text) return;

    btn.dataset.terdaftar = String(terdaftar);
    text.innerHTML = terdaftar
        ? `<i class="fas fa-bell-slash mr-1"></i> Notifikasi Aktif`
        : `<i class="fas fa-bell mr-1"></i> Beritahu Saya`;
}


/* ──────────────────────────────────────────────────────────────────
   7. TOAST / MODAL FEEDBACK — muncul saat toggle notifikasi berhasil
      atau gagal. Dibuat dinamis agar tidak konflik dengan modal lain.
────────────────────────────────────────────────────────────────── */

/**
 * Tampilkan modal feedback (success / error) secara dinamis.
 * Modal akan hilang otomatis setelah 3 detik.
 * @param {string} message - pesan yang ditampilkan
 * @param {'success'|'error'} type
 */
function showToast(message, type = 'success') {
    const isSuccess = type === 'success';
    const modalId   = isSuccess ? 'modalSuccess' : 'modalError';
    const color     = isSuccess ? 'green' : 'red';
    const label     = isSuccess ? 'Berhasil!' : 'Gagal!';
    const iconPath  = isSuccess
        ? 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'
        : 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z';

    const modal = document.createElement('div');
    modal.id        = modalId;
    modal.className = 'fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50 backdrop-blur-sm transition-opacity duration-300 opacity-0';
    modal.innerHTML = `
        <div class="bg-white p-8 rounded-xl shadow-2xl text-center max-w-md w-full mx-4">
            <div class="h-1.5 bg-gray-200 rounded-full mb-6 overflow-hidden">
                <div id="${modalId}Progress" class="h-full bg-${color}-500 rounded-full" style="width:100%; transition: width 3s linear;"></div>
            </div>
            <div class="flex justify-center mb-4">
                <svg class="w-16 h-16 text-${color}-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${iconPath}"></path>
                </svg>
            </div>
            <h2 class="text-3xl font-bold mb-4 text-${color}-600">${label}</h2>
            <p class="text-gray-700 mb-6 text-lg">${message}</p>
            <button onclick="fadeOutModal('${modalId}')"
                    class="bg-${color}-600 text-white px-6 py-3 rounded-lg hover:bg-${color}-700 transition-colors duration-300 font-medium text-lg shadow-md">
                Tutup
            </button>
        </div>`;
    document.body.appendChild(modal);

    // Fade in
    setTimeout(() => modal.classList.remove('opacity-0'), 10);

    // Mulai progress bar mengecil
    setTimeout(() => {
        const bar = document.getElementById(`${modalId}Progress`);
        if (bar) bar.style.width = '0%';
    }, 50);

    // Tutup otomatis setelah 3 detik
    setTimeout(() => fadeOutModal(modalId), 3050);
}

/**
 * Fade out lalu hapus modal dari DOM
 * @param {string} modalId
 */
function fadeOutModal(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;
    modal.classList.add('opacity-0');
    setTimeout(() => modal.remove(), 300);
}


/* ──────────────────────────────────────────────────────────────────
   8. ZONA RINGKAS POLLING — perbarui kartu zona tiap 5 detik
────────────────────────────────────────────────────────────────── */

/** Fetch semua data slot zona dan perbarui kartu ringkas */
function realtimeUpdateZona() {
    fetch('/api/zona-slot')
        .then(res => res.json())
        .then(data => data.forEach(zona => updateKartuZona(zona.id, zona.tersedia, zona.total)))
        .catch(err => console.error('Gagal mengambil data zona:', err));
}

/**
 * Perbarui tampilan satu kartu zona
 * @param {number} zonaId
 * @param {number} tersedia - slot tersedia saat ini
 * @param {number} total    - total slot zona
 */
function updateKartuZona(zonaId, tersedia, total) {
    const availableEl  = document.getElementById(`available-zona-${zonaId}`);
    const percentageEl = document.getElementById(`percentage-zona-${zonaId}`);
    const totalEl      = document.getElementById(`total-zona-${zonaId}`);
    const btnNotif     = document.getElementById(`btn-notif-${zonaId}`);

    if (!availableEl || !percentageEl || !totalEl) return;

    const percentage         = total === 0 ? 0 : Math.round((tersedia / total) * 100);
    availableEl.textContent  = tersedia;
    percentageEl.textContent = percentage + '%';
    totalEl.textContent      = `/${total} total`;

    // Tampilkan tombol notifikasi hanya jika parkir penuh (tersedia = 0)
    if (btnNotif) {
        btnNotif.classList.toggle('hidden', tersedia !== 0);
    }
}

</script>

@endsection