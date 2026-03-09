@extends('layouts.admin')
@section('title', 'Laporan')

@section('content')

<div class="date-bar">
    <div class="date-label">📅</div>
    <input type="date" id="tanggalHari" value="{{ $tanggal }}"
           onchange="window.location.href=updateQS('tanggal',this.value)">
    <button class="factory-select-btn" onclick="showFactoryPicker()">🏭</button>
</div>
<div class="shift-toggle-bar">
    <button class="shift-toggle-btn {{ $shift==='A'?'active':'' }}" onclick="switchShift('A')">SHIFT A</button>
    <button class="shift-toggle-btn {{ $shift==='B'?'active':'' }}" onclick="switchShift('B')">SHIFT B</button>
</div>

{{-- Header + export --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px">
    <div style="font-family:'Orbitron',sans-serif;font-size:13px;font-weight:700;color:var(--navy)">
        LAPORAN HARIAN
    </div>
    <div style="display:flex;gap:8px">
        <a href="{{ route('admin.reports.export', ['tanggal'=>$tanggal,'factory'=>$factory,'shift'=>$shift]) }}"
           style="padding:8px 14px;border-radius:8px;background:var(--green);color:#fff;text-decoration:none;font-size:12px;font-weight:700">
            📤 CSV
        </a>
        <a href="{{ route('admin.reports.backup', ['tanggal'=>$tanggal,'factory'=>$factory,'shift'=>$shift]) }}"
           style="padding:8px 14px;border-radius:8px;background:var(--navy);color:#fff;text-decoration:none;font-size:12px;font-weight:700">
            💾 JSON
        </a>
    </div>
</div>

{{-- Info card --}}
<div style="background:#fff;border-radius:var(--radius);box-shadow:var(--shadow);padding:14px;margin-bottom:12px">
    <div style="display:flex;gap:16px;flex-wrap:wrap;font-size:12px">
        <div><span style="color:#888">Factory:</span> <strong>{{ $factory }}</strong></div>
        <div><span style="color:#888">Shift:</span> <strong>{{ $shift }}</strong></div>
        <div><span style="color:#888">Tanggal:</span> <strong>{{ \Carbon\Carbon::parse($tanggal)->locale('id')->isoFormat('D MMMM YYYY') }}</strong></div>
    </div>
</div>

{{-- 4M Summary chart --}}
@php
    $manCount      = $logs->where('jenis','Man')->count();
    $machineCount  = $logs->where('jenis','Machine')->count();
    $materialCount = $logs->where('jenis','Material')->count();
    $methodCount   = $logs->where('jenis','Method')->count();
    $openCount     = $logs->where('status','open')->count();
    $closedCount   = $logs->where('status','closed')->count();
@endphp

<div style="background:#fff;border-radius:var(--radius);box-shadow:var(--shadow);padding:14px;margin-bottom:12px">
    <div class="section-title">4M Summary</div>
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:8px;margin-bottom:12px">
        <div style="text-align:center;padding:10px;background:rgba(231,76,60,.08);border-radius:10px">
            <div style="font-family:'Orbitron',sans-serif;font-size:20px;font-weight:700;color:var(--red)">{{ $manCount }}</div>
            <div style="font-size:10px;color:#888;text-transform:uppercase;font-weight:700">Man</div>
        </div>
        <div style="text-align:center;padding:10px;background:rgba(31,60,136,.08);border-radius:10px">
            <div style="font-family:'Orbitron',sans-serif;font-size:20px;font-weight:700;color:var(--navy)">{{ $machineCount }}</div>
            <div style="font-size:10px;color:#888;text-transform:uppercase;font-weight:700">Machine</div>
        </div>
        <div style="text-align:center;padding:10px;background:rgba(243,156,18,.08);border-radius:10px">
            <div style="font-family:'Orbitron',sans-serif;font-size:20px;font-weight:700;color:var(--yellow)">{{ $materialCount }}</div>
            <div style="font-size:10px;color:#888;text-transform:uppercase;font-weight:700">Material</div>
        </div>
        <div style="text-align:center;padding:10px;background:rgba(114,158,63,.08);border-radius:10px">
            <div style="font-family:'Orbitron',sans-serif;font-size:20px;font-weight:700;color:var(--green)">{{ $methodCount }}</div>
            <div style="font-size:10px;color:#888;text-transform:uppercase;font-weight:700">Method</div>
        </div>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
        <div style="text-align:center;padding:10px;background:rgba(231,76,60,.05);border-radius:10px;border:1.5px solid rgba(231,76,60,.2)">
            <div style="font-family:'Orbitron',sans-serif;font-size:18px;font-weight:700;color:var(--red)">{{ $openCount }}</div>
            <div style="font-size:10px;color:#888;font-weight:700">OPEN</div>
        </div>
        <div style="text-align:center;padding:10px;background:rgba(46,125,50,.05);border-radius:10px;border:1.5px solid rgba(46,125,50,.2)">
            <div style="font-family:'Orbitron',sans-serif;font-size:18px;font-weight:700;color:var(--green)">{{ $closedCount }}</div>
            <div style="font-size:10px;color:#888;font-weight:700">CLOSED</div>
        </div>
    </div>
</div>

{{-- Detail log table --}}
<div style="background:#fff;border-radius:var(--radius);box-shadow:var(--shadow);padding:14px;margin-bottom:80px">
    <div class="section-title">Detail Problem Log ({{ $logs->count() }})</div>
    @forelse($logs as $i => $log)
        <div style="border-bottom:1px solid #f0f0f0;padding:10px 0;{{ $loop->last?'border:none':'' }}">
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;flex-wrap:wrap">
                <span style="font-family:'Orbitron',sans-serif;font-size:11px;color:#aaa">#{{ str_pad($i+1,2,'0',STR_PAD_LEFT) }}</span>
                <span class="log-badge log-{{ strtolower($log->jenis) }}">{{ $log->jenis }}</span>
                <span style="font-size:11px;color:#888">{{ $log->waktu_mulai }}{{ $log->waktu_selesai ? '–'.$log->waktu_selesai : '' }}</span>
                @if($log->durasi)
                    <span style="font-size:11px;background:#f0f0f0;padding:2px 6px;border-radius:4px;color:#666">{{ $log->durasi }}</span>
                @endif
                <span style="margin-left:auto;font-size:10px;padding:2px 8px;border-radius:6px;font-weight:700;
                      background:{{ $log->status==='open' ? 'rgba(231,76,60,.1)' : 'rgba(46,125,50,.1)' }};
                      color:{{ $log->status==='open' ? 'var(--red)' : 'var(--green)' }}">
                    {{ strtoupper($log->status) }}
                </span>
            </div>
            <div style="font-size:11px;color:#888;margin-bottom:2px">📍 {{ $log->lokasi }}</div>
            <div style="font-size:13px;font-weight:500;color:var(--gray);margin-bottom:4px">{{ $log->deskripsi }}</div>
            @if($log->cause)
                <div style="font-size:11px;color:#888">🔍 {{ $log->cause }}</div>
            @endif
            @if($log->countermeasure)
                <div style="font-size:11px;color:#888">🔧 {{ $log->countermeasure }}</div>
            @endif
            @if($log->pic)
                <div style="font-size:11px;color:#aaa">👤 PIC: {{ $log->pic }}</div>
            @endif
        </div>
    @empty
        <div class="empty-state">
            <div class="ei">📊</div>
            <div class="et">Belum ada log untuk {{ $factory }}<br>Shift {{ $shift }} tanggal {{ $tanggal }}</div>
        </div>
    @endforelse
</div>

@endsection

@push('scripts')
<script>
function updateQS(key, val) {
    const u = new URL(window.location);
    u.searchParams.set(key, val);
    return u.toString();
}
</script>
@endpush
