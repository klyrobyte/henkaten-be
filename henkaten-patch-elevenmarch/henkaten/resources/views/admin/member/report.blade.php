@extends('layouts.admin')
@section('title', 'Rekap Absen - Member Management')

@section('content')

<div class="app-header">
    <a href="{{ route('admin.members.index') }}" class="h-back">←</a>
    <div class="h-title">REKAP ABSEN</div>
    <div class="h-badge">{{ \Carbon\Carbon::parse($tanggal)->format('d/m') }}</div>
</div>

<nav class="bottom-nav">
    <button class="nav-btn" onclick="window.location='{{ route('admin.members.index') }}'">
        <span class="ni">👥</span>Member
    </button>
    <button class="nav-btn" onclick="window.location='{{ route('admin.absence.index') }}'">
        <span class="ni">✅</span>Absen
    </button>
    <button class="nav-btn active">
        <span class="ni">📊</span>Rekap
    </button>
    <button class="nav-btn" onclick="window.location='{{ route('admin.members.index') }}'">
        <span class="ni">📥</span>Import
    </button>
</nav>

<div class="main">

    {{-- Date nav — menggantikan changeReportDate() JS --}}
    <div class="report-date-nav">
        <a href="{{ route('admin.absence.report', ['tanggal' => \Carbon\Carbon::parse($tanggal)->subDay()->toDateString()]) }}"
           class="rdn-btn">←</a>
        <div class="rdn-date">
            {{ \Carbon\Carbon::parse($tanggal)->locale('id')->isoFormat('ddd, D MMM YYYY') }}
        </div>
        <a href="{{ route('admin.absence.report', ['tanggal' => \Carbon\Carbon::parse($tanggal)->addDay()->toDateString()]) }}"
           class="rdn-btn">→</a>
    </div>

    <div style="display:flex;gap:8px;margin-bottom:14px">
        <a href="{{ route('admin.absence.report', ['tanggal' => today()->toDateString()]) }}"
           class="btn btn-sm btn-primary" style="flex:1">🔄 Hari Ini</a>
        <a href="{{ route('admin.absence.export', ['tanggal' => $tanggal]) }}"
           class="btn btn-sm btn-navy">📤 Export CSV</a>
    </div>

    {{-- Report cards per factory + shift — menggantikan generateReport() JS --}}
    @forelse($reports as $r)
        @php
            $dotColors = [
                'Cuti'   => '#1F3C88',
                'Sakit'  => '#f39c12',
                'Ijin'   => '#e74c3c',
                'Mangkir'=> '#8e44ad',
                'Tugas'  => '#2ecc71',
            ];
        @endphp
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
                        <div class="rbar-val" style="color:var(--navy)">{{ $r['members']->count() }}</div>
                        <div class="rbar-lbl">Total</div>
                    </div>
                    <div class="rbar-item">
                        <div class="rbar-val" style="color:var(--green)">{{ $r['hadirList']->count() }}</div>
                        <div class="rbar-lbl">Hadir</div>
                    </div>
                    <div class="rbar-item">
                        <div class="rbar-val" style="color:var(--red)">{{ $r['absenList']->count() }}</div>
                        <div class="rbar-lbl">Absen</div>
                    </div>
                </div>

                {{-- Progress bar --}}
                <div class="attendance-pct-bar">
                    <div class="apb-fill" style="width:{{ $r['pct'] }}%"></div>
                </div>
                <div style="font-size:11px;color:#888;margin-bottom:8px">
                    Kehadiran: <strong style="color:var(--green)">{{ $r['pct'] }}%</strong>
                </div>

                {{-- Reasons summary --}}
                @if(count($r['reasons']))
                    <div class="absent-detail" style="margin-bottom:10px">
                        @foreach($r['reasons'] as $reason => $count)
                            <div class="absent-detail-item">
                                <div class="adr-dot"
                                     style="background:{{ $dotColors[$reason] ?? '#888' }}"></div>
                                {{ $reason }}: <strong>{{ $count }} orang</strong>
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Detail ketidakhadiran --}}
                <div class="section-title">Detail Ketidakhadiran</div>
                @if($r['absenList']->isEmpty())
                    <div style="text-align:center;padding:12px;color:#aaa;font-size:12px">
                        ✅ Semua hadir!
                    </div>
                @else
                    @foreach($r['absenList'] as $m)
                        @php $rec = $r['records'][$m->id] ?? null; @endphp
                        <div style="display:flex;align-items:center;gap:8px;padding:6px 0;border-bottom:1px solid #f0f0f0">
                            <div style="width:32px;height:32px;border-radius:50%;overflow:hidden;background:#f0f0f0;display:flex;align-items:center;justify-content:center;font-size:14px;flex-shrink:0">
                                @if($m->photo_url)
                                    <img src="{{ $m->photo_url }}" style="width:100%;height:100%;object-fit:cover">
                                @else
                                    👤
                                @endif
                            </div>
                            <div style="flex:1;min-width:0">
                                <div style="font-size:12px;font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                                    {{ $m->nama }}
                                </div>
                                <div style="font-size:10px;color:#888">
                                    {{ $m->jabatan }}{{ $m->mesin ? ' — '.$m->mesin : '' }}
                                </div>
                            </div>
                            <span style="background:{{ $dotColors[$rec?->reason ?? ''] ?? '#888' }};color:#fff;padding:2px 8px;border-radius:6px;font-size:10px;font-weight:700;flex-shrink:0">
                                {{ $rec?->reason ?? '-' }}
                            </span>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    @empty
        <div class="empty-state">
            <div class="ei">📊</div>
            <div class="et">Belum ada data member.<br>
                <a href="{{ route('admin.members.index') }}" style="color:var(--green)">Import member dulu</a>
            </div>
        </div>
    @endforelse

</div>{{-- /main --}}
@endsection
