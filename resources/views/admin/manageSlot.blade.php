@extends('layout/mainAdmin')

@section('main')
    @include('admin.component.success-error')

    {{-- Data slot existing untuk ditampilkan di canvas --}}
    @php
        $slotsData = $slots->map(fn($s) => [
            'id'         => $s->id,
            'nomor_slot' => $s->nomor_slot,
            'keterangan' => $s->keterangan,
            'x1' => $s->x1, 'y1' => $s->y1,
            'x2' => $s->x2, 'y2' => $s->y2,
            'x3' => $s->x3, 'y3' => $s->y3,
            'x4' => $s->x4, 'y4' => $s->y4,
        ])->values();
    @endphp

    <div class="flex flex-col p-6 -ml-5 space-y-4 -mt-7">

        {{-- Dropdown Pilih Zona --}}
        <div class="ml-10 dropdown">
            <div tabindex="0" role="button" class="btn hover:bg-[#95AFE5] font-bold w-36 pr-10">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                </svg>
                {{ $selectedZona ? $selectedZona->nama_zona : 'Pilih Zona' }}
            </div>
            <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-[1] w-36 p-2 shadow">
                @foreach ($zonas as $zona)
                    <li>
                        <a href="{{ route('admin-slot', ['zona' => $zona->id]) }}"
                            class="hover:bg-[#95AFE5] {{ $selectedZona && $selectedZona->id == $zona->id ? 'bg-[#95AFE5]' : '' }}">
                            {{ $zona->nama_zona }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="flex items-center justify-between w-full px-5 mx-6 gap-6">
            {{-- Pilih SubZona --}}
            <div>
                <label class="block text-sm font-bold text-gray-700">Pilih SubZona :</label>
                <select onchange="window.location.href='{{ route('slot.getBySubzona', ['subzonaId' => '__ID__']) }}'.replace('__ID__', this.value);"
                    class="block mt-1 font-bold border-gray-300 rounded-md shadow-sm w-36 focus:border-blue-300 focus:ring-indigo-500 sm:text-sm">
                    @foreach ($subzonas as $subzona)
                        <option value="{{ $subzona->id }}" {{ $selectedSubzona && $selectedSubzona->id == $subzona->id ? 'selected' : '' }}>
                            {{ $subzona->nama_subzona }}
                        </option>
                    @endforeach
                </select>
            </div>
            {{-- Tombol Tambah Slot --}}
            <div class="pt-6 ml-auto">
                <button onclick="pickerModeNew()"
                    class="rounded-md bg-base-200 hover:bg-[#95AFE5] p-2 px-3 flex items-center gap-2 font-bold">
                    <i class="fas fa-plus"></i> Tambah Slot
                </button>
            </div>
        </div>

        {{-- ============================================================
             POLYGON PICKER SECTION
             Hanya tampil kalau subzona sudah dipilih
             ============================================================ --}}
        @if($selectedSubzona)
        <div class="px-5 mx-6 w-full">
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4">

                {{-- Header --}}
                <div class="flex items-center justify-between mb-3 flex-wrap gap-2">
                    <h3 class="font-bold text-gray-700 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.88v6.24a1 1 0 01-1.447.894L15 14M3 8a2 2 0 012-2h10a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/>
                        </svg>
                        {{ $selectedSubzona->nama_subzona }} — Kamera {{ $selectedSubzona->camera_id }}
                    </h3>
                    <div class="flex items-center gap-2">
                        <span class="text-xs bg-blue-50 text-blue-600 border border-blue-200 px-2 py-0.5 rounded">480 × 320 px</span>
                        <span id="picker-mode-badge" class="text-xs px-2 py-0.5 rounded bg-gray-100 text-gray-500">
                            Mode: Lihat
                        </span>
                    </div>
                </div>

                <div class="flex gap-6 flex-wrap">

                    {{-- VIDEO + CANVAS --}}
                    <div class="flex-shrink-0">
                        <div class="relative rounded-lg overflow-hidden border border-gray-300"
                             style="width:480px; height:320px; background:#1a1a2e;">

                            {{-- Stream video dari camera_id subzona yang dipilih --}}
                            <img id="picker-feed"
                                 src="{{ config('services.cloudflare.url') }}/clean_video_feed?camera_id={{ $selectedSubzona->camera_id }}"
                                 style="width:480px; height:320px; display:block; object-fit:cover;"
                                 alt="Live feed kamera {{ $selectedSubzona->camera_id }}">

                            <canvas id="picker-canvas"
                                    width="480" height="320"
                                    class="absolute top-0 left-0"
                                    style="width:480px; height:320px;">
                            </canvas>
                        </div>

                        {{-- Info titik saat mode gambar --}}
                        <div id="picker-points-info" class="mt-2 flex gap-2 flex-wrap min-h-[24px]"></div>
                    </div>

                    {{-- PANEL KANAN --}}
                    <div class="flex flex-col gap-3 flex-1 min-w-[220px]">

                        {{-- Status mode --}}
                        <div id="picker-panel-view" class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                            <p class="text-xs font-medium text-gray-500 mb-2">Slot Terdaftar</p>
                            @forelse($slots as $slot)
                                <div class="flex items-center justify-between py-1.5 border-b border-gray-100 last:border-0">
                                    <div class="flex items-center gap-2">
                                        <span id="slot-color-dot-{{ $slot->id }}"
                                              class="inline-block w-3 h-3 rounded-full"
                                              style="background:#3b82f6"></span>
                                        <span class="text-sm font-medium text-gray-700">Slot {{ $slot->nomor_slot }}</span>
                                        <span class="text-xs text-gray-400">{{ $slot->keterangan }}</span>
                                    </div>
                                    <div class="flex gap-1">
                                        <button onclick="pickerModeEdit({{ $slot->id }})"
                                            class="text-xs px-2 py-1 bg-blue-50 text-blue-600 rounded hover:bg-blue-100">
                                            <i class="fas fa-pen"></i> Edit
                                        </button>
                                        <button data-modal-target="hapus-slot-{{ $slot->id }}"
                                            data-modal-toggle="hapus-slot-{{ $slot->id }}"
                                            class="text-xs px-2 py-1 bg-red-50 text-red-500 rounded hover:bg-red-100">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            @empty
                                <p class="text-xs text-gray-400 text-center py-2">Belum ada slot. Klik "Tambah Slot" untuk mulai.</p>
                            @endforelse

                            <button onclick="pickerModeNew()"
                                class="mt-3 w-full py-1.5 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">
                                + Tambah Slot Baru
                            </button>
                        </div>

                        {{-- Panel mode GAMBAR (tambah / edit) --}}
                        <div id="picker-panel-draw" class="hidden flex-col gap-3">

                            {{-- Info status titik --}}
                            <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                                <p class="text-xs font-medium text-gray-500 mb-1">
                                    Mode: <span id="draw-mode-label" class="text-blue-600">Tambah Baru</span>
                                </p>
                                <p id="picker-status" class="text-sm font-bold text-orange-500">0 / 4 titik</p>
                                <p class="text-xs text-gray-400 mt-1">Klik 4 sudut area parkir di video</p>
                                <div class="mt-1 text-xs text-gray-400 space-y-0.5">
                                    <div>① Kiri atas &nbsp;② Kanan atas</div>
                                    <div>③ Kanan bawah &nbsp;④ Kiri bawah</div>
                                </div>
                            </div>

                            {{-- Koordinat realtime --}}
                            <div class="grid grid-cols-2 gap-2 text-xs">
                                @foreach(['1','2','3','4'] as $n)
                                <div class="bg-gray-50 border border-gray-200 rounded p-2">
                                    <span class="text-gray-400">P{{ $n }}:</span>
                                    <span class="font-mono font-medium text-gray-700" id="picker-coord-{{ $n }}">—</span>
                                </div>
                                @endforeach
                            </div>

                            {{-- Tombol aksi --}}
                            <button id="picker-btn-apply" onclick="pickerApply()" disabled
                                class="w-full py-2 px-4 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 disabled:opacity-40 disabled:cursor-not-allowed">
                                ✓ Simpan Koordinat ke Form
                            </button>
                            <button onclick="pickerModeView()"
                                class="w-full py-1.5 px-4 bg-gray-100 text-gray-600 text-sm rounded-lg hover:bg-gray-200">
                                ✕ Batal
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Modal Tambah Slot --}}
        <div id="tambah-slot" tabindex="-1" aria-hidden="true"
            class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
            <div class="relative w-full max-w-md max-h-full p-4">
                <div class="relative bg-white rounded-lg shadow">
                    <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t bg-[#95AFE5]">
                        <h3 class="text-lg font-semibold text-white">Tambah Slot Baru</h3>
                        <button type="button" onclick="pickerModeView()"
                            data-modal-toggle="tambah-slot"
                            class="inline-flex items-center justify-center w-8 h-8 text-sm text-white bg-transparent rounded-lg hover:bg-gray-200 hover:text-gray-900 ms-auto">
                            <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                            </svg>
                        </button>
                    </div>
                    <form action="{{ route('slot.store') }}" method="POST" class="p-4 md:p-5">
                        @csrf
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div class="col-span-2">
                                <label class="block mb-2 text-sm font-medium text-gray-900">Sub-Zona <span class="text-red-500">*</span></label>
                                <select name="subzona_id" id="tambah-subzona_id" required
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
                                    <option value="" disabled selected>Pilih Sub-Zona</option>
                                    @foreach ($subzonas as $subzona)
                                        <option value="{{ $subzona->id }}"
                                            {{ ($selectedSubzona && $selectedSubzona->id == $subzona->id) || old('subzona_id') == $subzona->id ? 'selected' : '' }}>
                                            {{ $subzona->nama_subzona }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-span-2">
                                <label class="block mb-2 text-sm font-medium text-gray-900">Nomor Slot <span class="text-red-500">*</span></label>
                                <input type="number" name="nomor_slot" id="tambah-nomor_slot"
                                    value="{{ old('nomor_slot') }}" min="1"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5"
                                    placeholder="Masukkan Nomor Slot" required>
                            </div>
                            <div class="col-span-2">
                                <label class="block mb-2 text-sm font-medium text-gray-900">Keterangan <span class="text-red-500">*</span></label>
                                <select name="keterangan" id="tambah-keterangan" required
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
                                    <option value="" disabled selected>Pilih Keterangan</option>
                                    <option value="Tersedia">Tersedia</option>
                                    <option value="Terisi">Terisi</option>
                                    <option value="Perbaikan">Perbaikan</option>
                                </select>
                            </div>
                            <div class="col-span-2">
                                <label class="block mb-2 text-sm font-medium text-gray-900">
                                    Koordinat
                                    <span class="ml-1 text-xs text-blue-500 font-normal">← sudah diisi dari polygon picker</span>
                                </label>
                                <div class="grid grid-cols-4 gap-2">
                                    @foreach(['x1','y1','x2','y2','x3','y3','x4','y4'] as $c)
                                    <input type="number" name="{{ $c }}" id="tambah-{{ $c }}"
                                        value="{{ old($c) }}" min="0" step="1"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5"
                                        placeholder="{{ $c }}" required>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <button type="submit"
                            class="text-white inline-flex items-center bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-5 py-2.5">
                            <svg class="w-5 h-5 me-1 -ms-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"/>
                            </svg>
                            Tambah Slot
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Modal Edit Slot --}}
        @foreach ($slots as $slot)
        <div id="edit-slot-{{ $slot->id }}" tabindex="-1" aria-hidden="true"
            class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
            <div class="relative w-full max-w-md max-h-full p-4">
                <div class="relative bg-white rounded-lg shadow">
                    <div class="flex items-center justify-between bg-[#95AFE5] p-4 md:p-5 border-b rounded-t">
                        <h3 class="text-lg font-semibold text-white">Edit Slot {{ $slot->nomor_slot }}</h3>
                        <button type="button" data-modal-toggle="edit-slot-{{ $slot->id }}"
                            class="inline-flex items-center justify-center w-8 h-8 text-sm text-white bg-transparent rounded-lg hover:bg-gray-200 hover:text-gray-900 ms-auto">
                            <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                            </svg>
                        </button>
                    </div>
                    <form action="{{ route('slot.update', $slot->id) }}" method="POST" class="p-4 md:p-5">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="subzona_id" value="{{ $selectedSubzona->id }}">
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div class="col-span-2">
                                <label class="block mb-2 text-sm font-medium text-gray-900">Nama Subzona</label>
                                <input type="text" value="{{ $slot->subzona->nama_subzona }}"
                                    class="bg-gray-200 border border-gray-300 text-gray-500 text-sm rounded-lg block w-full p-2.5" disabled>
                            </div>
                            <div class="col-span-2">
                                <label class="block mb-2 text-sm font-medium text-gray-900">Nomor Slot</label>
                                <input type="text" name="nomor_slot" value="{{ $slot->nomor_slot }}"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
                            </div>
                            <div class="col-span-2">
                                <label class="block mb-2 text-sm font-medium text-gray-900">Keterangan <span class="text-red-500">*</span></label>
                                <select name="keterangan" required
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
                                    <option value="Tersedia" {{ $slot->keterangan == 'Tersedia' ? 'selected' : '' }}>Tersedia</option>
                                    <option value="Terisi" {{ $slot->keterangan == 'Terisi' ? 'selected' : '' }}>Terisi</option>
                                    <option value="Perbaikan" {{ $slot->keterangan == 'Perbaikan' ? 'selected' : '' }}>Perbaikan</option>
                                </select>
                            </div>
                            <div class="col-span-2">
                                <label class="block mb-2 text-sm font-medium text-gray-900">
                                    Koordinat
                                    <span class="ml-1 text-xs text-blue-500 font-normal">← gambar ulang lewat picker untuk ubah</span>
                                </label>
                                <div class="grid grid-cols-4 gap-2" id="edit-coords-{{ $slot->id }}">
                                    @foreach(['x1','y1','x2','y2','x3','y3','x4','y4'] as $c)
                                    <input type="number" name="{{ $c }}"
                                        value="{{ old($c, $slot->$c) }}"
                                        min="0" step="1"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5"
                                        placeholder="{{ $c }}" required>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <button type="submit"
                            class="text-white inline-flex items-center bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-5 py-2.5">
                            Simpan
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach

        {{-- Modal Hapus Slot --}}
        @foreach ($slots as $slot)
        <div id="hapus-slot-{{ $slot->id }}" tabindex="-1"
            class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
            <div class="relative w-full max-w-md max-h-full p-4">
                <div class="relative bg-white rounded-lg shadow">
                    <div class="p-4 text-center">
                        <h3 class="mb-5 text-lg font-normal text-gray-500">
                            Apakah Anda yakin ingin menghapus slot "{{ $slot->nomor_slot }}"?
                        </h3>
                        <form action="{{ route('slot.destroy', $slot->id) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="text-white bg-red-600 hover:bg-red-800 font-medium rounded-lg text-sm px-5 py-2.5 mr-2">
                                Ya, Hapus
                            </button>
                        </form>
                        <button data-modal-hide="hapus-slot-{{ $slot->id }}" type="button"
                            class="text-gray-500 bg-white hover:bg-gray-100 border border-gray-200 font-medium rounded-lg text-sm px-5 py-2.5">
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endforeach

    </div>

    {{-- ================================================================
         POLYGON PICKER SCRIPT
         ================================================================ --}}
    <script>
    (function () {
        const CANVAS_W = 480, CANVAS_H = 320;
        const SLOT_COLORS = ['#3b82f6','#ef4444','#22c55e','#f59e0b','#a855f7','#06b6d4','#f97316'];

        // Data slot existing dari PHP
        const existingSlots = @json($slotsData ?? []);

        let pickerPoints  = [];   // titik yang sedang digambar user
        let currentMode   = 'view';  // 'view' | 'new' | 'edit'
        let editingSlotId = null;

        const canvas = document.getElementById('picker-canvas');
        if (!canvas) return; // subzona belum dipilih

        const ctx = canvas.getContext('2d');

        // ── PARSE existing slot jadi array titik ──
        function slotToPoints(slot) {
            return [
                { x: slot.x1, y: slot.y1 },
                { x: slot.x2, y: slot.y2 },
                { x: slot.x3, y: slot.y3 },
                { x: slot.x4, y: slot.y4 },
            ];
        }

        // ── REDRAW semua polygon di canvas ──
        function redraw() {
            ctx.clearRect(0, 0, CANVAS_W, CANVAS_H);

            // 1. Gambar semua slot existing
            existingSlots.forEach((slot, si) => {
                const color = SLOT_COLORS[si % SLOT_COLORS.length];
                const pts   = slotToPoints(slot);

                // Update warna dot di panel
                const dot = document.getElementById('slot-color-dot-' + slot.id);
                if (dot) dot.style.background = color;

                // Polygon filled
                ctx.beginPath();
                ctx.moveTo(pts[0].x, pts[0].y);
                pts.forEach(p => ctx.lineTo(p.x, p.y));
                ctx.closePath();

                // Sorot slot yang sedang diedit
                const isEditing = currentMode === 'edit' && editingSlotId === slot.id;
                ctx.fillStyle   = isEditing ? color + '50' : color + '28';
                ctx.fill();
                ctx.strokeStyle = color;
                ctx.lineWidth   = isEditing ? 3 : 1.5;
                ctx.setLineDash(isEditing ? [6, 3] : []);
                ctx.stroke();
                ctx.setLineDash([]);

                // Label nomor slot di tengah
                const cx = pts.reduce((a, p) => a + p.x, 0) / 4;
                const cy = pts.reduce((a, p) => a + p.y, 0) / 4;
                ctx.fillStyle = color;
                ctx.font = 'bold 13px sans-serif';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillText('S' + slot.nomor_slot, cx, cy);
                ctx.textAlign = 'left';
                ctx.textBaseline = 'alphabetic';
            });

            // 2. Gambar titik yang sedang digambar user (mode new/edit)
            if (pickerPoints.length > 0 && currentMode !== 'view') {
                ctx.beginPath();
                ctx.moveTo(pickerPoints[0].x, pickerPoints[0].y);
                for (let i = 1; i < pickerPoints.length; i++)
                    ctx.lineTo(pickerPoints[i].x, pickerPoints[i].y);
                if (pickerPoints.length === 4) {
                    ctx.closePath();
                    ctx.fillStyle = '#ffffff30';
                    ctx.fill();
                }
                ctx.strokeStyle = '#fff';
                ctx.lineWidth = 2;
                ctx.setLineDash(pickerPoints.length < 4 ? [6, 3] : []);
                ctx.stroke();
                ctx.setLineDash([]);

                pickerPoints.forEach((p, i) => {
                    ctx.beginPath();
                    ctx.arc(p.x, p.y, 7, 0, Math.PI * 2);
                    ctx.fillStyle = SLOT_COLORS[i] || '#fff';
                    ctx.fill();
                    ctx.strokeStyle = '#fff';
                    ctx.lineWidth = 2;
                    ctx.stroke();
                    ctx.fillStyle = '#fff';
                    ctx.font = 'bold 10px sans-serif';
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.fillText(i + 1, p.x, p.y);
                    ctx.textAlign = 'left';
                    ctx.textBaseline = 'alphabetic';
                });
            }
        }

        // ── UPDATE UI status titik ──
        function updatePickerUI() {
            const n = pickerPoints.length;
            const statusEl = document.getElementById('picker-status');
            if (statusEl) {
                statusEl.textContent = `${n} / 4 titik`;
                statusEl.className = n === 4
                    ? 'text-sm font-bold text-green-600'
                    : 'text-sm font-bold text-orange-500';
            }
            ['1','2','3','4'].forEach((num, i) => {
                const el = document.getElementById('picker-coord-' + num);
                if (el) el.textContent = pickerPoints[i]
                    ? `${pickerPoints[i].x}, ${pickerPoints[i].y}` : '—';
            });
            const applyBtn = document.getElementById('picker-btn-apply');
            if (applyBtn) applyBtn.disabled = n !== 4;

            // Info badge titik
            const infoEl = document.getElementById('picker-points-info');
            if (infoEl) {
                infoEl.innerHTML = pickerPoints.map((p, i) =>
                    `<span style="font-size:11px;padding:2px 8px;background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe;border-radius:4px;">
                        P${i+1}: (${p.x}, ${p.y})
                    </span>`
                ).join('');
            }
        }

        // ── SWITCH MODE ──
        function setMode(mode, slotId = null) {
            currentMode   = mode;
            editingSlotId = slotId;
            pickerPoints  = [];

            const panelView = document.getElementById('picker-panel-view');
            const panelDraw = document.getElementById('picker-panel-draw');
            const modeBadge = document.getElementById('picker-mode-badge');
            const modeLabel = document.getElementById('draw-mode-label');

            if (mode === 'view') {
                canvas.style.cursor = 'default';
                canvas.onclick = null;
                if (panelView) panelView.classList.remove('hidden');
                if (panelDraw) panelDraw.classList.add('hidden');
                if (modeBadge) { modeBadge.textContent = 'Mode: Lihat'; modeBadge.className = 'text-xs px-2 py-0.5 rounded bg-gray-100 text-gray-500'; }
            } else {
                canvas.style.cursor = 'crosshair';
                canvas.onclick = pickerClick;
                if (panelView) panelView.classList.add('hidden');
                if (panelDraw) { panelDraw.classList.remove('hidden'); panelDraw.classList.add('flex'); }

                if (mode === 'new') {
                    if (modeLabel) modeLabel.textContent = 'Tambah Slot Baru';
                    if (modeBadge) { modeBadge.textContent = 'Mode: Gambar'; modeBadge.className = 'text-xs px-2 py-0.5 rounded bg-green-100 text-green-600'; }
                } else {
                    const slot = existingSlots.find(s => s.id === slotId);
                    if (modeLabel) modeLabel.textContent = `Edit Slot ${slot?.nomor_slot ?? slotId}`;
                    if (modeBadge) { modeBadge.textContent = 'Mode: Edit'; modeBadge.className = 'text-xs px-2 py-0.5 rounded bg-yellow-100 text-yellow-600'; }
                }
            }

            updatePickerUI();
            redraw();
        }

        function pickerClick(event) {
            if (pickerPoints.length >= 4) return;
            const rect = canvas.getBoundingClientRect();
            const scaleX = CANVAS_W / rect.width;
            const scaleY = CANVAS_H / rect.height;
            pickerPoints.push({
                x: Math.round((event.clientX - rect.left) * scaleX),
                y: Math.round((event.clientY - rect.top) * scaleY),
            });
            redraw();
            updatePickerUI();
        }

        // ── APPLY koordinat ke form lalu buka modal ──
        function applyCoords() {
            const pts = pickerPoints;
            if (pts.length !== 4) return;
            const coords = ['x1','y1','x2','y2','x3','y3','x4','y4'];
            const vals   = [pts[0].x, pts[0].y, pts[1].x, pts[1].y,
                            pts[2].x, pts[2].y, pts[3].x, pts[3].y];

            if (currentMode === 'new') {
                coords.forEach((c, i) => {
                    const el = document.getElementById('tambah-' + c);
                    if (el) el.value = vals[i];
                });
                // Buka modal tambah
                const m = document.getElementById('tambah-slot');
                if (m) { m.classList.remove('hidden'); m.classList.add('flex'); }
            } else if (currentMode === 'edit') {
                const container = document.getElementById('edit-coords-' + editingSlotId);
                if (container) {
                    coords.forEach((c, i) => {
                        const el = container.querySelector(`[name="${c}"]`);
                        if (el) el.value = vals[i];
                    });
                }
                // Buka modal edit
                const m = document.getElementById('edit-slot-' + editingSlotId);
                if (m) { m.classList.remove('hidden'); m.classList.add('flex'); }
            }
        }

        // ── EXPOSE ke window ──
        window.pickerModeView = () => setMode('view');
        window.pickerModeNew  = () => setMode('new');
        window.pickerModeEdit = (id) => setMode('edit', id);
        window.pickerApply    = applyCoords;

        // Init: tampilkan semua slot existing
        setMode('view');
    })();
    </script>

@endsection
