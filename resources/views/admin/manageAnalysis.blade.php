@extends('layout/mainAdmin')

@section('main')
    {{-- Select Zona --}}
    <div class="-mt-2">
        <select id="zonaSelect" class="select select-bordered w-52">
            @foreach ($zonas as $zona)
                <option value="{{ $zona->id }}">{{ $zona->nama_zona }}</option>
            @endforeach
        </select>
    </div>

    <div class="flex mt-6 space-x-4">

        {{-- Chart 1: Jumlah Kendaraan --}}
        <div class="w-full max-w-sm p-4 bg-white border-2 rounded-lg shadow dark:bg-gray-800 md:p-4">
            <div class="flex justify-between pb-4 mb-4 border-b border-gray-400 dark:border-gray-700">
                <div class="flex items-center">
                    <div class="flex items-center justify-center rounded-lg bg-gray-50 dark:bg-gray-700 me-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="45" height="45" fill="#000000" viewBox="0 0 256 256">
                            <path d="M240,104H229.2L201.42,41.5A16,16,0,0,0,186.8,32H69.2a16,16,0,0,0-14.62,9.5L26.8,104H16a8,8,0,0,0,0,16h8v80a16,16,0,0,0,16,16H64a16,16,0,0,0,16-16V184h96v16a16,16,0,0,0,16,16h24a16,16,0,0,0,16-16V120h8a8,8,0,0,0,0-16ZM69.2,48H186.8l24.89,56H44.31ZM64,200H40V184H64Zm128,0V184h24v16Zm24-32H40V120H216ZM56,144a8,8,0,0,1,8-8H80a8,8,0,0,1,0,16H64A8,8,0,0,1,56,144Zm112,0a8,8,0,0,1,8-8h16a8,8,0,0,1,0,16H176A8,8,0,0,1,168,144Z"/>
                        </svg>
                    </div>
                    <div>
                        <h5 class="text-2xl font-bold leading-none text-gray-900 dark:text-white">Jumlah Kendaraan</h5>
                    </div>
                </div>
            </div>
            <div id="column-chart-1"></div>
        </div>

        {{-- Chart 2: Durasi Parkir --}}
        <div class="w-full max-w-sm p-4 bg-white border-2 rounded-lg shadow dark:bg-gray-800 md:p-4">
            <div class="flex justify-between pb-4 mb-4 border-b border-gray-400 dark:border-gray-700">
                <div class="flex items-center">
                    <div class="flex items-center justify-center bg-gray-100 rounded-lg dark:bg-gray-700 me-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="45" height="45" fill="#000000" viewBox="0 0 256 256">
                            <path d="M136,80v43.47l36.12,21.67a8,8,0,0,1-8.24,13.72l-40-24A8,8,0,0,1,120,128V80a8,8,0,0,1,16,0Zm88-24a8,8,0,0,0-8,8V82c-6.35-7.36-12.83-14.45-20.12-21.83a96,96,0,1,0-2,137.7,8,8,0,0,0-11-11.64A80,80,0,1,1,184.54,71.4C192.68,79.64,199.81,87.58,207,96H184a8,8,0,0,0,0,16h40a8,8,0,0,0,8-8V64A8,8,0,0,0,224,56Z"/>
                        </svg>
                    </div>
                    <div>
                        <h5 class="pb-1 text-2xl font-bold leading-none text-gray-900 dark:text-white">Durasi Parkir</h5>
                    </div>
                </div>
            </div>
            <div id="column-chart-2" class="mt-4"></div>
        </div>

        {{-- Jam Sibuk Parkir --}}
        <div class="w-full max-w-sm p-4 bg-white border-2 rounded-lg shadow dark:bg-gray-800 md:p-4">
            <div class="flex justify-between pb-4 mb-4 border-b border-gray-400 dark:border-gray-700">
                <div class="flex items-center">
                    <div class="flex items-center justify-center rounded-lg bg-gray-50 dark:bg-gray-700 me-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="45" height="45" fill="#000000" viewBox="0 0 256 256">
                            <path d="M211.18,196.56,139.57,128l71.61-68.56a1.59,1.59,0,0,1,.13-.13A16,16,0,0,0,200,32H56A16,16,0,0,0,44.7,59.31l.12.13L116.43,128,44.82,196.56l-.12.13A16,16,0,0,0,56,224H200a16,16,0,0,0,11.32-27.31A1.59,1.59,0,0,1,211.18,196.56ZM56,48h0v0ZM97.79,88h60.42L128,116.92ZM200,48,174.92,72H81.08L56,48ZM56,208l64-61.26V168a8,8,0,0,0,16,0V146.74L200,208Z"/>
                        </svg>
                    </div>
                    <div>
                        <h5 class="text-2xl font-bold leading-none text-gray-900 dark:text-white">Jam Sibuk Parkir</h5>
                    </div>
                </div>
            </div>
            <div>
                <ul id="jam-sibuk-list" class="space-y-2 list-inside text-sm text-gray-600">
                    <li class="text-gray-400 italic">Memuat data...</li>
                </ul>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <script>
        let chart1Instance = null;
        let chart2Instance = null;

        function buildChartOptions(color, seriesName, data) {
            return {
                colors: [color],
                series: [{ name: seriesName, color: color, data: data }],
                chart: {
                    type: "bar",
                    height: "320px",
                    fontFamily: "Inter, sans-serif",
                    toolbar: { show: false },
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: "50%",
                        borderRadiusApplication: "end",
                        borderRadius: 8,
                    },
                },
                tooltip: {
                    shared: true,
                    intersect: false,
                    style: { fontFamily: "Inter, sans-serif" },
                },
                states: { hover: { filter: { type: "darken", value: 1 } } },
                stroke: { show: true, width: 0, colors: ["transparent"] },
                grid: {
                    show: false,
                    strokeDashArray: 4,
                    padding: { left: 2, right: 2, top: -14 },
                },
                dataLabels: { enabled: false },
                legend: { show: false },
                xaxis: {
                    floating: false,
                    labels: {
                        show: true,
                        style: {
                            fontFamily: "Inter, sans-serif",
                            cssClass: 'text-xs font-normal fill-gray-500 dark:fill-gray-400'
                        }
                    },
                    axisBorder: { show: false },
                    axisTicks: { show: false },
                },
                yaxis: { show: false },
                fill: { opacity: 1 },
            };
        }

        function loadZonaData(zonaId) {
            fetch(`/api/statistik-zona?zona_id=${zonaId}`)
                .then(res => res.json())
                .then(data => {

                    // Chart 1: Jumlah Kendaraan
                    const chartData1 = data.chart.labels.map((label, i) => ({
                        x: label,
                        y: data.chart.data[i]
                    }));

                    if (chart1Instance) chart1Instance.destroy();
                    document.getElementById("column-chart-1").innerHTML = '';
                    chart1Instance = new ApexCharts(
                        document.getElementById("column-chart-1"),
                        buildChartOptions("#1A56DB", "Kendaraan", chartData1)
                    );
                    chart1Instance.render();

                    // Chart 2: Durasi Parkir
                    const durasiData = data.durasi_chart
                        ? data.durasi_chart.labels.map((label, i) => ({
                            x: label,
                            y: data.durasi_chart.data[i]
                          }))
                        : chartData1;

                    if (chart2Instance) chart2Instance.destroy();
                    document.getElementById("column-chart-2").innerHTML = '';
                    chart2Instance = new ApexCharts(
                        document.getElementById("column-chart-2"),
                        buildChartOptions("#057A55", "Menit", durasiData)
                    );
                    chart2Instance.render();

                    // Jam Sibuk List
                    const jamSibukList = document.getElementById('jam-sibuk-list');
                    if (data.jam_sibuk && Object.keys(data.jam_sibuk).length > 0) {
                        jamSibukList.innerHTML = '';
                        for (const [hari, jamList] of Object.entries(data.jam_sibuk)) {
                            const slots = Object.keys(jamList).join(', ');
                            const li = document.createElement('li');
                            li.textContent = `${hari} : ${slots}`;
                            jamSibukList.appendChild(li);
                        }
                    } else {
                        jamSibukList.innerHTML = '<li class="text-gray-400 italic">Tidak ada data jam sibuk.</li>';
                    }
                })
                .catch(err => {
                    console.error('Gagal memuat data zona:', err);
                    document.getElementById('jam-sibuk-list').innerHTML =
                        '<li class="text-red-400">Gagal memuat data. Silakan coba lagi.</li>';
                });
        }

        // Event listener select zona
        document.getElementById('zonaSelect').addEventListener('change', function () {
            loadZonaData(this.value);
        });

        // Auto-load zona pertama saat halaman dibuka
        document.getElementById('zonaSelect').dispatchEvent(new Event('change'));
    </script>
@endpush
