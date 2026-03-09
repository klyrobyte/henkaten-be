@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')

{{-- ── Tanggal + Shift controls ── --}}
<div class="date-bar">
    <div class="date-label">📅</div>
    <input type="date" id="tanggalHari" value="{{ $tanggal }}" onchange="onDateChange(this.value)">
    <button class="factory-select-btn" onclick="showFactoryPicker()">🏭</button>
</div>

<div class="shift-toggle-bar">
    <button class="shift-toggle-btn {{ $shift === 'A' ? 'active' : '' }}" onclick="switchShift('A')">SHIFT A</button>
    <button class="shift-toggle-btn {{ $shift === 'B' ? 'active' : '' }}" onclick="switchShift('B')">SHIFT B</button>
</div>

{{-- ── 4M Legend ── --}}
<div class="legend-4m">
    <div class="legend-4m-item"><div class="l4m-dot" style="background:var(--red)"></div>Man (Absen)</div>
    <div class="legend-4m-item"><div class="l4m-dot" style="background:var(--navy)"></div>Machine</div>
    <div class="legend-4m-item"><div class="l4m-dot" style="background:var(--yellow)"></div>Material</div>
    <div class="legend-4m-item"><div class="l4m-dot" style="background:var(--green-light)"></div>Method</div>
</div>

{{-- ── Status Panel ── --}}
<div class="status-panel">
    <div class="status-panel-title">📊 Status Keseluruhan</div>

    {{-- Auto Refresh Bar --}}
    <div class="auto-refresh-bar">
        <div class="ar-left">
            <div class="ar-dot" id="arDot"></div>
            <span id="arLabel">Auto Refresh</span>
        </div>
        <span class="ar-countdown" id="arCountdown">30s</span>
        <button class="ar-toggle" id="arToggleBtn" onclick="toggleAutoRefresh()">⏸ Pause</button>
    </div>
    <div class="ar-last-updated" id="arLastUpdated">Belum diperbarui</div>

    {{-- Emot Cards --}}
    <div class="status-emot-row">
        <div class="emot-card {{ $statusLevel === 0 ? 'active-green' : '' }}"      id="ec-green">
            <span class="emot-icon">🟢</span><div class="emot-label">AMAN</div>
        </div>
        <div class="emot-card {{ $statusLevel === 1 ? 'active-yellowgreen' : '' }}" id="ec-yg">
            <span class="emot-icon">🟡</span><div class="emot-label">RINGAN</div>
        </div>
        <div class="emot-card {{ $statusLevel === 2 ? 'active-yellow' : '' }}"     id="ec-yellow">
            <span class="emot-icon">⚠️</span><div class="emot-label">KHUSUS</div>
        </div>
        <div class="emot-card {{ $statusLevel === 3 ? 'active-red' : '' }}"        id="ec-red">
            <span class="emot-icon">🔴</span><div class="emot-label">BAHAYA</div>
        </div>
    </div>

    @php
        $chipMap = ['chip-green','chip-yellowgreen','chip-yellow','chip-red'];
        $chipTxt = ['🟢 AMAN — Semua Normal','🟡 PERHATIAN RINGAN — Absen 1','⚠️ PERHATIAN KHUSUS — Absen 2–3','🔴 BAHAYA — Absen ≥4 / MC Problem'];
    @endphp
    <div class="status-chip {{ $chipMap[$statusLevel] }}" id="statusChip">
        {{ $chipTxt[$statusLevel] }}
    </div>

    {{-- Live Indicators --}}
    <div class="status-live-row">
        <div class="live-item">
            <div class="live-value" id="liveAbsen">{{ $absenceSummary?->total_absen ?? 0 }}</div>
            <div class="live-label">Absen MP</div>
        </div>
        <div class="live-item">
            <div class="live-value" id="liveMCProblem">{{ $machineSummary['problem'] }}</div>
            <div class="live-label">MC Problem</div>
        </div>
        <div class="live-item">
            <div class="live-value" id="liveLogOpen">{{ $openLogsCount }}</div>
            <div class="live-label">Log Open</div>
        </div>
    </div>
</div>

{{-- ── Report Summary ── --}}
<div class="section-title">Report Summary</div>
<div class="summary-grid">
    <div class="summary-card">
        <div class="s-val" id="sTotalMC">{{ $machineSummary['total'] }}</div>
        <div class="s-lbl">Total MC</div>
    </div>
    <div class="summary-card">
        <div class="s-val" id="sNormal">{{ $machineSummary['total'] - $machineSummary['problem'] }}</div>
        <div class="s-lbl">Normal</div>
    </div>
    <div class="summary-card" style="border-bottom-color:var(--red)">
        <div class="s-val" id="sMan" style="color:var(--red)">{{ $machineSummary['man'] }}</div>
        <div class="s-lbl">Man</div>
    </div>
    <div class="summary-card" style="border-bottom-color:var(--navy)">
        <div class="s-val" id="sMachine" style="color:var(--navy)">{{ $machineSummary['machine'] }}</div>
        <div class="s-lbl">Machine</div>
    </div>
    <div class="summary-card" style="border-bottom-color:var(--yellow)">
        <div class="s-val" id="sMaterial" style="color:var(--yellow)">{{ $machineSummary['material'] }}</div>
        <div class="s-lbl">Material</div>
    </div>
    <div class="summary-card" style="border-bottom-color:var(--green-light)">
        <div class="s-val" id="sMethod" style="color:var(--green-light)">{{ $machineSummary['method'] }}</div>
        <div class="s-lbl">Method</div>
    </div>
</div>

{{-- ── Diagram Kehadiran ── --}}
<div class="section-title">Diagram Kehadiran</div>
<div class="attendance-chart-wrap">
    <div class="chart-container">
        <canvas id="myChart"></canvas>
        <div class="chart-center">
            <div class="cv" id="centerValue">
                {{ $absenceSummary ? $absenceSummary->mp_hadir . '/' . $absenceSummary->total_member : '0/0' }}
            </div>
            <div class="cl">MP</div>
        </div>
    </div>
    <div class="legend-grid">
        <div class="legend-item"><span class="leg-dot" style="background:#729E3F"></span>MP Hadir</div>
        <div class="legend-item"><span class="leg-dot" style="background:#e74c3c"></span>MP Absen</div>
        <div class="legend-item"><span class="leg-dot" style="background:#1F3C88"></span>SPV Cuti</div>
        <div class="legend-item"><span class="leg-dot" style="background:#5dade2"></span>SPV Sakit</div>
        <div class="legend-item"><span class="leg-dot" style="background:#ff8000"></span>SPV Ijin</div>
        <div class="legend-item"><span class="leg-dot" style="background:#ff69b4"></span>OP Cuti</div>
        <div class="legend-item"><span class="leg-dot" style="background:#8e44ad"></span>OP Sakit</div>
        <div class="legend-item"><span class="leg-dot" style="background:#f1c40f"></span>OP Ijin</div>
    </div>
    <div style="width:100%">
        <div class="absen-bar">
            @php
                $pct = ($absenceSummary && $absenceSummary->total_member > 0)
                    ? round($absenceSummary->mp_hadir / $absenceSummary->total_member * 100, 1)
                    : 0;
            @endphp
            <div class="absen-fill" id="absenFill" style="width:{{ $pct }}%">{{ $pct }}%</div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
Chart.register(ChartDataLabels);

// ── Data awal dari server (SSR) ───────────────────────────────────────────
const initAbsence = @json($absenceSummary);
let dashChart = null;

// ── Render chart dari data ────────────────────────────────────────────────
function renderChart(data) {
    if (!data) return;
    const values = [
        data.mp_hadir, data.mp_absen,
        data.p_cuti,  data.p_sakit,  data.p_ijin,
        data.o_cuti,  data.o_sakit,  data.o_ijin,
    ];
    const total = values.reduce((a, b) => a + b, 0);
    if (!total) return;

    const pct = ((data.mp_hadir / total) * 100).toFixed(1);
    document.getElementById('centerValue').textContent = `${data.mp_hadir}/${total}`;
    document.getElementById('absenFill').style.width  = pct + '%';
    document.getElementById('absenFill').textContent  = pct + '%';

    if (dashChart) dashChart.destroy();
    dashChart = new Chart(document.getElementById('myChart'), {
        type: 'doughnut',
        data: {
            datasets: [{
                data: values,
                backgroundColor: ['#729E3F','#e74c3c','#1F3C88','#5dade2','#FF8F1F','#ff69b4','#8e44ad','#f1c40f'],
                borderWidth: 0,
            }],
        },
        options: {
            cutout: '75%', responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false }, datalabels: { display: false } },
        },
    });
}

// ── Update status panel dari API ──────────────────────────────────────────
async function refreshStatus() {
    try {
        const d = document.getElementById('tanggalHari').value;
        const data = await api(
            `{{ route('admin.status') }}?tanggal=${d}&factory=${encodeURIComponent(CURRENT_FACTORY)}&shift=${CURRENT_SHIFT}`
        );

        // Update live indicators
        document.getElementById('liveAbsen').textContent     = data.total_absen;
        document.getElementById('liveMCProblem').textContent = data.problem_mc;
        document.getElementById('liveLogOpen').textContent   = data.open_logs;

        // Update summary grid
        document.getElementById('sTotalMC').textContent  = data.summary.total;
        document.getElementById('sNormal').textContent   = data.summary.total - data.summary.problem;
        document.getElementById('sMan').textContent      = data.summary.man;
        document.getElementById('sMachine').textContent  = data.summary.machine;
        document.getElementById('sMaterial').textContent = data.summary.material;
        document.getElementById('sMethod').textContent   = data.summary.method;

        // Update emot + chip
        const levels  = ['ec-green','ec-yg','ec-yellow','ec-red'];
        const classes = ['active-green','active-yellowgreen','active-yellow','active-red'];
        const chips   = ['chip-green','chip-yellowgreen','chip-yellow','chip-red'];
        const texts   = [
            '🟢 AMAN — Semua Normal','🟡 PERHATIAN RINGAN — Absen 1',
            '⚠️ PERHATIAN KHUSUS — Absen 2–3','🔴 BAHAYA — Absen ≥4 / MC Problem',
        ];
        levels.forEach((id, i) => {
            const el = document.getElementById(id);
            el.classList.remove(...classes);
            if (i === data.status_level) el.classList.add(classes[i]);
        });
        const chip = document.getElementById('statusChip');
        chip.className = `status-chip ${chips[data.status_level]}`;
        chip.textContent = texts[data.status_level];

        // Update chart jika ada data absen
        if (data.absence) renderChart(data.absence);

        // Timestamp
        document.getElementById('arLastUpdated').textContent = `Diperbarui: ${data.updated_at}`;
    } catch (e) {
        console.error('refreshStatus error:', e);
    }
}

// ── Auto Refresh (menggantikan setInterval JS lama) ───────────────────────
let _arInterval = null, _arCountdown = 30, _arPaused = false;

function startAutoRefresh() {
    if (_arInterval) clearInterval(_arInterval);
    _arCountdown = 30;
    _arInterval = setInterval(() => {
        if (_arPaused) return;
        _arCountdown--;
        document.getElementById('arCountdown').textContent = _arCountdown + 's';
        if (_arCountdown <= 0) {
            _arCountdown = 30;
            refreshStatus();
        }
    }, 1000);
}

function toggleAutoRefresh() {
    _arPaused = !_arPaused;
    const btn = document.getElementById('arToggleBtn');
    const dot = document.getElementById('arDot');
    if (_arPaused) {
        dot.classList.add('paused');
        btn.textContent = '▶ Resume';
        document.getElementById('arCountdown').textContent = '--';
    } else {
        _arCountdown = 30;
        dot.classList.remove('paused');
        btn.textContent = '⏸ Pause';
    }
}

function onDateChange(val) {
    const url = new URL(window.location);
    url.searchParams.set('tanggal', val);
    window.location = url;
}

// ── Init ─────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    if (initAbsence) renderChart(initAbsence);
    startAutoRefresh();
});
</script>
@endpush
