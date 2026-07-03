@extends('layouts.admin')

@section('title', 'Skill Management - HENKATEN BOARD')

@push('styles')
    <style>
        .skill-container {
            padding: 20px;
            max-width: 100%;
            margin: 0 auto;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            background: #fff;
            padding: 16px 24px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .page-title h1 {
            margin: 0;
            font-size: 20px;
            color: #185E35;
            font-family: 'Roboto Condensed', sans-serif;
            font-weight: 700;
        }

        .page-title p {
            margin: 4px 0 0;
            color: #666;
            font-size: 13px;
        }

        .filter-bar {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .filter-select {
            padding: 8px 16px;
            border-radius: 8px;
            border: 1px solid #ddd;
            background: #f8f9fa;
            font-weight: 600;
        }

        .matrix-card {
            background: #fff;
            border-radius: 12px;
            padding: 0;
            /* removed padding for better sticky edge */
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            overflow: auto;
            max-height: 70vh;
            border: 1px solid #eee;
        }

        .m-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-family: 'Roboto', sans-serif;
            font-size: 13px;
        }

        .m-table th {
            background: #185E35;
            color: #fff;
            padding: 10px 8px;
            font-weight: 600;
            text-align: center;
            border-right: 1px solid #114526;
            border-bottom: 1px solid #114526;
            white-space: nowrap;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .m-table thead tr:nth-child(2) th {
            top: 39px; /* adjust exact height */
            z-index: 9;
        }

        .m-table th.col-name, .m-table td.col-name {
            width: 250px;
            min-width: 250px;
            max-width: 250px;
            left: 0;
            position: sticky;
        }
        
        .m-table th.col-shift, .m-table td.col-shift {
            width: 80px;
            min-width: 80px;
            max-width: 80px;
            left: 250px;
            position: sticky;
        }

        .m-table th.col-name, .m-table th.col-shift {
            z-index: 12 !important; /* Above table body sticky columns */
            background: #185E35; /* Opaque background so scroll doesn't show behind */
        }

        .m-table td.col-name, .m-table td.col-shift {
            z-index: 11;
            background: #fff;
            box-shadow: 2px 0 5px -2px rgba(0,0,0,0.1);
        }

        .m-table thead tr:first-child th:first-child {
            border-top-left-radius: 11px;
        }

        .m-table thead tr:first-child th:last-child {
            border-top-right-radius: 11px;
        }

        .m-table tbody tr:last-child td:first-child {
            border-bottom-left-radius: 11px;
        }

        .m-table tbody tr:last-child td:last-child {
            border-bottom-right-radius: 11px;
        }

        .m-table td {
            padding: 6px;
            border-right: 1px solid #eee;
            border-bottom: 1px solid #eee;
            text-align: center;
            color: #333;
            vertical-align: middle;
            background: #fff;
        }

        .m-table td.col-name {
            text-align: left;
            font-weight: 600;
        }

        .m-table tr:nth-child(even) td:not(.col-name):not(.col-shift) {
            background: #fdfdfd;
        }

        .m-table tr:hover td:not(.col-name):not(.col-shift) {
            background: #f1f5f9;
        }

        /* ILUO Style Circles for Skill */
        .skill-circle {
            display: inline-block;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            border: 1px solid #333;
            position: relative;
            background: #fff;
            vertical-align: middle;
        }

        .skill-100 {
            background: #333;
        }

        .skill-75 {
            background: conic-gradient(#333 0deg 270deg, #fff 270deg 360deg);
        }

        .skill-50 {
            background: conic-gradient(#333 0deg 180deg, #fff 180deg 360deg);
        }

        .skill-25 {
            background: conic-gradient(#333 0deg 90deg, #fff 90deg 360deg);
        }

        .skill-0 {
            background: #fff;
            border-color: #ccc;
        }

        .chip-level {
            font-size: 11px;
            font-weight: 700;
            color: #2E7D32;
            padding: 4px 8px;
            background: #dcfce7;
            border-radius: 12px;
            white-space: nowrap;
        }

        .chip-level.training {
            color: #d97706;
            background: #fef3c7;
        }

        .chip-level.newbie {
            color: #dc2626;
            background: #fee2e2;
        }

        .skill-input-wrap {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #fff;
            padding: 2px 6px;
            border-radius: 6px;
            border: 1px solid #ddd;
            transition: border-color 0.2s;
        }

        .skill-input-wrap:focus-within {
            border-color: #185E35;
        }

        .skill-input {
            width: 45px;
            border: none;
            outline: none;
            text-align: center;
            font-weight: 600;
            font-size: 13px;
            color: #333;
            background: transparent;
        }

        .skill-input::-webkit-inner-spin-button,
        .skill-input::-webkit-outer-spin-button {
            opacity: 1;
            /* Keep arrows visible for easy clicking */
        }

        .saving-indicator {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #333;
            color: #fff;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            transform: translateY(100px);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1000;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .saving-indicator.show {
            transform: translateY(0);
        }

        .saving-spinner {
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            100% {
                transform: rotate(360deg);
            }
        }

        /* Modal Styles */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 2000;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-content {
            background: #fff;
            border-radius: 12px;
            width: 100%;
            max-width: 500px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }

        .modal-header {
            background: #185E35;
            color: #fff;
            padding: 16px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h3 {
            margin: 0;
            font-size: 18px;
        }

        .modal-close {
            background: none;
            border: none;
            color: #fff;
            font-size: 20px;
            cursor: pointer;
        }

        .modal-body {
            padding: 20px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 6px;
            font-size: 13px;
            color: #444;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-family: inherit;
            font-size: 14px;
        }

        .btn-primary {
            background: #185E35;
            color: #fff;
            border: none;
            padding: 10px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
        }

        .btn-primary:hover {
            background: #114526;
        }

        .btn-manage {
            background: #0284c7;
            color: #fff;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-manage:hover {
            background: #0369a1;
        }

        .item-list {
            border: 1px solid #ddd;
            border-radius: 6px;
            max-height: 200px;
            overflow-y: auto;
            margin-top: 10px;
        }

        .item-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 16px;
            border-bottom: 1px solid #eee;
        }

        .item-row:last-child {
            border-bottom: none;
        }

        .item-row span {
            font-size: 13px;
            font-weight: 600;
        }

        .btn-danger-sm {
            background: #ef4444;
            color: #fff;
            border: none;
            padding: 4px 8px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
        }

        .info-alert {
            background: #e0f2fe;
            border-left: 4px solid #0ea5e9;
            padding: 12px;
            margin-bottom: 16px;
            font-size: 13px;
            color: #0369a1;
            border-radius: 4px;
        }
    </style>
@endpush

@section('content')
    <div class="skill-container">
        <div class="page-header">
            <div class="page-title">
                <h1>⚙️ Skill Management Matrix</h1>
                <p>Manajemen persentase skill member terhadap mesin. Input 0-100% (ILUO format).</p>
            </div>
            <div class="filter-bar">
                <button class="btn-manage" onclick="openManageModal()">
                    <span>⚙️</span> Kelola Item Mesin
                </button>
                <form id="filterForm" method="GET" style="display:flex; gap:12px;">
                    <select name="factory" class="filter-select" onchange="document.getElementById('filterForm').submit()">
                        @foreach($factories as $f)
                            <option value="{{ $f->name }}" {{ $factory == $f->name ? 'selected' : '' }}>
                                {{ $f->short_label ?? $f->name }}
                            </option>
                        @endforeach
                    </select>
                    <select name="shift" class="filter-select" onchange="document.getElementById('filterForm').submit()">
                        <option value="all" {{ $shift == 'all' ? 'selected' : '' }}>Semua Shift</option>
                        <option value="A" {{ $shift == 'A' ? 'selected' : '' }}>Shift A</option>
                        <option value="B" {{ $shift == 'B' ? 'selected' : '' }}>Shift B</option>
                        <option value="NS" {{ $shift == 'NS' ? 'selected' : '' }}>Non-Shift (NS)</option>
                    </select>
                </form>
            </div>
        </div>

        <div class="info-alert" style="display:flex; justify-content:space-between; align-items:center;">
            <div>
                <strong>PENTING:</strong> Sistem sekarang menggunakan mode <strong>Manual Save</strong>. Ketik angka (0, 25,
                50, 75, 100). Bulatan akan berubah secara langsung. Setelah selesai mengubah, klik tombol <strong>Simpan
                    Semua</strong> di sebelah kanan.
            </div>
            <button class="btn-primary" onclick="saveAllSkills()"
                style="background:#059669; font-size:14px; padding:10px 24px; box-shadow:0 4px 6px rgba(0,0,0,0.1);">
                💾 Simpan Semua
            </button>
        </div>

        <div class="matrix-card">
            @if(count($members) == 0)
                <div style="text-align:center;padding:40px;color:#888;">
                    Tidak ada data member aktif pada area/shift ini.
                </div>
            @elseif(count($machines) == 0)
                <div style="text-align:center;padding:40px;color:#888;">
                    Belum ada data mesin di factory ini.
                </div>
            @else
                <table class="m-table">
                    <thead>
                        <tr>
                            <th class="col-name" rowspan="2">Nama Operator</th>
                            <th class="col-shift" rowspan="2">Shift</th>
                            @foreach($machines as $mac)
                                @php
                                    $procs = $machineProcesses->get($mac, collect());
                                    $colspan = max(1, $procs->count());
                                @endphp
                                <th colspan="{{ $colspan }}">
                                    {{ $mac }}
                                </th>
                            @endforeach
                            <th rowspan="2">Level Prediksi</th>
                        </tr>
                        <tr>
                            @foreach($machines as $mac)
                                @php
                                    $procs = $machineProcesses->get($mac, collect());
                                @endphp
                                @if($procs->isEmpty())
                                    <th style="font-size:11px;color:#cfdfd4;">ALL</th>
                                @else
                                    @foreach($procs as $p)
                                        <th style="font-size:11px; font-weight:normal;">
                                            {{ $p->process_name }}
                                        </th>
                                    @endforeach
                                @endif
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($members as $m)
                            @php
                                $avgScore = 0;
                                $count = 0;
                            @endphp
                            <tr>
                                <td class="col-name">
                                    <div style="display:flex;align-items:center;gap:10px;">
                                        <div
                                            style="width:32px;height:32px;border-radius:16px;background:#e9ecef;display:flex;align-items:center;justify-content:center;font-weight:700;color:#666;font-size:12px;flex-shrink:0;overflow:hidden;">
                                            @if($m->photo)
                                                <img src="{{ $m->photo_url }}" style="width:100%;height:100%;object-fit:cover;">
                                            @else
                                                {{ strtoupper(substr($m->nama, 0, 2)) }}
                                            @endif
                                        </div>
                                        <div>
                                            <div style="font-weight:700;color:#333;">{{ $m->nama }}</div>
                                            <div style="font-size:11px;color:#666;">{{ $m->jabatan }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="col-shift" style="color:#2E7D32;font-weight:700;">{{ $m->shift }}</td>

                                @foreach($machines as $mac)
                                    @php
                                        $procs = $machineProcesses->get($mac, collect());
                                    @endphp
                                    @if($procs->isEmpty())
                                        @php
                                            $pct = isset($skills[$m->id][$mac]['-']) ? $skills[$m->id][$mac]['-']->skill_pct : 0;
                                            $circleClass = 'skill-0';
                                            if ($pct >= 100)
                                                $circleClass = 'skill-100';
                                            elseif ($pct >= 75)
                                                $circleClass = 'skill-75';
                                            elseif ($pct >= 50)
                                                $circleClass = 'skill-50';
                                            elseif ($pct > 0)
                                                $circleClass = 'skill-25';

                                            $avgScore += $pct;
                                            $count++;
                                        @endphp
                                        <td>
                                            <div class="skill-input-wrap">
                                                <div class="skill-circle {{ $circleClass }}"
                                                    id="circle-{{ $m->id }}-{{ Str::slug($mac) }}-none"></div>
                                                <input type="number" class="skill-input" min="0" max="100" step="25" value="{{ $pct }}"
                                                    data-member-id="{{ $m->id }}" data-machine="{{ $mac }}" data-process=""
                                                    oninput="updateCircleVisual(this)">
                                            </div>
                                        </td>
                                    @else
                                        @foreach($procs as $p)
                                            @php
                                                $pct = isset($skills[$m->id][$mac][$p->process_name]) ? $skills[$m->id][$mac][$p->process_name]->skill_pct : 0;
                                                $circleClass = 'skill-0';
                                                if ($pct >= 100)
                                                    $circleClass = 'skill-100';
                                                elseif ($pct >= 75)
                                                    $circleClass = 'skill-75';
                                                elseif ($pct >= 50)
                                                    $circleClass = 'skill-50';
                                                elseif ($pct > 0)
                                                    $circleClass = 'skill-25';

                                                $avgScore += $pct;
                                                $count++;
                                            @endphp
                                            <td>
                                                <div class="skill-input-wrap">
                                                    <div class="skill-circle {{ $circleClass }}"
                                                        id="circle-{{ $m->id }}-{{ Str::slug($mac) }}-{{ $p->id }}"></div>
                                                    <input type="number" class="skill-input" min="0" max="100" step="25" value="{{ $pct }}"
                                                        data-member-id="{{ $m->id }}" data-machine="{{ $mac }}"
                                                        data-process="{{ $p->process_name }}" oninput="updateCircleVisual(this)">
                                                </div>
                                            </td>
                                        @endforeach
                                    @endif
                                @endforeach

                                @php
                                    $finalAvg = $count > 0 ? round($avgScore / $count) : 0;
                                    $levelLabel = 'Baru';
                                    $levelClass = 'newbie';
                                    if ($finalAvg >= 75) {
                                        $levelLabel = 'Master';
                                        $levelClass = '';
                                    } elseif ($finalAvg >= 40) {
                                        $levelLabel = 'Berkembang';
                                        $levelClass = 'training';
                                    }
                                @endphp
                                <td>
                                    <span class="chip-level {{ $levelClass }}" id="level-{{ $m->id }}">{{ $levelLabel }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    <div class="saving-indicator" id="globalToast">
        <div class="saving-spinner" id="toastSpinner"></div>
        <span id="toastMsg">Menyimpan...</span>
    </div>

    <!-- Modal Manage Items -->
    <div class="modal-overlay" id="manageModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Kelola Item Mesin</h3>
                <button class="modal-close" onclick="closeManageModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Pilih Mesin</label>
                    <select id="modalMachineSelect" class="form-control" onchange="loadMachineItems()">
                        <option value="" disabled selected>-- Pilih Mesin --</option>
                        @foreach($machines as $mac)
                            <option value="{{ $mac }}">{{ $mac }}</option>
                        @endforeach
                    </select>
                </div>

                <hr style="border:0; border-top:1px solid #eee; margin:20px 0;">

                <div id="itemManagementSection" style="display:none;">
                    <div class="form-group">
                        <label>Tambah Item Baru</label>
                        <div style="display:flex; gap:10px;">
                            <input type="text" id="newItemInput" class="form-control" placeholder="Contoh: DOORTRIM D74">
                            <button class="btn-primary" onclick="submitNewItem()">Tambah</button>
                        </div>
                    </div>

                    <label>Daftar Item / Proses</label>
                    <div class="item-list" id="modalItemList">
                        <!-- Populated via JS -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const factory = {!! json_encode($factory) !!};
        const csrfToken = "{{ csrf_token() }}";
        const machineProcs = {!! json_encode($machineProcesses) !!};

        function openManageModal() {
            document.getElementById('manageModal').classList.add('active');
        }
        function closeManageModal() {
            document.getElementById('manageModal').classList.remove('active');
        }

        function loadMachineItems() {
            const mac = document.getElementById('modalMachineSelect').value;
            const section = document.getElementById('itemManagementSection');
            const list = document.getElementById('modalItemList');

            if (!mac) {
                section.style.display = 'none';
                return;
            }

            section.style.display = 'block';
            list.innerHTML = '';

            const items = machineProcs[mac] || [];
            if (items.length === 0) {
                list.innerHTML = '<div style="padding:16px;text-align:center;color:#888;">Belum ada item khusus. (Default: ALL)</div>';
            } else {
                items.forEach(p => {
                    const safeName = p.process_name.replace(/'/g, "\\'");
                    list.innerHTML += `
                            <div class="item-row">
                                <span>${p.process_name}</span>
                                <button class="btn-danger-sm" onclick="delProc(${p.id}, '${safeName}')">Hapus</button>
                            </div>
                        `;
                });
            }
        }

        async function submitNewItem() {
            const mac = document.getElementById('modalMachineSelect').value;
            const input = document.getElementById('newItemInput');
            const procName = input.value.trim();

            if (!mac) return alert("Pilih mesin terlebih dahulu.");
            if (!procName) return alert("Nama item tidak boleh kosong.");

            showToast('Menambahkan item...', false, true);
            try {
                const res = await fetch('/api/skills/processes/add', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: JSON.stringify({ factory, machine_name: mac, process_name: procName })
                });
                const data = await res.json();
                if (!data.ok) throw new Error('Failed');
                window.location.reload(); // Reload to refresh grid
            } catch (e) {
                showToast('Gagal menambahkan item!', true, false);
            }
        }
        const toast = document.getElementById('globalToast');
        const toastMsg = document.getElementById('toastMsg');
        const toastSpinner = document.getElementById('toastSpinner');
        let toastTimeout;

        function showToast(msg, isError = false, isSaving = false) {
            clearTimeout(toastTimeout);
            toastMsg.textContent = msg;
            toastSpinner.style.display = isSaving ? 'block' : 'none';
            toast.style.backgroundColor = isError ? '#dc2626' : (isSaving ? '#333' : '#16a34a');
            toast.classList.add('show');

            if (!isSaving) {
                toastTimeout = setTimeout(() => {
                    toast.classList.remove('show');
                }, 3000);
            }
        }

        function updateCircleVisual(inputEl) {
            let val = parseInt(inputEl.value) || 0;
            if (val > 100) val = 100;

            let cClass = 'skill-0';
            if (val >= 100) cClass = 'skill-100';
            else if (val >= 75) cClass = 'skill-75';
            else if (val >= 50) cClass = 'skill-50';
            else if (val >= 25) cClass = 'skill-25';

            const circle = inputEl.previousElementSibling;
            circle.className = `skill-circle ${cClass}`;
        }

        async function saveAllSkills() {
            const inputs = document.querySelectorAll('.skill-input');
            let payload = [];

            inputs.forEach(input => {
                let val = parseInt(input.value);
                if (isNaN(val)) return;

                payload.push({
                    member_id: input.getAttribute('data-member-id'),
                    machine_name: input.getAttribute('data-machine'),
                    process_name: input.getAttribute('data-process') || '',
                    factory: factory,
                    skill_pct: val
                });
            });

            if (payload.length === 0) {
                alert("Tidak ada data untuk disimpan.");
                return;
            }

            showToast('Menyimpan Semua Data...', false, true);

            try {
                const res = await fetch('/api/skills/save-batch', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: JSON.stringify({ skills: payload })
                });
                const data = await res.json();
                if (!data.ok) {
                    alert('Gagal menyimpan: ' + (data.error || 'Unknown Error'));
                    showToast('Gagal menyimpan data!', true, false);
                    return;
                }

                showToast('Semua data berhasil disimpan!', false, false);
                setTimeout(() => { window.location.reload(); }, 1000);
            } catch (e) {
                alert('Gagal menyimpan data: ' + e.message);
                showToast('Gagal menyimpan data!', true, false);
            }
        }

        async function delProc(id, name) {
            if (!confirm(`Hapus item "${name}"? Semua data persentase yang sudah diisi untuk item ini akan ikut terhapus.`)) return;

            showToast('Menghapus...', false, true);
            try {
                const res = await fetch('/api/skills/processes/delete', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: JSON.stringify({ id })
                });
                const data = await res.json();
                if (!data.ok) {
                    alert('Gagal: ' + (data.error || 'Unknown Error'));
                    return;
                }
                window.location.reload();
            } catch (e) {
                alert('Gagal menghapus item: ' + e.message);
            }
        }
    </script>
@endsection