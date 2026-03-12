@extends('layouts.admin')
@section('title', 'Rekap Absen')

@push('styles')
<style>
.report-date-nav {
    display: flex; align-items: center; justify-content: space-between;
    background: #fff; border-radius: 12px; padding: 10px 14px;
    box-shadow: 0 2px 8px rgba(0,0,0,.07); margin-bottom: 14px;
}
.rdn-btn {
    width: 34px; height: 34px; border-radius: 50%;
    background: var(--navy); color: #fff; border: none;
    font-size: 16px; font-weight: 900; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    text-decoration: none; transition: opacity .15s;
}
.rdn-btn:hover { opacity: .8; }
.rdn-date {
    font-family: 'Roboto Condensed', sans-serif;
    font-size: 13px; font-weight: 800; color: #333; text-align: center;
}
.report-card {
    background: #fff; border-radius: 14px;
    box-shadow: 0 2px 10px rgba(0,0,0,.07);
    overflow: hidden; margin-bottom: 16px;
}
.report-card-header {
    background: linear-gradient(135deg, var(--navy), #2c4a9e);
    padding: 12px 16px; color: #fff;
    display: flex; justify-content: space-between; align-items: center;
}
.rch-title { font-family: 'Orbitron', sans-serif; font-size: 12px; font-weight: 900; letter-spacing: .8px; }
.report-card-body { padding: 14px 16px; }
.report-bar { display: flex; gap: 8px; margin-bottom: 10px; }
.rbar-item {
    flex: 1; text-align: center; background: #f8fafb;
    border-radius: 10px; padding: 8px 4px;
}
.rbar-val { font-family: 'Orbitron', sans-serif; font-size: 20px; font-weight: 700; line-height: 1; }
.rbar-lbl { font-family: 'Roboto Condensed', sans-serif; font-size: 10px; color: #aaa; font-weight: 700; text-transform: uppercase; margin-top: 2px; }
.attendance-pct-bar {
    height: 8px; background: #eee; border-radius: 4px;
    overflow: hidden; margin-bottom: 6px;
}
.apb-fill { height: 100%; background: linear-gradient(90deg, var(--green-light), var(--green)); border-radius: 4px; transition: width .5s; }
.absent-detail { display: flex; flex-direction: column; gap: 4px; }
.absent-detail-item { display: flex; align-items: center; gap: 6px; font-size: 12px; color: #555; }
.adr-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
.export-bar { display: flex; gap: 8px; margin-bottom: 16px; }
.export-btn {
    flex: 1; display: flex; align-items: center; justify-content: center; gap: 6px;
    padding: 10px 12px; border-radius: 10px; border: none; cursor: pointer;
    font-family: 'Roboto Condensed', sans-serif; font-size: 12px; font-weight: 800;
    text-transform: uppercase; letter-spacing: .4px; text-decoration: none;
    transition: opacity .15s;
}
.export-btn:active { opacity: .8; }
.btn-export-csv  { background: linear-gradient(135deg, #2e7d32, #43a047); color: #fff; }
.btn-export-xlsx { background: linear-gradient(135deg, var(--navy), #2c4a9e); color: #fff; }
</style>
@endpush

@section('content')

<div style="padding: 0 0 110px;">

    <div class="section-title" style="margin-bottom:12px">📊 Rekap Absen</div>

    {{-- Date navigation --}}
    <div class="report-date-nav">
        <a href="{{ route('admin.absence.report', ['tanggal' => \Carbon\Carbon::parse($tanggal)->subDay()->toDateString(), 'factory' => $factory, 'shift' => $shift]) }}"
           class="rdn-btn">←</a>
        <div class="rdn-date">
            {{ \Carbon\Carbon::parse($tanggal)->locale('id')->isoFormat('ddd, D MMMM YYYY') }}
        </div>
        <a href="{{ route('admin.absence.report', ['tanggal' => \Carbon\Carbon::parse($tanggal)->addDay()->toDateString(), 'factory' => $factory, 'shift' => $shift]) }}"
           class="rdn-btn">→</a>
    </div>

    {{-- Quick nav hari ini --}}
    <div style="display:flex;gap:8px;margin-bottom:14px">
        <a href="{{ route('admin.absence.report', ['tanggal' => today()->toDateString(), 'factory' => $factory, 'shift' => $shift]) }}"
           style="flex:1;display:flex;align-items:center;justify-content:center;gap:5px;padding:9px 12px;background:var(--green);color:#fff;border-radius:10px;font-family:'Roboto Condensed',sans-serif;font-size:12px;font-weight:800;text-transform:uppercase;text-decoration:none;">
            🔄 Hari Ini
        </a>
    </div>

    {{-- Export buttons --}}
    <div class="export-bar">
        <a href="{{ route('admin.absence.export', ['tanggal' => $tanggal, 'factory' => $factory, 'shift' => $shift]) }}"
           class="export-btn btn-export-csv">
            📤 Export CSV
        </a>
        <a href="{{ route('admin.absence.export-excel', ['tanggal' => $tanggal, 'factory' => $factory, 'shift' => $shift]) }}"
           class="export-btn btn-export-xlsx">
            📊 Export Excel
        </a>
    </div>

    {{-- Report cards --}}
    @php
        $dotColors = [
            'Cuti'    => '#1F3C88',
            'Sakit'   => '#f39c12',
            'Ijin'    => '#e74c3c',
            'Mangkir' => '#8e44ad',
            'Tugas'   => '#2ecc71',
        ];
    @endphp

    @forelse($reports as $r)
    <div class="report-card">
        <div class="report-card-header">
            <div class="rch-title">{{ $r['factory'] }} — Shift {{ $r['shift'] }}</div>
            <div style="font-size:11px;opacity:.8">
                {{ \Carbon\Carbon::parse($tanggal)->locale('id')->isoFormat('D MMM YYYY') }}
            </div>
        </div>
        <div class="report-card-body">

            {{-- Summary bar --}}
            <div class="report-bar">
                <div class="rbar-item">
                    <div class="rbar-val" style="color:var(--navy)">{{ $r['total'] }}</div>
                    <div class="rbar-lbl">Total</div>
                </div>
                <div class="rbar-item">
                    <div class="rbar-val" style="color:var(--green)">{{ $r['hadir'] }}</div>
                    <div class="rbar-lbl">Hadir</div>
                </div>
                <div class="rbar-item">
                    <div class="rbar-val" style="color:var(--red)">{{ $r['absen'] }}</div>
                    <div class="rbar-lbl">Absen</div>
                </div>
            </div>

            {{-- Progress bar --}}
            <div class="attendance-pct-bar">
                <div class="apb-fill" style="width:{{ $r['pct'] }}%"></div>
            </div>
            <div style="font-size:11px;color:#888;margin-bottom:10px">
                Kehadiran: <strong style="color:var(--green)">{{ $r['pct'] }}%</strong>
            </div>

            {{-- Reasons summary --}}
            @if(count($r['reasons']))
                <div class="absent-detail" style="margin-bottom:12px">
                    @foreach($r['reasons'] as $reason => $cnt)
                        <div class="absent-detail-item">
                            <div class="adr-dot" style="background:{{ $dotColors[$reason] ?? '#888' }}"></div>
                            {{ $reason }}: <strong>{{ $cnt }} orang</strong>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Detail ketidakhadiran --}}
            <div class="section-title" style="margin-bottom:8px">Detail Ketidakhadiran</div>
            @if(empty($r['absen_list']) || count($r['absen_list']) === 0)
                <div style="text-align:center;padding:12px;color:#aaa;font-size:12px">
                    ✅ Semua hadir!
                </div>
            @else
                @foreach($r['absen_list'] as $m)
                @php $m = (object) $m; @endphp
                <div style="display:flex;align-items:center;gap:8px;padding:8px 0;border-bottom:1px solid #f0f0f0">
                    <div style="width:34px;height:34px;border-radius:50%;overflow:hidden;background:linear-gradient(135deg,#ef9a9a,#c0392b);display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:900;color:#fff;flex-shrink:0;font-family:'Roboto Condensed',sans-serif;">
                        {{ mb_strtoupper(mb_substr($m->nama ?? '?', 0, 1)) }}
                    </div>
                    <div style="flex:1;min-width:0">
                        <div style="font-size:12px;font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                            {{ $m->nama }}
                        </div>
                        <div style="font-size:10px;color:#888">
                            {{ $m->jabatan }}{{ $m->mesin ? ' — '.$m->mesin : '' }}
                        </div>
                    </div>
                    <span style="background:{{ $dotColors[$m->reason ?? ''] ?? '#888' }};color:#fff;padding:3px 9px;border-radius:6px;font-size:10px;font-weight:700;flex-shrink:0;font-family:'Roboto Condensed',sans-serif;">
                        {{ $m->reason ?? '-' }}
                    </span>
                </div>
                @endforeach
            @endif
        </div>
    </div>
    @empty
    <div style="text-align:center;padding:40px 20px;color:#aaa">
        <div style="font-size:40px;margin-bottom:10px">📊</div>
        <div style="font-size:13px">Belum ada data member untuk factory & shift ini.<br>
            <a href="{{ route('admin.members.index') }}" style="color:var(--green)">Import member dulu →</a>
        </div>
    </div>
    @endforelse

</div>
@endsection