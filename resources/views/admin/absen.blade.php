@extends('layouts.admin')
@section('title', 'Input Absen')

@section('content')

    {{-- ── Filter bar ── --}}
    <form method="GET" action="{{ route('admin.absence.index') }}" id="filterForm">
        <div class="card" style="padding:12px 16px;margin-bottom:10px">
            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
                <div class="field-group" style="flex:1;min-width:120px">
                    <label>Tanggal</label>
                    <input type="date" name="tanggal" value="{{ $tanggal }}"
                        onchange="document.getElementById('filterForm').submit()">
                </div>
                <div class="field-group" style="flex:1;min-width:100px">
                    <label>Factory</label>
                    <select name="factory" onchange="document.getElementById('filterForm').submit()" {{ count($factories) === 1 ? 'disabled' : '' }}>
                        @foreach($factories as $f)
                            <option value="{{ $f->name }}" {{ ($factory === $f->name || count($factories) === 1) ? 'selected' : '' }}>{{ $f->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field-group" style="flex:1;min-width:90px">
                    <label>Shift</label>
                    <select name="shift" onchange="document.getElementById('filterForm').submit()">
                        @php 
                            $user = auth()->user();
                            $userRole = $user->role; 
                            $userShift = $user->shift; 
                        @endphp
                        @if($user->isSuperAdmin() || $userRole === 'admin' || !$userShift || $userShift === 'A')
                        <option value="A" {{ $shift === 'A' ? 'selected' : '' }}>Shift A</option>
                        @endif
                        @if($user->isSuperAdmin() || $userRole === 'admin' || !$userShift || $userShift === 'B')
                        <option value="B" {{ $shift === 'B' ? 'selected' : '' }}>Shift B</option>
                        @endif
                    </select>
                </div>
            </div>
        </div>
    </form>

    {{-- ── Summary counts ── --}}
    <div class="absen-summary-grid">
        <div class="absen-stat">
            <div class="as-val" id="asTotal" style="color:var(--navy)">{{ $members->count() }}</div>
            <div class="as-lbl">Total</div>
        </div>
        <div class="absen-stat">
            <div class="as-val" id="asHadir" style="color:var(--green)">{{ $hadir }}</div>
            <div class="as-lbl">Hadir</div>
        </div>
        <div class="absen-stat">
            <div class="as-val" id="asAbsen" style="color:var(--red)">{{ $absen }}</div>
            <div class="as-lbl">Tidak Hadir</div>
        </div>
    </div>

    {{-- ── Cari Member ── --}}
    <div style="margin-bottom:14px">
        <input type="text" id="searchMember" placeholder="🔍 Cari nama member..."
            style="width:100%;padding:10px 14px;border:1.5px solid #e0e0e0;border-radius:10px;font-family:inherit;font-size:14px;box-sizing:border-box;outline:none;transition:border-color 0.2s;"
            onfocus="this.style.borderColor='#2e7d32'" onblur="this.style.borderColor='#e0e0e0'"
            onkeyup="filterMembers()">
    </div>

    {{-- ── Action buttons ── --}}
    <div style="display:flex;gap:8px;margin-bottom:14px;flex-wrap:wrap">
        <button class="btn btn-sm btn-primary" onclick="broadcastAbsen()" style="flex:1">
            💾 Simpan &amp; Sync
        </button>
    </div>

    {{-- ── Member list ── --}}
    @if($members->isEmpty())
        <div class="empty-state">
            <div class="ei">👥</div>
            <div class="et">Tidak ada member untuk factory/shift ini.<br>
                <a href="{{ route('admin.members.index') }}" style="color:var(--green)">Import data dulu</a>
            </div>
        </div>
    @else
        <div class="absen-member-list" id="absenList">
            @foreach($members as $m)
                @php
                    $rec = $records[$m->id] ?? null;
                    $isAbsen = $rec && $rec->status === 'absen';
                    $reason = $rec?->reason ?? '';
                @endphp
                <div class="absen-member-row" id="arow-{{ $m->id }}">
                    <div class="amr-photo">
                                                @if($m->photo_url)
                                                    <img src="{{ $m->photo_url }}" alt="{{ $m->nama }}" loading="lazy">
                                                @else
                                                    👤
                                                @endif
                                            </div>
                    <div class="amr-info">
                        <div class="amr-name">{{ $m->nama }}</div>
                        <div class="amr-role">
                            {{ $m->jabatan }}{{ $m->mesin ? '  - ' . $m->mesin : '' }}
                        </div>
                    </div>
                    <div style="display:flex;align-items:center;gap:4px;flex-shrink:0;flex-wrap:wrap">
                        <button class="absen-btn hadir {{ !$isAbsen ? 'active' : '' }}"
                            onclick="setAbsenState({{ $m->id }}, 'hadir')">✓ Hadir</button>
                        <button class="absen-btn absen {{ $isAbsen ? 'active' : '' }}"
                            onclick="setAbsenState({{ $m->id }}, 'absen', document.getElementById('reason-{{ $m->id }}').value)">
                            ✗ Absen
                        </button>
                        <select class="reason-sel {{ $isAbsen ? 'show' : '' }}" id="reason-{{ $m->id }}"
                            onchange="if(absenState[{{ $m->id }}]?.status==='absen') setAbsenState({{ $m->id }},'absen',this.value)">
                            <option value="Cuti" {{ $reason === 'Cuti' ? 'selected' : '' }}>Cuti</option>
                            <option value="Sakit" {{ $reason === 'Sakit' ? 'selected' : '' }}>Sakit</option>
                            <option value="Ijin" {{ $reason === 'Ijin' ? 'selected' : '' }}>Ijin</option>
                            <option value="Alpha" {{ $reason === 'Alpha' ? 'selected' : '' }}>Alpha</option>
                        </select>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- REKAP  - navigasi tanggal + export CSV --}}
    {{-- dipindah dari page-report di member management --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div style="margin-top:24px">
        <div class="report-date-nav">
            <button class="rdn-btn" onclick="changeReportDate(-1)">←</button>
            <div class="rdn-date" id="reportDateLabel"> -</div>
            <button class="rdn-btn" onclick="changeReportDate(1)">→</button>
        </div>
        <div style="display:flex;gap:8px;margin-bottom:14px">
            <button class="btn btn-sm btn-primary" onclick="generateReport()" style="flex:1">🔄 Refresh</button>

        </div>
        <div id="reportContent">
            <div class="empty-state">
                <div class="ei">📊</div>
                <div class="et">Klik Refresh untuk muat laporan</div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        const CSRF = '{{ csrf_token() }}';
        const TANGGAL = '{{ $tanggal }}';
        const FACTORY = '{{ $factory }}';
        const SHIFT = '{{ $shift }}';

        let currentReportDate = TANGGAL;

        // ── State absen (init dari server) ───────────────────────────
        let absenState = {
            @foreach($members as $m)
                @php $rec = $records[$m->id] ?? null; @endphp
                {{ $m->id }}: {
                    status: '{{ $rec ? $rec->status : 'hadir' }}',
                    reason: '{{ $rec?->reason ?? '' }}',
                },
            @endforeach
    };

        // ── Client-Side Search ──
        function filterMembers() {
            const query = document.getElementById('searchMember').value.toLowerCase();
            const rows = document.querySelectorAll('.absen-member-row');
            let visibleCount = 0;
            
            rows.forEach(row => {
                const nameEl = row.querySelector('.amr-name');
                if (nameEl) {
                    const nameStr = nameEl.textContent.toLowerCase();
                    if (nameStr.includes(query)) {
                        row.style.display = 'flex';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                }
            });

            // (Opsional) Jika ingin menandai array kosong
            // Bisa tambahkan state empty-search jika visibleCount === 0
        }

        // ── Toggle hadir / absen ─────────────────────────────────────
        function setAbsenState(memberId, status, reason) {
            absenState[memberId] = {
                status,
                reason: status === 'absen' ? (reason || 'Cuti') : '',
            };

            const hadirBtn = document.querySelector(`#arow-${memberId} .absen-btn.hadir`);
            const absenBtn = document.querySelector(`#arow-${memberId} .absen-btn.absen`);
            const sel = document.getElementById('reason-' + memberId);

            if (status === 'hadir') {
                hadirBtn?.classList.add('active');
                absenBtn?.classList.remove('active');
                sel?.classList.remove('show');
            } else {
                absenBtn?.classList.add('active');
                hadirBtn?.classList.remove('active');
                sel?.classList.add('show');
                if (sel && reason) sel.value = reason;
            }

            updateAbsenCounts();
        }

        // ── Update counter ───────────────────────────────────────────
        function updateAbsenCounts() {
            const vals = Object.values(absenState);
            const hadir = vals.filter(v => v.status === 'hadir').length;
            const absen = vals.filter(v => v.status === 'absen').length;
            document.getElementById('asTotal').textContent = vals.length;
            document.getElementById('asHadir').textContent = hadir;
            document.getElementById('asAbsen').textContent = absen;
        }

        // ── Save ke server ────────────────────────────────────────────
        async function saveAbsenData() {
            if (!Object.keys(absenState).length) {
                showToast('Tidak ada data absen', 'error'); return;
            }
            showLoading('Menyimpan data absen...');
            try {
                const res = await fetch('/api/absence/save', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                    },
                    body: JSON.stringify({
                        tanggal: TANGGAL,
                        factory: FACTORY,
                        shift: SHIFT,
                        records: absenState,
                    }),
                });
                const data = await res.json();
                hideLoading();
                if (data.ok) {
                    const absenCount = Object.values(absenState).filter(v => v.status === 'absen').length;
                    const hadirCount = Object.values(absenState).length - absenCount;
                    showToast(`✅ Absen disimpan! Hadir: ${hadirCount} | Absen: ${absenCount}`, 'success');
                    updateAbsenCounts();
                } else {
                    showToast('Gagal menyimpan', 'error');
                }
            } catch (e) {
                hideLoading();
                showToast('Gagal menyimpan: ' + e.message, 'error');
            }
        }

        // ── Sync ke Board ─────────────────────────────────────────────
        async function broadcastAbsen() {
            await saveAbsenData();
            showToast('📡 Data disinkronkan ke Board!', 'success');
        }

        // ── Rekap: navigasi tanggal ───────────────────────────────────
        function changeReportDate(d) {
            const dt = new Date(currentReportDate + 'T12:00:00'); // Use 12:00:00 to avoid timezone shift
            dt.setDate(dt.getDate() + d);

            // Cek apakah melebih hari ini
            const today = new Date();
            today.setHours(23, 59, 59, 999);
            if (dt > today) {
                return; // tidak boleh lewati hari ini
            }

            const year = dt.getFullYear();
            const month = String(dt.getMonth() + 1).padStart(2, '0');
            const day = String(dt.getDate()).padStart(2, '0');
            currentReportDate = `${year}-${month}-${day}`;

            renderReportDateLabel();
            generateReport();
        }

        function renderReportDateLabel() {
            const dt = new Date(currentReportDate + 'T00:00:00');
            const lbl = dt.toLocaleDateString('id-ID', { weekday: 'short', day: 'numeric', month: 'short', year: 'numeric' });
            document.getElementById('reportDateLabel').textContent = lbl;
            document.getElementById('exportReportBtn').href =
                `/admin/absence/export?tanggal=${currentReportDate}`;
        }

        async function generateReport() {
            document.getElementById('reportContent').innerHTML =
                '<div style="text-align:center;padding:24px;color:#aaa">⏳ Memuat laporan...</div>';

            try {
                const res = await fetch(`/admin/absence/report?tanggal=${currentReportDate}`, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();
                renderReportContent(data);
            } catch (e) {
                document.getElementById('reportContent').innerHTML =
                    `<div class="empty-state"><div class="ei">⚠️</div><div class="et">${e.message}</div></div>`;
            }
        }

        function renderReportContent(reports) {
            if (!reports || !reports.length) {
                document.getElementById('reportContent').innerHTML =
                    '<div class="empty-state"><div class="ei">📊</div><div class="et">Belum ada data absen untuk tanggal ini.</div></div>';
                return;
            }

            const html = reports.map(r => {
                const pctColor = r.pct >= 90 ? 'var(--green)' : (r.pct >= 75 ? 'var(--orange)' : 'var(--red)');
                const absenRows = r.absen_list.map(m => {
                    const reasonColor = { Cuti: '#2196f3', Sakit: '#ff9800', Ijin: '#9c27b0', Alpha: 'var(--red)' }[m.reason] ?? '#888';
                    return `<div style="display:flex;align-items:center;justify-content:space-between;
                                    padding:7px 10px;background:#fff8f8;border-radius:8px;margin-bottom:4px;
                                    border-left:3px solid ${reasonColor}">
                    <div>
                        <div style="font-size:13px;font-weight:500">${m.nama}</div>
                        <div style="font-size:11px;color:#aaa">${m.jabatan}${m.mesin ? '  - ' + m.mesin : ''}</div>
                    </div>
                    <span style="font-size:11px;font-weight:600;color:${reasonColor};
                                 background:${reasonColor}15;padding:2px 8px;border-radius:99px">
                        ${m.reason ?? '-'}
                    </span>
                </div>`;
                }).join('');

                const reasonTags = Object.entries(r.reasons ?? {}).map(([reason, count]) =>
                    `<span style="font-size:11px;padding:3px 10px;border-radius:99px;background:#f0f0f0;color:#555">
                    ${reason}: ${count}
                </span>`
                ).join('');

                return `<div class="card" style="margin-bottom:14px">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
                    <div>
                        <div style="font-size:14px;font-weight:700;color:var(--navy)">${r.factory}</div>
                        <div style="font-size:12px;color:#888">Shift ${r.shift}</div>
                    </div>
                    <div style="text-align:right">
                        <div style="font-size:22px;font-weight:800;color:${pctColor}">${r.pct}%</div>
                        <div style="font-size:11px;color:#aaa">Kehadiran</div>
                    </div>
                </div>
                <div style="background:#f0f0f0;border-radius:99px;height:6px;margin-bottom:12px">
                    <div style="background:${pctColor};width:${r.pct}%;height:6px;border-radius:99px"></div>
                </div>
                <div style="display:flex;gap:8px;margin-bottom:12px">
                    <div style="flex:1;text-align:center;padding:8px;background:#f9f9f9;border-radius:8px">
                        <div style="font-size:18px;font-weight:700;color:var(--navy)">${r.total}</div>
                        <div style="font-size:10px;color:#aaa">Total</div>
                    </div>
                    <div style="flex:1;text-align:center;padding:8px;background:#f0fff4;border-radius:8px">
                        <div style="font-size:18px;font-weight:700;color:var(--green)">${r.hadir}</div>
                        <div style="font-size:10px;color:#aaa">Hadir</div>
                    </div>
                    <div style="flex:1;text-align:center;padding:8px;background:#fff0f0;border-radius:8px">
                        <div style="font-size:18px;font-weight:700;color:var(--red)">${r.absen}</div>
                        <div style="font-size:10px;color:#aaa">Absen</div>
                    </div>
                </div>
                ${absenRows || '<div style="text-align:center;padding:10px;color:#aaa;font-size:12px">🎉 Semua hadir!</div>'}
                ${reasonTags ? `<div style="display:flex;gap:6px;flex-wrap:wrap;margin-top:8px">${reasonTags}</div>` : ''}
            </div>`;
            }).join('');

            document.getElementById('reportContent').innerHTML = html;
        }

        // ── Init ─────────────────────────────────────────────────────
        document.addEventListener('DOMContentLoaded', () => {
            updateAbsenCounts();
            renderReportDateLabel();
        });
    </script>
@endpush