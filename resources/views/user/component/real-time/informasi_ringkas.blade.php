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
            'from-cyan-500 to-cyan-600'
        ];

        $zones = $zonas ?? [];
        $zoneCount = count($zones);
    @endphp

    @if($zoneCount > 0)
        <div id="zona-container" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
            @foreach ($zones as $index => $zone)
                @php
                    $available = $zone->available ?? 0;
                    $total = $zone->total ?? 0;
                    $percentage = $total > 0 ? round(($available / $total) * 100) : 0;
                    $colorIndex = $index % count($zoneColors);
                    $bgClass = $zoneColors[$colorIndex];
                @endphp

                <div id="zona-{{ $zone->id }}" class="relative overflow-hidden bg-gradient-to-br {{ $bgClass }} p-5 rounded-xl shadow-md text-white transform transition-all duration-300 hover:scale-[1.03] hover:shadow-xl group">
                    <div class="absolute top-0 right-0 w-16 h-16 -mr-5 -mt-5 rounded-full opacity-20 group-hover:opacity-30 transition-opacity duration-300"></div>
                    <div class="relative z-10">

                        {{-- Header zona --}}
                        <div class="flex items-start justify-between">
                            <h3 class="text-xl font-bold truncate">{{ $zone->nama_zona ?? 'Zona ' . ($index + 1) }}</h3>
                            <span id="percentage-zona-{{ $zone->id }}" class="px-2 py-1 text-xs font-bold bg-white bg-opacity-20 rounded-full">{{ $percentage }}%</span>
                        </div>

                        {{-- Info slot --}}
                        <div class="mt-4">
                            <p class="text-sm opacity-90">Slot Tersedia</p>
                            <div class="flex items-end justify-between mt-1">
                                <span id="available-zona-{{ $zone->id }}" class="text-3xl font-bold">{{ $available }}</span>
                                <span id="total-zona-{{ $zone->id }}" class="text-sm opacity-80">/{{ $total }} total</span>
                            </div>
                        </div>

                        {{-- Tombol notifikasi --}}
                        @auth
                        <div class="mt-4 pt-3 border-t border-white border-opacity-30">
                            <button
                                id="btn-notif-{{ $zone->id }}"
                                data-terdaftar="false"
                                onclick="toggleNotifikasi({{ $zone->id }}, this)"
                                class="w-full py-2 px-3 rounded-lg text-xs font-semibold transition-all duration-300
                                       bg-white bg-opacity-20 hover:bg-opacity-30 text-white border border-white border-opacity-40
                                       flex items-center justify-center gap-2 {{ $available > 0 ? 'hidden' : '' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                <span id="btn-notif-text-{{ $zone->id }}">🔕 Beritahu Saya</span>
                            </button>
                        </div>
                        @endauth

                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="py-12 text-center bg-gray-50 rounded-xl">
            <div class="relative inline-block mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-20 h-20 mx-auto text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="absolute inset-0 bg-gradient-to-br from-white to-transparent opacity-60 rounded-full"></div>
            </div>
            <h3 class="text-xl font-medium text-gray-600">Tidak ada data zona tersedia</h3>
            <p class="max-w-md mx-auto mt-2 text-gray-500">Admin belum menambahkan zona, silahkan tambahkan zona terlebih dahulu</p>
        </div>
    @endif
</div>

{{-- SATU script gabungan, tidak ada duplikasi --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {

        // Cek status notifikasi semua tombol saat pertama load
        document.querySelectorAll('[id^="btn-notif-"]').forEach(btn => {
            const zonaId = btn.id.replace('btn-notif-', '');
            cekStatusNotifikasi(zonaId);
        });

        // Polling realtime setiap 5 detik
        realtimeUpdate();
        setInterval(realtimeUpdate, 5000);
    });

    // ─── Realtime polling ────────────────────────────────────────
    function realtimeUpdate() {
        fetch('/api/zona-slot')
            .then(res => res.json())
            .then(data => {
                data.forEach(zona => updateUI(zona.id, zona.tersedia, zona.total));
            })
            .catch(err => console.error('Gagal mengambil data zona:', err));
    }

    function updateUI(zonaId, tersedia, total) {
        const availableEl  = document.getElementById(`available-zona-${zonaId}`);
        const percentageEl = document.getElementById(`percentage-zona-${zonaId}`);
        const totalEl      = document.getElementById(`total-zona-${zonaId}`);
        const btnNotif     = document.getElementById(`btn-notif-${zonaId}`);

        if (!availableEl || !percentageEl || !totalEl) return;

        const percentage = total === 0 ? 0 : Math.round((tersedia / total) * 100);
        availableEl.textContent  = tersedia;
        percentageEl.textContent = percentage + '%';
        totalEl.textContent      = `/${total} total`;

        // Show/hide tombol notifikasi berdasarkan ketersediaan slot
        if (btnNotif) {
            if (tersedia === 0) {
                btnNotif.classList.remove('hidden'); // penuh → tampilkan tombol
            } else {
                btnNotif.classList.add('hidden');    // ada slot → sembunyikan
            }
        }
    }

    // ─── Notifikasi ──────────────────────────────────────────────
    function cekStatusNotifikasi(zonaId) {
        fetch(`/notifikasi-slot/status?zona_id=${zonaId}`)
            .then(res => res.json())
            .then(data => updateTombolNotifikasi(zonaId, data.terdaftar))
            .catch(console.error);
    }

    function toggleNotifikasi(zonaId, btn) {
        const terdaftar = btn.dataset.terdaftar === 'true';
        const url = terdaftar ? '/notifikasi-slot/batal' : '/notifikasi-slot/daftar';

        btn.disabled = true;

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ zona_id: zonaId }),
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

    function updateTombolNotifikasi(zonaId, terdaftar) {
        const btn  = document.getElementById(`btn-notif-${zonaId}`);
        const text = document.getElementById(`btn-notif-text-${zonaId}`);
        if (!btn || !text) return;

        btn.dataset.terdaftar = String(terdaftar);
        text.textContent = terdaftar ? '🔔 Notifikasi Aktif' : '🔕 Beritahu Saya';
    }

    // // ─── Toast ───────────────────────────────────────────────────
    function showToast(message, type = 'success') {
        if (type === 'success') {
            // Buat modal success dinamis
            const modal = document.createElement('div');
            modal.id = 'modalSuccess';
            modal.className = 'fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50 backdrop-blur-sm transition-opacity duration-300 opacity-0';
            modal.innerHTML = `
                <div class="bg-white p-8 rounded-xl shadow-2xl text-center max-w-md w-full mx-4 animate-bounce-in">
                    <div class="h-1.5 bg-gray-200 rounded-full mb-6 overflow-hidden">
                        <div id="successProgress" class="h-full bg-green-500 rounded-full" style="width: 100%; transition: width 3s linear;"></div>
                    </div>
                    <div class="flex justify-center mb-4">
                        <svg class="w-16 h-16 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h2 class="text-3xl font-bold mb-4 text-green-600">Berhasil!</h2>
                    <p class="text-gray-700 mb-6 text-lg">${message}</p>
                    <button onclick="fadeOutModal('modalSuccess')"
                        class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition-colors duration-300 font-medium text-lg shadow-md">
                        Tutup
                    </button>
                </div>`;
            document.body.appendChild(modal);

        } else {
            // Buat modal error dinamis
            const modal = document.createElement('div');
            modal.id = 'modalError';
            modal.className = 'fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50 backdrop-blur-sm transition-opacity duration-300 opacity-0';
            modal.innerHTML = `
                <div class="bg-white p-8 rounded-xl shadow-2xl text-center max-w-md w-full mx-4 animate-bounce-in">
                    <div class="h-1.5 bg-gray-200 rounded-full mb-6 overflow-hidden">
                        <div id="errorProgress" class="h-full bg-red-500 rounded-full" style="width: 100%; transition: width 3s linear;"></div>
                    </div>
                    <div class="flex justify-center mb-4">
                        <svg class="w-16 h-16 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h2 class="text-3xl font-bold mb-4 text-red-600">Gagal!</h2>
                    <p class="text-gray-700 mb-6 text-lg">${message}</p>
                    <button onclick="fadeOutModal('modalError')"
                        class="bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700 transition-colors duration-300 font-medium text-lg shadow-md">
                        Tutup
                    </button>
                </div>`;
            document.body.appendChild(modal);
        }

        // Tampilkan modal dengan animasi
        const modalId = type === 'success' ? 'modalSuccess' : 'modalError';
        const progressId = type === 'success' ? 'successProgress' : 'errorProgress';

        setTimeout(() => {
            document.getElementById(modalId)?.classList.remove('opacity-0');
        }, 10);

        setTimeout(() => {
            const progress = document.getElementById(progressId);
            if (progress) progress.style.width = '0%';
        }, 50);

        // Tutup otomatis setelah 3 detik
        setTimeout(() => fadeOutModal(modalId), 3050);
    }
</script>
