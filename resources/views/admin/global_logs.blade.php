@extends('layouts.admin')
@section('title', 'Global Logs')

@push('styles')
<style>
    .gl-wrap { padding: 16px 12px 100px; max-width: 1400px; margin: 0 auto; }

    .gl-header {
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
        border-radius: 16px;
        padding: 20px 24px;
        margin-bottom: 20px;
        color: #fff;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: 0 4px 20px rgba(15, 52, 96, .4);
    }
    .gl-header-icon {
        width: 50px; height: 50px; border-radius: 14px;
        background: linear-gradient(135deg, #e94560, #c23152);
        display: flex; align-items: center; justify-content: center;
        font-size: 24px; flex-shrink: 0;
        box-shadow: 0 3px 10px rgba(233, 69, 96, .4);
    }
    .gl-header-title { font-family:'Roboto Condensed',sans-serif; font-size:22px; font-weight:900; letter-spacing:.5px; }
    .gl-header-sub { font-size:12px; opacity:.7; margin-top:2px; }
    .gl-badge {
        margin-left: auto;
        background: rgba(233,69,96,.25);
        border: 1px solid rgba(233,69,96,.5);
        border-radius: 20px;
        padding: 4px 14px;
        font-family: 'Roboto Condensed', sans-serif;
        font-size: 11px;
        font-weight: 800;
        color: #e94560;
        letter-spacing: .5px;
    }

    .gl-filters {
        background: #fff;
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 16px;
        box-shadow: 0 2px 8px rgba(0,0,0,.06);
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        align-items: flex-end;
    }
    .gl-filter-group { display: flex; flex-direction: column; gap: 4px; }
    .gl-filter-group label { font-family:'Roboto Condensed',sans-serif; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:.5px; color:#666; }
    .gl-filter-group input, .gl-filter-group select {
        padding: 7px 10px; border: 1.5px solid #e0e0e0; border-radius: 8px;
        font-family:'Roboto Condensed',sans-serif; font-size:12px; font-weight:600;
        min-width: 130px; background: #f8f9fa;
    }
    .gl-filter-group input:focus, .gl-filter-group select:focus { outline: none; border-color: #0f3460; }

    .btn-gl-filter {
        padding: 8px 20px; background: #0f3460; color: #fff; border: none;
        border-radius: 8px; font-family:'Roboto Condensed',sans-serif;
        font-weight:800; font-size:12px; cursor:pointer; transition:all .2s;
    }
    .btn-gl-filter:hover { background: #16213e; transform: translateY(-1px); }

    .btn-gl-export {
        padding: 8px 18px; background: #2e7d32; color: #fff; border: none;
        border-radius: 8px; font-family:'Roboto Condensed',sans-serif;
        font-weight:800; font-size:12px; cursor:pointer; transition:all .2s;
        text-decoration: none; display: inline-flex; align-items: center; gap: 6px;
    }
    .btn-gl-export:hover { background: #1b5e20; color:#fff; transform:translateY(-1px); }

    .gl-table-wrap {
        background: #fff; border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,.06);
        overflow: hidden;
    }
    .gl-table { width:100%; border-collapse: collapse; font-family:'Roboto Condensed',sans-serif; }
    .gl-table th {
        background: #0f3460; color:#fff; padding: 10px 12px;
        font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:.5px;
        text-align: left; white-space: nowrap;
    }
    .gl-table td {
        padding: 8px 12px; border-bottom: 1px solid #f0f0f0;
        font-size: 12px; color: #333; vertical-align: middle;
    }
    .gl-table tr:hover td { background: #f7f9ff; }

    .action-pill {
        display: inline-block; padding: 2px 8px; border-radius: 12px;
        font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing:.3px;
    }
    .ap-get    { background:#e8f5e9; color:#2e7d32; }
    .ap-post   { background:#e3f2fd; color:#1565c0; }
    .ap-patch  { background:#fff8e1; color:#f57f17; }
    .ap-delete { background:#fdeaea; color:#c62828; }
    .ap-ui     { background:#f3e5f5; color:#6a1b9a; }
    .ap-other  { background:#f5f5f5; color:#555; }

    .gl-empty { padding: 40px; text-align:center; color:#aaa; font-size:13px; }
    .gl-pagination { padding: 16px; display:flex; justify-content:center; }
    .gl-pagination .page-link {
        padding: 6px 12px; border: 1.5px solid #e0e0e0; border-radius: 6px;
        font-family:'Roboto Condensed',sans-serif; font-size:12px; font-weight:700;
        color:#0f3460; text-decoration:none; margin:0 2px; transition:all .15s;
    }
    .gl-pagination .page-link:hover, .gl-pagination .page-link.active { background:#0f3460; color:#fff; border-color:#0f3460; }
</style>
@endpush

@section('content')
<div class="gl-wrap">

    {{-- Header --}}
    <div class="gl-header">
        <div class="gl-header-icon">🔍</div>
        <div>
            <div class="gl-header-title">Global Activity Logs</div>
            <div class="gl-header-sub">Audit trail terenkripsi — hanya Super Admin yang dapat melihat halaman ini</div>
        </div>
        <div class="gl-badge">⚡ SUPER ADMIN ONLY</div>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.global-logs.index') }}" class="gl-filters" id="glFilterForm">
        <div class="gl-filter-group">
            <label>Dari Tanggal</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}">
        </div>
        <div class="gl-filter-group">
            <label>Sampai Tanggal</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}">
        </div>
        <div class="gl-filter-group">
            <label>Aksi</label>
            <select name="action">
                <option value="">-- Semua --</option>
                @foreach(['GET','POST','PATCH','DELETE','UI'] as $act)
                    <option value="{{ $act }}" @selected(request('action') === $act)>{{ $act }}</option>
                @endforeach
            </select>
        </div>
        <div class="gl-filter-group">
            <label>Factory</label>
            <select name="factory">
                <option value="">-- Semua --</option>
                @foreach($factories as $fac)
                    <option value="{{ $fac->name }}" @selected(request('factory') === $fac->name)>{{ $fac->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="gl-filter-group">
            <label>Role</label>
            <select name="role">
                <option value="">-- Semua --</option>
                @foreach(['superadmin','admin','gl','tl','pengawas','tv'] as $r)
                    <option value="{{ $r }}" @selected(request('role') === $r)>{{ $r }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn-gl-filter" id="glFilterBtn">🔎 Filter</button>
        <a href="{{ route('admin.global-logs.index') }}" class="btn-gl-filter" style="background:#607d8b;text-decoration:none;">✕ Reset</a>
        <a href="{{ route('admin.global-logs.export', request()->query()) }}"
           class="btn-gl-export" id="glExportBtn">⬇ Export CSV</a>
    </form>

    {{-- Table --}}
    <div class="gl-table-wrap">
        @if($logs->isEmpty())
            <div class="gl-empty">📋 Tidak ada log yang cocok dengan filter yang dipilih.</div>
        @else
            <table class="gl-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Waktu</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Aksi</th>
                        <th>Target</th>
                        <th>Factory</th>
                        <th>IP</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                        @php
                            $detail = $log->detail ? json_decode($log->detail, true) : [];
                            $statusCode = $detail['status'] ?? '-';
                            $act = strtolower($log->action ?? '');
                            $pillCls = match($act) {
                                'get'    => 'ap-get',
                                'post'   => 'ap-post',
                                'patch'  => 'ap-patch',
                                'delete' => 'ap-delete',
                                'ui'     => 'ap-ui',
                                default  => 'ap-other',
                            };
                        @endphp
                        <tr>
                            <td style="color:#aaa;font-size:10px">{{ $log->id }}</td>
                            <td style="white-space:nowrap;font-weight:700">{{ $log->created_at->format('d/m H:i:s') }}</td>
                            <td style="font-weight:800">{{ \App\Models\GlobalLog::safeDecrypt($log->username_enc) }}</td>
                            <td>{{ $log->role ?? '-' }}</td>
                            <td><span class="action-pill {{ $pillCls }}">{{ $log->action }}</span></td>
                            <td style="font-family:monospace;font-size:11px;max-width:220px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="{{ $log->target }}">{{ $log->target }}</td>
                            <td>{{ $log->factory ?? '-' }}</td>
                            <td style="font-family:monospace;font-size:11px">{{ \App\Models\GlobalLog::safeDecrypt($log->ip_enc) }}</td>
                            <td style="font-weight:700;color:{{ str_starts_with((string)$statusCode,'2') ? '#2e7d32' : (str_starts_with((string)$statusCode,'4') ? '#c62828' : '#555') }}">
                                {{ $statusCode }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Pagination --}}
            <div class="gl-pagination">
                @if($logs->onFirstPage())
                    <span class="page-link" style="opacity:.4">‹ Prev</span>
                @else
                    <a class="page-link" href="{{ $logs->previousPageUrl() }}">‹ Prev</a>
                @endif

                <span class="page-link active">{{ $logs->currentPage() }} / {{ $logs->lastPage() }}</span>

                @if($logs->hasMorePages())
                    <a class="page-link" href="{{ $logs->nextPageUrl() }}">Next ›</a>
                @else
                    <span class="page-link" style="opacity:.4">Next ›</span>
                @endif
            </div>
        @endif
    </div>

    <p style="text-align:center;color:#aaa;font-size:11px;margin-top:12px">
        Total: {{ number_format($logs->total()) }} record — Halaman {{ $logs->currentPage() }} dari {{ $logs->lastPage() }}
    </p>
</div>
@endsection
