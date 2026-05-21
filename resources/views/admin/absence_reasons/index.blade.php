@extends('layouts.admin')
@section('title', 'Absence Detail Management')

@push('styles')
    <style>
        .ar-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 4px;
        }

        .ar-item {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .07);
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 14px;
            transition: box-shadow .15s, transform .15s;
        }

        .ar-color-preview {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            flex-shrink: 0;
            border: 2px solid #eee;
        }

        .ar-item-info {
            flex: 1;
            min-width: 0;
        }

        .ar-item-name {
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 14px;
            font-weight: 900;
            color: #222;
        }

        .ar-item-actions {
            display: flex;
            gap: 6px;
            flex-shrink: 0;
        }

        .ar-act-btn {
            padding: 6px 12px;
            border-radius: 8px;
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 11px;
            font-weight: 800;
            cursor: pointer;
            border: 1.5px solid;
            transition: all .12s;
        }

        .ar-act-edit {
            border-color: #1f3c88;
            color: #1f3c88;
            background: #eef1fa;
        }

        .ar-act-edit:hover {
            background: #dde4f5;
        }

        .ar-act-del {
            border-color: #e74c3c;
            color: #e74c3c;
            background: #fdeaea;
        }

        .ar-act-del:hover {
            background: #fbd0d0;
        }

        .ar-add-btn {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 9px 18px;
            border-radius: 10px;
            border: none;
            background: linear-gradient(135deg, #1f3c88, #2e57d4);
            color: #fff;
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .5px;
            cursor: pointer;
            box-shadow: 0 3px 12px rgba(31, 60, 136, .35);
            transition: opacity .15s, transform .1s;
            white-space: nowrap;
        }

        .ar-add-btn:hover {
            opacity: .9;
            transform: translateY(-1px);
        }

        .ar-field {
            display: flex;
            flex-direction: column;
            gap: 5px;
            margin-bottom: 12px;
        }

        .ar-field label {
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .4px;
            color: #666;
        }

        .ar-field input {
            padding: 10px 12px;
            border: 1.5px solid #e0e0e0;
            border-radius: 10px;
            font-size: 13px;
            font-family: inherit;
            transition: border-color .15s;
            box-sizing: border-box;
            width: 100%;
            background: #fff;
        }

        .ar-field input:focus {
            outline: none;
            border-color: #1f3c88;
        }

        .ar-save-btn {
            width: 100%;
            padding: 13px;
            border-radius: 12px;
            border: none;
            background: linear-gradient(135deg, #1f3c88, #2e57d4);
            color: #fff;
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 14px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .5px;
            cursor: pointer;
            margin-top: 6px;
            margin-bottom: 18px;
            box-shadow: 0 4px 14px rgba(31, 60, 136, .35);
            transition: opacity .15s;
        }
    </style>
@endpush

@section('content')
    <div style="padding: 6px 0 110px;">

        {{-- Page Header --}}
        <div style="margin-bottom:18px; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <div class="section-title" style="margin-bottom:4px;">📋 Absence Detail Management</div>
                <div class="mm-page-sub">Kelola alasan absen (Cuti, Sakit, Ijin, dll) agar dinamis.</div>
            </div>
            <button class="ar-add-btn" onclick="openArModal()">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19" />
                    <line x1="5" y1="12" x2="19" y2="12" />
                </svg>
                Tambah Alasan
            </button>
        </div>

        @if(session('success'))
            <div style="background:#e8f5e9; color:#2e7d32; padding:12px; border-radius:10px; margin-bottom:18px; font-family:'Roboto Condensed',sans-serif; font-weight:700; font-size:13px;">
                {{ session('success') }}
            </div>
        @endif

        {{-- Absence Reason List --}}
        <div class="ar-list">
            @forelse($reasons as $reason)
                <div class="ar-item">
                    <div class="ar-color-preview" style="background:{{ $reason->color }};"></div>
                    <div class="ar-item-info">
                        <div class="ar-item-name">{{ $reason->name }}</div>
                    </div>
                    <div class="ar-item-actions">
                        <button class="ar-act-btn ar-act-edit" 
                            onclick="openArModal({{ $reason->id }}, '{{ addslashes($reason->name) }}', '{{ $reason->color }}')">
                            ✏️ Edit
                        </button>
                        <form action="{{ route('admin.absence-reasons.destroy', $reason->id) }}" method="POST" onsubmit="return confirm('Hapus alasan ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="ar-act-btn ar-act-del">🗑️</button>
                        </form>
                    </div>
                </div>
            @empty
                <div style="text-align:center; padding:40px; color:#aaa;">Belum ada alasan absen.</div>
            @endforelse
        </div>

    </div>

    {{-- Add/Edit Absence Reason Modal --}}
    <div class="modal-overlay" id="arModal">
        <div class="modal-sheet">
            <div class="modal-sheet-handle"></div>
            <div class="modal-sheet-header">
                <h3 id="arModalTitle">Tambah Alasan</h3>
                <button class="modal-sheet-close" onclick="closeSheet('arModal')">✕</button>
            </div>
            <div class="mm-modal-body">
                <form id="arForm" method="POST" action="{{ route('admin.absence-reasons.store') }}">
                    @csrf
                    <div id="arMethod"></div>

                    <div class="ar-field">
                        <label>Nama Alasan *</label>
                        <input type="text" name="name" id="arName" placeholder="Contoh: Mangkir" required>
                    </div>

                    <div class="ar-field">
                        <label>Warna Label *</label>
                        <input type="color" name="color" id="arColor" value="#888888" style="height:44px; padding:4px;">
                    </div>

                    <button type="submit" class="ar-save-btn">💾 Simpan</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function openArModal(id = null, name = '', color = '#888888') {
            const isEdit = !!id;
            const form = document.getElementById('arForm');
            const title = document.getElementById('arModalTitle');
            const methodDiv = document.getElementById('arMethod');
            
            title.textContent = isEdit ? '✏️ Edit Alasan' : '➕ Tambah Alasan';
            form.action = isEdit ? `/admin/absence-reasons/${id}` : '{{ route('admin.absence-reasons.store') }}';
            methodDiv.innerHTML = isEdit ? '<input type="hidden" name="_method" value="PUT">' : '';
            
            document.getElementById('arName').value = name;
            document.getElementById('arColor').value = color;
            
            openSheet('arModal');
        }
    </script>
@endpush
