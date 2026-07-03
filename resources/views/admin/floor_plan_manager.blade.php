@extends('layouts.admin')
@section('title', 'Floor Plan Manager')

@push('styles')
<style>
.fp-wrap { padding: 16px; max-width: 1400px; margin: 0 auto; }
.fp-header {
    background: #fff; border-radius: 12px; padding: 16px 20px; margin-bottom: 16px;
    display: flex; justify-content: space-between; align-items: center;
    box-shadow: 0 2px 8px rgba(0,0,0,.06);
}
.fp-header h1 { margin: 0; font-size: 19px; color: #2e7d32; }
.fp-header p  { margin: 4px 0 0; font-size: 12px; color: #888; }
.fac-select   { padding: 8px 14px; border-radius: 8px; border: 1px solid #ddd; font-weight: 600; }

.fp-grid { display: grid; grid-template-columns: 1fr 340px; gap: 16px; }
@media(max-width:900px){ .fp-grid{ grid-template-columns: 1fr; } }

.card { background: #fff; border-radius: 12px; padding: 18px; box-shadow: 0 2px 8px rgba(0,0,0,.06); }
.card-title { font-size: 14px; font-weight: 700; color: #333; margin: 0 0 14px; }

/* Upload */
.upload-zone {
    border: 2.5px dashed #c8e6c9; border-radius: 10px; padding: 14px 16px;
    background: #f9fef9; display: flex; align-items: center; gap: 10px;
    margin-bottom: 12px; flex-wrap: wrap;
}
.upload-zone input[type=file]{ flex: 1; min-width: 180px; }
.btn-up {
    background: #2e7d32; color: #fff; border: none;
    padding: 9px 18px; border-radius: 8px; font-weight: 700; cursor: pointer;
}
.btn-up:disabled { background: #a5d6a7; cursor: default; }

/* Canvas wrapper — pins position relative to this */
.canvas-outer {
    width: 100%; background: #f1f3f5;
    border: 2px solid #e0e0e0; border-radius: 10px;
    overflow: hidden; min-height: 360px;
    display: flex; align-items: center; justify-content: center;
}
/* img-wrap is sized exactly to the rendered image */
.img-wrap {
    position: relative; display: inline-block;
    line-height: 0; /* remove bottom gap */
}
.img-wrap img {
    display: block; max-width: 100%; height: auto;
    cursor: crosshair; user-select: none; -webkit-user-drag: none;
}
.canvas-placeholder { color: #bbb; text-align: center; font-size: 14px; line-height: 1.8; padding: 20px; }

/* Pins */
.pin-dot {
    position: absolute; width: 32px; height: 32px; border-radius: 50%;
    transform: translate(-50%, -50%);
    border: 2.5px solid #fff; box-shadow: 0 2px 8px rgba(0,0,0,.35);
    display: flex; align-items: center; justify-content: center;
    font-size: 10px; font-weight: 900; color: #fff;
    cursor: pointer; z-index: 5;
}
.pin-dot.no-coord  { background: #9e9e9e; }
.pin-dot.has-coord { background: #2e7d32; }
.pin-dot.active-pin{ background: #1565c0; box-shadow: 0 0 0 5px rgba(21,101,192,.3); }

.pin-name-label {
    position: absolute; bottom: 34px; left: 50%; transform: translateX(-50%);
    background: rgba(0,0,0,.78); color: #fff;
    padding: 3px 7px; border-radius: 5px;
    font-size: 11px; white-space: nowrap;
    font-family: 'Roboto Condensed', sans-serif;
    pointer-events: none;
}

/* Right panel */
.right-panel { display: flex; flex-direction: column; gap: 14px; }
.form-label  { font-size: 12px; font-weight: 700; color: #555; display: block; margin-bottom: 4px; }
.form-select, .form-input {
    width: 100%; padding: 9px; border-radius: 8px; border: 1.5px solid #ddd;
    font-size: 14px; box-sizing: border-box;
}
.form-input { background: #f8f9fa; }
.form-input.filled { border-color: #2e7d32; background: #f0fff4; }
.coord-row { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin: 10px 0; }

.btn-save { width: 100%; padding: 11px; background: #1565c0; color: #fff; border: none; border-radius: 8px; font-weight: 700; font-size: 14px; cursor: pointer; }
.btn-save:disabled { background: #bbdefb; cursor: default; }
.btn-del  { width: 100%; padding: 8px; background: transparent; color: #e53935; border: 1.5px solid #e53935; border-radius: 8px; font-weight: 600; font-size: 13px; cursor: pointer; margin-top: 6px; }
.hint-box { font-size: 12px; color: #666; background: #f5f5f5; border-radius: 8px; padding: 10px 12px; line-height: 1.7; margin: 10px 0; }
.hint-box strong { color: #2e7d32; }
#save-status { text-align: center; font-size: 12px; margin-top: 6px; min-height: 16px; }

/* Table */
.m-table { width: 100%; border-collapse: collapse; font-size: 13px; margin-top: 14px; }
.m-table th { background: #f5f5f5; padding: 8px 10px; text-align: left; font-size: 12px; color: #777; font-weight: 700; border-bottom: 2px solid #eee; }
.m-table td { padding: 8px 10px; border-bottom: 1px solid #f0f0f0; vertical-align: middle; }
.m-table tr:hover td { background: #fafafa; }
.badge-ok  { background: #e8f5e9; color: #2e7d32; padding: 2px 8px; border-radius: 10px; font-size: 11px; font-weight: 700; }
.badge-no  { background: #f5f5f5; color: #bbb; padding: 2px 8px; border-radius: 10px; font-size: 11px; }
.btn-edit  { background: none; border: none; color: #1565c0; cursor: pointer; font-size: 12px; font-weight: 600; }
.btn-del-r { background: none; border: none; color: #e53935; cursor: pointer; font-size: 12px; }
.saved-hdr { font-size: 14px; font-weight: 700; color: #333; display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px; }
.saved-cnt { background: #e8f5e9; color: #2e7d32; padding: 2px 8px; border-radius: 12px; font-size: 12px; font-weight: 700; }
</style>
@endpush

@section('content')
<div class="fp-wrap">

    <div class="fp-header">
        <div>
            <h1>📍 Floor Plan Manager</h1>
            <p>Upload layout pabrik → pilih mesin → klik gambar untuk menentukan titik koordinat.</p>
        </div>
        <form id="facForm" method="GET">
            <select name="factory" class="fac-select" onchange="document.getElementById('facForm').submit()">
                @foreach($factories as $f)
                    <option value="{{ $f->name }}" {{ $factory == $f->name ? 'selected' : '' }}>{{ $f->short_label ?? $f->name }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <div class="fp-grid">

        {{-- LEFT --}}
        <div>
            <div class="card" style="padding-bottom:12px;">
                <p class="card-title">🖼️ Layout Pabrik</p>

                <div class="upload-zone">
                    <input type="file" id="fileInput" accept="image/*">
                    <button class="btn-up" id="btnUpload" onclick="doUpload()" disabled>Upload Layout</button>
                    <span id="up-status" style="font-size:12px;color:#666;">Pilih foto layout (max 5 MB)</span>
                </div>

                {{-- Canvas --}}
                <div class="canvas-outer" id="canvasOuter">
                    @if($layout && $layout->image_url)
                        {{-- img-wrap: position relative → pins positioned inside this --}}
                        <div class="img-wrap" id="imgWrap" onclick="onMapClick(event)">
                            <img id="layoutImg" src="{{ $layout->image_url }}" alt="Layout" ondragstart="return false">
                            <div id="pinsLayer"></div>
                        </div>
                    @else
                        <div class="canvas-placeholder" id="canvasPlaceholder">
                            📷 Belum ada layout.<br>Upload foto layout pabrik terlebih dahulu.
                        </div>
                    @endif
                </div>
            </div>

            {{-- Saved list --}}
            <div class="card" style="margin-top:14px;">
                <div class="saved-hdr">
                    <span>📋 Daftar Mesin &amp; Koordinat</span>
                    <span class="saved-cnt" id="savedCount">0 tersimpan</span>
                </div>
                <table class="m-table">
                    <thead>
                        <tr><th>#</th><th>Nama Mesin</th><th>Section</th><th>Koordinat</th><th>Aksi</th></tr>
                    </thead>
                    <tbody id="machineTableBody">
                        <tr><td colspan="5" style="text-align:center;color:#bbb;padding:20px;">Loading...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- RIGHT --}}
        <div class="right-panel">
            <div class="card">
                <p class="card-title">🎯 Set Koordinat Mesin</p>

                <label class="form-label">Pilih Mesin:</label>
                <select class="form-select" id="machineSelect" onchange="onMachineSelect()">
                    <option value="">-- Pilih mesin --</option>
                </select>

                <div class="hint-box">
                    1. Pilih mesin dari dropdown<br>
                    2. <strong>Klik titik</strong> pada gambar layout<br>
                    3. Koordinat X &amp; Y otomatis terisi<br>
                    4. Tekan <strong>Simpan Koordinat</strong>
                </div>

                <div class="coord-row">
                    <div>
                        <label class="form-label">X (%)</label>
                        <input class="form-input" type="number" id="coordX" step="0.01" placeholder="Klik gambar" readonly>
                    </div>
                    <div>
                        <label class="form-label">Y (%)</label>
                        <input class="form-input" type="number" id="coordY" step="0.01" placeholder="Klik gambar" readonly>
                    </div>
                </div>

                <button class="btn-save" id="btnSave" onclick="savePin()" disabled>💾 Simpan Koordinat</button>
                <button class="btn-del"  id="btnClear" onclick="clearPin()" style="display:none;">🗑️ Hapus Titik Ini</button>
                <p id="save-status"></p>
            </div>

            <div class="card" id="layoutInfo" style="display:{{ $layout ? 'block' : 'none' }};">
                <p class="card-title">📐 Info Layout</p>
                <div style="font-size:13px;color:#555;line-height:1.9;">
                    <div>Factory: <strong>{{ $factory }}</strong></div>
                    <div>Dimensi: <strong id="infoDims">{{ $layout?->layout_width }}×{{ $layout?->layout_height }}px</strong></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const FACTORY = @json($factory);
const CSRF    = document.querySelector('meta[name="csrf-token"]').content;

let machinesData    = [];
let selectedMachine = null;
let pendingX = null, pendingY = null;

document.addEventListener('DOMContentLoaded', () => {
    loadMachines();
    document.getElementById('fileInput').addEventListener('change', function(){
        document.getElementById('btnUpload').disabled = !this.files.length;
        document.getElementById('up-status').textContent = this.files.length ? this.files[0].name : 'Pilih foto layout (max 5 MB)';
    });
});

/* ─── Load machines ─── */
async function loadMachines() {
    try {
        const r = await fetch(`/api/floor-plan-manager/machines?factory=${encodeURIComponent(FACTORY)}`);
        machinesData = await r.json();
        buildDropdown();
        renderPins();
        renderTable();
    } catch(e) { console.error(e); }
}

function buildDropdown() {
    const sel = document.getElementById('machineSelect');
    sel.innerHTML = '<option value="">-- Pilih mesin --</option>';
    machinesData.forEach(m => {
        const opt = document.createElement('option');
        opt.value = m.id;
        opt.textContent = m.name + (m.floor_cx !== null ? ' ✓' : '');
        sel.appendChild(opt);
    });
}

/* ─── Machine selected ─── */
function onMachineSelect() {
    const id = document.getElementById('machineSelect').value;
    selectedMachine = machinesData.find(m => m.id == id) || null;
    pendingX = pendingY = null;

    if (selectedMachine) {
        if (selectedMachine.floor_cx !== null) {
            pendingX = selectedMachine.floor_cx;
            pendingY = selectedMachine.floor_cy;
            setCoords(pendingX, pendingY);
        } else {
            setCoords(null, null);
        }
        document.getElementById('btnSave').disabled = false;
        document.getElementById('btnClear').style.display = selectedMachine.floor_cx !== null ? 'block' : 'none';
    } else {
        setCoords(null, null);
        document.getElementById('btnSave').disabled = true;
        document.getElementById('btnClear').style.display = 'none';
    }
    renderPins();
}

/* ─── Click on image ─── */
function onMapClick(e) {
    if (!selectedMachine) { showStatus('⚠️ Pilih mesin dulu!', '#e65100'); return; }
    // Target is imgWrap – coords relative to imgWrap = relative to image
    const wrap = document.getElementById('imgWrap');
    const rect = wrap.getBoundingClientRect();
    pendingX = ((e.clientX - rect.left) / rect.width  * 100).toFixed(2);
    pendingY = ((e.clientY - rect.top)  / rect.height * 100).toFixed(2);
    setCoords(pendingX, pendingY);
    document.getElementById('btnSave').disabled = false;
    renderPins();
}

function setCoords(x, y) {
    const xEl = document.getElementById('coordX');
    const yEl = document.getElementById('coordY');
    xEl.value = x ?? '';
    yEl.value = y ?? '';
    xEl.classList.toggle('filled', x !== null);
    yEl.classList.toggle('filled', y !== null);
}

/* ─── Render pins — relative to imgWrap ─── */
function renderPins() {
    const layer = document.getElementById('pinsLayer');
    if (!layer) return;
    layer.innerHTML = '';

    machinesData.forEach(m => {
        const isSelected = selectedMachine && selectedMachine.id === m.id;
        const cx = isSelected && pendingX !== null ? pendingX : m.floor_cx;
        const cy = isSelected && pendingY !== null ? pendingY : m.floor_cy;
        if (cx === null) return;

        const dot = document.createElement('div');
        dot.className = 'pin-dot ' + (isSelected ? 'active-pin' : 'has-coord');
        dot.style.left = cx + '%';
        dot.style.top  = cy + '%';

        // Always visible name label above pin
        dot.innerHTML = `<div class="pin-name-label">${m.name}</div>${m.name.substring(0,2).toUpperCase()}`;
        dot.onclick = ev => {
            ev.stopPropagation();
            document.getElementById('machineSelect').value = m.id;
            onMachineSelect();
        };
        layer.appendChild(dot);
    });
}

/* ─── Table ─── */
function renderTable() {
    const tbody = document.getElementById('machineTableBody');
    const cnt = machinesData.filter(m => m.floor_cx !== null).length;
    document.getElementById('savedCount').textContent = `${cnt}/${machinesData.length} tersimpan`;

    if (!machinesData.length) {
        tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;color:#bbb;padding:16px;">Tidak ada mesin untuk factory ini.</td></tr>';
        return;
    }
    tbody.innerHTML = machinesData.map((m,i) => {
        const ok = m.floor_cx !== null;
        const badge = ok
            ? `<span class="badge-ok">✓ (${parseFloat(m.floor_cx).toFixed(1)}%, ${parseFloat(m.floor_cy).toFixed(1)}%)</span>`
            : `<span class="badge-no">Belum diset</span>`;
        return `<tr>
            <td style="color:#bbb;">${i+1}</td>
            <td style="font-weight:600;">${esc(m.name)}</td>
            <td style="color:#888;font-size:12px;">${esc(m.section||'-')}</td>
            <td>${badge}</td>
            <td>
                <button class="btn-edit" onclick="jumpTo(${m.id})">✏️</button>
                ${ok ? `<button class="btn-del-r" onclick="deletePinById(${m.id})" title="Hapus koordinat">✕</button>` : ''}
            </td>
        </tr>`;
    }).join('');
}

function jumpTo(id) {
    document.getElementById('machineSelect').value = id;
    onMachineSelect();
    document.getElementById('coordX').scrollIntoView({ behavior:'smooth', block:'center' });
}

/* ─── Save ─── */
async function savePin() {
    if (!selectedMachine || pendingX === null) {
        showStatus('⚠️ Klik gambar untuk menentukan titik!', '#e65100'); return;
    }
    document.getElementById('btnSave').disabled = true;
    showStatus('Menyimpan...', '#555');
    try {
        const res = await fetch(`/api/floor-plan-manager/pin/${selectedMachine.id}`, {
            method: 'PATCH',
            headers: { 'Content-Type':'application/json','X-CSRF-TOKEN':CSRF,'Accept':'application/json' },
            body: JSON.stringify({ floor_cx: parseFloat(pendingX), floor_cy: parseFloat(pendingY), floor_plan: FACTORY })
        });
        const data = await res.json();
        if (data.ok) {
            const idx = machinesData.findIndex(m => m.id === selectedMachine.id);
            if (idx !== -1) { machinesData[idx].floor_cx = pendingX; machinesData[idx].floor_cy = pendingY; selectedMachine = machinesData[idx]; }
            buildDropdown();
            document.getElementById('machineSelect').value = selectedMachine.id;
            document.getElementById('btnClear').style.display = 'block';
            renderPins(); renderTable();
            showStatus('✅ Koordinat tersimpan!', '#2e7d32');
        }
    } catch(e) { showStatus('❌ Error: ' + e.message, '#e53935'); }
    finally { document.getElementById('btnSave').disabled = false; }
}

async function clearPin() {
    if (!selectedMachine || !confirm(`Hapus titik "${selectedMachine.name}"?`)) return;
    await deletePinById(selectedMachine.id);
}

async function deletePinById(id) {
    const m = machinesData.find(x => x.id == id);
    if (!m) return;
    try {
        const res = await fetch(`/api/floor-plan-manager/pin/${id}`, {
            method: 'PATCH',
            headers: { 'Content-Type':'application/json','X-CSRF-TOKEN':CSRF,'Accept':'application/json' },
            body: JSON.stringify({ floor_cx: null, floor_cy: null, floor_plan: null })
        });
        const data = await res.json();
        if (data.ok) {
            const idx = machinesData.findIndex(x => x.id == id);
            if (idx !== -1) { machinesData[idx].floor_cx = null; machinesData[idx].floor_cy = null; }
            pendingX = pendingY = null;
            setCoords(null, null);
            document.getElementById('btnClear').style.display = 'none';
            buildDropdown();
            if (selectedMachine?.id == id) document.getElementById('machineSelect').value = id;
            renderPins(); renderTable();
            showStatus('✅ Titik dihapus.', '#2e7d32');
        }
    } catch(e) { showStatus('❌ Error: ' + e.message, '#e53935'); }
}

/* ─── Upload ─── */
async function doUpload() {
    const file = document.getElementById('fileInput').files[0];
    if (!file) return;
    document.getElementById('btnUpload').disabled = true;
    document.getElementById('up-status').textContent = 'Mengupload...';

    const fd = new FormData();
    fd.append('layout', file);
    fd.append('factory', FACTORY);
    try {
        const res  = await fetch('/admin/floor-plan-manager/upload', { method:'POST', headers:{'X-CSRF-TOKEN':CSRF}, body:fd });
        const data = await res.json();
        if (data.ok) {
            document.getElementById('up-status').textContent = '✅ Berhasil!';
            const outer = document.getElementById('canvasOuter');
            const ph    = document.getElementById('canvasPlaceholder');
            if (ph) ph.remove();

            // Build imgWrap + img + pinsLayer
            let wrap = document.getElementById('imgWrap');
            if (!wrap) {
                wrap = document.createElement('div');
                wrap.className = 'img-wrap'; wrap.id = 'imgWrap';
                wrap.onclick = onMapClick;
                const img = document.createElement('img');
                img.id = 'layoutImg'; img.setAttribute('ondragstart','return false');
                const layer = document.createElement('div'); layer.id = 'pinsLayer';
                wrap.appendChild(img); wrap.appendChild(layer);
                outer.appendChild(wrap);
            }
            document.getElementById('layoutImg').src = data.url + '?t=' + Date.now();
            document.getElementById('layoutInfo').style.display = 'block';
            if (data.width && data.height) document.getElementById('infoDims').textContent = `${data.width}×${data.height}px`;
            renderPins();
        } else {
            document.getElementById('up-status').textContent = '❌ Gagal upload.';
        }
    } catch(e) { document.getElementById('up-status').textContent = '❌ Error: ' + e.message; }
    finally { document.getElementById('btnUpload').disabled = false; }
}

function showStatus(msg, color) {
    const el = document.getElementById('save-status');
    el.textContent = msg; el.style.color = color;
    if (color === '#2e7d32') setTimeout(() => { el.textContent = ''; }, 3000);
}
function esc(s) { return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }
</script>
@endsection
