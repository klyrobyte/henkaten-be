{{--
Halaman mesin sudah dipindah ke Dashboard.
File ini tidak lagi dipakai - lihat MachineController@index yang sekarang redirect ke dashboard.
--}}
@extends('layouts.admin')
@section('title', 'Mesin')
@section('content')
    <div
        style="display:flex;flex-direction:column;align-items:center;justify-content:center;min-height:60vh;gap:16px;padding:24px;text-align:center">
        <div style="font-size:48px">⚙️</div>
        <div style="font-family:'Roboto Condensed',sans-serif;font-size:14px;font-weight:700;color:var(--navy)">Halaman
            Mesin Dipindah</div>
        <div style="font-size:13px;color:#888;max-width:280px">Status mesin sekarang tersedia langsung di halaman Dashboard.
        </div>
        <a href="{{ route('admin.dashboard') }}"
            style="margin-top:8px;padding:10px 24px;background:var(--navy);color:#fff;border-radius:10px;text-decoration:none;font-family:'Roboto Condensed',sans-serif;font-weight:700;font-size:13px">
            🏠 Ke Dashboard
        </a>
    </div>
    <script>
        // Auto redirect setelah 1.5 detik
        setTimeout(() => { window.location.href = '{{ route('admin.dashboard') }}'; }, 1500);
    </script>
@endsection