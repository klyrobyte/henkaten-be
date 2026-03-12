# Ringkasan Struktur & Logika Project HENKATEN (Laravel)

## Struktur Folder Utama
- **app/**
  - **Http/Controllers/**: Controller utama (Dashboard, Machine, Log, Absence, Assignment, dsb)
  - **Models/**: Model Eloquent (AbsenceRecord, AbsenceSummary, AssignmentReplacement, Machine, MachineStatus, Member, ProblemLog, User)
  - **Services/**: Logika bisnis khusus (AbsenceSummaryService, FactoryConfigService)
- **config/**: Konfigurasi aplikasi (auth, filesystems, dsb)
- **database/**: Migration, seeder, dan factory
- **public/**: Entry point aplikasi & asset publik
- **resources/**: View (Blade), JS, CSS
- **routes/**: Definisi route (web.php, console.php)
- **storage/**: File upload, cache, log
- **tests/**: Unit & feature test

## Alur & Logika Utama
- **Autentikasi**: Login pakai username (bukan email), role-based (superadmin, admin, operator)
- **Dashboard**: Menampilkan ringkasan status mesin, absensi, dan problem log harian per factory & shift
- **Absensi**: 
  - Data absensi per member per hari (AbsenceRecord)
  - Rekap absensi otomatis (AbsenceSummary) dihitung oleh AbsenceSummaryService
  - Absensi dibedakan Operator & SPV (mapping jabatan)
- **Penugasan & Pengganti**:
  - AssignmentReplacement: Catat pengganti mesin jika ada member absen
  - Assignment harian: Penugasan member ke mesin
- **Problem Log**: 
  - Catat masalah harian (jenis: Machine, Material, Method)
  - Status open/close, durasi, PIC, dsb
- **Machine & Status**:
  - Data mesin per factory (FactoryConfigService)
  - Status mesin harian (MachineStatus)
- **Member**:
  - Data karyawan, relasi ke absensi, assignment, dsb

## Service & Helper
- **AbsenceSummaryService**: Hitung rekap absensi (Operator/SPV, hadir/cuti/sakit/ijin/mangkir)
- **FactoryConfigService**: Konfigurasi mesin & grup per factory, jumlah slot/circle mesin

## Route Penting (web.php)
- /login, /logout
- /admin (dashboard, status, context)
- /admin/machines, /admin/logs, /admin/attendance, /admin/reports, /admin/members
- /admin/absence, /admin/assignment, /admin/replacements

## Catatan Pengembangan Lanjutan
- Struktur sudah modular, mudah dikembangkan (tambah fitur, factory, jenis mesin, dsb)
- Logika absensi & pengganti terpisah, mudah diubah
- Service terpisah untuk perhitungan & konfigurasi
- Model relasi sudah rapi (Eloquent)
- Perhatikan migration jika menambah kolom/tabel baru

---

**Lihat SETUP.md untuk panduan setup & troubleshooting.**
