@extends('layout/mainAdmin')

@section('main')
    @include('admin.component.success-error')

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
        </div>

        @if($selectedSubzona)
        <div class="px-5 mx-6 w-full">
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4">

                <div class="flex items-center justify-between mb-3 flex-wrap gap-2">
                    <h3 class="font-bold text-gray-700 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.88v6.24a1 1 0 01-1.447.894L15 14M3 8a2 2 0 012-2h10a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/>
                        </svg>
                        {{ $selectedSubzona->nama_subzona }} — Kamera {{ $selectedSubzona->camera_id }}
                    </h3>
                    <div class="flex items-center gap-2">
                        <span class="text-xs bg-blue-50 text-blue-600 border border-blue-200 px-2 py-0.5 rounded">480 × 320 px</span>
                        <span id="picker-mode-badge" class="text-xs px-2 py-0.5 rounded bg-gray-100 text-gray-500">Mode: Lihat</span>
                    </div>
                </div>

                <div class="flex gap-6 flex-wrap">

                    {{-- VIDEO + CANVAS --}}
                    <div class="flex-shrink-0">
                        <div class="relative rounded-lg overflow-hidden border border-gray-300"
                             style="width:480px; height:320px; background:#1a1a2e;">
                            <img id="picker-feed"
                                 src="{{ config('services.cloudflare.url') }}/clean_video_feed?camera_id={{ $selectedSubzona->camera_id }}"
                                 style="width:480px; height:320px; display:block; object-fit:cover;"
                                 alt="Live feed kamera {{ $selectedSubzona->camera_id }}">
                            <canvas id="picker-canvas" width="480" height="320"
                                    class="absolute top-0 left-0"
                                    style="width:480px; height:320px;"></canvas>
                        </div>
                        <div id="picker-points-info" class="mt-2 flex gap-2 flex-wrap min-h-[24px]"></div>
                    </div>

                    {{-- PANEL KANAN --}}
                    <div class="flex flex-col gap-3 flex-1 min-w-[240px]">

                        {{-- PANEL: MODE LIHAT (list slot) --}}
                        <div id="picker-panel-view" class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                            <p class="text-xs font-medium text-gray-500 mb-2">Slot Terdaftar</p>
                            @forelse($slots as $slot)
                                <div class="flex items-center justify-between py-1.5 border-b border-gray-100 last:border-0">
                                    <div class="flex items-center gap-2">
                                        <span id="slot-color-dot-{{ $slot->id }}"
                                              class="inline-block w-3 h-3 rounded-full flex-shrink-0"
                                              style="background:#3b82f6"></span>
                                        <span class="text-sm font-medium text-gray-700">Slot {{ $slot->nomor_slot }}</span>
                                        <span class="text-xs px-1.5 py-0.5 rounded
                                            {{ $slot->keterangan == 'Tersedia' ? 'bg-green-100 text-green-700' :
                                               ($slot->keterangan == 'Terisi' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                                            {{ $slot->keterangan }}
                                        </span>
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
                                <p class="text-xs text-gray-400 text-center py-2">Belum ada slot.</p>
                            @endforelse
                            <button onclick="pickerModeNew()"
                                class="mt-3 w-full py-1.5 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">
                                + Tambah Slot Baru
                            </button>
                        </div>

                        {{-- PANEL: MODE GAMBAR (tambah baru) --}}
                        <div id="picker-panel-new" class="hidden flex-col gap-3">
                            <div class="bg-green-50 rounded-lg p-3 border border-green-200">
                                <p class="text-xs font-medium text-green-700 mb-1">Tambah Slot Baru</p>
                                <p id="picker-status" class="text-sm font-bold text-orange-500">0 / 4 titik</p>
                                <p class="text-xs text-green-600 mt-1">Klik 4 sudut area parkir di video, lalu isi form dan simpan.</p>
                                <div class="mt-1 text-xs text-gray-400">
                                    <div>① Kiri atas &nbsp;② Kanan atas</div>
                                    <div>③ Kanan bawah &nbsp;④ Kiri bawah</div>
                                </div>
                            </div>

                            {{-- Preview 4 titik --}}
                            <div class="grid grid-cols-2 gap-2 text-xs">
                                @foreach(['1','2','3','4'] as $n)
                                <div class="bg-gray-50 border border-gray-200 rounded p-2">
                                    <span class="text-gray-400">P{{ $n }}:</span>
                                    <span class="font-mono font-medium text-gray-700" id="picker-coord-{{ $n }}">—</span>
                                </div>
                                @endforeach
                            </div>

                            <form action="{{ route('slot.store') }}" method="POST" class="flex flex-col gap-3">
                                @csrf
                                <input type="hidden" name="subzona_id" value="{{ $selectedSubzona->id }}">
                                <input type="hidden" id="new-x1" name="x1">
                                <input type="hidden" id="new-y1" name="y1">
                                <input type="hidden" id="new-x2" name="x2">
                                <input type="hidden" id="new-y2" name="y2">
                                <input type="hidden" id="new-x3" name="x3">
                                <input type="hidden" id="new-y3" name="y3">
                                <input type="hidden" id="new-x4" name="x4">
                                <input type="hidden" id="new-y4" name="y4">

                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Nomor Slot <span class="text-red-500">*</span></label>
                                    <input type="number" name="nomor_slot" id="new-nomor_slot" min="1" required
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2"
                                        placeholder="Nomor slot">
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Keterangan <span class="text-red-500">*</span></label>
                                    <select name="keterangan" required
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2">
                                        <option value="Tersedia" selected>Tersedia</option>
                                        <option value="Terisi">Terisi</option>
                                        <option value="Perbaikan">Perbaikan</option>
                                    </select>
                                </div>

                                <div class="flex gap-2">
                                    <button type="submit" id="picker-btn-apply"
                                        class="flex-1 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 font-medium disabled:opacity-40 disabled:cursor-not-allowed"
                                        disabled>
                                        <i class="fas fa-save mr-1"></i> Simpan
                                    </button>
                                    <button type="button" onclick="pickerModeView()"
                                        class="flex-1 py-2 bg-gray-100 text-gray-600 text-sm rounded-lg hover:bg-gray-200">
                                        ✕ Batal
                                    </button>
                                </div>
                            </form>
                        </div>

                        {{-- PANEL: MODE EDIT INLINE --}}
                        <div id="picker-panel-edit" class="hidden flex-col gap-3">
                            <div class="bg-yellow-50 rounded-lg p-3 border border-yellow-200">
                                <p class="text-xs font-medium text-yellow-700 mb-1">Edit Slot <span id="edit-slot-label" class="font-bold"></span></p>
                                <p class="text-xs text-yellow-600">Gambar ulang polygon di video untuk ubah koordinat, atau langsung simpan perubahan keterangan.</p>
                            </div>

                            {{-- Form edit inline --}}
                            <form id="inline-edit-form" method="POST" action="" class="flex flex-col gap-3">
                                @csrf
                                @method('PUT')
                                <input type="hidden" id="edit-slot-id" name="_slot_id">
                                <input type="hidden" name="subzona_id" value="{{ $selectedSubzona->id }}">
                                <input type="hidden" id="edit-x1" name="x1">
                                <input type="hidden" id="edit-y1" name="y1">
                                <input type="hidden" id="edit-x2" name="x2">
                                <input type="hidden" id="edit-y2" name="y2">
                                <input type="hidden" id="edit-x3" name="x3">
                                <input type="hidden" id="edit-y3" name="y3">
                                <input type="hidden" id="edit-x4" name="x4">
                                <input type="hidden" id="edit-y4" name="y4">
                                <input type="hidden" id="edit-nomor_slot" name="nomor_slot">

                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Nomor Slot</label>
                                    <input type="number" id="edit-nomor_slot_display" min="1"
                                        onchange="document.getElementById('edit-nomor_slot').value=this.value"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2">
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Keterangan</label>
                                    <select name="keterangan" id="edit-keterangan" required
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2">
                                        <option value="Tersedia">Tersedia</option>
                                        <option value="Terisi">Terisi</option>
                                        <option value="Perbaikan">Perbaikan</option>
                                    </select>
                                </div>

                                {{-- Koordinat (readonly, diisi dari picker) --}}
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">
                                        Koordinat Polygon
                                        <span id="coords-status" class="ml-1 text-orange-500">(belum digambar ulang)</span>
                                    </label>
                                    <div class="grid grid-cols-4 gap-1 text-xs" id="edit-coords-preview">
                                        @foreach(['x1','y1','x2','y2','x3','y3','x4','y4'] as $c)
                                        <div class="bg-gray-100 border border-gray-200 rounded px-1.5 py-1 text-center font-mono text-gray-500" id="preview-{{ $c }}">
                                            {{ $c }}: —
                                        </div>
                                        @endforeach
                                    </div>
                                    <p class="text-xs text-gray-400 mt-1">Klik 4 titik di video untuk update koordinat, atau biarkan jika tidak ingin ubah posisi.</p>
                                </div>

                                {{-- Status titik --}}
                                <div id="edit-picker-status" class="hidden bg-gray-50 rounded p-2 border border-gray-200">
                                    <p class="text-xs text-gray-500">Titik baru: <span id="edit-picker-count" class="font-bold text-orange-500">0/4</span></p>
                                    <div class="flex gap-1 mt-1 flex-wrap" id="edit-picker-coords"></div>
                                </div>

                                <div class="flex gap-2">
                                    <button type="submit"
                                        class="flex-1 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 font-medium">
                                        <i class="fas fa-save mr-1"></i> Simpan
                                    </button>
                                    <button type="button" onclick="pickerModeView()"
                                        class="flex-1 py-2 bg-gray-100 text-gray-600 text-sm rounded-lg hover:bg-gray-200">
                                        Batal
                                    </button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        @endif

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

    <script>
    (function () {
        const CANVAS_W = 480, CANVAS_H = 320;
        const SLOT_COLORS = ['#3b82f6','#ef4444','#22c55e','#f59e0b','#a855f7','#06b6d4','#f97316'];
        const BASE_URL = '{{ url("admin/slot") }}';

        let existingSlots = @json($slotsData ?? []);
        let pickerPoints     = [];
        let currentMode      = 'view';
        let editingSlotId    = null;
        let editPickerPoints = [];

        // Debug: pastikan data terbaca
        console.log('existingSlots:', existingSlots);

        const canvas = document.getElementById('picker-canvas');
        if (!canvas) return;
        const ctx = canvas.getContext('2d');

        function slotToPoints(slot) {
            return [
                { x: slot.x1, y: slot.y1 },
                { x: slot.x2, y: slot.y2 },
                { x: slot.x3, y: slot.y3 },
                { x: slot.x4, y: slot.y4 },
            ];
        }

        function redraw() {
            ctx.clearRect(0, 0, CANVAS_W, CANVAS_H);
            existingSlots.forEach((slot, si) => {
                const color = SLOT_COLORS[si % SLOT_COLORS.length];
                const pts   = slotToPoints(slot);
                const dot   = document.getElementById('slot-color-dot-' + slot.id);
                if (dot) dot.style.background = color;

                ctx.beginPath();
                ctx.moveTo(pts[0].x, pts[0].y);
                pts.forEach(p => ctx.lineTo(p.x, p.y));
                ctx.closePath();

                const isEditing = currentMode === 'edit' && editingSlotId == slot.id;
                ctx.fillStyle   = isEditing ? color + '40' : color + '28';
                ctx.fill();
                ctx.strokeStyle = color;
                ctx.lineWidth   = isEditing ? 2.5 : 1.5;
                ctx.setLineDash(isEditing ? [6, 3] : []);
                ctx.stroke();
                ctx.setLineDash([]);

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

            const drawPts = currentMode === 'new' ? pickerPoints : editPickerPoints;
            if (drawPts.length > 0 && currentMode !== 'view') {
                ctx.beginPath();
                ctx.moveTo(drawPts[0].x, drawPts[0].y);
                for (let i = 1; i < drawPts.length; i++) ctx.lineTo(drawPts[i].x, drawPts[i].y);
                if (drawPts.length === 4) { ctx.closePath(); ctx.fillStyle = '#ffffff25'; ctx.fill(); }
                ctx.strokeStyle = '#fff';
                ctx.lineWidth = 2;
                ctx.setLineDash(drawPts.length < 4 ? [6, 3] : []);
                ctx.stroke();
                ctx.setLineDash([]);
                drawPts.forEach((p, i) => {
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

        function updatePickerUI() {
            const n = pickerPoints.length;
            const el = document.getElementById('picker-status');
            if (el) {
                el.textContent = `${n} / 4 titik`;
                el.className = n === 4 ? 'text-sm font-bold text-green-600' : 'text-sm font-bold text-orange-500';
            }
            ['1','2','3','4'].forEach((num, i) => {
                const c = document.getElementById('picker-coord-' + num);
                if (c) c.textContent = pickerPoints[i] ? `${pickerPoints[i].x}, ${pickerPoints[i].y}` : '—';
            });
            const btn = document.getElementById('picker-btn-apply');
            if (btn) btn.disabled = n !== 4;
            const info = document.getElementById('picker-points-info');
            if (info) info.innerHTML = pickerPoints.map((p, i) =>
                `<span style="font-size:11px;padding:2px 8px;background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe;border-radius:4px;">P${i+1}: (${p.x}, ${p.y})</span>`
            ).join('');
        }

        function updateEditPickerUI() {
            const n = editPickerPoints.length;
            const statusEl    = document.getElementById('edit-picker-status');
            const countEl     = document.getElementById('edit-picker-count');
            const coordsEl    = document.getElementById('edit-picker-coords');
            const coordsStatus = document.getElementById('coords-status');

            if (statusEl) statusEl.classList.toggle('hidden', n === 0);
            if (countEl) { countEl.textContent = `${n}/4`; countEl.className = n === 4 ? 'font-bold text-green-600' : 'font-bold text-orange-500'; }
            if (coordsEl) coordsEl.innerHTML = editPickerPoints.map((p, i) =>
                `<span style="font-size:10px;padding:1px 6px;background:#fefce8;color:#92400e;border:1px solid #fde68a;border-radius:4px;">P${i+1}: ${p.x},${p.y}</span>`
            ).join('');

            if (n === 4 && coordsStatus) {
                coordsStatus.textContent = '(koordinat baru siap)';
                coordsStatus.className = 'ml-1 text-green-600';
                const coords = ['x1','y1','x2','y2','x3','y3','x4','y4'];
                const vals = [
                    editPickerPoints[0].x, editPickerPoints[0].y,
                    editPickerPoints[1].x, editPickerPoints[1].y,
                    editPickerPoints[2].x, editPickerPoints[2].y,
                    editPickerPoints[3].x, editPickerPoints[3].y,
                ];
                coords.forEach((c, i) => {
                    const hidden  = document.getElementById('edit-' + c);
                    const preview = document.getElementById('preview-' + c);
                    if (hidden)  hidden.value        = vals[i];
                    if (preview) preview.textContent = `${c}: ${vals[i]}`;
                });
            }
        }

        function setMode(mode, slotId = null) {
            currentMode      = mode;
            editingSlotId    = slotId ? parseInt(slotId) : null; // pastikan integer
            pickerPoints     = [];
            editPickerPoints = [];

            const panelView = document.getElementById('picker-panel-view');
            const panelNew  = document.getElementById('picker-panel-new');
            const panelEdit = document.getElementById('picker-panel-edit');
            const modeBadge = document.getElementById('picker-mode-badge');
            const info      = document.getElementById('picker-points-info');

            [panelView, panelNew, panelEdit].forEach(p => {
                p?.classList.add('hidden');
                p?.classList.remove('flex');
            });
            if (info) info.innerHTML = '';

            if (mode === 'view') {
                canvas.style.cursor = 'default';
                canvas.onclick = null;
                panelView?.classList.remove('hidden');
                if (modeBadge) { modeBadge.textContent = 'Mode: Lihat'; modeBadge.className = 'text-xs px-2 py-0.5 rounded bg-gray-100 text-gray-500'; }

            } else if (mode === 'new') {
                canvas.style.cursor = 'crosshair';
                canvas.onclick = pickerClickNew;
                panelNew?.classList.remove('hidden');
                panelNew?.classList.add('flex');
                const btn = document.getElementById('picker-btn-apply');
                if (btn) btn.disabled = true;
                if (modeBadge) { modeBadge.textContent = 'Mode: Gambar'; modeBadge.className = 'text-xs px-2 py-0.5 rounded bg-green-100 text-green-600'; }

            } else if (mode === 'edit') {
                canvas.style.cursor = 'crosshair';
                canvas.onclick = pickerClickEdit;
                panelEdit?.classList.remove('hidden');
                panelEdit?.classList.add('flex');
                if (modeBadge) { modeBadge.textContent = 'Mode: Edit'; modeBadge.className = 'text-xs px-2 py-0.5 rounded bg-yellow-100 text-yellow-600'; }

                // Cari slot — gunakan == bukan === supaya int/string tidak masalah
                const slot = existingSlots.find(s => s.id == slotId);
                console.log('edit slotId:', slotId, '| found:', slot);

                if (slot) {
                    const label = document.getElementById('edit-slot-label');
                    if (label) label.textContent = `Slot ${slot.nomor_slot}`;

                    const nomorDisplay = document.getElementById('edit-nomor_slot_display');
                    const nomorHidden  = document.getElementById('edit-nomor_slot');
                    if (nomorDisplay) nomorDisplay.value = slot.nomor_slot;
                    if (nomorHidden)  nomorHidden.value  = slot.nomor_slot;

                    const ket = document.getElementById('edit-keterangan');
                    if (ket) ket.value = slot.keterangan;

                    ['x1','y1','x2','y2','x3','y3','x4','y4'].forEach(c => {
                        const hidden  = document.getElementById('edit-' + c);
                        const preview = document.getElementById('preview-' + c);
                        if (hidden)  hidden.value        = slot[c];
                        if (preview) preview.textContent = `${c}: ${slot[c]}`;
                    });

                    const coordsStatus = document.getElementById('coords-status');
                    if (coordsStatus) { coordsStatus.textContent = '(koordinat lama)'; coordsStatus.className = 'ml-1 text-gray-400'; }
                    document.getElementById('edit-picker-status')?.classList.add('hidden');

                    // ← Set hidden input ID, bukan form.action
                    const slotIdInput = document.getElementById('edit-slot-id');
                    if (slotIdInput) slotIdInput.value = slot.id;
                }
            }

            updatePickerUI();
            redraw();
        }

        // ← Submit handler: set action tepat sebelum kirim
        document.getElementById('inline-edit-form')?.addEventListener('submit', function(e) {
            e.preventDefault();
            const slotIdInput = document.getElementById('edit-slot-id');
            const id = slotIdInput ? slotIdInput.value : null;

            console.log('submit edit, id:', id);

            if (!id || id == '0') {
                alert('Error: ID slot tidak ditemukan, coba klik Edit lagi.');
                return;
            }

            this.action = BASE_URL + '/' + id;
            console.log('form action set to:', this.action);
            this.submit();
        });

        function pickerClickNew(event) {
            if (pickerPoints.length >= 4) return;
            const rect = canvas.getBoundingClientRect();
            pickerPoints.push({
                x: Math.round((event.clientX - rect.left) * (CANVAS_W / rect.width)),
                y: Math.round((event.clientY - rect.top)  * (CANVAS_H / rect.height)),
            });
            redraw();
            updatePickerUI();
            if (pickerPoints.length === 4) {
                ['x1','y1','x2','y2','x3','y3','x4','y4'].forEach((c, i) => {
                    const vals = [pickerPoints[0].x, pickerPoints[0].y, pickerPoints[1].x, pickerPoints[1].y,
                                pickerPoints[2].x, pickerPoints[2].y, pickerPoints[3].x, pickerPoints[3].y];
                    const hidden = document.getElementById('new-' + c);
                    if (hidden) hidden.value = vals[i];
                });
            }
        }

        function pickerClickEdit(event) {
            if (editPickerPoints.length >= 4) return;
            const rect = canvas.getBoundingClientRect();
            editPickerPoints.push({
                x: Math.round((event.clientX - rect.left) * (CANVAS_W / rect.width)),
                y: Math.round((event.clientY - rect.top)  * (CANVAS_H / rect.height)),
            });
            redraw();
            updateEditPickerUI();
        }

        window.pickerModeView = () => setMode('view');
        window.pickerModeNew  = () => setMode('new');
        window.pickerModeEdit = (id) => setMode('edit', id);

        setMode('view');
    })();
    </script>
@endsection
