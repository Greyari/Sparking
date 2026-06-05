@extends('layout.mainUser')
@include('user.component.headerUser')

@section('main')

{{-- ============================================================
     HALAMAN STATISTIK PARKIR
     - Statistik Kendaraan (Chart mingguan per zona)
     - Analisis Jam Sibuk (Hari & jam terpadat)
     ============================================================ --}}

<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 p-4 md:p-8">
    <div class="max-w-4xl mx-auto space-y-6">


        {{-- ============================================================
             TOMBOL KEMBALI
             ============================================================ --}}
        <div class="mb-3 w-max">
            <a href="{{ route('user-dashboard') }}"
                class="inline-flex items-center bg-white hover:bg-gray-50 border border-gray-200 text-gray-700 px-4 py-2 rounded-lg shadow-sm hover:shadow-md transition-all duration-300 hover:-translate-x-0.5">
                <i class="fas fa-arrow-left mr-2 text-gray-500"></i>
                Kembali
            </a>
        </div>


        {{-- ============================================================
             DROPDOWN PILIH ZONA PARKIR
             - Berisi semua zona dari database ($zonas)
             - Perubahan zona akan trigger fetch chart via JS di bawah
             ============================================================ --}}
        <div class="bg-white rounded-xl shadow-lg overflow-hidden transition-all duration-500 hover:shadow-xl transform hover:-translate-y-1">
            <div class="p-5">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <h2 class="text-xl font-bold text-gray-800 mb-3 md:mb-0 flex items-center">
                        <i class="fas fa-map-marker-alt text-blue-500 mr-3"></i>
                        Pilih Zona Parkir
                    </h2>
                    <select id="zoneSelect"
                        class="px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300 bg-white shadow-sm">
                        @foreach ($zonas as $zona)
                            <option value="{{ $zona->id }}">{{ $zona->nama_zona }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>


        {{-- ============================================================
             STATISTIK KENDARAAN — CHART MINGGUAN
             - Chart bar ditampilkan di dalam .chart-container
             - Data diambil dari API: /api/statistik-zona?zona_id=...
             - Statistik ringkas: Total, Hari Puncak, Rata-rata
             ============================================================ --}}
        <div class="bg-white rounded-xl shadow-lg overflow-hidden transition-all duration-500 hover:shadow-xl">
            <div class="p-5">

                {{-- Header Statistik Kendaraan --}}
                <div class="flex items-center mb-4">
                    <div class="p-3 mr-4 rounded-lg bg-blue-100 text-blue-600">
                        <i class="fas fa-car text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">Statistik Kendaraan</h2>
                        <p class="text-sm text-gray-500">Jumlah penggunaan slot parkir selama satu minggu terakhir</p>
                    </div>
                </div>

                {{-- Area Canvas Chart (dinamis, diganti lewat JS) --}}
                <div class="chart-container flex justify-center h-64 md:h-80">
                    <canvas id="vehicleChart"></canvas>
                </div>

                {{-- Ringkasan Angka: Total, Hari Puncak, Rata-rata --}}
                <div class="mt-4 flex justify-center space-x-4">
                    <div class="text-center p-3 bg-green-50 rounded-lg w-24">
                        <div class="text-2xl font-bold text-green-600" id="totalVehicles">0</div>
                        <div class="text-xs text-gray-500">Total</div>
                    </div>
                    <div class="text-center p-3 bg-red-50 rounded-lg w-24">
                        <div class="text-2xl font-bold text-red-600" id="peakDay">-</div>
                        <div class="text-xs text-gray-500">Hari Puncak</div>
                    </div>
                    <div class="text-center p-3 bg-yellow-50 rounded-lg w-24">
                        <div class="text-2xl font-bold text-yellow-600" id="avgVehicles">0</div>
                        <div class="text-xs text-gray-500">Rata-rata</div>
                    </div>
                </div>

            </div>
        </div>


        {{-- ============================================================
             ANALISIS JAM SIBUK PARKIR
             - Data diambil dari API: /api/statistik-zona?zona_id=...
             - Menampilkan jam sibuk per hari dengan warna sesuai kepadatan
             ============================================================ --}}
        <div class="bg-white rounded-xl shadow-lg overflow-hidden transition-all duration-500 hover:shadow-xl">
            <div class="p-5">

                {{-- Header Jam Sibuk --}}
                <div class="flex items-center mb-4">
                    <div class="p-3 mr-4 rounded-lg bg-blue-100 text-blue-600">
                        <i class="fas fa-clock text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">Analisis Jam Sibuk Parkir</h2>
                        <p class="text-sm text-gray-500">Waktu dengan kepadatan kendaraan tertinggi</p>
                    </div>
                </div>

                {{-- Card: Hari Terpadat & Jam Puncak --}}
                <div class="grid md:grid-cols-2 gap-4 mb-4">

                    {{-- Card Hari Terpadat --}}
                    <div class="bg-gradient-to-r from-red-50 to-orange-50 p-4 rounded-xl border border-red-100 h-full">
                        <div class="flex h-full">
                            <div class="flex-1 flex flex-col justify-start">
                                <div class="flex items-center mb-2">
                                    <div class="p-2 mr-3 bg-red-100 rounded-lg text-red-600">
                                        <i class="fas fa-calendar-check"></i>
                                    </div>
                                    <h3 class="font-semibold text-gray-800">Hari Terpadat</h3>
                                </div>
                                <div>
                                    {{-- Diisi via JS dari API --}}
                                    <p class="text-2xl font-bold text-gray-800" id="hariTerpadatText">-</p>
                                    <p class="text-sm text-gray-500" id="hariTerpadatDesc">Memuat data...</p>
                                </div>
                            </div>
                            <div class="flex items-center pl-4">
                                <span class="flex items-center px-3 py-1 bg-blue-100 text-blue-600 rounded-full text-xs font-medium whitespace-nowrap">
                                    <i class="fas fa-chart-line mr-1"></i> Puncak
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Card Jam Puncak --}}
                    <div class="bg-gradient-to-r from-red-50 to-orange-50 p-4 rounded-xl border border-red-100 h-full">
                        <div class="flex h-full">
                            <div class="flex-1 flex flex-col justify-start">
                                <div class="flex items-center mb-2">
                                    <div class="p-2 mr-3 bg-red-100 rounded-lg text-red-600">
                                        <i class="fas fa-fire-alt"></i>
                                    </div>
                                    <h3 class="font-semibold text-gray-800">Jam Puncak</h3>
                                </div>
                                <div>
                                    {{-- Diisi via JS dari API --}}
                                    <p class="text-2xl font-bold text-gray-800" id="jamPuncakText">-</p>
                                    <p class="text-sm text-gray-500" id="durasiText">Memuat data...</p>
                                </div>
                            </div>
                            <div class="flex items-center pl-4">
                                <span class="flex items-center px-3 py-1 bg-red-100 text-red-600 rounded-full text-xs font-medium animate-pulse whitespace-nowrap">
                                    <i class="fas fa-exclamation-triangle mr-1"></i> Sibuk
                                </span>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Tabel Detail Jam Sibuk per Hari --}}
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="font-semibold text-gray-800 flex items-center">
                            <i class="fas fa-calendar-week mr-2 text-blue-500"></i>
                            Detail Jam Sibuk per Hari
                        </h3>
                    </div>

                    {{-- Diisi via JS dari API --}}
                    <div id="jam-sibuk-container" class="space-y-3">
                        <div class="flex justify-center py-4">
                            <div class="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-blue-500"></div>
                        </div>
                    </div>

                    {{-- Legenda Warna --}}
                    <div class="mt-4 pt-3 border-t border-gray-200 flex items-center flex-wrap gap-y-2">
                        <div class="flex items-center mr-4">
                            <span class="w-3 h-3 bg-red-500 rounded-full mr-1"></span>
                            <span class="text-xs text-gray-600">Sangat Sibuk (≥10)</span>
                        </div>
                        <div class="flex items-center mr-4">
                            <span class="w-3 h-3 bg-orange-400 rounded-full mr-1"></span>
                            <span class="text-xs text-gray-600">Sibuk (≥7)</span>
                        </div>
                        <div class="flex items-center mr-4">
                            <span class="w-3 h-3 bg-yellow-400 rounded-full mr-1"></span>
                            <span class="text-xs text-gray-600">Agak Sibuk (≥4)</span>
                        </div>
                        <div class="flex items-center">
                            <span class="w-3 h-3 bg-blue-400 rounded-full mr-1"></span>
                            <span class="text-xs text-gray-600">Normal</span>
                        </div>
                    </div>
                </div>

                {{-- Tips Parkir --}}
                <div class="mt-4 bg-indigo-50 rounded-lg p-3 border border-indigo-100">
                    <p class="text-sm text-indigo-800 flex items-start">
                        <i class="fas fa-lightbulb mr-2 mt-1 text-indigo-500"></i>
                        <span>
                            <span class="font-medium">Tips:</span>
                            Untuk pengalaman parkir lebih nyaman, hindari jam-jam sibuk atau datang 30 menit lebih awal.
                        </span>
                    </p>
                </div>

            </div>
        </div>


    </div>
</div>


{{-- ============================================================
     JAVASCRIPT — CHART KENDARAAN PER ZONA
     Trigger: perubahan pada #zoneSelect (dropdown zona)
     Flow:
       1. Tampilkan loading spinner
       2. Fetch data dari /api/statistik-zona?zona_id=...
       3. Update angka ringkasan (total, avg, peak)
       4. Render ulang Chart.js dengan data baru
       5. Highlight bar hari puncak dengan warna merah
       6. Update card hari terpadat & jam puncak
       7. Render detail jam sibuk per hari
     ============================================================ --}}
<script>
    document.getElementById('zoneSelect').addEventListener('change', function () {
        const zonaId = this.value;
        const chartContainer = document.querySelector('.chart-container');

        // --- Tampilkan loading spinner saat fetch berlangsung ---
        chartContainer.innerHTML = `
            <div class="chart-loading flex justify-center items-center h-full">
                <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500"></div>
            </div>`;

        // --- Tampilkan loading spinner di jam sibuk ---
        document.getElementById('jam-sibuk-container').innerHTML = `
            <div class="flex justify-center py-4">
                <div class="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-blue-500"></div>
            </div>`;

        // --- Fetch data statistik zona dari API ---
        fetch(`/api/statistik-zona?zona_id=${zonaId}`)
            .then(res => res.json())
            .then(data => {

                // --- Update ringkasan angka chart ---
                document.getElementById('totalVehicles').innerText = data.total;
                document.getElementById('avgVehicles').innerText   = data.avg_per_day;
                document.getElementById('peakDay').innerText       = data.hari_terpadat;

                // --- Update Card Hari Terpadat ---
                document.getElementById('hariTerpadatText').innerText = data.hari_terpadat ?? '-';
                document.getElementById('hariTerpadatDesc').innerText =
                    `Jumlah parkir pada hari ${data.hari_terpadat} adalah ${data.total_hari_terpadat ?? 0} kendaraan.`;

                // --- Update Card Jam Puncak ---
                document.getElementById('jamPuncakText').innerText = data.jam_tersibuk ?? '-';
                document.getElementById('durasiText').innerText    =
                    `Rata-rata durasi parkir adalah ${data.durasi_format ?? '-'}.`;

                // --- Kembalikan canvas untuk chart baru ---
                chartContainer.innerHTML = '<canvas id="vehicleChart"></canvas>';
                const ctx = document.getElementById('vehicleChart').getContext('2d');

                // --- Hancurkan chart lama agar tidak tumpang tindih ---
                if (window.vehicleChartInstance) {
                    window.vehicleChartInstance.destroy();
                }

                // --- Hitung step sumbu Y secara dinamis ---
                const maxValue = Math.max(...data.chart.data);
                const step     = Math.ceil(maxValue / 4);

                // --- Gradient warna default bar (biru) ---
                const gradient = ctx.createLinearGradient(0, 0, 0, 400);
                gradient.addColorStop(0, 'rgba(56, 182, 255, 0.8)');
                gradient.addColorStop(1, 'rgba(16, 112, 255, 0.2)');

                // --- Highlight bar hari puncak dengan warna merah ---
                const peakDayIndex = data.chart.labels.indexOf(data.hari_terpadat);

                const barColors    = data.chart.labels.map((_, idx) => idx === peakDayIndex ? 'rgba(255, 99, 132, 0.8)' : gradient);
                const borderColors = data.chart.labels.map((_, idx) => idx === peakDayIndex ? 'rgba(255, 99, 132, 1)'   : 'rgba(16, 112, 255, 1)');
                const hoverColors  = data.chart.labels.map((_, idx) => idx === peakDayIndex ? 'rgba(255, 99, 132, 1)'   : 'rgba(16, 112, 255, 0.9)');

                // --- Render Chart.js ---
                window.vehicleChartInstance = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.chart.labels,
                        datasets: [{
                            label: 'Jumlah Kendaraan',
                            data: data.chart.data,
                            backgroundColor: barColors,
                            borderColor: borderColors,
                            borderWidth: 2,
                            borderRadius: 6,
                            borderSkipped: false,
                            hoverBackgroundColor: hoverColors,
                            hoverBorderColor: borderColors,
                            hoverBorderWidth: 3
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                titleFont: { size: 14, weight: 'bold' },
                                bodyFont: { size: 13 },
                                padding: 12,
                                cornerRadius: 8,
                                displayColors: false,
                                callbacks: {
                                    label: function (context) {
                                        return ` ${context.parsed.y} kendaraan`;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                suggestedMax: maxValue + step,
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)',
                                    drawBorder: false
                                },
                                ticks: {
                                    stepSize: step,
                                    maxTicksLimit: 6,
                                    precision: 0,
                                    color: '#6b7280',
                                    font: { size: 12 }
                                }
                            },
                            x: {
                                grid: { display: false, drawBorder: false },
                                ticks: {
                                    color: '#6b7280',
                                    font: { size: 12 }
                                }
                            }
                        },
                        animation: {
                            duration: 1000,
                            easing: 'easeOutQuart'
                        },
                        interaction: {
                            intersect: false,
                            mode: 'index'
                        }
                    }
                });

                // --- Render Detail Jam Sibuk per Hari ---
                const jamSibukContainer = document.getElementById('jam-sibuk-container');
                jamSibukContainer.innerHTML = '';

                if (data.jam_sibuk && Object.keys(data.jam_sibuk).length > 0) {
                    for (const [hari, slots] of Object.entries(data.jam_sibuk)) {
                        let badgesHtml = '';

                        // Loop tiap slot jam beserta jumlah kendaraan
                        for (const [slot, jumlah] of Object.entries(slots)) {
                            // Tentukan warna badge berdasarkan tingkat kepadatan
                            let warna;
                            if (jumlah >= 10)     warna = 'bg-red-100 text-red-600';
                            else if (jumlah >= 7) warna = 'bg-orange-100 text-orange-600';
                            else if (jumlah >= 4) warna = 'bg-yellow-100 text-yellow-600';
                            else                  warna = 'bg-blue-100 text-blue-600';

                            badgesHtml += `
                                <span class="px-2 py-1 ${warna} rounded-full text-xs font-medium">
                                    ${slot} (${jumlah} kendaraan)
                                </span>`;
                        }

                        // Tambahkan baris hari + badge ke container
                        jamSibukContainer.innerHTML += `
                            <div class="group flex items-center p-2 hover:bg-white rounded-lg transition-colors duration-200">
                                <span class="font-medium w-24 text-gray-700 flex-shrink-0">${hari}</span>
                                <div class="flex flex-wrap gap-2">${badgesHtml}</div>
                            </div>`;
                    }
                } else {
                    // Tampilkan pesan jika tidak ada data jam sibuk
                    jamSibukContainer.innerHTML =
                        '<p class="text-gray-400 italic text-sm">Tidak ada data jam sibuk minggu ini.</p>';
                }

            })
            .catch(error => {
                // --- Tampilkan pesan error jika fetch gagal ---
                console.error('Error fetch statistik zona:', error);
                chartContainer.innerHTML = `
                    <div class="chart-error text-center py-8 text-red-500 bg-red-100 rounded-lg">
                        Gagal memuat data grafik. Silakan coba lagi.
                    </div>`;
                document.getElementById('jam-sibuk-container').innerHTML =
                    '<p class="text-red-400 italic text-sm">Gagal memuat data. Silakan coba lagi.</p>';
            });
    });

    // --- Trigger otomatis saat halaman pertama kali dimuat ---
    document.getElementById('zoneSelect').dispatchEvent(new Event('change'));
</script>

@endsection
