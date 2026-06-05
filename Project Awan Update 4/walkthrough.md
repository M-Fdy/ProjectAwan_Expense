# Walkthrough & Panduan Eksekusi: Laravel Personal Finance Manager (Awan Expense)

Aplikasi **Awan Expense** kini telah dikembangkan sepenuhnya menjadi pengelola keuangan pribadi terperinci. Aplikasi ini melacak **Pemasukan** (Incomes) dan **Pengeluaran** (Expenses), menghitung **Saldo Aktif**, menyajikan **diagram visual Chart.js** yang dinamis, serta menyediakan **riwayat transaksi terpadu** dengan filter instan.

Berikut adalah ringkasan perubahan, penjelasan arsitektur baru, dan panduan lengkap langkah-demi-langkah bagi Anda.

---

## 1. Ringkasan File yang Dibuat & Dimodifikasi

Semua file dikonfigurasi menggunakan standar penamaan yang bersih dan modern:

### Konfigurasi & Migrasi Database
* **[add_type_to_categories_table.php](file:///c:/Users/Pongo/Downloads/Project%20Awan/Project%20Awan/database/migrations/2026_06_04_000000_add_type_to_categories_table.php)**: Menambahkan kolom `type` ('expense' / 'income') ke tabel `categories` untuk mengkategorikan kategori transaksi secara logis.
* **[create_incomes_table.php](file:///c:/Users/Pongo/Downloads/Project%20Awan/Project%20Awan/database/migrations/2026_06_04_000001_create_incomes_table.php)**: Membuat tabel `incomes` untuk menampung data transaksi pemasukan.
* **[CategorySeeder.php](file:///c:/Users/Pongo/Downloads/Project%20Awan/Project%20Awan/database/seeders/CategorySeeder.php)**: Memperbarui proses seeder untuk menyemai kategori pengeluaran dan pemasukan (Gaji, Investasi, Usaha, Hadiah, Lain-lain).

### Models & Relasi Eloquent
* **[Category.php](file:///c:/Users/Pongo/Downloads/Project%20Awan/Project%20Awan/app/Models/Category.php)**: Membuka akses fillable `type` dan menetapkan relasi `incomes()` (`hasMany`).
* **[User.php](file:///c:/Users/Pongo/Downloads/Project%20Awan/Project%20Awan/app/Models/User.php)**: Menambahkan relasi `incomes()` (`hasMany`).
* **[Income.php](file:///c:/Users/Pongo/Downloads/Project%20Awan/Project%20Awan/app/Models/Income.php)**: Membuat model baru untuk pemasukan lengkap dengan relasi `belongsTo` ke `User` dan `Category`.

### Controllers & Routing
* **[IncomeController.php](file:///c:/Users/Pongo/Downloads/Project%20Awan/Project%20Awan/app/Http/Controllers/IncomeController.php)**: Mengontrol operasi CRUD (Simpan, Ubah, Hapus) untuk transaksi pemasukan.
* **[DashboardController.php](file:///c:/Users/Pongo/Downloads/Project%20Awan/Project%20Awan/app/Http/Controllers/DashboardController.php)**: Controller terpusat untuk memuat data pengeluaran dan pemasukan, menghitung statistik dasbor, serta menyusun data visualisasi bagan Chart.js.
* **[web.php](file:///c:/Users/Pongo/Downloads/Project%20Awan/Project%20Awan/routes/web.php)**: Memetakan rute `/home` ke `DashboardController` dan menambahkan endpoint CRUD untuk `/incomes`.

### Antarmuka Pengguna (View)
* **[home.blade.php](file:///c:/Users/Pongo/Downloads/Project%20Awan/Project%20Awan/resources/views/home.blade.php)**: Dasbor premium glassmorphic baru yang mencakup:
  1. **Kartu Statistik Ringkasan**: Saldo Aktif, Total Pemasukan, Total Pengeluaran.
  2. **Formulir Tab Dinamis**: Toggle instan antara form "Catat Pengeluaran" dan "Catat Pemasukan".
  3. **Visualisasi Bagan (Chart.js)**: Doughnut chart untuk Rasio Keuangan (Uang Masuk vs Keluar) dan Pie chart untuk penyebaran kategori masing-masing tipe transaksi.
  4. **Riwayat Transaksi Terpadu**: Tabel kronologis gabungan antara pemasukan dan pengeluaran.
  5. **Filter Client-side Instan**: Tombol filter "Semua", "Pemasukan", dan "Pengeluaran".
  6. **Modal Edit Universal**: Modal tunggal yang menyesuaikan judul, route, dan dropdown kategori secara dinamis menggunakan JS saat mendeteksi tipe transaksi.

---

## 2. Panduan Langkah-demi-Langkah Menjalankan Aplikasi

Berikut adalah urutan perintah untuk mengaktifkan dan menguji aplikasi di lingkungan lokal Anda (XAMPP / Powershell):

### Langkah A: Migrasi Database & Seeding
Terapkan skema tabel baru dan isi data awal dengan menjalankan:
```bash
php artisan migrate:fresh --seed
```
* **Database**: `expense_tracker` (sesuai konfigurasi `.env`).
* **Akun Demo Bawaan**:
  * **Email**: `admin@example.com`
  * **Password**: `password123`

### Langkah B: Jalankan Server Lokal
Jalankan server pengembangan Laravel:
```bash
php artisan serve
```
Akses di browser Anda melalui alamat: **[http://127.0.0.1:8000](http://127.0.0.1:8000)**.

---

## 3. Hasil Pengujian & Verifikasi Otomatis

Seluruh alur aplikasi, otentikasi sesi, serta fitur CRUD pemasukan/pengeluaran dan kalkulasi saldo akhir dasbor telah diverifikasi dengan **13 tests (34 assertions) yang berhasil lulus**.

Untuk menjalankan pengujian kembali:
```bash
php artisan test
```

Hasil pengujian terbaru:
```
   PASS  Tests\Unit\ExampleTest
  ✓ that true is true

   PASS  Tests\Feature\ExampleTest
  ✓ root redirects to login
  ✓ login page renders
  ✓ register page renders
  ✓ home page requires authentication
  ✓ authenticated user can access home
  ✓ authenticated user can create expense
  ✓ authenticated user can update expense
  ✓ authenticated user can delete expense
  ✓ authenticated user can create income
  ✓ authenticated user can update income
  ✓ authenticated user can delete income
  ✓ dashboard displays correct balance and totals

  Tests:    13 passed (34 assertions)
  Duration: 1.37s
```
