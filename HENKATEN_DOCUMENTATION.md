# 🏭 HENKATEN BOARD - System Documentation

Henkaten Board adalah sistem informasi terintegrasi berbasis **Laravel** yang dirancang khusus untuk manajemen lantai produksi (Shopfloor Management). Kata "Henkaten" (変更点) berasal dari bahasa Jepang yang berarti "Poin Perubahan" dalam elemen produksi: **Man (Manusia), Machine (Mesin), Material (Material), dan Method (Metode)** (4M). 

Sistem ini digunakan untuk memantau perubahan tersebut secara *real-time* di seluruh Service Center (SC) dan Factory, baik melalui panel Admin maupun visualisasi layar besar (TV Mode) di area produksi.

---

## 🎯 Tujuan Utama Sistem
1. **Digitalisasi Papan Henkaten**: Mengganti pencatatan manual kehadiran, status mesin, dan problem produksi menjadi digital.
2. **Monitoring Real-Time (TV Mode)**: Memberikan informasi instan kepada Leader, Supervisor, dan Manajemen mengenai status Manpower dan Mesin saat itu juga (per-shift).
3. **Isolasi Data (Multi-Tenant SC)**: Memisahkan data berdasarkan Service Center (SC) secara ketat, di mana pengguna hanya bisa melihat/mengubah data yang sesuai dengan cakupan (scope) SC dan Factory yang ditugaskan kepadanya.

---

## 🏗️ Arsitektur & Teknologi Utama
- **Framework Utama**: Laravel (PHP)
- **Database**: MySQL (dikelola via Laravel Migrations & Eloquent ORM)
- **Frontend / UI**: Laravel Blade Templates + JavaScript Vanilla + CSS native (dengan pendekatan responsif dan interaktif).
- **Arsitektur Keamanan**: 
  - `ScContextGuard`: Middleware yang mengisolasi akses antar Service Center.
  - `VerifyAppSecret` / Nonce: Proteksi request API antara web app dan browser client.
  - Role-based Access Control (SuperAdmin, Admin, SPV, TL, GL, KY, Operator, TV).

---

## 🚀 Fitur Inti (Core Features)

### 1. 👥 Manpower (Manajemen Member & Absensi)
Mengelola status dan penugasan karyawan per shift:
- **Member Management**: CRUD data karyawan (Nama, NIK, Jabatan, Mesin). Mendukung fitur *Import/Export via Excel*. Terdapat pengelompokan jabatan: **Operator** dan **Pengawas (SPV, TL, GL, KY)**.
- **Sistem Shift Dinamis**: Member di-mapping ke `Shift A`, `Shift B`, atau `NS (Non-Shift)`. 
  - *Fitur NS*: Member NS secara cerdas mengikuti siklus rotasi shift yang sedang berjalan pada minggu tersebut (bergantian antara A dan B) berkat sistem `NonShiftResolver`.
- **Modul Absensi**: Supervisor dapat mencatat kehadiran harian (Hadir, Sakit, Cuti, Ijin, Alpha). Absensi ini otomatis menghitung persentase kehadiran per shift dan menampilkannya dalam ringkasan grafis (Absence Summary).
- **Pengganti (Replacement)**: Apabila *Key Person* absen, sistem memungkinkan input "Member Pengganti" (Assignment Replacement) untuk mengisi kekosongan mesin tersebut pada shift berjalan.

### 2. ⚙️ Machine (Manajemen Mesin & Floor Plan)
Pemetaan fisik mesin di dalam pabrik:
- **Floor Plan Visualizer**: Denah pabrik interaktif (Drag & Drop) di mana letak mesin divisualisasikan.
- **Machine Status**: Memantau apakah mesin sedang beroperasi normal, perbaikan, atau stop. Menampilkan indikator lampu (hijau/kuning/merah).
- **Kategorisasi Mesin**: Pengelompokan mesin berdasarkan *Section* (cth: Resin Injection, Robot Assy).

### 3. ⚠️ Problem Logs (Catatan Perubahan / Isu)
Fitur sentral "Henkaten", mencatat perubahan atau masalah selain absensi:
- **Log Mesin (Machine)**: Pencatatan kerusakan mesin (breakdown), perbaikan, dan waktu mulai-selesai.
- **Log Material**: Pencatatan keterlambatan part, part NG (Not Good), dsb.
- **Log Metode (Method)**: Perubahan instruksi kerja atau metode standar produksi.
- Sistem mencatat secara mendetail "Kapan masalah terjadi", "Apa masalahnya", "Penanggulangan (Countermeasure)", dan "Status Log (Open/Closed)".

### 4. 📺 TV Mode (Dashboard Produksi)
Mode visual (Read-only) khusus untuk dipasang di layar TV di lantai produksi:
- Menampilkan persentase kehadiran (*Manpower Status*).
- Menampilkan grafik kondisi pabrik.
- Auto-refresh setiap beberapa detik tanpa perlu refresh halaman penuh (berbasis Ajax).
- Menginformasikan secara realtime masalah (Problem Logs) yang sedang berstatus *Open*.

### 5. 🌐 SC Management (Service Center) & Hierarki
- Sebuah platform Henkaten dapat menaungi banyak "Service Center" (SC).
- **SuperAdmin** dapat mengatur seluruh SC, menambah pabrik (Factory), dan mengelola pengguna (User Management).
- Tiap **User** (Admin/GL) hanya ditugaskan pada Factory tertentu di dalam satu SC tertentu. Semua query, list, dan laporan (Absensi, Logs) difilter secara ketat agar data SC A tidak bocor ke SC B.

---

## 🔄 Workflow Sistem (Cara Kerja Sehari-hari)

Berikut adalah siklus penggunaan Henkaten Board di lapangan:

1. **Awal Shift (Preparation)**
   - Group Leader (GL) / Pengawas membuka halaman **Input Absen** untuk pabrik dan shiftnya.
   - Sistem menarik daftar member aktif (termasuk member Non-Shift / NS yang rotasinya jatuh pada shift tersebut minggu ini).
   - GL menandai siapa saja yang tidak hadir dan alasannya, lalu menekan **Simpan & Sync**.
   - Sistem secara otomatis menghitung *Absence Summary* (berapa SPV hadir, berapa Operator cuti, dll).

2. **Penentuan Pengganti (Manpower Allocation)**
   - Jika ada Key Person (misal TL) yang absen, GL membuka **Penugasan Harian (Daily Assignment)**.
   - Mengalokasikan personil pengganti dari mesin lain agar lini produksi tidak terhenti.

3. **Berjalannya Shift (Execution)**
   - Layar **TV Mode** di tengah pabrik otomatis memperbarui angka kehadiran menjadi *Real-time*.
   - Jika terjadi kendala mesin, GL memasukkan laporan ke menu **Problem Logs** (Jenis: Mesin).
   - Begitu log masuk, alarm / indikator di TV Mode akan menyala menandakan ada status "Open" pada masalah mesin di Factory tersebut. Tim *Repair Department* bisa segera merespons.
   - Ketika masalah beres, log di-*close*.

4. **Akhir Shift (Reporting)**
   - Admin/SuperAdmin bisa melihat **Laporan (Report)** untuk mengekspor rekap Absensi, Logs, dan status Mesin dalam bentuk Excel atau CSV.

---

## 📅 Logika Khusus: Rotasi "Non-Shift" (NS)
*Implementasi: `app/Services/NonShiftResolver.php`*

Dalam pabrik, tidak semua orang terikat mati pada "Shift A" (Malam terus) atau "Shift B" (Pagi terus). Ada peran *Non-Shift (NS)* (contohnya Leader atau mekanik) yang masuk di shift pagi secara bergantian minggu demi minggu.
- **Minggu Ganjil**: NS bekerja bersama regu Shift A.
- **Minggu Genap**: NS bekerja bersama regu Shift B.
Sistem Henkaten tidak memerlukan GL untuk memindah-mindahkan NS secara manual tiap hari Senin. Modul `NonShiftResolver` melacak tanggal saat ini (dibandingkan dengan tanggal Epoch pertama `NS_SHIFT_EPOCH`) dan secara magis menampilkan daftar member NS ke layar absensi Shift A atau Shift B yang sesuai.

---

*Dokumentasi ini mencakup secara umum bagaimana Henkaten Board berfungsi dalam lingkungan pabrik industri modern.*
