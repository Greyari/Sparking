<div class="w-full px-5 mx-6 mb-6" x-data="polygonPicker()">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-3">
        <h3 class="font-bold text-gray-700">
            <i class="mr-1 fas fa-draw-polygon text-blue-500"></i>
            Tandai Area Slot di Video
        </h3>
        <div class="flex items-center gap-2">
            <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">480 × 320 px</span>
            <button type="button" @click="resetPoints()"
                class="text-xs text-red-500 hover:underline">
                Reset titik
            </button>
        </div>
    </div>

    {{-- Canvas + Video wrapper --}}
    <div class="relative inline-block border border-gray-300 rounded-lg overflow-hidden"
         style="width:480px; height:320px;">

        {{-- Video feed (background) --}}
        <img id="polygon-video-feed"
             :src="feedUrl"
             style="width:480px; height:320px; display:block; object-fit:cover;"
             onerror="this.style.background='#1a1a2e'; this.src=''">

        {{-- Canvas overlay --}}
        <canvas id="polygon-canvas"
                width="480" height="320"
                @click="addPoint($event)"
                class="absolute top-0 left-0 cursor-crosshair"
                style="width:480px; height:320px;">
        </canvas>
    </div>

    {{-- Kontrol kamera --}}
    <div class="flex items-center gap-3 mt-3 flex-wrap">
        <div>
            <label class="text-xs font-medium text-gray-600">Camera ID:</label>
            <select x-model="cameraId" @change="updateFeed()"
                class="ml-1 text-sm border border-gray-300 rounded px-2 py-1">
                @foreach($subzonas as $sz)
                    <option value="{{ $sz->camera_id }}">
                        {{ $sz->nama_subzona }} (cam {{ $sz->camera_id }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="flex items-center gap-1">
            <span class="text-xs text-gray-500">Slot aktif:</span>
            <template x-for="(slot, i) in slots" :key="i">
                <button type="button"
                    @click="activeSlot = i"
                    :class="activeSlot === i
                        ? 'bg-blue-500 text-white'
                        : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                    class="text-xs px-2 py-1 rounded font-medium">
                    S<span x-text="i+1"></span>
                </button>
            </template>
            <button type="button" @click="addSlot()"
                class="text-xs px-2 py-1 rounded bg-green-100 text-green-700 hover:bg-green-200">
                + Slot
            </button>
        </div>

        <span class="text-xs font-medium"
            :class="currentPoints.length === 4 ? 'text-green-600' : 'text-orange-500'">
            <span x-text="currentPoints.length"></span>/4 titik
            <span x-show="currentPoints.length === 4"> ✓ Selesai</span>
        </span>
    </div>

    {{-- Info titik --}}
    <div class="mt-2 flex gap-2 flex-wrap">
        <template x-for="(pt, i) in currentPoints" :key="i">
            <span class="text-xs bg-blue-50 text-blue-700 border border-blue-200 px-2 py-0.5 rounded">
                P<span x-text="i+1"></span>: (<span x-text="pt.x"></span>, <span x-text="pt.y"></span>)
            </span>
        </template>
    </div>

    {{-- Tombol apply ke form --}}
    <div class="mt-3 flex gap-2">
        <button type="button" @click="applyToTambahForm()"
            :disabled="currentPoints.length !== 4"
            class="text-sm px-4 py-1.5 bg-blue-600 text-white rounded hover:bg-blue-700 disabled:opacity-40 disabled:cursor-not-allowed">
            <i class="fas fa-check mr-1"></i> Apply ke Form Tambah
        </button>
        <button type="button" @click="resetPoints()"
            class="text-sm px-4 py-1.5 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
            Reset
        </button>
    </div>
</div>

{{-- =====================================================
     ALPINE.JS COMPONENT
     ===================================================== --}}
<script>
function polygonPicker() {
    return {
        cameraId: {{ $subzonas->first()->camera_id ?? 0 }},
        streamBaseUrl: '{{ config("services.cloudflare.stream_url") }}',
        feedUrl: '',
        activeSlot: 0,
        slots: [{ points: [] }],
        canvas: null,
        ctx: null,
        COLORS: ['#3b82f6','#ef4444','#22c55e','#f59e0b','#a855f7','#06b6d4'],

        get currentPoints() {
            return this.slots[this.activeSlot]?.points ?? [];
        },

        init() {
            this.feedUrl = `${this.streamBaseUrl}/video_feed?camera_id=${this.cameraId}`;
            this.$nextTick(() => {
                this.canvas = document.getElementById('polygon-canvas');
                this.ctx = this.canvas.getContext('2d');
                this.redraw();
            });
        },

        updateFeed() {
            this.feedUrl = `${this.streamBaseUrl}/video_feed?camera_id=${this.cameraId}`;
        },

        addSlot() {
            this.slots.push({ points: [] });
            this.activeSlot = this.slots.length - 1;
        },

        addPoint(event) {
            const rect = this.canvas.getBoundingClientRect();
            const scaleX = 480 / rect.width;
            const scaleY = 320 / rect.height;
            const x = Math.round((event.clientX - rect.left) * scaleX);
            const y = Math.round((event.clientY - rect.top) * scaleY);

            if (this.currentPoints.length < 4) {
                this.slots[this.activeSlot].points.push({ x, y });
                this.redraw();
            }
        },

        resetPoints() {
            this.slots[this.activeSlot].points = [];
            this.redraw();
        },

        redraw() {
            if (!this.ctx) return;
            this.ctx.clearRect(0, 0, 480, 320);

            this.slots.forEach((slot, si) => {
                const pts = slot.points;
                const color = this.COLORS[si % this.COLORS.length];
                const isActive = si === this.activeSlot;
                if (pts.length === 0) return;

                // Polygon
                this.ctx.beginPath();
                this.ctx.moveTo(pts[0].x, pts[0].y);
                for (let i = 1; i < pts.length; i++) this.ctx.lineTo(pts[i].x, pts[i].y);
                if (pts.length === 4) this.ctx.closePath();
                this.ctx.strokeStyle = color;
                this.ctx.lineWidth = isActive ? 2.5 : 1.5;
                this.ctx.setLineDash(pts.length < 4 ? [6, 3] : []);
                this.ctx.stroke();
                this.ctx.setLineDash([]);

                if (pts.length === 4) {
                    this.ctx.fillStyle = color + '30';
                    this.ctx.fill();
                    const cx = pts.reduce((a, p) => a + p.x, 0) / 4;
                    const cy = pts.reduce((a, p) => a + p.y, 0) / 4;
                    this.ctx.fillStyle = color;
                    this.ctx.font = 'bold 14px sans-serif';
                    this.ctx.textAlign = 'center';
                    this.ctx.textBaseline = 'middle';
                    this.ctx.fillText('S' + (si + 1), cx, cy);
                    this.ctx.textAlign = 'left';
                    this.ctx.textBaseline = 'alphabetic';
                }

                pts.forEach((p, pi) => {
                    this.ctx.beginPath();
                    this.ctx.arc(p.x, p.y, isActive ? 6 : 4, 0, Math.PI * 2);
                    this.ctx.fillStyle = color;
                    this.ctx.fill();
                    this.ctx.strokeStyle = '#fff';
                    this.ctx.lineWidth = 1.5;
                    this.ctx.stroke();
                    this.ctx.fillStyle = '#fff';
                    this.ctx.font = 'bold 9px sans-serif';
                    this.ctx.textAlign = 'center';
                    this.ctx.textBaseline = 'middle';
                    this.ctx.fillText(pi + 1, p.x, p.y);
                    this.ctx.textAlign = 'left';
                    this.ctx.textBaseline = 'alphabetic';
                });
            });
        },

        applyToTambahForm() {
            const pts = this.currentPoints;
            if (pts.length !== 4) return;
            // Isi input form tambah slot
            document.getElementById('x1').value = pts[0].x;
            document.getElementById('y1').value = pts[0].y;
            document.getElementById('x2').value = pts[1].x;
            document.getElementById('y2').value = pts[1].y;
            document.getElementById('x3').value = pts[2].x;
            document.getElementById('y3').value = pts[2].y;
            document.getElementById('x4').value = pts[3].x;
            document.getElementById('y4').value = pts[3].y;

            // Buka modal tambah slot
            document.getElementById('tambah-slot').classList.remove('hidden');
            document.getElementById('tambah-slot').classList.add('flex');
        },

        applyToEditForm(slotId) {
            const pts = this.currentPoints;
            if (pts.length !== 4) return;
            document.querySelector(`#edit-slot-${slotId} [name="x1"]`).value = pts[0].x;
            document.querySelector(`#edit-slot-${slotId} [name="y1"]`).value = pts[0].y;
            document.querySelector(`#edit-slot-${slotId} [name="x2"]`).value = pts[1].x;
            document.querySelector(`#edit-slot-${slotId} [name="y2"]`).value = pts[1].y;
            document.querySelector(`#edit-slot-${slotId} [name="x3"]`).value = pts[2].x;
            document.querySelector(`#edit-slot-${slotId} [name="y3"]`).value = pts[2].y;
            document.querySelector(`#edit-slot-${slotId} [name="x4"]`).value = pts[3].x;
            document.querySelector(`#edit-slot-${slotId} [name="y4"]`).value = pts[3].y;
        }
    }
}
</script>
