# HENKATEN Laravel  - Panduan Setup & Fix Login

## Masalah: Setelah login tidak redirect ke dashboard

Penyebabnya ada **3 hal yang harus diatur** setelah `composer create-project`:

---

## LANGKAH 1  - Hapus migration bawaan Laravel

Laravel 11 generate migration default ini yang **konflik** dengan migration kita:

```
database/migrations/0001_01_01_000000_create_users_table.php   ← HAPUS
database/migrations/0001_01_01_000001_create_cache_table.php   ← HAPUS (opsional)
database/migrations/0001_01_01_000002_create_jobs_table.php    ← HAPUS (opsional)
```

> Migration default Laravel buat tabel `users` dengan kolom `email`,
> sedangkan HENKATEN pakai `username`. Kalau keduanya jalan, tabel salah.

```bash
# Di root project Laravel:
rm database/migrations/0001_01_01_000000_create_users_table.php
rm database/migrations/0001_01_01_000001_create_cache_table.php
rm database/migrations/0001_01_01_000002_create_jobs_table.php
```

---

## LANGKAH 2  - Copy file dari output/

Copy semua file dari folder `output/` ke root project Laravel:

```bash
# Dari folder henkaten-laravel/output/ ke project Laravel
cp -r output/app/               your-laravel-project/app/
cp -r output/config/auth.php    your-laravel-project/config/auth.php
cp -r output/database/          your-laravel-project/database/
cp -r output/resources/         your-laravel-project/resources/
cp -r output/routes/web.php     your-laravel-project/routes/web.php
cp -r output/public/assets/     your-laravel-project/public/assets/
```

---

## LANGKAH 3  - Jalankan migration + seeder

```bash
cd your-laravel-project

# Jalankan migration (fresh jika ada masalah)
php artisan migrate

# Atau jika mau reset dari awal:
php artisan migrate:fresh

# Buat symlink storage (untuk foto member)
php artisan storage:link

# Seeder user (skip kalau mau buat user manual)
# php artisan db:seed --class=UserSeeder
```

---

## LANGKAH 4  - Buat user login manual

Karena saya skip seeder, buat user via Tinker:

```bash
php artisan tinker
```

Lalu paste ini:

```php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

User::create([
    'username' => 'admin',
    'password' => Hash::make('admin123'),
    'role'     => 'superadmin',
    'name'     => 'Administrator',
]);
```

Atau buat semua user sekaligus:

```php
$users = [
    ['username'=>'admin',   'password'=>Hash::make('admin123'),   'role'=>'superadmin', 'name'=>'Super Admin'],
    ['username'=>'sugity',  'password'=>Hash::make('sugity123'),  'role'=>'admin',      'name'=>'Sugity Admin'],
    ['username'=>'factory', 'password'=>Hash::make('factory123'), 'role'=>'operator',   'name'=>'Factory Operator'],
];
foreach ($users as $u) User::create($u);
```

Ketik `exit` untuk keluar Tinker.

---

## LANGKAH 5  - Test login

```bash
php artisan serve
```

Buka http://localhost:8000/login

| Username | Password   |
|----------|------------|
| admin    | admin123   |
| sugity   | sugity123  |
| factory  | factory123 |

---

## Troubleshooting

### Error: "SQLSTATE: Column not found: email"
→ Migration default Laravel masih ada. Hapus file di Langkah 1, lalu `php artisan migrate:fresh`.

### Error: "username or password incorrect" padahal benar
→ User belum dibuat. Jalankan Tinker di Langkah 4.

### Redirect balik ke /login terus setelah login
→ Cek `SESSION_DRIVER` di `.env`. Pastikan bukan `array`.
→ Jalankan: `php artisan config:clear && php artisan cache:clear`

### Error 419 (CSRF token mismatch) saat submit login
→ Jalankan: `php artisan config:clear`
→ Pastikan `.env` punya `APP_KEY` (jalankan `php artisan key:generate` jika kosong)

### Halaman kosong / error 500
→ Cek `storage/logs/laravel.log` untuk detail error.
→ Pastikan permission: `chmod -R 775 storage bootstrap/cache`
